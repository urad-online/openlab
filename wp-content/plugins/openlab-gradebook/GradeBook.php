<?php
/*
  Plugin Name: OpenLab GradeBook
  Plugin URI: https://github.com/livinglab/openlab
  Description: A modification of AN Gradebook https://wordpress.org/plugins/an-gradebook/
  Version: 0.0.3
  Author: Joe Unander
  Author URI: http://early-adopter.com/
  License: GPL
 */


//establishing some constants
define("OPENLAB_GRADEBOOK_VERSION", "0.0.3");
define("OPENLAB_GRADEBOOK_FEATURES_TRACKER", 0.3);
define("OPLB_GRADEBOOK_STORAGE_SLUG", "zzoplb-gradebook-storagezz");

/**
 * Attempting to make this plugin BP and OpenLab agnostic
 * Right now this is only partially implemented
 * @todo: full audit of plugin for BP-dependent features
 * @todo: full audit of plugin for OpenLab-dependent features
 */
function oplb_verify_buddypress() {

    define("OPLB_BP_AVAILABLE", true);
}

add_action('bp_include', 'oplb_verify_buddypress');

/**
 * Legacy: includes database files, where most of the backend functionality lives
 */
$database_file_list = glob(dirname(__FILE__) . '/database/*.php');
foreach ($database_file_list as $database_file) {
    include($database_file);
}

//sidebar widget
include(dirname(__FILE__) . '/components/sidebar-widget.php');

//legacy globals
$oplb_gradebook_api = new oplb_gradebook_api();
$oplb_gradebook_course_api = new gradebook_course_API();
$oplb_gradebook_assignment_api = new gradebook_assignment_API();
$oplb_gradebook_cell_api = new gradebook_cell_API();
$oplb_gradebookapi = new OPLB_GradeBookAPI();
$oplb_course_list = new OPLB_COURSE_LIST();
$oplb_gradebook = new OPLB_GRADEBOOK();
$oplb_user = new OPLB_USER();
$oplb_user_list = new OPLB_USER_LIST();
$oplb_statistics = new OPLB_STATISTICS();
$oplb_upload_csv = new gradebook_upload_csv_API();

/**
 * Legacy: setup OpenLab Gradebook admin
 */
function register_oplb_gradebook_menu_page() {
    $roles = wp_get_current_user()->roles;

    //in at least one case a super admin was not properly assigned a role
    if (empty($roles) && is_super_admin()) {
        $roles[0] = 'administrator';
    }

    $my_admin_page = add_menu_page('OpenLab GradeBook', 'OpenLab GradeBook', $roles[0], 'oplb_gradebook', 'init_oplb_gradebook', 'dashicons-book-alt', '6.12');
    $add_submenu_page_settings = in_array($roles[0], array_keys(get_option('oplb_gradebook_settings')));
    if ($add_submenu_page_settings) {
        add_submenu_page('oplb_gradebook', 'Settings', 'Settings', 'administrator', 'oplb_gradebook_settings', 'init_oplb_gradebook_settings');
    }
}

add_action('admin_menu', 'register_oplb_gradebook_menu_page', 10);

/**
 * Updating admin menu to appends "#courses" to the Gradebook URL
 * That hash initiates the client-side app functionality
 */
function oplb_gradebook_admin_menu_custom(){
    global $menu, $submenu;

    foreach($menu as &$menu_item){

        if(in_array('oplb_gradebook', $menu_item)){

            $menu_item[2] = 'admin.php?page=oplb_gradebook#courses';

        }

    }

    if(!isset($submenu['oplb_gradebook'])){
        return false;
    }

    foreach ($submenu['oplb_gradebook'] as &$submenu_item){
        
        if(!is_array($submenu_item)){
            break;
        }

        foreach($submenu_item as &$item){

            if($item === 'oplb_gradebook'){
                $item = 'admin.php?page=oplb_gradebook#courses';
            }

        }

    }

}

add_action('admin_menu', 'oplb_gradebook_admin_menu_custom', 100);

/**
 * Legacy: setup OpenLab admin enqueues
 * @param type $hook
 * @return type
 */
