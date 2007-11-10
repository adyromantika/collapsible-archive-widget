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
Version: 2.1
Author URI: http://www.romantika.name/v2/
*/

function ara_collapsiblearchive($before,$after)
{
	global $wpdb;
	$options = (array) get_option('widget_ara_collapsiblearchive');
	$title = $options['title'];
	$list_type = $options['type'] ? $options['type'] : 'ul';
	$count = $options['count'] ? 1 : 0;
	$abbr = $options['abbr'] ? 1 : 0;
	$scriptaculous = $options['scriptaculous'] ? 1 : 0;
	$effectexpand = $options['effectexpand'] ? $options['effectexpand'] : 1;
	$effectcollapse = $options['effectcollapse'] ? $options['effectcollapse'] : 1;
	$defaultexpand = $options['defaultexpand'] ? 1 : 0;

	# years
	$years	= ara_get_archivesbyyear();

	# Header
	$string_to_echo  =  ($before.$title.$after."\n");

	if($scriptaculous > 0) # Only do this if scriptaculous is selected
	{
		switch($effectexpand)
		{
			case 1: $effxp = 'Appear'; break;
			case 2: $effxp = 'BlindDown'; break;
			case 3: $effxp = 'SlideDown'; break;
			case 4: $effxp = 'Grow'; break;
		}

		switch($effectcollapse)
		{
			case 1: $effcl = 'Fade'; break;
			case 2: $effcl = 'Puff'; break;
			case 3: $effcl = 'BlindUp'; break;
			case 4: $effcl = 'SwitchOff'; break;
			case 5: $effcl = 'SlideUp'; break;
			case 6: $effcl = 'DropOut'; break;
			case 7: $effcl = 'Squish'; break;
			case 8: $effcl = 'Fold'; break;
			case 9: $effcl = 'Shrink'; break;
		}

		?>
			<script src="<?php echo get_settings('home'); ?>/wp-includes/js/scriptaculous/prototype.js" type="text/javascript"></script>
			<script src="<?php echo get_settings('home'); ?>/wp-includes/js/scriptaculous/effects.js" type="text/javascript"></script>
			<script language="JavaScript" type="text/javascript">
				collapsiblearchive_toggle_year = function(idyear,visible)
				{
					(visible == false ?
						new Effect.<?php echo $effxp ?>(document.getElementById('ara_ca_mo' + idyear)) :
						new Effect.<?php echo $effcl ?>(document.getElementById('ara_ca_mo' + idyear))
						);
					var sign = document.getElementById('ara_ca_mosign' + idyear);
					(visible == false ? sign.innerHTML = '[-]' : sign.innerHTML = '[+]');
					visible = (visible == false ? true : false);
					return visible;
				}
				collapsiblearchive_toggle_month = function(idymonth,visible)
				{
					(visible == false ?
						new Effect.<?php echo $effxp ?>(document.getElementById('ara_ca_po' + idymonth)) :
						new Effect.<?php echo $effcl ?>(document.getElementById('ara_ca_po' + idymonth))
						);
					var sign = document.getElementById('ara_ca_posign' + idymonth);
					(visible == false ? sign.innerHTML = '[-]' : sign.innerHTML = '[+]');
					visible = (visible == false ? true : false);
					return visible;
				}
			</script>
		<?php
	}

	list($parentOpen, $parentClose, $lineStart, $lineEnd, $childOpen, $childClose, $preappend, $parentPreOpen, $parentPreClose, $list_type) = ara_getlisttype();

	$string_to_echo .= $parentPreOpen;
	for ($x=0;$x<count($years);$x++ )
	{
		if (strlen($parentOpen) > 0 ) $string_to_echo .= $parentOpen;
		if($scriptaculous > 0)
		{
			?><script language="JavaScript" type="text/javascript">var visible_<?php echo $years[$x]->year ?> = <?php echo ($defaultexpand ? 'true' : 'false') ?>;</script><?php
			$string_to_echo .= '<a style="cursor:pointer;" onClick="visible_'.$years[$x]->year.' = collapsiblearchive_toggle_year(\''.$years[$x]->year.'\',visible_'.$years[$x]->year.')"><span id="ara_ca_mosign'.$years[$x]->year.'">['.($defaultexpand ? '-' : '+').']</span></a> <a href="'.get_year_link($years[$x]->year).'">'.$years[$x]->year.'</a>';
		}
		else
		{
			$string_to_echo .= '<a style="cursor:pointer;" onClick="var listobject = document.getElementById(\'ara_ca_mo'.$years[$x]->year.'\'); var sign = document.getElementById(\'ara_ca_mosign\' + '.$years[$x]->year.'); if(listobject.style.display == \'block\') { listobject.style.display = \'none\'; sign.innerHTML = \'[+]\'; } else { listobject.style.display = \'block\'; sign.innerHTML = \'[-]\'; }"><span id="ara_ca_mosign'.$years[$x]->year.'">['.($defaultexpand ? '-' : '+').']</span></a> <a href="'.get_year_link($years[$x]->year).'">'.$years[$x]->year.'</a>';
		}
		if($count > 0) $string_to_echo .= '&nbsp;('.$years[$x]->posts.')';
		$string_to_echo .= $childOpen.' id="ara_ca_mo'.$years[$x]->year.'" style="display:'.($defaultexpand ? 'block' : 'none').'">';
		$string_to_echo .= ara_get_archivesbymonth($years[$x]->year,$count,$lineStart.$preappend,$lineEnd,$abbr);
		$string_to_echo .= $childClose;
		if (strlen($parentClose) > 0) $string_to_echo .= $parentClose;
	}
	$string_to_echo .= $parentPreClose;

	return $string_to_echo;
}

