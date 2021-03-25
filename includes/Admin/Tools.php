<?php 
namespace Simple301Redirects\Admin;

class Tools { 
    public function __construct()
    {
        add_action('admin_init', [$this, 'export_data']);
        add_action('admin_init', [$this, 'import_data']);
        add_action('wp_ajax_simple301redirects/admin/get_import_info', [$this, 'get_import_info']);
    }
    public function export_data()
    {
        $page = isset($_GET['page']) ? $_GET['page'] : '';
		$export = isset($_GET['export']) ? $_GET['export'] : false;
		if ($page === '301options' && $export == true) {
			$content = json_encode(get_option(SIMPLE301REDIRECTS_SETTINGS_NAME));
			$filename = 'simple-301-redirects.' . date('Y-m-d') . '.json';
			($file = fopen($filename, 'w')) or die('Unable to open file!');
			fwrite($file, $content);
			fclose($file);
			header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
			header('Content-Type: application/force-download');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Type: text/plain');

			echo $content;
			exit();
		}
    }
    public function import_data()
	{
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		$import = isset($_GET['import']) ? $_GET['import'] : false;
		if ($page === '301options' && $import == true) {
			if (!empty($_FILES['upload_file']['tmp_name'])) {
				$fileContent = json_decode(file_get_contents($_FILES['upload_file']['tmp_name']), true);
                if (!empty($fileContent)) {
                    $results = $this->process_data($fileContent);
                    $_SESSION['simple_301_redirects_import_info'] = json_encode($results);
                } 
			}
		}
    }
    public function process_data($data)
	{
		$message = '';
		if (isset($data) && is_array($data) && count($data) > 0) {
            $oldData = get_option(SIMPLE301REDIRECTS_SETTINGS_NAME);
            $value = (!empty($oldData) ? array_unique (array_merge (get_option(SIMPLE301REDIRECTS_SETTINGS_NAME), $data)) : $data);
            $restuls = update_option(SIMPLE301REDIRECTS_SETTINGS_NAME, $value);
            if( $restuls){
                $message = 'All Data has been successfully Imported.';
            } else {
                $message = 'Import Failed.';
            }
		}
		return $message;
    }
    public function get_import_info()
	{
		check_ajax_referer('wp_rest', 'security');
		$results = '';
		if (isset($_SESSION['simple_301_redirects_import_info'])) {
			$results = $_SESSION['simple_301_redirects_import_info'];
			unset($_SESSION['simple_301_redirects_import_info']);
		}
		wp_send_json_success($results);
		wp_die();
	}
}