<?php
namespace Simple301Redirects\Admin;

#[\AllowDynamicProperties]
class Tools
{
    public function __construct()
    {
        add_action('admin_init', [$this, 'export_data']);
        add_action('admin_init', [$this, 'import_data']);
        add_action('wp_ajax_simple301redirects/admin/get_import_info', [$this, 'get_import_info']);
    }
    public function export_data()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        $export = isset($_REQUEST['export']) ? $_REQUEST['export'] : false;
        if ($page === '301options' && $export == true && current_user_can('manage_options')) {
            check_ajax_referer('simple301redirects', 'security');
            $content = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
            $content = $this->prepare_csv_file_data(get_option(SIMPLE301REDIRECTS_SETTINGS_NAME));
            $filename = 'simple-301-redirects.' . date('Y-m-d') . '.csv';
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            $f = fopen('php://output', 'w');
            foreach ($content as $line) {
                fputcsv($f, $line);
            }
            exit();
        }
    }
    public function prepare_csv_file_data($data)
    {
        $formatted_data = [];
        foreach ($data as $key => $value) {
            $formatted_data[] = [
                $key,
                $value
            ];
        }
        if (is_array($data) && count($data) > 0) {
            return array_merge([['request', 'destination']], $formatted_data);
        }
        return [];
    }
    public function import_data()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        $import = isset($_REQUEST['import']) ? $_REQUEST['import'] : false;
        if ($page === '301options' && $import == true && current_user_can('manage_options')) {
            check_ajax_referer('simple301redirects', 'security');

            $file = isset($_FILES['upload_file']) ? $_FILES['upload_file'] : null;
            $message = '';

            // The browser/PHP rejected the upload before it reached us (size, partial, none).
            if (empty($file) || empty($file['tmp_name']) || (isset($file['error']) && $file['error'] !== UPLOAD_ERR_OK)) {
                $upload_errors = [
                    UPLOAD_ERR_INI_SIZE  => __('The file exceeds the server upload limit (upload_max_filesize). Please import a smaller file or raise the limit.', 'simple-301-redirects'),
                    UPLOAD_ERR_FORM_SIZE => __('The file exceeds the allowed form size limit.', 'simple-301-redirects'),
                    UPLOAD_ERR_PARTIAL   => __('The file was only partially uploaded. Please try again.', 'simple-301-redirects'),
                    UPLOAD_ERR_NO_FILE   => __('No file was selected. Please choose a .csv file to import.', 'simple-301-redirects'),
                ];
                $code = isset($file['error']) ? $file['error'] : UPLOAD_ERR_NO_FILE;
                $message = isset($upload_errors[$code]) ? $upload_errors[$code] : __('The file could not be uploaded. It may exceed the server upload limit.', 'simple-301-redirects');
            } elseif (strtolower((string) pathinfo($file['name'], PATHINFO_EXTENSION)) !== 'csv') {
                // Accept .csv regardless of letter case; reject anything else with a clear reason.
                $message = __('Invalid file type. Please upload a file with a .csv extension.', 'simple-301-redirects');
            } else {
                $fileContent = fopen($file['tmp_name'], 'r');
                if (empty($fileContent)) {
                    $message = __('The uploaded file could not be opened for reading.', 'simple-301-redirects');
                } else {
                    $message = $this->process_data($fileContent);
                    fclose($fileContent);
                }
            }

            // Always store a human-readable result so the UI can show *why* it failed.
            set_transient('simple_301_redirects_import_info', $message, 60 * 60 * 5);
        }
    }
    public function process_data($csv)
    {
        $count = 0;
        $skipped = 0;
        $data = [];
        $this->link_header = [];

        while (($item = fgetcsv($csv)) !== false) {
            // Skip blank lines.
            if ($item === [null] || (count($item) === 1 && trim((string) $item[0]) === '')) {
                continue;
            }

            if ($count === 0) {
                // Strip a UTF-8 BOM (Excel) from the first header cell, then normalize names.
                if (isset($item[0])) {
                    $item[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $item[0]);
                }
                $this->link_header = array_map(function ($header) {
                    return strtolower(trim((string) $header));
                }, $item);
                $count++;
                continue;
            }

            // PHP 8 throws a fatal ValueError if the counts differ — guard before combining.
            if (count($this->link_header) !== count($item)) {
                $skipped++;
                continue;
            }

            $item = array_combine($this->link_header, $item);
            $item = \Simple301Redirects\Helper::sanitize_text_or_array_field($item);

            if (!isset($item['request'], $item['destination'])) {
                $skipped++;
                continue;
            }

            $request = trim((string) $item['request']);
            $destination = trim((string) $item['destination']);
            if ($request === '' || $destination === '') {
                $skipped++;
                continue;
            }

            $data[$request] = $destination;
        }

        // Header didn't contain the columns we need — tell the user exactly what we found.
        if (!in_array('request', $this->link_header, true) || !in_array('destination', $this->link_header, true)) {
            $found = empty($this->link_header) ? __('(no header row found)', 'simple-301-redirects') : implode(', ', $this->link_header);
            /* translators: %s: the column headers detected in the uploaded file. */
            return sprintf(__('Import failed: the CSV must have a header row with "request" and "destination" columns. Detected columns: %s. If your file uses a semicolon separator, re-save it as comma-separated UTF-8.', 'simple-301-redirects'), $found);
        }

        if (count($data) === 0) {
            return $skipped > 0
                /* translators: %d: number of skipped rows. */
                ? sprintf(__('Import failed: no valid redirect rows were found. %d row(s) were skipped due to empty values or a column count that does not match the header.', 'simple-301-redirects'), $skipped)
                : __('Import failed: the file contains a header but no redirect rows.', 'simple-301-redirects');
        }

        $oldData = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
        $value = (!empty($oldData) && is_array($oldData)) ? array_merge($oldData, $data) : $data;

        // update_option() returns false when the stored value is unchanged; that is not a failure.
        if ($value == $oldData) {
            /* translators: %d: number of redirect rules. */
            return sprintf(__('%d redirect rule(s) processed. They already existed, so nothing changed.', 'simple-301-redirects'), count($data));
        }

        $results = update_option(SIMPLE301REDIRECTS_SETTINGS_NAME, $value);
        if ($results) {
            /* translators: %d: number of imported redirect rules. */
            $message = sprintf(__('%d redirect rule(s) imported successfully.', 'simple-301-redirects'), count($data));
            if ($skipped > 0) {
                /* translators: %d: number of skipped rows. */
                $message .= ' ' . sprintf(__('%d row(s) were skipped (empty values or mismatched columns).', 'simple-301-redirects'), $skipped);
            }
            return $message;
        }

        return __('Import failed while saving the redirects to the database.', 'simple-301-redirects');
    }
    public function get_import_info()
    {
        check_ajax_referer('simple301redirects', 'security');
        if (!current_user_can('manage_options')) {
            wp_die();
        }
        $results = get_transient('simple_301_redirects_import_info');
        if ($results !== false) {
            delete_transient('simple_301_redirects_import_info');
            wp_send_json_success($results);
            wp_die();
        }
        wp_send_json_error(__('No import information is available.', 'simple-301-redirects'));
        wp_die();
    }
}