function enqueue_oplb_gradebook_scripts($hook) {

    $app_base = plugins_url('js', __FILE__);

    if ($hook == "toplevel_page_oplb_gradebook" || $hook == 'openlab-gradebook_page_oplb_gradebook_settings') {

        //for media functions (to upload CSV files)
        wp_enqueue_media();
        wp_enqueue_script('jquery-ui-datepicker');
    
        $oplb_gradebook_develop = false;

        if (WP_DEBUG) {
            $oplb_gradebook_develop = true;
        }

        wp_register_style('jquery_ui_css', $app_base . '/lib/jquery-ui/jquery-ui.css', array(), '0.0.0.2', false);
        wp_register_style('OplbGradeBook_css', plugins_url('GradeBook.css', __File__), array('bootstrap_css', 'jquery_ui_css'), '0.0.0.7', false);
        wp_register_style('bootstrap_css', $app_base . '/lib/bootstrap/css/bootstrap.css', array(), '0.0.0.2', false);
        wp_register_script('jscrollpane-js', $app_base . '/lib/jscrollpane/jscrollpane.dist.js', array('jquery'), '0.0.0.2', true);
        wp_register_script('requirejs', $app_base . '/require.js', array('jquery', 'media-views'), '0.0.3.3', true);
        wp_enqueue_style('OplbGradeBook_css');
        wp_enqueue_script('jscrollpane-js');
        wp_enqueue_script('requirejs');

        wp_localize_script('requirejs', 'oplbGradebook', array(
            'ajaxURL' => admin_url('admin-ajax.php'),
            'depLocations' => oplb_gradebook_get_dep_locations(),
            'nonce' => wp_create_nonce('oplb_gradebook'),
            'storagePage' => get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG),
            'currentYear' => date('Y'),
            'initName' => oplb_gradebook_gradebook_init_placeholder(),
        ));

        wp_localize_script('requirejs', 'require', array(
            'baseUrl' => $app_base,
            'deps' => array($app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js?ver=0.0.3.3' : '/oplb-gradebook-app-min.js?ver=0.0.3.3'))
        ));
        
    } else {
        return;
    }
}

add_action('admin_enqueue_scripts', 'enqueue_oplb_gradebook_scripts');

/**
 * Legacy: callback for OpenLab Gradebook instantiation
 * Adds template files to page so that BackBone JS client-side app can access them
 */
function init_oplb_gradebook() {
    $template_list = glob(dirname(__FILE__) . '/js/app/templates/*.php');

    foreach ($template_list as $template) {

        //get template name
        $template_explode = explode('/', $template);
        $template_filename = esc_html(str_replace('.php', '', array_pop($template_explode)));
        echo "<script id='{$template_filename}' type='text/template'>";
        include($template);
        echo "</script>";
    }
}

/**
 * Legacy: callback for OpenLab Gradebook settings instantiation
 * Setups up templates for Backbone JS client-side app responsible for settings
 */
function init_oplb_gradebook_settings() {
    ob_start();
    include( dirname(__FILE__) . '/components/parts/pages/settings-template.php' );
    echo ob_get_clean();
}

/**
 * Legacy: delete user hooks
 * @todo: determine if this is necessary; actions may already be completed in database/User.php
 * @global type $wpdb
 * @param type $user_id
 */
function oplb_gradebook_my_delete_user($user_id) {
    global $wpdb;
    $results1 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_users", array('uid' => $user_id));
    $results2 = $wpdb->delete("{$wpdb->prefix}oplb_gradebook_cells", array('uid' => $user_id));
}

add_action('delete_user', 'oplb_gradebook_my_delete_user');

/**
 * Legacy: makes ajaxurl accessible to client-side app
 * @todo: move this to wp_localize_script
 */
function oplb_gradebook_ajaxurl() {
    ?>
    <script type="text/javascript">
        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
    <?php
}

add_action('wp_head', 'oplb_gradebook_ajaxurl');

/**
 * Prevent notices from other plugins from appearing on OpenLab Gradebook pages
 * These notices can sometimes interfere with client-side functionality
 * @global type $wp_filter
 * @return boolean
 */
function oplb_gradebook_admin_notices() {
    global $wp_filter;
    $screen = get_current_screen();

    //if this is not OpenLab Gradebook, we're not doing anything here
    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base !== "toplevel_page_oplb_gradebook" && $screen->base !== 'openlab-gradebook_page_oplb_gradebook_settings') {
            return false;
        }
    }

    if (isset($wp_filter['admin_notices'])
            && !empty($wp_filter['admin_notices']->callbacks)) {

        foreach ($wp_filter['admin_notices']->callbacks as $priority => $callback) {

            foreach ($callback as $hookname => $hook) {

                if (strpos($hookname, 'oplb_gradebook') === false) {
                    $result = remove_action('admin_notices', $hookname, $priority);
                }
            }
        }
    }
}

add_action('admin_notices', 'oplb_gradebook_admin_notices', 1);

/**
 * Legacy: OpenLab Gradebook shortcode; currently not in use
 * @todo: consider for deprecation, not currently part of OpenLab Gradebook scope, and may not work properly
 * @return string
 */