function ara_get_archivesbymonth($year, $count, $before, $after, $abbr)
{
	global $wpdb, $wp_locale;
	$options = (array) get_option('widget_ara_collapsiblearchive');
	$scriptaculous = $options['scriptaculous'] ? 1 : 0;
	$defaultexpand = $options['defaultexpand'] ? 1 : 0;
	$show_individual_posts = $options['showposts'];

	$monthresults = $wpdb->get_results("SELECT DISTINCT YEAR(post_date) AS `year`, MONTH(post_date) AS `month`, count(ID) as posts"
		. " FROM $wpdb->posts"
		. " WHERE $wpdb->posts.post_status = 'publish'"
		. " AND $wpdb->posts.post_type = 'post'"
		. " AND YEAR(post_date) = $year"
		. " GROUP BY YEAR(post_date), MONTH(post_date) ORDER BY post_date DESC");

	$result_string = '';

	foreach ($monthresults as $month)
	{
		$url = get_month_link($year, $month->month);
		if($show_individual_posts > 0)
		{
			if($scriptaculous > 0)
			{
				$add_before = '<script language="JavaScript" type="text/javascript">var visible_'.$year.$month->month.' = '.($defaultexpand ? 'true' : 'false').'</script>';
				$add_before .= '<a style="cursor:pointer;" onClick="visible_'.$year.$month->month.' = collapsiblearchive_toggle_month(\''.$year.$month->month.'\',visible_'.$year.$month->month.')"><span id="ara_ca_posign'.$year.$month->month.'">['.($defaultexpand ? '-' : '+').']</span></a> ';
			}
			else
			{
				$add_before = '<a style="cursor:pointer;" onClick="var listobject = document.getElementById(\'ara_ca_po'.$year.$month->month.'\'); var sign = document.getElementById(\'ara_ca_posign\' + '.$year.$month->month.'); if(listobject.style.display == \'block\') { listobject.style.display = \'none\'; sign.innerHTML = \'[+]\'; } else { listobject.style.display = \'block\'; sign.innerHTML = \'[-]\'; }"><span id="ara_ca_posign'.$year.$month->month.'">['.($defaultexpand ? '-' : '+').']</span></a> ';
			}
		}
		else $add_before = '';
		$text = sprintf(__('%1$s %2$d'), ($abbr > 0 ? $wp_locale->get_month_abbrev($wp_locale->get_month($month->month)) : $wp_locale->get_month($month->month)), $year);
		if ($count > 0)	$aftertext = '&nbsp;('.$month->posts.')' . $after;
		else $aftertext = $after;
		$result_string .= get_archives_link($url, $text, 'custom', $before.$add_before, $aftertext);

		if($show_individual_posts)
		{
			$result_string .= ara_get_postsbymonth($year, $month->month, '', '');
		}
	}

	return $result_string;
}

