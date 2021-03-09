<?php
namespace Simple301Redirects\API;

class Settings {
    private $namespace;
    private $rest_base;
    public function __construct() {
		$this->namespace = 'simple301redirects/v1';
        $this->rest_base = 'settings';
        add_action('rest_api_init', array($this, 'register_routes'));
    }
    
	public function register_routes() {

		register_rest_route(
			$this->namespace,
			'/' . $this->rest_base,
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this, 'get_items' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_args_schema(),
				),
				array(
					'methods'             => \WP_REST_Server::CREATABLE,
					'callback'            => array( $this, 'create_item' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_args_schema(),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this, 'update_item' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_args_schema(),
				),
				array(
					'methods'             => \WP_REST_Server::DELETABLE,
					'callback'            => array( $this, 'delete_item' ),
					'permission_callback' => array( $this, 'permissions_check' ),
					'args'                => $this->get_args_schema(),
				)
			)
		);
    }

    public function get_items($request)
    {
        return new \WP_REST_Response(
			get_option('301_redirects'),
			200
		);
    }

    public function create_item($request)
    {
		$param = $request->get_params();
		$current_data = get_option('301_redirects');
		if(!isset($current_data[$param['key']])){
			$current_data[$param['key']] = $param['value'];
			update_option('301_redirects', $current_data);
		}
		return new \WP_REST_Response(
			get_option('301_redirects'),
			200
		);
	}
	
	public function update_item($request)
	{
		$param = $request->get_params();
		$current_data = get_option('301_redirects');
		if(isset($current_data[$param['oldKey']])){
			if(isset($param['oldKey']) && $param['oldKey'] != $param['key']){
				unset($current_data[$param['oldKey']]);
			}
			$current_data[$param['key']] = $param['value'];
			update_option('301_redirects', $current_data);
		}
		return new \WP_REST_Response(
			get_option('301_redirects'),
			200
		);
	}

	public function delete_item($request)
	{
		$param = $request->get_params();
		$current_data = get_option('301_redirects');
		if(isset($current_data[$param['key']])){
			unset($current_data[$param['key']]);
			update_option('301_redirects', $current_data);
		}
		return new \WP_REST_Response(
			get_option('301_redirects'),
			200
		);
	}

	public function get_args_schema() 
	{
		return [
			'key' => [
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'value' => [
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			],
			'oldKey' => [
				'type' => 'string',
				'sanitize_callback' => 'sanitize_text_field',
			]
		];
	}
    public function permissions_check($request)
	{
		return current_user_can('manage_options');
	}
}