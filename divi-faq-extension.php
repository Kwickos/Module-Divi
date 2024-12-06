<?php
/*
Plugin Name: Divi FAQ Extension
Plugin URI: 
Description: Module FAQ personnalisé pour Divi avec support des Custom Post Types
Version: 1.0.0
Author: 
Author URI: 
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: divi-faq-extension
Domain Path: /languages
*/

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'DIVI_FAQ_EXTENSION_VERSION', '1.0.0' );
define( 'DIVI_FAQ_EXTENSION_PATH', plugin_dir_path( __FILE__ ) );
define( 'DIVI_FAQ_EXTENSION_URL', plugin_dir_url( __FILE__ ) );

function divi_faq_extension_has_parent_theme() {
    $theme = wp_get_theme();
    return 'Divi' === $theme->get('Name') || 'Divi' === $theme->get('Parent Theme');
}

function initialize_divi_faq_extension() {
    if ( ! class_exists( 'ET_Builder_Module' ) ) {
        return;
    }
    require_once DIVI_FAQ_EXTENSION_PATH . 'includes/DiviFaqExtension.php';
}

function divi_faq_extension_init() {
    if ( divi_faq_extension_has_parent_theme() ) {
        add_action( 'et_builder_ready', 'initialize_divi_faq_extension' );
    }
}
add_action( 'init', 'divi_faq_extension_init' );

// Enregistrement du Custom Post Type FAQ
function create_faq_post_type() {
    $labels = array(
        'name'               => __( 'FAQs', 'divi-faq-extension' ),
        'singular_name'      => __( 'FAQ', 'divi-faq-extension' ),
        'menu_name'          => __( 'FAQs', 'divi-faq-extension' ),
        'add_new'            => __( 'Ajouter', 'divi-faq-extension' ),
        'add_new_item'       => __( 'Ajouter une FAQ', 'divi-faq-extension' ),
        'edit_item'          => __( 'Modifier la FAQ', 'divi-faq-extension' ),
        'new_item'           => __( 'Nouvelle FAQ', 'divi-faq-extension' ),
        'view_item'          => __( 'Voir la FAQ', 'divi-faq-extension' ),
        'search_items'       => __( 'Rechercher des FAQs', 'divi-faq-extension' ),
        'not_found'          => __( 'Aucune FAQ trouvée', 'divi-faq-extension' ),
        'not_found_in_trash' => __( 'Aucune FAQ trouvée dans la corbeille', 'divi-faq-extension' )
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'faq' ),
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'menu_position'       => null,
        'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
        'menu_icon'           => 'dashicons-format-chat',
        'show_in_rest'        => true
    );

    register_post_type( 'faq', $args );

    $taxonomy_labels = array(
        'name'              => __( 'Catégories FAQ', 'divi-faq-extension' ),
        'singular_name'     => __( 'Catégorie FAQ', 'divi-faq-extension' ),
        'search_items'      => __( 'Rechercher des catégories', 'divi-faq-extension' ),
        'all_items'         => __( 'Toutes les catégories', 'divi-faq-extension' ),
        'parent_item'       => __( 'Catégorie parente', 'divi-faq-extension' ),
        'parent_item_colon' => __( 'Catégorie parente:', 'divi-faq-extension' ),
        'edit_item'         => __( 'Modifier la catégorie', 'divi-faq-extension' ),
        'update_item'       => __( 'Mettre à jour la catégorie', 'divi-faq-extension' ),
        'add_new_item'      => __( 'Ajouter une nouvelle catégorie', 'divi-faq-extension' ),
        'new_item_name'     => __( 'Nom de la nouvelle catégorie', 'divi-faq-extension' ),
        'menu_name'         => __( 'Catégories FAQ', 'divi-faq-extension' ),
    );

    $taxonomy_args = array(
        'hierarchical'      => true,
        'labels'            => $taxonomy_labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'faq-category' ),
        'show_in_rest'      => true
    );

    register_taxonomy( 'faq_category', array( 'faq' ), $taxonomy_args );
}
add_action( 'init', 'create_faq_post_type' );

function add_faq_order_field() {
    add_meta_box(
        'faq_order',
        __( 'Ordre d\'affichage', 'divi-faq-extension' ),
        'faq_order_callback',
        'faq',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'add_faq_order_field' );

function faq_order_callback( $post ) {
    wp_nonce_field( 'faq_order_nonce', 'faq_order_nonce' );
    $value = get_post_meta( $post->ID, '_faq_order', true );
    echo '<input type="number" id="faq_order" name="faq_order" value="' . esc_attr( $value ) . '" style="width:100%">';
}

function save_faq_order( $post_id ) {
    if ( ! isset( $_POST['faq_order_nonce'] ) ) {
        return;
    }
    if ( ! wp_verify_nonce( $_POST['faq_order_nonce'], 'faq_order_nonce' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    if ( isset( $_POST['faq_order'] ) ) {
        update_post_meta( $post_id, '_faq_order', sanitize_text_field( $_POST['faq_order'] ) );
    }
}
add_action( 'save_post', 'save_faq_order' );

function add_faq_order_to_rest() {
    register_rest_field( 'faq',
        '_faq_order',
        array(
            'get_callback'    => function( $post ) {
                return get_post_meta( $post['id'], '_faq_order', true );
            },
            'update_callback' => null,
            'schema'          => null,
        )
    );
}
add_action( 'rest_api_init', 'add_faq_order_to_rest' );

// Message d'erreur si Divi n'est pas actif
function divi_faq_extension_admin_notice() {
    if ( ! divi_faq_extension_has_parent_theme() ) {
        echo '<div class="error"><p>' . esc_html__( 'Divi FAQ Extension nécessite le thème Divi pour fonctionner.', 'divi-faq-extension' ) . '</p></div>';
    }
}
add_action( 'admin_notices', 'divi_faq_extension_admin_notice' );