function oplb_gradebook_shortcode() {
    init_oplb_gradebook();
    $oplb_gradebook_develop = false;
    $app_base = plugins_url('js', __FILE__);
    wp_register_script('init_front_end_gradebookjs', $app_base . '/init_front_end_gradebook.js', array('jquery'), null, true);
    wp_enqueue_script('init_front_end_gradebookjs');
    if (1 == 1) {
        wp_register_style('jquery_ui_css', $app_base . '/lib/jquery-ui/jquery-ui.css', array(), null, false);
        wp_register_style('OplbGradeBook_css', plugins_url('GradeBook.css', __File__), array('bootstrap_css', 'jquery_ui_css'), null, false);
        wp_register_style('bootstrap_css', $app_base . '/lib/bootstrap/css/bootstrap.css', array(), null, false);
        wp_register_script('requirejs', $app_base . '/require.js', array(), null, true);
        wp_enqueue_style('OplbGradeBook_css');
        wp_enqueue_script('requirejs');
        wp_localize_script('requirejs', 'require', array(
            'baseUrl' => $app_base,
            'deps' => array($app_base . ($oplb_gradebook_develop ? '/oplb-gradebook-app.js' : '/oplb-gradebook-app-min.js')
        )));
    } else {
        return;
    }
    return '<div id="wpbody-content"></div>';
}

//add_shortcode('oplb_gradebook', 'oplb_gradebook_shortcode');

/**
 * Grab dependencies already stored in WP (to avoid conflicts)
 */
function oplb_gradebook_get_dep_locations() {

    $include_dir = includes_url() . 'js/';

    $deps = array(
        'jquery' => $include_dir . 'jquery/jquery',
        'jqueryui' => $include_dir . 'jquery-ui/jquery-ui.min',
        'backbone' => $include_dir . 'backbone.min',
        'underscore' => $include_dir . 'underscore.min',
    );

    return $deps;
}

//activation and deactivation hooks
register_activation_hook(__FILE__, 'activate_oplb_gradebook');
register_deactivation_hook(__FILE__, 'deactivate_oplb_gradebook');

//
function oplb_gradebook_gradebook_init_placeholder(){

    return apply_filters('oplb_gradebook_gradebook_init_placeholder', 'Please provide a Name');

}

/**
 * Openlab Gradebook activation actions
 */
function activate_oplb_gradebook() {
    global $wpdb;

    //initialize databases
    $oplb_database = new OPLB_DATABASE();
    $oplb_database->database_init();
    $oplb_database->database_alter();

    //create the instructor user so the instructor has permissions to create a Gradebook
    $user = wp_get_current_user();

    $query = $wpdb->prepare("SELECT id FROM {$wpdb->prefix}oplb_gradebook_courses WHERE gbid = %d AND role = %s AND uid = %d", 0, 'instructor', $user->ID);
    $init_instructor = $wpdb->get_results($query);

    if (empty($init_instructor)) {
        $result = $wpdb->insert("{$wpdb->prefix}oplb_gradebook_users", array(
            'uid' => $user->ID,
            'gbid' => 0,
            'role' => 'instructor',
            'current_grade_average' => 0.00,
                ), array(
            '%d',
            '%d',
            '%s',
            '%f',
                )
        );
    }

    //add custom page for csv storage - make the slug something very unlikely to be used
    oplb_gradebook_custom_page(OPLB_GRADEBOOK_STORAGE_SLUG, 'OpenLab Gradebook Storage');
    update_option('oplb_gradebook_features_tracker', OPENLAB_GRADEBOOK_FEATURES_TRACKER);
}

/**
 * OpenLab Gradebook deactivation actions
 * @todo: remove storage page
 */
function deactivate_oplb_gradebook() {

    delete_option('oplb_gradebook_features_tracker');
    delete_option('oplb_gradebook_db_version');
    delete_option('oplb_gradebook_settings');
    delete_option('oplb_gradebook_db_version');
}

/**
 * Hook into wp_handle_upload to run our specific CSV uploads
 * 1) Check to make sure file is a CSV
 * @todo Check to make sure uploader is a faculty member
 * @param type $file
 * @return type
 */
function oplb_gradebook_wp_handle_upload_prefilter($file_info) {
    global $oplb_upload_csv;

    $storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

    if (isset($_REQUEST['post_id']) && intval($_REQUEST['post_id']) === intval($storage_page->ID)) {

        if ($file_info['type'] !== 'text/csv') {
            $file_info['error'] = 'This file does not appear to be a CSV.';
            return $file_info;
        }

        if (!isset($_REQUEST['name']) || !isset($_REQUEST['gbid'])) {
            $file_info['error'] - 'There was a problem with the upload; please try again.';
        }

        $name = 'temp.csv';

        $name = sanitize_file_name($_REQUEST['name']);
        $file_info['gbid'] = intval(sanitize_text_field($_REQUEST['gbid']));

        $result = $oplb_upload_csv->upload_csv($file_info, $name);

        if ($result['response'] === 'oplb-gradebook-error') {
            $file_info['error'] = $result['content'];
            return $file_info;
        }
    }

    return $file_info;
}

