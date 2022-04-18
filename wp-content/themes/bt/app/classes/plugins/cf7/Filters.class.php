<?php namespace app\classes\plugins\cf7;

defined('ABSPATH') || exit;

use Timber;

class Filters {
  static public function init () {
    add_filter('wpcf7_default_template', [__CLASS__, 'default_template'], 10, 2);
  }

  static public function default_template ($template, $prop) {
    if ($prop === 'form') {
      $template = Timber::compile('plugins\cf7\form-default-template.twig');
    } elseif ($prop === 'mail') {
      $template['additional_headers'] = 'Reply-To: [email]';
      $template['body']               = Timber::compile('plugins\cf7\mail-default-message-body.twig');;
    }

    return $template;
  }
}