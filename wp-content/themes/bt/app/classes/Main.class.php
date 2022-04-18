<?php namespace app\classes;

defined('ABSPATH') || exit;

class Main {
	const NAMESPACE_PREFIX = __NAMESPACE__ . '\\';

	const PATH_PREFIX = BDIR . '\\' . self::NAMESPACE_PREFIX;

	const NAMESPACES = [
		[
			'namespace' => self::NAMESPACE_PREFIX . 'theme\\',
			'path' 		=> self::PATH_PREFIX . 'theme\\'
		],
		[
			'namespace' => self::NAMESPACE_PREFIX . 'admin\\',
			'path' 		=> self::PATH_PREFIX . 'admin\\',
			'condition' => 'is_admin'
		],
		[
			'namespace' => self::NAMESPACE_PREFIX . 'timber\\',
			'path' 		=> self::PATH_PREFIX . 'timber\\',
		],
		[
			'namespace' => self::NAMESPACE_PREFIX . 'plugins\\acf\\',
			'path' 		=> self::PATH_PREFIX . 'plugins\\acf\\',
			'condition' => 'class_exists|ACF'
		],
		[
			'namespace' => self::NAMESPACE_PREFIX . 'plugins\\woocommerce\\',
			'path' 		=> self::PATH_PREFIX . 'plugins\\woocommerce\\',
			'condition' => 'class_exists|woocommerce'
		],
		[
			'namespace' => self::NAMESPACE_PREFIX . 'plugins\\cf7\\',
			'path' 		=> self::PATH_PREFIX . 'plugins\\cf7\\',
			'condition' => 'class_exists|WPCF7'
		]
	];

	static public function init () {
		foreach (apply_filters('bt_theme_classes_namespaces', self::NAMESPACES) as $namespace) {
			if (isset($namespace['condition'])) {
				$condition = explode('|', $namespace['condition']);

				if (!($condition[0])(isset($condition[1]) ? $condition[1] : '')) continue;
			}

			foreach (scandir(str_replace('\\', '/', $namespace['path'])) as $file_name) {
				if (strpos($file_name, '.class.php') === false) continue;

				if (!method_exists($class = $namespace['namespace'] . explode('.', $file_name)[0], 'init')) {
					trigger_error("Class {$class} is missing the init method");
					continue;
				}

				("{$class}::init")();
			}
		}
	}
}