add_filter('wp_handle_upload', 'oplb_gradebook_wp_handle_upload_prefilter');

/**
 * Use wp_prepare_attachment_for_js to clean up CSV and send cleaned confirmation data back to the upload modal
 * @param type $response
 * @param type $attachment
 * @param type $meta
 * @return type
 */
function oplb_gradebook_wp_prepare_attachment_for_js($response, $attachment, $meta) {

    $storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

    if (isset($response['uploadedTo']) && $response['uploadedTo'] === $storage_page->ID) {
        wp_delete_attachment($response['id'], true);
    }

    return $response;
}

add_filter('wp_prepare_attachment_for_js', 'oplb_gradebook_wp_prepare_attachment_for_js', 10, 3);

/**
 * Create custom page
 * @param type $slug
 * @param type $title
 * @return type
 */
function oplb_gradebook_custom_page($slug, $title) {

    $post_id = -1;
    $author_id = 1;

    if (null == get_page_by_path($slug)) {

        $post_id = wp_insert_post(
                array(
                    'comment_status' => 'closed',
                    'ping_status' => 'closed',
                    'post_author' => $author_id,
                    'post_name' => $slug,
                    'post_title' => $title,
                    'post_status' => 'publish',
                    'post_type' => 'page'
                )
        );
    } else {
        $post_id = -2;
    }

    return $post_id;
}

/**
 * Exclude custom pages from admin (so nobody messes with 'em)
 * @global type $pagenow
 * @global type $post_type
 * @param type $query
 * @return type
 */
function oplb_gradebook_exclude_pages_from_admin($query) {

    if (!is_admin())
        return $query;

    global $pagenow, $post_type;

    if ($pagenow == 'edit.php' && $post_type == 'page') {

        $csv_storage_page = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);

        $query->query_vars['post__not_in'] = array($csv_storage_page->ID);
    }
}

add_filter('parse_query', 'oplb_gradebook_exclude_pages_from_admin');

/**
 * Custom pages: remove link from admin bar
 * @global type $post
 * @global type $wp_admin_bar
 */
function oplb_gradebook_remove_admin_bar_edit_link() {
    global $post;

    $exclusions = array(OPLB_GRADEBOOK_STORAGE_SLUG);

    if ($post && in_array($post->post_name, $exclusions)) {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('edit');
    }
}

add_action('wp_before_admin_bar_render', 'oplb_gradebook_remove_admin_bar_edit_link');

/**
 * Add param to modal upload to delineate type of upload
 * @todo: leverage this param to improve how upload differentiates between 
 * customized CSV upload and other types of uploads
 * @param string $params
 * @return string
 */
function oplb_gradebook_plupload_default_params($params) {
    $screen = new stdClass();

    if (function_exists('get_current_screen')) {
        $screen = get_current_screen();
    }

    if (is_object($screen) && isset($screen->base)) {

        if ($screen->base === "toplevel_page_oplb_gradebook" || $screen->base === 'openlab-gradebook_page_oplb_gradebook_settings') {
            $params['oplb_gb_upload_type'] = 'oplb_gb_csv';
        }
    }

    return $params;
}

add_filter('plupload_default_params', 'oplb_gradebook_plupload_default_params');

//legacy update
$option = get_option('oplb_gradebook_features_tracker');

//for legacy versions, add stoarge page
if (!$option || floatval($option) < 0.3) {

    oplb_gradebook_custom_page(OPLB_GRADEBOOK_STORAGE_SLUG, 'OpenLab Gradebook Storage');
    update_option('oplb_gradebook_features_tracker', OPENLAB_GRADEBOOK_FEATURES_TRACKER);
}

/**
 * Any custom storage pages we create need to be hidden from the fallback menu
 * @param array $args
 * @return type
 */
function oplb_gradebook_wp_page_menu_args($args) {
    $excludes = array();

    $storage_page_obj = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);
    $excludes[] = $storage_page_obj->ID;
    $args['exclude'] = implode(',', $excludes);

    return $args;
}

add_filter('wp_page_menu_args', 'oplb_gradebook_wp_page_menu_args');

/**
 * Add custom storage pages to wp_list_pages exclusions
 * Some menu fallbacks use wp_list_pages
 * @param type $exclude_array
 * @return type
 */
function oplb_gradebook_wp_list_pages_excludes($exclude_array){
    
    $storage_page_obj = get_page_by_path(OPLB_GRADEBOOK_STORAGE_SLUG);
    $exclude_array[] = $storage_page_obj->ID;
    
    return $exclude_array;
}

add_filter('wp_list_pages_excludes', 'oplb_gradebook_wp_list_pages_excludes');
