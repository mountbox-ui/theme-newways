<?php
/**
 * Simple Locations Management System
 * Creates a Locations menu where you can add location pages that are listed ONLY in Locations menu and NOT in Pages menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Locations menu to WordPress admin
 */
function add_locations_admin_menu() {
    add_menu_page(
        'Locations',                     // Page title
        'Locations',                     // Menu title
        'manage_options',                // Capability
        'locations-management',          // Menu slug
        'locations_admin_page',          // Function to display page
        'dashicons-location',           // Icon
        26                              // Position (after Services)
    );
}
add_action('admin_menu', 'add_locations_admin_menu');

/**
 * Main Locations admin page
 */
function locations_admin_page() {
    // Handle location creation
    if (isset($_GET['action']) && $_GET['action'] === 'create_location') {
        $location_id = wp_insert_post(array(
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'draft',
            'post_type' => 'page',
            'post_author' => get_current_user_id()
        ));
        
        if ($location_id) {
            update_post_meta($location_id, 'is_location_page', '1');
            update_post_meta($location_id, '_wp_page_template', 'templates/page-location.php');
            
            // Use JavaScript redirect instead of PHP redirect
            echo '<script>window.location.href = "' . admin_url('post.php?post=' . $location_id . '&action=edit') . '";</script>';
            echo '<p>Creating location... <a href="' . admin_url('post.php?post=' . $location_id . '&action=edit') . '">Click here if not redirected automatically</a></p>';
            return;
        }
    }
    
    // Get all location pages
    $location_pages = get_posts(array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft', 'private'),
        'meta_query' => array(
            array(
                'key' => 'is_location_page',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Locations Management</h1>
        <a href="<?php echo admin_url('admin.php?page=locations-management&action=create_location'); ?>" class="page-title-action">Add New Location</a>
        <hr class="wp-header-end">
        
        <!-- Locations List -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">Your Locations</h2>
            </div>
            <div class="inside">
                <?php if (empty($location_pages)): ?>
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <div style="font-size: 48px; margin-bottom: 20px;">📍</div>
                        <h3>No Locations Yet</h3>
                        <p>Create your first location using the "Add New Location" button above.</p>
                    </div>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Location Title</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($location_pages as $location): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($location->ID); ?>">
                                                <?php echo esc_html($location->post_title ?: '(No Title)'); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $location->post_status;
                                        $status_colors = array(
                                            'publish' => array('bg' => '#d4edda', 'color' => '#155724', 'text' => 'Published'),
                                            'draft' => array('bg' => '#fff3cd', 'color' => '#856404', 'text' => 'Draft'),
                                            'private' => array('bg' => '#f8d7da', 'color' => '#721c24', 'text' => 'Private')
                                        );
                                        $status_info = $status_colors[$status];
                                        ?>
                                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; text-transform: uppercase; background: <?php echo $status_info['bg']; ?>; color: <?php echo $status_info['color']; ?>;">
                                            <?php echo $status_info['text']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo get_the_date('M j, Y', $location->ID); ?></td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link($location->ID); ?>" class="button button-small">Edit</a>
                                        <?php if ($location->post_status === 'publish'): ?>
                                            <a href="<?php echo get_permalink($location->ID); ?>" class="button button-small" target="_blank">View</a>
                                        <?php endif; ?>
                                        <a href="<?php echo get_delete_post_link($location->ID); ?>" class="button button-small" onclick="return confirm('Delete this location?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Hide location pages from the main Pages list
 */
function hide_location_pages_from_pages_list($query) {
    if (is_admin() && $query->is_main_query()) {
        global $pagenow;
        
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            $query->set('meta_query', array(
                array(
                    'key' => 'is_location_page',
                    'compare' => 'NOT EXISTS'
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'hide_location_pages_from_pages_list');

/**
 * Add location type column to Pages list (for admin reference)
 */
function add_location_column_to_pages($columns) {
    $columns['location_type'] = 'Type';
    return $columns;
}
add_filter('manage_pages_columns', 'add_location_column_to_pages');

/**
 * Display location type in Pages list
 */
function display_location_column_content($column, $post_id) {
    if ($column === 'location_type') {
        $is_location = get_post_meta($post_id, 'is_location_page', true);
        if ($is_location === '1') {
            echo '<span style="color: #dc3545; font-weight: bold;">📍 Location</span>';
        } else {
            $is_service = get_post_meta($post_id, 'is_service_page', true);
            if ($is_service === '1') {
                echo '<span style="color: #016A7C; font-weight: bold;">🛠️ Service</span>';
            } else {
                echo '<span style="color: #28a745;">📄 Page</span>';
            }
        }
    }
}
add_action('manage_pages_custom_column', 'display_location_column_content', 10, 2);
?>
