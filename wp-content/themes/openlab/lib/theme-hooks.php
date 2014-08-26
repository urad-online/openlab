<?php

/**
 * Theme based hooks
 */
function openlab_header_bar() {
    ?>
    <nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <div class="navbar-header">
                <h1 id="title"><a href="<?php echo home_url(); ?>" title="<?php _ex('Home', 'Home page banner link title', 'buddypress'); ?>"><?php bp_site_name(); ?></a></h1>
            </div>
            <div class="navbar-collapse"> 
                <?php
                //this adds the main menu, controlled through the WP menu interface
                $args = array(
                    'theme_location' => 'main',
                    'container' => false,
                    'menu_class' => 'nav navbar-nav',
                );

                wp_nav_menu($args);
                ?>
                <div class="navbar-right search">
                    <?php openlab_site_wide_bp_search(); ?>
                </div>
            </div>
        </div>
    </nav>
    <?php
}

add_action('bp_before_header', 'openlab_header_bar', 10);

function openlab_custom_menu_items($items, $menu) {

    if (is_user_logged_in()) {
        $opl_link = '<li ' . (bp_is_my_profile() ? 'class="current-menu-item"' : '') . '>';
        $opl_link .= '<a href="' . bp_loggedin_user_domain() . '">My OpenLab</a>';
        $opl_link .= '</li>';
    }

    return $items . $opl_link;
}

add_filter('wp_nav_menu_items', 'openlab_custom_menu_items', 10, 2);

function openlab_activity_log_text($text){
    $text = '%s';
    
    return $text;
}

add_filter('bp_core_time_since_ago_text','openlab_activity_log_text');

function openlab_form_classes($classes){
    
   $classes[] = 'field-group';
    
   return $classes; 
}

add_filter('bp_field_css_classes','openlab_form_classes');