function ara_get_postsbymonth($year, $month, $before, $after)
{
	global $wpdb;

    if (empty($year) || empty($month)) {
        return null;
    }

   	list($parentOpen, $parentClose, $lineStart, $lineEnd, $childOpen, $childClose, $preappend, $parentPreOpen, $parentPreClose, $list_type) = ara_getlisttype();

	$postresults = $wpdb->get_results("SELECT ID, post_title, post_name"
        . " FROM $wpdb->posts"
        . " WHERE $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post'"
        . " AND YEAR(post_date) = $year AND MONTH(post_date) = $month"
        . " ORDER BY post_date DESC");

	$result_string = '';

	$sectionstart = true;

	foreach ($postresults as $post)
	{
		if($sectionstart)
		{
			$result_string .= $childOpen.' id="ara_ca_po'.$year.$month.'" style="display:'.($defaultexpand ? 'block' : 'none').'">';
			$sectionstart = false;
		}
		$url  = get_permalink($post->ID);
		$result_string .= $lineStart.($list_type == 'br' || $list_type == 'p' ? $preappend.$preappend : $preappend).'<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>'.$lineEnd;
	}

	$result_string .= $childClose;

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
		$newoptions['abbr'] = isset($_POST['collapsiblearchive-monthabbr']);
		$newoptions['scriptaculous'] = isset($_POST['collapsiblearchive-scriptaculous']);
		$newoptions['effectexpand'] = $_POST['collapsiblearchive-effectexpand'];
		$newoptions['effectcollapse'] = $_POST['collapsiblearchive-effectcollapse'];
		$newoptions['showposts'] = $_POST['collapsiblearchive-showposts'];
		$newoptions['defaultexpand'] = $_POST['collapsiblearchive-defaultexpand'];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_ara_collapsiblearchive', $options);
	}
	$count = $options['count'] ? 'checked="checked"' : '';
	$abbr = $options['abbr'] ? 'checked="checked"' : '';
	$showposts = $options['showposts'] ? 'checked="checked"' : '';
	$defaultexpand = $options['defaultexpand'] ? 'checked="checked"' : '';
	$scriptaculous = $options['scriptaculous'] ? 'checked="checked"' : '';
