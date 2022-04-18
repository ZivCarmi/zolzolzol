<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

use WP_REST_Response as Res;

class Rest {
	// namespace for all endpoints
	static private $namespace = 'bt/v1';
	
	static public function init () {
		// register all routes
		add_action('rest_api_init', [__CLASS__, 'register_routes']);
	}
	
	// create and return an api response
	static private function res ($data = [], $status = 200, $headers = []) {
		// create the response object
		$res = new Res($data);
		
		// add status code
		$res->set_status($status);
		
		// if headers were passed, set them
		if (!empty($headers)) {
			foreach ($headers as $handle => $value) {
				$res->header($handle, $value);
			}
		}
		
		return $res;
	}
	
	// register all routes
	static public function register_routes () {
		register_rest_route(self::$namespace, 'test', [
			'methods'  					  => 'get',
			'callback' 					  => [__CLASS__, 'test'],
			'permission_callback' => ['app\classes\helpers\Middleware', 'current_user_can_administrator']
		]);
	}

	static public function test () {
		echo 'Passed!';
	}
}
