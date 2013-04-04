<?php   
    /* 
    Plugin Name: Amazon Affiliate Link Globalizer
    Plugin URI: http://www.affiliate-geo-target.com/amazon-wordpress-plugin.html
    Description: Rewrites Amazon.com/Amzn.com links to the <a href="http://A-FWD.com">A-FWD</a> webservice. This webservice performs user IP Geolocation and forwards the visitor to 'their' country specific Amazon store. In contrast to similar plugins, this plugin does not use any Javascript and does not perform external HTTP requests by itself.  Visit <a href="options-general.php?page=woboq_amazon_link_globalizer_admin">the settings</a> to configure.
    Author: Markus Goetz (Woboq)
    Version: 1.0 
    Author URI: http://www.woboq.com/
    */  

global $woboq_amazon_link_globalizer_tlds;
$woboq_amazon_link_globalizer_tlds = array("com", "ca", "uk", "de", "fr", "es", "it", "cn", "jp");

function woboq_amazon_link_globalizer_install() {
	global $woboq_amazon_link_globalizer_tlds;
	foreach ($woboq_amazon_link_globalizer_tlds as $tld) {
		add_option('woboq_amazon_link_globalizer_affiliate_id_'. $tld,'','',yes);
	}

	// Init those options by taking the value from other plugins
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_com"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_com",get_option('amzn_com'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_uk"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_uk",get_option('amzn_co_uk'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_de"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_de",get_option('amzn_de'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_fr"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_fr",get_option('amzn_fr'));	
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_ca"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_ca",get_option('amzn_ca'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_jp"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_jp",get_option('amzn_jp'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_it"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_it",get_option('amzn_it'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_cn"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_cn",get_option('amzn_cn'));
	if (!get_option("woboq_amazon_link_globalizer_affiliate_id_es"))
		update_option("woboq_amazon_link_globalizer_affiliate_id_es",get_option('amzn_es'));
}

function woboq_amazon_link_globalizer_remove() {
}

function woboq_amazon_link_globalizer_admin() {  
?>
<div class="wrap">
<?php screen_icon(); ?>
<h2>Amazon Affiliate Settings</h2>
<p style="max-width:100ex;">
If you specify your Amazon Affiliate IDs, this plugin will <b>automatically rewrite all Amazon.com/Amzn.com with ASIN</b> inside your posts
to the <a href="http://www.affiliate-geo-target.com/amazon.html">A-FWD</a> webservice. This webservice
will determine the users location (using IP Geolocation) and then forward them to their appropriate country store.
</p>
<p>
	This plugin does not need any Javascript.
</p>
<p>
You don't need to specify all IDs, they are optional.
</p>
<p>Please enter <b>your country specific Affiliate IDs</b> (or tracking IDs) here:</p>
<p>
<form method="post" action="options.php">
<?php wp_nonce_field('update-options'); ?>
<?php
$option_name_list = "";
global $woboq_amazon_link_globalizer_tlds;
foreach ($woboq_amazon_link_globalizer_tlds as $i => $tld) {
	$option_name = "woboq_amazon_link_globalizer_affiliate_id_" . $tld;
	$option_name_list .= $option_name . ",";
	$option_value = htmlspecialchars(get_option($option_name));
	if ($option_value == FALSE)
		$option_value = "";
    $html = <<<HERE
<label style="min-width:25ex;display:inline-block;" for="$option_name">Amazon <b>$tld</b> Affiliate ID</label>
<input name="$option_name" type="text" id="$option_name" value="$option_value" /><br/>
HERE;
	echo $html;
};
?>
<input type="hidden" name="action" value="update" />
<input type="hidden" name="page_options" value="<?php echo $option_name_list ?>" />
<input type="submit" value="Save Changes" />
</form>
<p style="text-align:right;font-size:smaller;">Note that to run the Geolocation service, A-FWD takes its own Affiliate ID for each <em>n</em> th request or if you do not specify an ID for that country.</p>
</p>
</div>
<?php
}  

function woboq_amazon_link_globalizer_admin_actions() {  
    add_options_page("Amazon Link Globalizer", "Amazon Link Globalizer", "manage_options", "woboq_amazon_link_globalizer_admin", "woboq_amazon_link_globalizer_admin");  
}

define('WOBOQ_AMAZON_REGEXP', '#(<a\s[^>]*href\s*=\s*)"(?:(?:http:\/\/(?:www\.)?amazon\.com/(?:[\w-&%]+\/)?(?:o\/ASIN|dp|ASIN|gp\/product|exec\/obidos\/ASIN)\/)|(?:http:\/\/amzn\.com\/))([A-Z0-9]{10})(?:[^"]+)?"#');

function woboq_amazon_link_globalizer_the_content_filter_callback($match) {
	list($whole, $a, $asin) = $match;
	//file_put_contents('php://stderr', print_r($match, TRUE));
	$ret = $a . "\"http://a-fwd.com/asin-com=" . $asin;
	// add affiliate IDs
	global $woboq_amazon_link_globalizer_tlds;
	foreach ($woboq_amazon_link_globalizer_tlds as $i => $tld) {
		$aId = get_option('woboq_amazon_link_globalizer_affiliate_id_'.$tld);
		if ($aId && strlen($aId) > 0)
			$ret = $ret . "&" . $tld . "=" . urlencode($aId);
	}
	$ret = $ret . "\" rel=\"nofollow\"";
	//file_put_contents('php://stderr', print_r($ret, TRUE));
	return $ret;
}


function woboq_amazon_link_globalizer_the_content_filter ($content) {
	if (function_exists("is_main_query") && !is_main_query())
		return $content;
	return preg_replace_callback(
		   WOBOQ_AMAZON_REGEXP,
           "woboq_amazon_link_globalizer_the_content_filter_callback", $content);
}
  
add_action('admin_menu', 'woboq_amazon_link_globalizer_admin_actions');  
register_activation_hook(__FILE__,'woboq_amazon_link_globalizer_install');
register_deactivation_hook( __FILE__, 'woboq_amazon_link_globalizer_remove' );
add_filter( 'the_content', 'woboq_amazon_link_globalizer_the_content_filter', 50 );


// TODO: Also parse visitors comments?
// TODO: sidebar links?

?>