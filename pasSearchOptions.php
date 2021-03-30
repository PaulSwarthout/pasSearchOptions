<?php
/*
	Plugin Name: Search Options
	Plugin URI: http://www.paulswarthout.com/WordPress/
	Description: Plugin will search wp_options for any option_name which is like the input string. It will list the options found.
	Version: 0.1
	Author: Paul A. Swarthout
	License: GPL2
*/
namespace search_options;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require( dirname( __FILE__ ) . '/includes/symlinks.php' );

$pluginDirectory= plugin_dir_path( __FILE__ );
$pluginName		= "Search Options";
$pluginFolder	= "pasSearchOptions";

add_action('admin_menu',					__NAMESPACE__ . '\pasSearchOptions_admin' );
add_action('admin_enqueue_scripts',			__NAMESPACE__ . '\pasSearchOptions_styles' );
add_action('admin_enqueue_scripts',			__NAMESPACE__ . '\pasSearchOptions_scripts');

add_action('wp_ajax_searchForIt',			__NAMESPACE__ . '\pasSearchOptions_findIt');
add_action('wp_ajax_killRecord',			__NAMESPACE__ . '\pasSearchOptions_killRecord');
add_action('wp_ajax_update_option_value', 	__NAMESPACE__ . '\pasSearchOptions_updateOption');
add_action('wp_ajax_get_option_value', 		__NAMESPACE__ . '\pasSearchOptions_getOption');

function pasSearchOptions_admin() {
	add_menu_page( 'SearchOptions', 'Search Options', 'manage_options', 'manage_options', __NAMESPACE__ . '\pasSearchOptions_search', "", 1);
}

function pasSearchOptions_styles() {
	$pluginDirectory = plugin_dir_url( __FILE__ );
	$debugging = constant('WP_DEBUG');
	wp_enqueue_style('pasSearchOptions', $pluginDirectory . "css/style.css" . ($debugging ? "?v=" . rand(0,99999) . "&" : ""), false);
}

function pasSearchOptions_scripts() {
	$file = __FILE__;
	$pluginDirectory = plugin_dir_url( __FILE__ );
	$debugging = constant('WP_DEBUG');
	wp_enqueue_script('pasSearchOptions', $pluginDirectory . "js/pasSearchOptions.js" . ($debugging ? "?v=" . rand(0,99999) . "&" : ""), false);
}

function pasSearchOptions_search() {
	echo "<div id='searchBox'>";
	echo "<input value='" . get_option('pas_search_string', '') . "' id='searchString' type='text' onkeyup='javascript:grabKey(this);'>&nbsp;&nbsp;<input type='button' id='searchBTN' value='Search' onclick='javascript:search();' class='blueButton'>";
	echo "</div>";

	echo "<div id='results'>";
	echo "</div>";
}

function pasSearchOptions_findIt() {
	global $wpdb;

	if (strlen($_POST['searchString']) > 0) {
		update_option('pas_search_string', $_POST['searchString']);
	} else {
		return;
	}

	if (array_key_exists('searchString', $_POST)) {
		$searchString = $_POST['searchString'];

		$iSQL = " select option_id, option_name, option_value "
					.	" from " . $wpdb->prefix . "options "
					. " where option_name like \"%" . $searchString . "%\" "
					. " order by option_name asc; ";

		$results = $wpdb->get_results($iSQL, ARRAY_A);
		if (0 < count($results)) {
			echo "<table border=0 cellspacing=3>";

			echo "<col width='25px'>";
			echo "<col width='25px'>";
			echo "<col width='40px'>";
			echo "<col width='40px'>";
			echo "<col width='100px'>";
			echo "<col width='600px'>";

			$row = $results[0];
			echo "<tr><th></th><th></th><th></th>";
			foreach ($row as $key => $value) {
				echo "<TH>" . $key . "</TH>";
			}
			echo "</tr>";

			foreach ($results as $row) {
				echo "<tr class='row'>";
				echo "<td><input type='checkbox' value='' name='del_{$row['option_id']}'></td>";
				echo "<td class='killField'>";
				echo "<input type='button' value='delete' class='killBTN' onclick='javascript:killThisRecord(" . $row['option_id'] . ");'>";
				echo "</td>";
				echo "<td><input type='button' value='Expand' onclick='javascript:pas_opt_expandValue(\"{$row['option_name']}\");'></td>";
				foreach ($row as $key => $value) {
					if ($key == 'option_value') {
						$style = " style='width:400px;' ";
					} else {
						$style = "";
					}
					echo "<td class='$key' {$style}>" . htmlentities($value) . "</td>";
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
function pasSearchOptions_updateOption() {
	global $wpdb;

	$id = $_POST['option_id'];
	$value = $_POST['option_value'];
	$options_table = $wpdb->prefix . "options";

	$isql = $wpdb->prepare(" update {$options_table} set option_value = %s where option_id = %d; ", $value, $id);
	$wpdb->get_results($isql);
}
function pasSearchOptions_getOption() {
	$option_name = sanitize_text_field($_POST['option_name']);
	$result = get_option($option_name, "");
	if (is_array($result)) {
		echo "<pre>" . print_r($result, true) . "</pre>";
	} else {
		echo "Not an array, nothing to expand";
	}
}