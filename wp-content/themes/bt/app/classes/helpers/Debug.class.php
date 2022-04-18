<?php namespace app\classes\helpers;

defined('ABSPATH') || exit;

class Debug {
	static public function _ (...$values) {
		self::handle_error_output('print', $values);
	}

	static public function _d (...$values) {
		self::handle_error_output('print', $values, true);
	}

	static public function p (...$values) {
		self::handle_error_output('print', $values);
	}

	static public function pd (...$values) {
		self::handle_error_output('print', $values, true);
	}

	static public function d (...$values) {
		self::handle_error_output('dump', $values);
	}

	static public function dd (...$values) {
		self::handle_error_output('dump', $values, true);
	}

	static public function e (...$values) {
		self::handle_error_output('export', $values);
	}

	static public function ed (...$values) {
		self::handle_error_output('export', $values, true);
	}

	static private function handle_error_output ($type, $values, $die = false) {
		echo '<div dir="ltr"><pre>';
		
		self::get_file_and_line(3, true);
		
		foreach ($values as $value) {
			switch ($type) {
				case 'print': print_r($value); break;
				case 'dump': var_dump($value); break;
				case 'export': var_export($value); break;
			}
		};

		echo '</pre></div>';

		if ($die) die;
	}

	// returns the file and line from which the method was called
	static private function get_file_and_line ($position = 3, $echo = false) {
		$backtrace = debug_backtrace();

		if (count($backtrace) >= $position) {
			$backtrace_description = 'File: ' . strstr($backtrace[$position - 1]['file'], 'bt' . DIRECTORY_SEPARATOR) . "\r\nLine: " . $backtrace[$position - 1]['line'] . "\r\n\r\n";
			
			if ($echo) echo $backtrace_description;
			else return $backtrace_description;
		}
	}

	// basic php error loger using print_r
	static public function el (...$values) {
		error_log(self::get_file_and_line(2), 3, BDIR . DS . 'log' . DS . 'log.txt');
		
		foreach ($values as $value) {
			error_log(print_r($value, true), 3, BDIR . DS . 'log' . DS . 'log.txt');
			error_log("\r\n\r\n", 3,  BDIR . DS . 'log' . DS . 'log.txt');
		}
	}

	// javascript debugging tool
	static public function console_log (...$values) {
		echo '<script>';

		echo 'console.log(' . json_encode(self::get_file_and_line(2)) . ');';
		
		foreach ($values as $value) {
			echo 'console.log(' . json_encode($value) . ');';
		}

		echo '</script>';
	}

	// javascript debugging tool
	static public function alert (...$values) {
		echo '<script>';

		echo 'alert(' . json_encode(self::get_file_and_line(2)) . ');';
		
		foreach ($values as $value) {
			echo 'alert(' . json_encode($value) . ');';
		}

		echo '</script>';
	}
}
