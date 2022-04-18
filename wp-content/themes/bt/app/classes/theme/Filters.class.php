<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Filters {
	static public function init () {
		add_filter('flush_rewrite_rules_hard', [__CLASS__, '__return_false']);

		// add_filter('mod_rewrite_rules', [__CLASS__, 'output_htaccess']);

		// add_action('admin_init', [__CLASS__, 'flush_the_htaccess_file']);

		add_filter('posts_where', [__CLASS__, 'atom_search_where']);
		add_filter('posts_join', [__CLASS__, 'atom_search_join']);
		add_filter('posts_groupby', [__CLASS__, 'atom_search_groupby']);
	}

	
	static public function output_htaccess( $rules ) {
		$new_rules = <<<EOD
		# custom rules for loading server images or any other uploaded media files
		RewriteCond %{REQUEST_FILENAME} !-f
		RewriteCond %{HTTP_HOST} ^localhost$
		RewriteRule ^.*/uploads/(.*)$ https://zolzolzol.simplyad.co.il/wp-content/uploads/$1 [L,R=301,NC]
	EOD;
		return $rules;
	}
	
	static public function flush_the_htaccess_file() {
		global $wp_rewrite;
		$wp_rewrite->flush_rules();
	}

	static public function atom_search_where($where){
		global $wpdb;
		if (is_search())
			$where .= "OR (t.name LIKE '%".get_search_query()."%' AND {$wpdb->posts}.post_status = 'publish')";
		return $where;
	}
	  
	static public function atom_search_join($join){
		global $wpdb;
		if (is_search())
			$join .= "LEFT JOIN {$wpdb->term_relationships} tr ON {$wpdb->posts}.ID = tr.object_id INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id INNER JOIN {$wpdb->terms} t ON t.term_id = tt.term_id";
		return $join;
	}
	  
	static public function atom_search_groupby($groupby){
		global $wpdb;
		
		// we need to group on post ID
		$groupby_id = "{$wpdb->posts}.ID";
		if(!is_search() || strpos($groupby, $groupby_id) !== false) return $groupby;
		
		// groupby was empty, use ours
		if(!strlen(trim($groupby))) return $groupby_id;
		
		// wasn't empty, append ours
		return $groupby.", ".$groupby_id;
	}
}
