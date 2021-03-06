<?php

/**
 * Class for the definition of a widget that is
 * called by the class of the main module
 *
 * @package SZGoogle
 * @subpackage Widgets
 * @author Massimo Della Rovere
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

if (!defined('SZ_PLUGIN_GOOGLE') or !SZ_PLUGIN_GOOGLE) die();

// Before the definition of the class, check if there is a definition
// with the same name or the same as previously defined in other script

if (!class_exists('SZGoogleWidget'))
{
	class SZGoogleWidget extends WP_Widget
	{
		/**
		 * Construction of the title of the widget to use for all
		 * widgets connected to the module enabled with this page PHP
		 */

		function common_title($args,$instance)
		{
			extract($args);

			if (empty($instance['title'])) $title = '';
				else $title = esc_attr(trim($instance['title']));

			if (!isset($before_title)) $before_title = '';
			if (!isset($after_title))  $after_title  = '';

			if ($title and $title <> '') {
				$title = $before_title.$title.$after_title;
			}

			// Back to the widget title calculated using parameters
			// configuration related to the theme currently applied

			return $title;
		}

		/**
		 * Control variables empty within an array
		 * containing the names of the options specified
		 */

		function common_empty($names,$instance) 
		{
			foreach ($names as $key=>$value) {
				if (empty($instance[$key])) $instance[$key] = trim($value);
					else $instance[$key] = trim($instance[$key]);
			}

			// Add the parameter action=widget in general parameters 
			// so that the function of the common code can locate the component

			if (!isset($instance['action'])) $instance['action'] = 'widget';

			// Return array class widget caller with all options
			// (trim) and set according to a default value

			return $instance;
		}

		/**
		 * Issue HTML code with calculating the title and
		 * default code before and after the widgets on sidebar
		 */

		function common_widget($args,$instance,$HTML) 
		{
			extract($args);

			// Calculation of the title attached to the widget 
			// through the variable on your instance parameters

			$title = $this->common_title($args,$instance);

			// Calculation of the HTML making the final wrap
			// with variable HTML generated by specific widget

			$output  = $before_widget;
			$output .= $title;
			$output .= $HTML;
			$output .= $after_widget;

			return $output;
		}

		/**
		 * Changing parameters and options related to the widget
		 * with storage of the values ​​directly in the database
		 */

		function common_update($names,$new_instance,$old_instance) 
		{
			$instance = $old_instance;

			foreach ($names as $key=>$value) 
			{
				if (!isset($new_instance[$key])) $instance[$key] = ''; 
					else $instance[$key] = trim($new_instance[$key]);

				if ($value == '1') $instance[$key] = strip_tags($instance[$key]);
			}

			// Back to the widget array with options to update correct
			// and any new elements that were missing on the old widget

			return $instance;
		}
	}
}