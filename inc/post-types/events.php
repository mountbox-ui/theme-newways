<?php
/**
 * Register Events Custom Post Type
 */

if (!defined('ABSPATH')) exit;

function neways_register_events_post_type() {
    $labels = array(
        'name'               => _x('Events', 'post type general name', 'neways'),
        'singular_name'      => _x('Event', 'post type singular name', 'neways'),
        'menu_name'          => _x('Events', 'admin menu', 'neways'),
        'name_admin_bar'     => _x('Event', 'add new on admin bar', 'neways'),
        'add_new'           => _x('Add New', 'event', 'neways'),
        'add_new_item'      => __('Add New Event', 'neways'),
        'new_item'          => __('New Event', 'neways'),
        'edit_item'         => __('Edit Event', 'neways'),
        'view_item'         => __('View Event', 'neways'),
        'all_items'         => __('All Events', 'neways'),
        'search_items'      => __('Search Events', 'neways'),
        'not_found'         => __('No events found.', 'neways'),
        'not_found_in_trash'=> __('No events found in Trash.', 'neways')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'events'),
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 5,
        'menu_icon'         => 'dashicons-calendar-alt',
        'supports'          => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'      => true,
    );

    register_post_type('event', $args);

    // Register Event Category Taxonomy
    $cat_labels = array(
        'name'              => _x('Event Categories', 'taxonomy general name', 'neways'),
        'singular_name'     => _x('Event Category', 'taxonomy singular name', 'neways'),
        'search_items'      => __('Search Event Categories', 'neways'),
        'all_items'         => __('All Event Categories', 'neways'),
        'parent_item'       => __('Parent Event Category', 'neways'),
        'parent_item_colon' => __('Parent Event Category:', 'neways'),
        'edit_item'         => __('Edit Event Category', 'neways'),
        'update_item'       => __('Update Event Category', 'neways'),
        'add_new_item'      => __('Add New Event Category', 'neways'),
        'new_item_name'     => __('New Event Category Name', 'neways'),
        'menu_name'         => __('Categories', 'neways'),
    );

    register_taxonomy('event_category', array('event'), array(
        'hierarchical'      => true,
        'labels'           => $cat_labels,
        'show_ui'          => true,
        'show_admin_column'=> true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'event-category'),
        'show_in_rest'     => true,
    ));
}
add_action('init', 'neways_register_events_post_type');

// WordPress meta boxes removed - using ACF plugin instead


