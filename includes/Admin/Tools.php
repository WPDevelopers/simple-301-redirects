<?php
namespace Simple301Redirects\Admin;

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
            $file = $_FILES['upload_file'];
            if (!empty($file['tmp_name']) && 'csv' === pathinfo($file['name'])[ 'extension' ]) {
                $fileContent = fopen($file['tmp_name'], "r");
                if (!empty($fileContent)) {
                    $results = $this->process_data($fileContent);
                    set_transient('simple_301_redirects_import_info', json_encode($results), 60 * 60 * 5);
                }
            }
        }
    }
    public function process_data($csv)
    {
        $message = '';
        $count = 0;
        $data = [];
        while (($item = fgetcsv($csv)) !== false) {
            if ($count === 0) {
                $this->link_header = $item;
                $count++;
                continue;
            }
            $item = array_combine($this->link_header, $item);
            $item = \Simple301Redirects\Helper::sanitize_text_or_array_field($item);
            $data[$item['request']] = $item['destination'];
        }
        if (count($data) > 0) {
            $oldData = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
            $value = (!empty($oldData) ? array_unique(array_merge(get_option(SIMPLE301REDIRECTS_SETTINGS_NAME), $data)) : $data);
            $restuls = update_option(SIMPLE301REDIRECTS_SETTINGS_NAME, $value);
            if ($restuls) {
                $message = 'All Data has been successfully Imported.';
            } else {
                $message = 'Import Failed.';
            }
        }
        return $message;
    }
    public function get_import_info()
    {
        check_ajax_referer('simple301redirects', 'security');
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