?>
			<div style="text-align:right;">
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
			<label for="collapsiblearchive-monthabbr" style="line-height:25px;display:block;"><?php _e('Abbreviate month names'); ?> <input class="checkbox" type="checkbox" <?php echo $abbr; ?> id="collapsiblearchive-monthabbr" name="collapsiblearchive-monthabbr" /></label>
			<label for="collapsiblearchive-scriptaculous" style="line-height:25px;display:block;"><?php _e('Use script.aculo.us effects'); ?> <input class="checkbox" type="checkbox" <?php echo $scriptaculous; ?> id="collapsiblearchive-scriptaculous" name="collapsiblearchive-scriptaculous" onChange="var slc1 = document.getElementById('collapsiblearchive-effectexpand'); var slc2 = document.getElementById('collapsiblearchive-effectcollapse'); if(this.checked) { slc1.disabled = false; slc2.disabled = false; } else { slc1.disabled = true; slc2.disabled = true; }" /></label>
			<label for="collapsiblearchive-effectexpand" style="line-height:25px;display:block;">
				<?php _e('Expand Effect:', 'widgets'); ?>
					<select style="width: 100px;" id="collapsiblearchive-effectexpand" name="collapsiblearchive-effectexpand" <?php if($scriptaculous == '') echo 'disabled'; ?>>
						<option value="1"<?php if ($options['effectexpand'] == '1') echo ' selected' ?>>Appear</option>
						<option value="2"<?php if ($options['effectexpand'] == '2') echo ' selected' ?>>BlindDown</option>
						<option value="3"<?php if ($options['effectexpand'] == '3') echo ' selected' ?>>SlideDown</option>
						<option value="4"<?php if ($options['effectexpand'] == '4') echo ' selected' ?>>Grow</option>
					</select>
			</label>
			<label for="collapsiblearchive-effectcollapse" style="line-height:25px;display:block;">
				<?php _e('Collapse Effect:', 'widgets'); ?>
					<select style="width: 100px;" id="collapsiblearchive-effectcollapse" name="collapsiblearchive-effectcollapse" <?php if($scriptaculous == '') echo 'disabled'; ?>>
						<option value="1"<?php if ($options['effectcollapse'] == '1') echo ' selected' ?>>Fade</option>
						<option value="2"<?php if ($options['effectcollapse'] == '2') echo ' selected' ?>>Puff</option>
						<option value="3"<?php if ($options['effectcollapse'] == '3') echo ' selected' ?>>BlindUp</option>
						<option value="4"<?php if ($options['effectcollapse'] == '4') echo ' selected' ?>>SwitchOff</option>
						<option value="5"<?php if ($options['effectcollapse'] == '5') echo ' selected' ?>>SlideUp</option>
						<option value="6"<?php if ($options['effectcollapse'] == '6') echo ' selected' ?>>DropOut</option>
						<option value="7"<?php if ($options['effectcollapse'] == '7') echo ' selected' ?>>Squish</option>
						<option value="8"<?php if ($options['effectcollapse'] == '8') echo ' selected' ?>>Fold</option>
						<option value="9"<?php if ($options['effectcollapse'] == '9') echo ' selected' ?>>Shrink</option>
					</select>
			</label>
			<label for="collapsiblearchive-defaultexpand" style="line-height:25px;display:block;"><?php _e('Expand the list by default'); ?> <input class="checkbox" type="checkbox" <?php echo $defaultexpand; ?> id="collapsiblearchive-defaultexpand" name="collapsiblearchive-defaultexpand" /></label>
			<label for="collapsiblearchive-showposts" style="line-height:25px;display:block;"><?php _e('Show individual posts (<a target="_blank" href="http://www.romantika.name/v2/2007/08/10/wordpress-plugin-collapsible-archive-widget/#showpostswarning">Warning</a>)'); ?> <input class="checkbox" type="checkbox" <?php echo $showposts; ?> id="collapsiblearchive-showposts" name="collapsiblearchive-showposts" /></label>
			<input type="hidden" name="collapsiblearchive-submit" id="collapsiblearchive-submit" value="1" />
			</div>
<?php
}

function ara_getlisttype()
{
	$options = (array) get_option('widget_ara_collapsiblearchive');
	$list_type = $options['type'] ? $options['type'] : 'ul';
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
			$parentPreOpen	=	null;
			$parentPreClose	=	null;
			break;
		case "p":
			$parentOpen	=	"\n\t<p>";
			$parentClose=	"\n\t</p>";
			$lineStart	=	"\n\t\t\t<p>";
			$lineEnd	=	"</p>";
			$childOpen	=	"\n\t\t<div";
			$childClose	=	"\t\t</div>";
			$preappend	=	"&nbsp;&nbsp;";
			$parentPreOpen	=	null;
			$parentPreClose	=	null;
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
	return array($parentOpen, $parentClose, $lineStart, $lineEnd, $childOpen, $childClose, $preappend, $parentPreOpen, $parentPreClose, $list_type);
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
	register_widget_control(array('Collapsible Archive', 'widgets'), 'widget_ara_collapsiblearchive_control', 300, 300);
}

// Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
add_action('widgets_init', 'widget_ara_collapsiblearchive_init');

?>
