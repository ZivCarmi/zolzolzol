<?php namespace app\classes\theme;

defined('ABSPATH') || exit;

class Pixels {
	static public function init () {
		// add google tag analytics
		add_action('bt_html_head_start', [__CLASS__, 'set_google_tag_analytics']);
		
		// add google tag manager
		add_action('bt_html_head_start', [__CLASS__, 'set_google_tag_manager']);
		
		// add google tag manager
		add_action('bt_html_body_start', [__CLASS__, 'set_google_tag_manager_noscript']);
	}

	// add google tag analytics
	static public function set_google_tag_analytics () {
		if (empty(GTAGA_ID)) return;
?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= GTAGA_ID; ?>"></script>
<script>
	window.dataLayer = window.dataLayer || [];
	function gtag(){dataLayer.push(arguments);}
	gtag('js', new Date());

	gtag('config', '<?= GTAGA_ID; ?>');
</script>
<?php
	}
	
	// add google tag manager
	static public function set_google_tag_manager () {
		if (empty(GTAG_ID)) return;
?>
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?= GTAG_ID; ?>');</script>
<?php
	}
	
	// add google tag manager
	static public function set_google_tag_manager_noscript () {
		if (empty(GTAG_ID)) return;
?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= GTAG_ID; ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php
	}
}
