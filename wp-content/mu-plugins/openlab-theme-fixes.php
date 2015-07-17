<?php

/**
 * Loads theme fixes for OpenLab site themes
 */
function openlab_load_theme_fixes() {
	$t = get_stylesheet();

	switch ( $t ) {
		case 'carrington-blog' :
		case 'coraline' :
		case 'herothemetrust' :
		case 'motion' :
		case 'pilcrow' :
		case 'themorningafter' :
		case 'wu-wei' :

			echo '<link rel="stylesheet" id="' . $t . '-fixes" type="text/css" media="screen" href="' . get_home_url() . '/wp-content/mu-plugins/theme-fixes/' . $t . '.css" />
';

			break;
	}
}
add_action( 'wp_print_styles', 'openlab_load_theme_fixes', 9999 );

/**
 * Arrange themes so that preferred themes appear first in the list.
 */
function openlab_reorder_theme_selections( $themes ) {
	$preferred_themes = array(
		'filtered',
		'herothemetrust',
		'twentyeleven',
		'twentyfourteen',
		'twentythirteen',
		'twentytwelve',
	);

	$t1 = $t2 = array();

	foreach ( $themes as $theme_name => $theme ) {
		if ( in_array( $theme_name, $preferred_themes, true ) ) {
			$t1[ $theme_name ] = $theme;
		} else {
			$t2[ $theme_name ] = $theme;
		}
	}

	return array_merge( $t1, $t2 );
}
add_filter( 'wp_prepare_themes_for_js', 'openlab_reorder_theme_selections' );
