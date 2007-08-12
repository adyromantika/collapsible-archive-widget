<?php
/*  Copyright 2007  ADY ROMANTIKA  (email : ady@romantika.name)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

/*
Plugin Name: Collapsible Archive Widget
Plugin URI: http://www.romantika.name/v2/2007/08/10/wordpress-plugin-collapsible-archive-widget/
Description: Display Collapsible Archive Widget.
Author: Ady Romantika
Version: 1.0.0
Author URI: http://www.romantika.name/v2/
*/

function ara_collapsiblearchive($before,$after)
{
	global $wpdb;
	$options = (array) get_option('widget_ara_collapsiblearchive');
	$title = $options['title'];
	$list_type = $options['type'] ? $options['type'] : 'ul';
	$count = $options['count'] ? 1 : 0;

	# years
	$years	= ara_get_archivesbyyear();

	# Header
	$string_to_echo  =  ($before.$title.$after."\n");

	switch($list_type)
	{
		case "br":
			$parentOpen	=	"\n\t<p>";
			$parentClose=	"\n\t</p>";
			$lineStart	=	"\n\t\t\t";
			$lineEnd	=	"<br />";
			$childOpen	=	"\n\t\t<div";
			$childClose	=	"\t\t</div>";
			$preappend	=	"&nbsp;&nbsp;";
			break;
		case "p":
			$parentOpen	=	"\n\t<p>";
			$parentClose=	"\n\t</p>";
			$lineStart	=	"\n\t\t\t<p>";
			$lineEnd	=	"</p>";
			$childOpen	=	"\n\t\t<div";
			$childClose	=	"\t\t</div>";
			$preappend	=	"&nbsp;&nbsp;";
			break;
		case "ul":
		default:
			$parentOpen	=	"\n\t<li>";
			$parentClose=	"\n\t</li>";
			$lineStart	=	"\n\t\t\t<li>";
			$lineEnd	=	"</li>";
			$childOpen	=   "\n\t\t<ul";
			$childClose	=	"\t\t</ul>";
			$preappend	=	'';
			$parentPreOpen	=	"\n<ul>";
			$parentPreClose	=	"\n</ul>";
	}

	$string_to_echo .= $parentPreOpen;
	for ($x=0;$x<count($years);$x++ )
	{
		if (strlen($parentOpen) > 0 ) $string_to_echo .= $parentOpen;
		$string_to_echo .= '<a style="cursor:pointer;" onClick="var listobject = document.getElementById(\'ara_ca'.$years[$x]->year.'\'); if(listobject.style.display == \'block\') listobject.style.display = \'none\'; else listobject.style.display = \'block\';">'.$years[$x]->year.'</a>';
		if($count > 0) $string_to_echo .= '&nbsp;('.$years[$x]->posts.')';
		$string_to_echo .= $childOpen.' id="ara_ca'.$years[$x]->year.'" style="display:none">';
		$string_to_echo .= ara_get_archivesbymonth($years[$x]->year,$count,$lineStart.$preappend,$lineEnd);
		$string_to_echo .= $childClose;
		if (strlen($parentClose) > 0) $string_to_echo .= $parentClose;
	}
	$string_to_echo .= $parentPreClose;
	return $string_to_echo;
}

function ara_get_archivesbymonth($year, $count, $before, $after)
{
	global $wpdb, $wp_locale;

	$monthresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts"
		. " FROM $wpdb->posts"
		. " WHERE $wpdb->posts.post_status = 'publish'"
		. " AND $wpdb->posts.post_type = 'post'"
		. " AND YEAR(post_date) = $year"
		. " GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");

	$result_string = '';

	foreach ($monthresults as $month)
	{
		$url	= get_month_link($year,	$month->month);
		$text = sprintf(__('%1$s %2$d'), $wp_locale->get_month_abbrev($wp_locale->get_month($month->month)), $year);
		if ($count > 0)	$aftertext = '&nbsp;('.$month->posts.')' . $after;
		else $aftertext = $after;
		$result_string .= get_archives_link($url, $text, 'custom', $before, $aftertext);
	}

	return $result_string;
}

function ara_get_archivesbyyear() {
	global $wpdb;

	$yearresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, count(ID) as posts"
		. " FROM $wpdb->posts"
		. " WHERE $wpdb->posts.post_status = 'publish'"
		. " AND $wpdb->posts.post_type = 'post'"
		. " GROUP BY YEAR(post_date) ORDER BY post_date DESC");

	return $yearresults;
}

function widget_ara_collapsiblearchive_control() {
	$options = $newoptions = get_option('widget_ara_collapsiblearchive');
	if ( $_POST['collapsiblearchive-submit'] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST['collapsiblearchive-title']));
		$newoptions['type'] = $_POST['collapsiblearchive-type'];
		$newoptions['count'] = isset($_POST['collapsiblearchive-count']);
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_ara_collapsiblearchive', $options);
	}
	$category = $options['cat'] ? $options['cat'] : '';
	$count = $options['count'] ? 'checked="checked"' : '';
?>
			<div style="text-align:right">
			<label for="collapsiblearchive-title" style="line-height:25px;display:block;"><?php _e('Widget title:', 'widgets'); ?> <input style="width: 200px;" type="text" id="collapsiblearchive-title" name="collapsiblearchive-title" value="<?php echo ($options['title'] ? wp_specialchars($options['title'], true) : 'Archives'); ?>" /></label>
			<label for="collapsiblearchive-type" style="line-height:25px;display:block;">
				<?php _e('List Type:', 'widgets'); ?>
					<select style="width: 200px;" id="collapsiblearchive-type" name="collapsiblearchive-type">
						<option value="ul"<?php if ($options['type'] == 'ul') echo ' selected' ?>>&lt;ul&gt;</option>
						<option value="br"<?php if ($options['type'] == 'br') echo ' selected' ?>>&lt;br/&gt;</option>
						<option value="p"<?php if ($options['type'] == 'p') echo ' selected' ?>>&lt;p&gt;</option>
					</select>
			</label>
			<label for="collapsiblearchive-count" style="line-height:25px;display:block;"><?php _e('Show post counts'); ?> <input class="checkbox" type="checkbox" <?php echo $count; ?> id="collapsiblearchive-count" name="collapsiblearchive-count" /></label>
			<input type="hidden" name="collapsiblearchive-submit" id="collapsiblearchive-submit" value="1" />
			</div>
<?php
}

function ara_collapsiblearchive_microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

function widget_ara_collapsiblearchive_init() {

	// Check for the required API functions
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
		return;

	// This prints the widget
	function widget_ara_collapsiblearchive($args) {
		extract($args);
		$start = ara_collapsiblearchive_microtime_float();
		echo $before_widget;
		echo "\n".'<!-- Collapsible Archive Widget: START -->'."\n";
		echo ara_collapsiblearchive($before_title, $after_title);
		echo "\n".'<!-- Collapsible Archive Widget: END -->'."\n";
		echo $after_widget;
		$end = ara_collapsiblearchive_microtime_float();
		echo "\n".'<!-- Time taken for the Collapsible Archive Widget plugin to complete loading is '.($end - $start).' seconds -->'."\n";
	}

	// Tell Dynamic Sidebar about our new widget and its control
	register_sidebar_widget(array('Collapsible Archive', 'widgets'), 'widget_ara_collapsiblearchive');
	register_widget_control(array('Collapsible Archive', 'widgets'), 'widget_ara_collapsiblearchive_control');
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('widgets_init', 'widget_ara_collapsiblearchive_init');

?>
