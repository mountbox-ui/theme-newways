<?php
/**
 * Register Team Custom Post Type
 */

if (!defined('ABSPATH')) exit;

function neways_register_team_post_type() {
    $labels = array(
        'name'               => _x('Team Members', 'post type general name', 'neways'),
        'singular_name'      => _x('Team Member', 'post type singular name', 'neways'),
        'menu_name'          => _x('Team', 'admin menu', 'neways'),
        'name_admin_bar'     => _x('Team Member', 'add new on admin bar', 'neways'),
        'add_new'           => _x('Add New', 'team member', 'neways'),
        'add_new_item'      => __('Add New Team Member', 'neways'),
        'new_item'          => __('New Team Member', 'neways'),
        'edit_item'         => __('Edit Team Member', 'neways'),
        'view_item'         => __('View Team Member', 'neways'),
        'all_items'         => __('All Team Members', 'neways'),
        'search_items'      => __('Search Team Members', 'neways'),
        'not_found'         => __('No team members found.', 'neways'),
        'not_found_in_trash'=> __('No team members found in Trash.', 'neways')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'query_var'         => true,
        'rewrite'           => array('slug' => 'team'),
        'capability_type'   => 'post',
        'has_archive'       => true,
        'hierarchical'      => false,
        'menu_position'     => 6,
        'menu_icon'         => 'dashicons-groups',
        'supports'          => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest'      => true,
    );

    register_post_type('team', $args);

    // Register Team Department Taxonomy
    $dept_labels = array(
        'name'              => _x('Departments', 'taxonomy general name', 'neways'),
        'singular_name'     => _x('Department', 'taxonomy singular name', 'neways'),
        'search_items'      => __('Search Departments', 'neways'),
        'all_items'         => __('All Departments', 'neways'),
        'parent_item'       => __('Parent Department', 'neways'),
        'parent_item_colon' => __('Parent Department:', 'neways'),
        'edit_item'         => __('Edit Department', 'neways'),
        'update_item'       => __('Update Department', 'neways'),
        'add_new_item'      => __('Add New Department', 'neways'),
        'new_item_name'     => __('New Department Name', 'neways'),
        'menu_name'         => __('Departments', 'neways'),
    );

    register_taxonomy('team_department', array('team'), array(
        'hierarchical'      => true,
        'labels'           => $dept_labels,
        'show_ui'          => true,
        'show_admin_column'=> true,
        'query_var'        => true,
        'rewrite'          => array('slug' => 'team-department'),
        'show_in_rest'     => true,
        'public'           => true,
        'publicly_queryable' => true,
        'show_in_nav_menus' => true,
        'show_tagcloud'    => true,
    ));
}
add_action('init', 'neways_register_team_post_type');

/**
 * Flush rewrite rules on theme activation
 */
function neways_flush_team_rewrite_rules() {
    neways_register_team_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'neways_flush_team_rewrite_rules');

/**
 * Add custom columns to team members admin list
 */
function neways_team_admin_columns($columns) {
    // Add new column after title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['highlight'] = __('Highlight', 'neways');
        }
    }
    return $new_columns;
}
add_filter('manage_team_posts_columns', 'neways_team_admin_columns');

/**
 * Display custom column content
 */
function neways_team_admin_column_content($column, $post_id) {
    if ($column === 'highlight') {
        // Simple approach: check if this post is marked as highlighted
        $is_highlighted = get_post_meta($post_id, '_is_highlighted', true) === '1';
        ?>
        <label class="neways-highlight-radio">
            <input type="radio" 
                   name="highlight_team" 
                   value="<?php echo esc_attr($post_id); ?>"
                   <?php checked($is_highlighted); ?>
                   data-is-highlighted="<?php echo $is_highlighted ? '1' : '0'; ?>"
                   onchange="if(typeof updateRadioVisualState === 'function') { updateRadioVisualState(this); } else { console.log('updateRadioVisualState not available'); }"
                   onclick="if(typeof handleRadioClick === 'function') { handleRadioClick(this); } else { console.log('handleRadioClick not available'); }"
                   style="margin-right: 8px;">
            <span style="<?php echo $is_highlighted ? 'color: #d97706; font-weight: 600;' : 'color: #6b7280;'; ?>">
                <?php echo $is_highlighted ? '★ Highlighted' : '☆ Highlight'; ?>
            </span>
        </label>
        <?php
    }
}
add_action('manage_team_posts_custom_column', 'neways_team_admin_column_content', 10, 2);

/**
 * Add save button above the team list
 */
