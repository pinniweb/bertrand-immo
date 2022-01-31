<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once get_theme_file_path('/classes/rentalsSearchAdv.php');

if ( !function_exists( 'wpestate_chld_thm_cfg_parent_css' ) ):
   function wpestate_chld_thm_cfg_parent_css() {

    $parent_style = 'wpestate_style'; 
    wp_enqueue_style('bootstrap',get_template_directory_uri().'/css/bootstrap.css', array(), '1.0', 'all');
    wp_enqueue_style('bootstrap-theme',get_template_directory_uri().'/css/bootstrap-theme.css', array(), '1.0', 'all');
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css',array('bootstrap','bootstrap-theme'),'all' );
    wp_enqueue_style( 'wpestate-child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
    
   }    
    
endif;
add_action( 'wp_enqueue_scripts', 'wpestate_chld_thm_cfg_parent_css' );
load_child_theme_textdomain('wprentals', get_stylesheet_directory().'/languages');
// END ENQUEUE PARENT ACTION

define('WPLANG', 'fr_FR');

add_filter('use_block_editor_for_post_type', '__return_false'); // gutemberg ça degage

// Begin tabs shortcode
function kbi_tabs_search_shortcode() {
    wp_enqueue_script('jquery_ui','https://code.jquery.com/ui/1.12.1/jquery-ui.js', array('jquery'));
    wp_enqueue_script('kbi_search',get_stylesheet_directory_uri(). '/js/kbi_tabs_search.js');
    wp_enqueue_script('kbi_search',get_stylesheet_directory_uri(). '/js/recall_form.js');
    wp_enqueue_style('jquery_ui_css', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.theme.min.css');
    wp_enqueue_style('kbi-tabs-css', get_stylesheet_directory_uri() . '/css/kbi_tabs_search.css');

    ob_start();
    include 'templates/kbi_tabs_search.php';
    return ob_get_clean();
}
add_shortcode("kbi_tabs","kbi_tabs_search_shortcode"); /*    [kbi_tabs]     */
// end tabs shortcode

function kbi_estate_agent_redirect_post() {
    if ( is_singular( 'estate_agent' ) ) {
        wp_redirect( home_url(), 301 );
        exit;
    }
}
add_action( 'template_redirect', 'kbi_estate_agent_redirect_post' );

function show_all_users($prepared_args) {
    unset($prepared_args['has_published_posts']);
    return $prepared_args;
}
add_filter('rest_user_query', 'show_all_users');

add_action('pre_get_posts', 'my_search_query'); // add the special search fonction on each get_posts query (this includes WP_Query())
function my_search_query($query) {
    if ($query->query_vars and (!empty($query->query_vars['s']) || !empty($query->query_vars['s_meta_keys']))) { // if we are searching using the 's' argument and added a 's_meta_keys' argument
        global $wpdb;
        $search = $query->query_vars['s'];
        $ids_posts = array(); // initiate array of martching post ids per searched keyword
        if ($search) {
            foreach (explode(' ',$search) as $term) { // explode keywords and look for matching results for each
                $term = trim($term); // remove unnecessary spaces
                if (!empty($term)) { // check the the keyword is not empty
                    $query_posts = $wpdb->prepare("SELECT * FROM {$wpdb->posts} WHERE post_status='publish' AND ((post_title LIKE '%%%s%%') OR (post_content LIKE '%%%s%%'))", $term, $term); // search in title and content like the normal function does
                    $ids_posts = [];
                    $results = $wpdb->get_results($query_posts);
                    if ($wpdb->last_error)
                        die($wpdb->last_error);
                    foreach ($results as $result)
                        $ids_posts[] = $result->ID; // gather matching post ids
                }
            }
        }
        $ids_metas = [];
        if (!empty($query->query_vars['s_meta_keys'])) {
            $query_meta = [];
            foreach($query->query_vars['s_meta_keys'] as $meta_key) {
                $query_meta[] = $wpdb->prepare("meta_key='%s' AND meta_value LIKE '%%%s%%'", $meta_key['key'], $meta_key['value']);
            }
            $results = $wpdb->get_results("SELECT * FROM {$wpdb->postmeta} WHERE ((".implode(') OR (',$query_meta)."))");
            if ($wpdb->last_error)
                die($wpdb->last_error);
            foreach ($results as $result)
                $ids_metas[] = $result->post_id; // gather matching post ids
        }

        $merged = array_merge($ids_posts,$ids_metas); // merge the title, content and meta ids resulting from both queries
        $unique = array_unique($merged); // remove duplicates
        if (!$unique)
            $unique = array(0); // if no result, add a "0" id otherwise all posts wil lbe returned
        $ids[] = $unique; // add array of matching ids into the main array
        if (count($ids)>1)
            $intersected = call_user_func_array('array_intersect',$ids); // if several keywords keep only ids that are found in all keywords' matching arrays
        else
            $intersected = $ids[0]; // otherwise keep the single matching ids array
        $unique = array_unique($intersected); // remove duplicates
        if (!$unique)
            $unique = array(0); // if no result, add a "0" id otherwise all posts wil lbe returned
        unset($query->query_vars['s']); // unset normal search query
        unset($query->query_vars['s_meta_keys']);
        $query->set('post__in',$unique); // add a filter by post id instead
    }
}

add_filter( 'woocommerce_checkout_get_value', 'kbi_custom_autofill_customer_data', 10, 2 );
function kbi_custom_autofill_customer_data( $value, $input ) {
    $current_user = wp_get_current_user();

    if ( $current_user ) {
        switch ( $input ) {
            case 'billing_first_name':
                return get_user_meta($current_user->ID, 'first_name', true);
            case 'shipping_first_name':
                return get_user_meta($current_user->ID, 'first_name', true);
            case 'billing_last_name':
                return get_user_meta($current_user->ID, 'last_name', true);
            case 'shipping_last_name':
                return get_user_meta($current_user->ID, 'last_name', true);
            case 'billing_phone':
                return get_user_meta($current_user->ID, 'phone', true);
            case 'billing_email':
                return $current_user->user_email;
            case 'shipping_email':
                return $current_user->user_email;
        }
    }

    return $value;
}

// do_action('start_synchronisation');