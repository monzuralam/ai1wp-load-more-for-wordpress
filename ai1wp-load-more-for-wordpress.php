<?php

/**
 * Plugin Name: Ai1wp Load More for WordPress
 * Description: Load More post on WordPress.
 * Plugin URI:  #
 * Version:     1.0.0
 * Author:      Monzur Alam
 * Author URI:  https://profiles.wordpress.org/monzuralam
 * Text Domain: almfw
 *
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Load more ajax shortcode
 */
function almfw_ajax_load_more($atts, $content = null) {
    $atts = shortcode_atts(array(
        'post_type'         =>  'post',
        'posts_per_page'    =>  4
    ), $atts );

    $args = array(
        'post_type'         =>  $atts['post_type'],
        'post_status'       =>  'publish',
        'posts_per_page'    =>  $atts['posts_per_page'],
    );

    $query = new WP_Query($args);
    
    ob_start();
    wp_enqueue_style('almfw');
    wp_enqueue_script('almfw');
    ?>
        <div class="almfw_wrapper">
            <div class="almfw_post_wrapper">
            <?php
                if( $query->have_posts() ){
                    while( $query->have_posts() ){
                        $query->the_post();
                        ?>
                            <h2><?php the_title(); ?></h2>
                        <?php
                    }
                }
                wp_reset_postdata();
            ?>
            </div>
            <div class="load_more_btn_wrapper">
                <a href="#!" id="load_more" data-post="<?php echo esc_attr($atts['post_type']); ?>" data-per-page="<?php echo esc_attr( $atts['posts_per_page'] ); ?>"><?php echo esc_html__( 'Load More', 'almfw' ); ?></a>
            </div>
        </div>
    <?php
    return ob_get_clean();
}
add_shortcode('almfw', 'almfw_ajax_load_more');

/**
 * Assets
 */
function almfw_assets(){
    wp_register_style( 'almfw', plugins_url( '', __FILE__ ) . '/assets/css/almfw.css' );
    wp_register_script( 'almfw', plugins_url( '', __FILE__ ) . '/assets/js/main.js', array('jquery'), time(), true );
    wp_localize_script( 'almfw', 'almfw_data', array(
        'ajax_url' => admin_url( 'admin-ajax.php' )
    ));
}
add_action('wp_enqueue_scripts', 'almfw_assets');

/**
 * Ajax Content Load
 */
function almfw_load_more_content(){
    $post_type = isset( $_POST['post_type'] ) ? $_POST['post_type'] : 'post';
    $post_per_page = isset( $_POST['post_per_page'] ) ? $_POST['post_per_page'] : 1;
    $paged = isset( $_POST['paged'] ) ? $_POST['paged'] : '';
    $args = array(
        'post_type'         =>  $post_type,
        'post_status'       =>  'publish',
        'posts_per_page'    =>  $post_per_page,
        'paged'             =>  $paged
    );
    $content = '';
    $query = new WP_Query($args);
    if($query->have_posts()){
        while( $query->have_posts()){
            $query->the_post();
            $content .= the_title('<h2>', '</h2>' );
        }
    }else{
        $content .= '';
    }
    echo esc_html( $content );
    wp_die();
}
add_action( 'wp_ajax_almfw_load_more', 'almfw_load_more_content');
add_action( 'wp_ajax_nopriv_almfw_load_more', 'almfw_load_more_content');