function neways_add_team_highlight_save_button() {
    global $pagenow, $typenow;
    if ($pagenow === 'edit.php' && $typenow === 'team') {
        ?>
        <div style="margin: 20px 0; padding: 15px; background: #f1f1f1; border: 1px solid #ccc; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0;">Highlight Team Member</h3>
            <p style="margin: 0 0 15px 0;">Select one team member to highlight, then click Save to apply changes.</p>
            <button type="button" id="save-highlight-btn" class="button button-primary" onclick="saveHighlightSelection()">
                <span class="save-text">Save Highlight Selection</span>
                <span class="loading-text" style="display: none;">Saving...</span>
            </button>
            <div id="highlight-status" style="margin-left: 10px; display: inline-block;"></div>
        </div>
        <?php
    }
}
add_action('all_admin_notices', 'neways_add_team_highlight_save_button');

/**
 * Add JavaScript for team highlight functionality
 */
function neways_team_admin_scripts($hook) {
    global $pagenow, $typenow;
    if (($hook === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'team') || 
        ($pagenow === 'edit.php' && $typenow === 'team')) {
        ?>
        <script type="text/javascript">
        console.log('=== Neways Team Highlight Script Loading ===');
        let lastChecked = null;
        
        // Make functions globally available
        window.handleRadioClick = function(clickedRadio) {
            console.log('handleRadioClick called');
            if (clickedRadio.checked && lastChecked === clickedRadio) {
                // If clicking the same radio that's already checked, uncheck it
                clickedRadio.checked = false;
                lastChecked = null;
                updateRadioVisualState(clickedRadio);
            } else {
                lastChecked = clickedRadio;
                updateRadioVisualState(clickedRadio);
            }
        };
        
        // Update visual state of radio buttons
        window.updateRadioVisualState = function(clickedRadio) {
            console.log('updateRadioVisualState called');
            const radios = document.querySelectorAll('input[name="highlight_team"]');
            
            radios.forEach(radio => {
                const span = radio.nextElementSibling;
                
                if (radio.checked) {
                    span.textContent = '★ Highlighted (pending save)';
                    span.style.color = '#059669';
                    span.style.fontWeight = '600';
                } else {
                    span.textContent = '☆ Highlight';
                    span.style.color = '#6b7280';
                    span.style.fontWeight = 'normal';
                }
            });
        };
        
        // Save highlight selection function
        window.saveHighlightSelection = function() {
            const saveBtn = document.getElementById('save-highlight-btn');
            const statusDiv = document.getElementById('highlight-status');
            const saveText = saveBtn.querySelector('.save-text');
            const loadingText = saveBtn.querySelector('.loading-text');
            
            // Get selected radio button
            const selectedRadio = document.querySelector('input[name="highlight_team"]:checked');
            const selectedPostId = selectedRadio ? selectedRadio.value : null;
            
            // Show loading state
            saveBtn.disabled = true;
            saveText.style.display = 'none';
            loadingText.style.display = 'inline';
            statusDiv.innerHTML = '';
            
            // Send AJAX request
            const data = new FormData();
            data.append('action', 'neways_save_team_highlight');
            data.append('post_id', selectedPostId || '');
            data.append('nonce', '<?php echo wp_create_nonce('neways_team_highlight_save_nonce'); ?>');
            
            fetch(ajaxurl, {
                method: 'POST',
                body: data
            })
            .then(response => response.json())
            .then(result => {
                // Reset button state
                saveBtn.disabled = false;
                saveText.style.display = 'inline';
                loadingText.style.display = 'none';
                
                if (result.success) {
                    statusDiv.innerHTML = '<span style="color: #46b450;">✓ Saved successfully!</span>';
                    // Reload page to show updated states
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    statusDiv.innerHTML = '<span style="color: #dc3232;">Error: ' + (result.data || 'Unknown error') + '</span>';
                }
            })
            .catch(error => {
                // Reset button state
                saveBtn.disabled = false;
                saveText.style.display = 'inline';
                loadingText.style.display = 'none';
                
                console.error('AJAX Error:', error);
                statusDiv.innerHTML = '<span style="color: #dc3232;">Error saving. Please try again.</span>';
            });
        };
        
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            console.log('=== Radio Button State Check ===');
            const radios = document.querySelectorAll('input[name="highlight_team"]');
            radios.forEach(radio => {
                const isHighlighted = radio.getAttribute('data-is-highlighted') === '1';
                const span = radio.nextElementSibling;
                
                console.log('Post ID:', radio.value, 'Should be highlighted:', isHighlighted);
                
                if (isHighlighted) {
                    radio.checked = true;
                    span.textContent = '★ Highlighted';
                    span.style.color = '#d97706';
                    span.style.fontWeight = '600';
                    lastChecked = radio;
                    console.log('✓ Set as highlighted');
                } else {
                    radio.checked = false;
                    span.textContent = '☆ Highlight';
                    span.style.color = '#6b7280';
                    span.style.fontWeight = 'normal';
                }
            });
        });
        </script>
        <style>
        .neways-highlight-radio {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
        }
        .neways-highlight-radio input[type="radio"] {
            margin-right: 8px;
        }
        </style>
        <?php
    }
}
add_action('admin_head', 'neways_team_admin_scripts');

