<?php
/*
	Plugin Name: Search Options
	Plugin URI: http://www.paulswarthout.com/WordPress/
	Description: Plugin will search wp_options for any option_name which is like the input string. It will list the options found.
	Version: 0.1
	Author: Paul A. Swarthout
	License: GPL2
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$pluginDirectory	= plugin_dir_path( __FILE__ );
$pluginName				= "Search Options";
$pluginFolder			= "pasSearchOptions";

add_action('admin_menu',							 'pasSearchOptions_admin' );
add_action('admin_enqueue_scripts',		 'pasSearchOptions_styles' );
add_action('admin_enqueue_scripts',		 'pasSearchOptions_scripts');

add_action('wp_ajax_searchForIt',				 'pasSearchOptions_findIt');
add_action('wp_ajax_killRecord',			 'pasSearchOptions_killRecord');

function pasSearchOptions_admin() {
	add_menu_page( 'SearchOptions', 'Search Options', 'manage_options', 'manage_options', 'pasSearchOptions_search', "", 1);
//	add_submenu_page('manage_child_themes', 'Generate ScreenShot', 'Generate ScreenShot', 'manage_options', 'genScreenShot', 'generateScreenShot');
}

function pasSearchOptions_styles() {
	$pluginDirectory = plugin_dir_url( __FILE__ );
	$debugging = constant('WP_DEBUG');
	wp_enqueue_style('pasSearchOptions', $pluginDirectory . "css/style.css" . ($debugging ? "?v=" . rand(0,99999) . "&" : ""), false);
}

function pasSearchOptions_scripts() {
	$pluginDirectory = plugin_dir_url( __FILE__ );
	$debugging = constant('WP_DEBUG');
	wp_enqueue_script('pasSearchOptions', $pluginDirectory . "js/pasSearchOptions.js" . ($debugging ? "?v=" . rand(0,99999) . "&" : ""), false);
}

function pasSearchOptions_search() {
	echo "<div id='searchBox'>";
	echo "<input id='searchString' type='text' onkeyup='javascript:grabKey(this);'>&nbsp;&nbsp;<input type='button' id='searchBTN' value='Search' onclick='javascript:search();' class='blueButton'>";
	echo "</div>";

	echo "<div id='results'>";
	echo "</div>";
}

function pasSearchOptions_findIt() {
	global $wpdb;

	if (array_key_exists('searchString', $_POST)) {
		$searchString = $_POST['searchString'];

		$iSQL = " select option_id, option_name, option_value "
					.	" from " . $wpdb->prefix . "options "
					. " where option_name like \"%" . $searchString . "%\" "
					. " order by option_name asc; ";
		$dbg->write(['heading'=>'$iSQL', 'data'=>$iSQL]);
//		$dbg->dump();

		$results = $wpdb->get_results($iSQL, ARRAY_A);
		if (0 < count($results)) {
			echo "<table border=0 cellspacing=3>";
			$row = $results[0];
			echo "<tr>";
			foreach ($row as $key => $value) {
				echo "<TH>" . $key . "</TH>";
			}
			echo "</tr>";

			foreach ($results as $row) {
				echo "<tr class='row'>";
				echo "<td class='killField'>";
				echo "<input type='button' value='delete' class='killBTN' onclick='javascript:killThisRecord(" . $row['option_id'] . ", \"$searchString\");'>";
				echo "</td>";
				foreach ($row as $key => $value) {
					echo "<td class='$key'>" . $value . "</td>";
				}
				echo "</tr>";
			}
			echo "</table>";
		} else {
			echo "No results found";
		}
	}
}
function pasSearchOptions_killRecord() {
	global $wpdb;
	$id = $_POST['optionID'];

	$isql = " delete from " . $wpdb->prefix . "options where option_id = %d; ";
	$isql = $wpdb->prepare($isql, $id);

	$results = $wpdb->get_results($isql);
	exit;

}