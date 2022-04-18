<?php namespace app\classes\helpers;

defined('ABSPATH') || exit;

use WP_Error as Err;

class Middleware {
  // create and return an api error
	static private function err ($message, $status = 401) {
		return new Err('rest_forbidden', $message, ['status' => $status]);
	}

  static public function current_user_can_administrator () {
    global $wpdb;

    $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];

    if (empty($auth)) return self::err('Missing authorization credentials');
    if (strpos($auth, 'Basic') === false) return self::err('Incorrect authorization type');

    // get user auth base64
    $auth_base64 = explode(' ', $auth)[1];

    // decode user auth
    $auth_decoded = base64_decode($auth_base64);

    if (strpos($auth_decoded, ':') === false) return self::err('Incorrect authorization format');

    $username_password = explode(':', $auth_decoded);

    $res = $wpdb->get_results($wpdb->prepare('SELECT ID, user_pass FROM ' . $wpdb->prefix . 'users WHERE user_login = %s LIMIT 1', $username_password[0]));

    if (empty($res)) return self::err('Incorrect authorization credentials');
    if (!wp_check_password($username_password[1], $res[0]->user_pass)) return self::err('Incorrect authorization credentials');

    // get user
    $user = get_user_by('id', $res[0]->ID);

    // check user role
    if (!in_array('administrator', $user->roles)) return self::err('Role level is to low');

    return true;
  }
}