/**
 * AJAX handler for save team highlight
 */
function neways_ajax_save_team_highlight() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'neways_team_highlight_save_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $post_id = !empty($_POST['post_id']) ? intval($_POST['post_id']) : null;
    
    // Remove highlight from all team posts first
    $all_team = get_posts(array(
        'post_type' => 'team',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));
    
    foreach ($all_team as $team_id) {
        delete_post_meta($team_id, '_is_highlighted');
    }
    
    // If a post was selected, mark it as highlighted
    if ($post_id) {
        update_post_meta($post_id, '_is_highlighted', '1');
        $message = 'Team member highlighted successfully';
    } else {
        $message = 'All highlights cleared successfully';
    }
    
    wp_send_json_success($message);
}
add_action('wp_ajax_neways_save_team_highlight', 'neways_ajax_save_team_highlight');

/**
 * Flush rewrite rules when team post type is updated
 */
function neways_flush_team_rules_on_save() {
    if (get_post_type() == 'team') {
        flush_rewrite_rules();
    }
}
add_action('save_post', 'neways_flush_team_rules_on_save');

/**
 * Custom function to render team member card
 */
function neways_render_team_card($post_id, $use_acf = false) {
    // Get team member details from ACF fields
    $name = '';
    $designation = '';
    $image = '';
    $message = '';
    $message_visibility = true;
    
    // Get ACF fields using your specific field names
    if ($use_acf && function_exists('get_field')) {
        $name = get_field('name', $post_id);
        $designation = get_field('designation', $post_id);
        $image = get_field('image', $post_id);
        $message = get_field('message', $post_id);
        $message_visibility = get_field('message_visibility', $post_id);
    }
    
    // Use post title as fallback for name
    if (empty($name)) {
        $name = get_the_title($post_id);
    }
    ?>
    <div class="team-member-card flex flex-col items-center text-center">
        <div class="relative w-32 h-32 mb-4">
            <?php 
            // Use ACF image if available, otherwise fallback to featured image
            if (!empty($image) && is_array($image)) {
                $image_url = $image['sizes']['medium'] ?? $image['url'];
                $image_alt = $image['alt'] ?? $name;
                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" class="w-full h-full rounded-full object-cover border-4 border-white shadow-lg" />';
            } elseif (has_post_thumbnail($post_id)) {
                echo get_the_post_thumbnail($post_id, 'medium', array('class' => 'w-full h-full rounded-full object-cover border-4 border-white shadow-lg'));
            } else {
                ?>
                <div class="w-full h-full rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <?php
            }
            ?>
        </div>
        
        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-1">
            <?php echo esc_html($name); ?>
        </h3>
        
        <?php if (!empty($designation)) : ?>
            <p class="text-lg text-blue-600 dark:text-blue-400 font-medium mb-2">
                <?php echo esc_html($designation); ?>
            </p>
        <?php endif; ?>
        
        <?php if (!empty($message) && $message_visibility) : ?>
            <div class="text-sm text-gray-700 dark:text-gray-300 mb-4 max-w-sm italic">
                <blockquote class="border-l-4 border-blue-500 pl-4">
                    "<?php echo esc_html($message); ?>"
                </blockquote>
            </div>
        <?php endif; ?>
        
    </div>
    <?php
}

/**
 * Auto-create team page if it doesn't exist
 */
function neways_auto_create_team_page() {
    // Check if team page exists
    $team_page = get_page_by_path('team');
    
    if (!$team_page) {
        // Create the team page
        $page_data = array(
            'post_title'   => 'Our Team',
            'post_name'    => 'team',
            'post_content' => 'Meet our amazing team members.',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_author'  => 1,
        );
        
        $page_id = wp_insert_post($page_data);
        
        if ($page_id && !is_wp_error($page_id)) {
            // Set the page template
            update_post_meta($page_id, '_wp_page_template', 'templates/page-team.php');
            
            // Flush rewrite rules
            flush_rewrite_rules();
        }
    }
}
add_action('init', 'neways_auto_create_team_page');
