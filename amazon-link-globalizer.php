<?php   
    /* 
    Plugin Name: Amazon Affiliate Link Globalizer
    Plugin URI: http://www.affiliate-geo-target.com/amazon-wordpress-plugin.html
    Description: Rewrites Amazon.com/Amzn.com links to the <a href="http://A-FWD.com">A-FWD</a> webservice. This webservice performs user IP Geolocation and forwards the visitor to 'their' country specific Amazon store. In contrast to similar plugins, this plugin does not use any Javascript and does not perform external HTTP requests by itself.  Visit <a href="options-general.php?page=woboq_amazon_link_globalizer_admin">the settings</a> to configure.
    Author: Markus Goetz (Woboq), Attila Gyoerkoes
    Version: 1.1 
    Author URI: http://www.woboq.com/
    */  

global $waalg_tlds;
$waalg_tlds = array("com", "ca", "uk", "de", "fr", "es", "it", "cn", "jp");

function waalg_install() {
	global $waalg_tlds;
      
	foreach ($waalg_tlds as $tld) {
		add_option('woboq_amazon_link_globalizer_affiliate_id_'. $tld,'','',yes);
	}
    add_option('woboq_amazon_link_globalizer_enable_asin', '1', '', yes);
    add_option('woboq_amazon_link_globalizer_enable_keyw', '1', '', yes); 
    
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

function waalg_remove() {
    global $waalg_tlds;
    
	foreach ($waalg_tlds as $tld) {
		delete_option('woboq_amazon_link_globalizer_affiliate_id_'.$tld);
	}
    delete_option('woboq_amazon_link_globalizer_enable_asin');
    delete_option('woboq_amazon_link_globalizer_enable_keyw');    
}

function waalg_admin() {
    global $waalg_tlds;
    
    if (isset($_POST['submit'])) {
		
        if ( function_exists('current_user_can') && !current_user_can('manage_options') )
			die(__('Cheatin&#8217; uh?'));
		check_admin_referer('waalg_nonce');
        
        foreach ($waalg_tlds as $tld) {
            if (isset($_POST['woboq_amazon_link_globalizer_affiliate_id_'.$tld]))
                update_option('woboq_amazon_link_globalizer_affiliate_id_'.$tld, 
                              strip_tags($_POST['woboq_amazon_link_globalizer_affiliate_id_'.$tld]));
        }        
        
		if (isset($_POST['woboq_amazon_link_globalizer_enable_asin']))
			update_option('woboq_amazon_link_globalizer_enable_asin', 1);
		else
			update_option('woboq_amazon_link_globalizer_enable_asin', 0);

		if (isset($_POST['woboq_amazon_link_globalizer_enable_keyw']))
			update_option('woboq_amazon_link_globalizer_enable_keyw', 1);
		else
			update_option('woboq_amazon_link_globalizer_enable_keyw', 0);
    }
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
  <p>
    Please enter <b>your country specific Affiliate IDs</b> (or tracking IDs) here:
  </p>
  <p>
    <form method="post" action="">
<?php 
    wp_nonce_field('update-options');
    if (function_exists('wp_nonce_field')) 
	    wp_nonce_field('waalg_nonce');
?>
<?php
$option_name_list = "";
global $waalg_tlds;
foreach ($waalg_tlds as $i => $tld) {
	$option_name = 'woboq_amazon_link_globalizer_affiliate_id_' . $tld;
	$option_name_list .= $option_name . ',';
	$option_value = htmlspecialchars(get_option($option_name));
	if ($option_value == FALSE)
		$option_value = '';
    $html = <<<HERE
<label style="min-width:25ex;display:inline-block;" for="$option_name">Amazon <b>$tld</b> Affiliate ID</label>
<input name="$option_name" type="text" id="$option_name" value="$option_value" /><br/>
HERE;
	echo $html;
};
?>
      <table class="form-table">
        <tr valign="top">
        <th scope="row">Replacement settings</th>
            <td>
              <fieldset>
                <legend class="screen-reader-text"><span>Replacement settings</span></legend>
                <label for="woboq_amazon_link_globalizer_enable_asin">
                  <input name="woboq_amazon_link_globalizer_enable_asin" 
                         type="checkbox" 
                         id="woboq_amazon_link_globalizer_enable_asin" 
                         value="1" 
                         <?php checked(get_option('woboq_amazon_link_globalizer_enable_asin'), 1); ?> />
                  Replace Amazon links containing an ASIN
                </label>
                <br />
                <label for="woboq_amazon_link_globalizer_enable_keyw">
                  <input name="woboq_amazon_link_globalizer_enable_keyw" 
                         type="checkbox" 
                         id="woboq_amazon_link_globalizer_enable_keyw" 
                         value="1" 
                         <?php checked(get_option('woboq_amazon_link_globalizer_enable_keyw'), 1); ?> />
                  Replace Amazon links containing search keywords
                </label>
                <br />
              </fieldset>
            </td>
        </tr>      
      </table>
      <br>
    
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="<?php echo $option_name_list ?>" />
      <input type="submit" name="submit" class="button button-primary" value="Save Changes" />     
    </form>
    <p style="text-align:right;font-size:smaller;">
      Note that to run the Geolocation service, A-FWD takes its own Affiliate ID for each <em>100</em>th request or 
      if you do not specify an ID for that country.
    </p>
  </p>
</div>
<?php
}  

function waalg_admin_actions() {
    add_options_page('Amazon Link Globalizer',
                     'Amazon Link Globalizer',
                     'manage_options',
                     'waalg_admin',
                     'waalg_admin');
}

// Regular Expression matching html anchors
define('WAALG_REGEXP_LINK', '#<a\s*([^>]*)\s*href\s*=\s*"([^"]*)"\s*([^>]*)\s*>#');
// Regular Expression matching Amazon urls containing an asin
define('WAALG_REGEXP_ASIN', '#(?:http:\/\/)?(?:www\.)?(?:(?:amazon\.com/(?:[\w-&%]+\/)?(?:o\/ASIN|dp|ASIN|gp\/product|exec\/obidos\/ASIN)\/)|(?:amzn\.com\/))([A-Z0-9]{10})(?:[^"]+)?#'); 
// Regular Expression matching Amazon urls containing keywords
define('WAALG_REGEXP_KEYW', '#(?:http:\/\/)?(?:www\.)?(?:amazon\.)(?:com\/)(?:(?:gp\/search\/)|(?:s\/))(?:[^"]*)(?:keywords=)([^"&]*)(?:[^"]*)?#');

/**
 * Callback for preg_replace_callback. If an URL is
 * pointing to amazon.com and containing an asin it will be
 * replaced with a link to a-fwd.com
 *
 */
function waalg_asin_url_replacer($match) {
    global $waalg_tlds;
    
    $asin = $match[1];
    $new_url = 'http://a-fwd.com/asin-com='.$asin;
    // Append tracking ids for every country specified
    foreach ($waalg_tlds as $tld) {
		$tid = get_option('woboq_amazon_link_globalizer_affiliate_id_'.$tld);
		if ($tid && strlen($tid) > 0)
            $new_url = $new_url.'&'.$tld.'='.urlencode($tid);
    }
    return $new_url;
}
    
/**
 * Callback for preg_replace_callback. If an URL is
 * pointing to amazon.com and containing keywords it will be
 * replaced with a link to a-fwd.com
 *
 */
function waalg_keyw_url_replacer($match) {
    global $waalg_tlds;
    
    $keywords = $match[1];
    $new_url = 'http://a-fwd.com/s='.$keywords;
    
    // Append tracking ids for every country specified
    foreach ($waalg_tlds as $tld) {
		$tid = get_option('woboq_amazon_link_globalizer_affiliate_id_'.$tld);
		if ($tid && strlen($tid) > 0)
            $new_url = $new_url.'&'.$tld.'='.urlencode($tid);
    }
    return $new_url;
}
    
/**
 * Callback for preg_replace_callback. Tries to replace html anchors
 * containing amazon URLs with anchors containing URLs to the
 * a-fwd.com webservice
 * 
 */
function waalg_link_replacer($match) {
    $attributes1 = $match[1];
    $url         = $match[2];
    $attributes2 = $match[3];
    $found_matches = 0; // count matches
        
    // Try replacing asin links
    if (get_option('woboq_amazon_link_globalizer_enable_asin') == '1') {
        $url = preg_replace_callback(WAALG_REGEXP_ASIN, 
                                     'waalg_asin_url_replacer', 
                                     $url,
                                     -1,
                                     $found_matches);
    }
    // Try replacing keyword links
    if ( ($found_matches <= 0) && 
         (get_option('woboq_amazon_link_globalizer_enable_keyw') == '1') ) {
        $url = preg_replace_callback(WAALG_REGEXP_KEYW, 
                                     'waalg_keyw_url_replacer', 
                                     $url,
                                     -1,
                                     $found_matches);
    }
    // Build link, but don't do anything, if no replacements were made
    if ($found_matches > 0) {
        // Change 'rel' attribute to 'nofollow'
        $attributes1 = preg_replace('#rel\s*=\s*"[^"]+"#',
                                    'rel="nofollow"',
                                     $attributes1,
                                     -1,
                                     $found_matches);
        if ($found_matches <= 0) {
            $attributes2 = preg_replace('#rel\s*=\s*"[^"]+"#',
                                        'rel="nofollow"',
                                        $attributes2,
                                        -1,
                                        $found_matches);
        }
        // No 'rel' attribute found, append one
        if ($found_matches <= 0) {
            $attributes2 = $attributes2.' rel="nofollow"';
        }
        // Build the actual link
        $new_link = '<a '.$attributes1.' href="'.$url.'" '.$attributes2.'>';
        return $new_link;
    }
    return $match[0];
}

function waalg_the_content_filter ($content) {
	if (function_exists('is_main_query') && !is_main_query())
		return $content;
    
    return preg_replace_callback(WAALG_REGEXP_LINK, 
                                 'waalg_link_replacer', 
                                 $content);                              
}
  
add_action('admin_menu', 'waalg_admin_actions');  
register_activation_hook(__FILE__, 'waalg_install');
register_deactivation_hook( __FILE__, 'waalg_remove');
add_filter('the_content', 'waalg_the_content_filter', 50);


// TODO: Also parse visitors comments?
// TODO: sidebar links?

?>