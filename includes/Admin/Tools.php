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
            $content = $this->prepare_csv_file_data(get_option(SIMPLE301REDIRECTS_SETTINGS_NAME, []));
            $filename = 'simple-301-redirects.' . gmdate('Y-m-d') . '.csv';
            // Drop anything already buffered (notices from other code) so it can't end up in the file.
            while (ob_get_level() > 0) {
                ob_end_clean();
            }
            nocache_headers();
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="'.$filename.'";');
            $f = fopen('php://output', 'w');
            foreach ($content as $line) {
                // PHP 8.4+ prints a deprecation into the file unless $escape is passed; "\\" is the old default.
                fputcsv($f, $line, ',', '"', '\\');
            }
            exit();
        }
    }
    public function prepare_csv_file_data($data)
    {
        if (!is_array($data) || count($data) === 0) {
            return [];
        }
        $formatted_data = [];
        foreach ($data as $key => $value) {
            $formatted_data[] = [
                $key,
                $value
            ];
        }
        return array_merge([['request', 'destination']], $formatted_data);
    }
    public function import_data()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        $import = isset($_REQUEST['import']) ? $_REQUEST['import'] : false;
        if ($page === '301options' && $import == true && current_user_can('manage_options')) {
            check_ajax_referer('simple301redirects', 'security');
            $file = isset($_FILES['upload_file']) ? $_FILES['upload_file'] : [];
            if (!empty($file['tmp_name']) && 'csv' === strtolower(pathinfo($file['name'], PATHINFO_EXTENSION))) {
                // Streams the uploaded CSV row by row; WP_Filesystem has no stream reader.
                $fileContent = fopen($file['tmp_name'], "r"); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
                if (!empty($fileContent)) {
                    $results = $this->process_data($fileContent);
                    fclose($fileContent); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
                    set_transient('simple_301_redirects_import_info', json_encode($results), 60 * 60 * 5);
                }
            }
        }
    }
    public function process_data($csv)
    {
        $data = [];
        $request_col = null;
        $destination_col = null;
        while (($item = fgetcsv($csv, 0, ',', '"', '\\')) !== false) {
            if ($request_col === null) {
                // Skip rows until the "request,destination" header. Files exported on PHP 8.4+ by
                // older versions have PHP notices (HTML) before it, sometimes glued to the header.
                $header = array_map(function ($cell) {
                    return strtolower(trim(wp_strip_all_tags(str_replace("\xEF\xBB\xBF", '', (string) $cell))));
                }, $item);
                $request_index = array_search('request', $header, true);
                $destination_index = array_search('destination', $header, true);
                if ($request_index !== false && $destination_index !== false) {
                    $request_col = $request_index;
                    $destination_col = $destination_index;
                }
                continue;
            }
            if (!isset($item[$request_col], $item[$destination_col])) {
                continue;
            }
            $request = sanitize_text_field($item[$request_col]);
            $destination = sanitize_text_field($item[$destination_col]);
            if ($request === '' || $destination === '') {
                continue;
            }
            $data[$request] = $destination;
        }
        if (count($data) === 0) {
            return __('No redirects found in the file. Its header row must have a request column and a destination column.', 'simple-301-redirects');
        }
        $oldData = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
        $value = (is_array($oldData) && !empty($oldData) ? array_merge($oldData, $data) : $data);
        // update_option() returns false when nothing changed, which is still a successful import.
        if ($value === $oldData || update_option(SIMPLE301REDIRECTS_SETTINGS_NAME, $value)) {
            return __('All Data has been successfully Imported.', 'simple-301-redirects');
        }
        return __('Import Failed.', 'simple-301-redirects');
    }
    public function get_import_info()
    {
        check_ajax_referer('simple301redirects', 'security');
        if( ! current_user_can( 'manage_options' ) ) wp_die();
        $results = get_transient('simple_301_redirects_import_info');
        if ($results) {
            delete_transient('simple_301_redirects_import_info');
            wp_send_json_success($results);
            wp_die();
        }
        wp_send_json_error($results);
        wp_die();
    }
}
