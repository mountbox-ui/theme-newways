<?php
/**
 * Simple Services Management System
 * Creates a Services menu where you can add services that are listed ONLY in Services menu and NOT in Pages menu
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add Services menu to WordPress admin
 */
function add_services_admin_menu() {
    add_menu_page(
        'Services',                    // Page title
        'Services',                    // Menu title
        'manage_options',              // Capability
        'services-management',         // Menu slug
        'services_admin_page',         // Function to display page
        'dashicons-admin-tools',       // Icon
        25                             // Position (after posts)
    );
}
add_action('admin_menu', 'add_services_admin_menu');

/**
 * Main Services admin page
 */
function services_admin_page() {
    // Handle service creation
    if (isset($_GET['action']) && $_GET['action'] === 'create_service') {
        $service_id = wp_insert_post(array(
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'draft',
            'post_type' => 'page',
            'post_author' => get_current_user_id()
        ));
        
        if ($service_id) {
            update_post_meta($service_id, 'is_service_page', '1');
            update_post_meta($service_id, '_wp_page_template', 'templates/page-service.php');
            
            // Use JavaScript redirect instead of PHP redirect
            echo '<script>window.location.href = "' . admin_url('post.php?post=' . $service_id . '&action=edit') . '";</script>';
            echo '<p>Creating service... <a href="' . admin_url('post.php?post=' . $service_id . '&action=edit') . '">Click here if not redirected automatically</a></p>';
            return;
        }
    }
    
    // Get all service pages
    $service_pages = get_posts(array(
        'post_type' => 'page',
        'posts_per_page' => -1,
        'post_status' => array('publish', 'draft', 'private'),
        'meta_query' => array(
            array(
                'key' => 'is_service_page',
                'value' => '1',
                'compare' => '='
            )
        ),
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    
    ?>
    <div class="wrap">
        <h1 class="wp-heading-inline">Services Management</h1>
        <a href="<?php echo admin_url('admin.php?page=services-management&action=create_service'); ?>" class="page-title-action">Add New Service</a>
        <hr class="wp-header-end">
        
        
        
        <!-- Services List -->
        <div class="postbox">
            <div class="postbox-header">
                <h2 class="hndle">Your Services</h2>
            </div>
            <div class="inside">
                <?php if (empty($service_pages)): ?>
                    <div style="text-align: center; padding: 40px; color: #666;">
                        <div style="font-size: 48px; margin-bottom: 20px;">🛠️</div>
                        <h3>No Services Yet</h3>
                        <p>Create your first service using the "Add New Service" button above.</p>
                    </div>
                <?php else: ?>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th>Service Title</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($service_pages as $service): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="<?php echo get_edit_post_link($service->ID); ?>">
                                                <?php echo esc_html($service->post_title ?: '(No Title)'); ?>
                                            </a>
                                        </strong>
                                    </td>
                                    <td>
                                        <?php
                                        $status = $service->post_status;
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
                                    <td><?php echo get_the_date('M j, Y', $service->ID); ?></td>
                                    <td>
                                        <a href="<?php echo get_edit_post_link($service->ID); ?>" class="button button-small">Edit</a>
                                        <?php if ($service->post_status === 'publish'): ?>
                                            <a href="<?php echo get_permalink($service->ID); ?>" class="button button-small" target="_blank">View</a>
                                        <?php endif; ?>
                                        <a href="<?php echo get_delete_post_link($service->ID); ?>" class="button button-small" onclick="return confirm('Delete this service?');">Delete</a>
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
 * Hide service pages from the main Pages list
 */
function hide_service_pages_from_pages_list($query) {
    if (is_admin() && $query->is_main_query()) {
        global $pagenow;
        
        if ($pagenow === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'page') {
            $query->set('meta_query', array(
                array(
                    'key' => 'is_service_page',
                    'compare' => 'NOT EXISTS'
                )
            ));
        }
    }
}
add_action('pre_get_posts', 'hide_service_pages_from_pages_list');

/**
 * Add service type column to Pages list (for admin reference)
 */
function add_service_column_to_pages($columns) {
    $columns['service_type'] = 'Type';
    return $columns;
}
add_filter('manage_pages_columns', 'add_service_column_to_pages');

/**
 * Display service type in Pages list
 */
function display_service_column_content($column, $post_id) {
    if ($column === 'service_type') {
        $is_service = get_post_meta($post_id, 'is_service_page', true);
        if ($is_service === '1') {
            echo '<span style="color: #dc3545; font-weight: bold;">🔒 Service</span>';
        } else {
            echo '<span style="color: #28a745;">📄 Page</span>';
        }
    }
}
add_action('manage_pages_custom_column', 'display_service_column_content', 10, 2);
?>