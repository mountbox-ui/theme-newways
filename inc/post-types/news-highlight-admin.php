<?php
/**
 * News Highlight Admin Functionality
 * Adds highlight radio button column to news listing page
 */

if (!defined('ABSPATH')) exit;

/**
 * Add custom columns to news admin list
 */
function neways_news_admin_columns($columns) {
    // Add custom columns after title
    $new_columns = array();
    foreach ($columns as $key => $value) {
        $new_columns[$key] = $value;
        if ($key === 'title') {
            $new_columns['highlight'] = __('Highlight', 'neways');
            $new_columns['show_on_homepage'] = __('Homepage', 'neways');
        }
    }
    return $new_columns;
}
add_filter('manage_news_posts_columns', 'neways_news_admin_columns');

/**
 * Display custom column content
 */
function neways_news_admin_column_content($column, $post_id) {
    switch ($column) {
        case 'highlight':
            // Simple approach: check if this post is marked as highlighted
            $is_highlighted = get_post_meta($post_id, '_is_highlighted', true) === '1';
            ?>
            <label class="neways-highlight-radio">
                <input type="radio" 
                       name="highlight_news" 
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
            break;
            
        case 'show_on_homepage':
            $show_on_homepage = get_post_meta($post_id, '_show_on_homepage', true);
            if ($show_on_homepage == '1') {
                echo '<span style="color: #059669; font-weight: 600;">✓ Yes</span>';
            } else {
                echo '<span style="color: #6b7280;">—</span>';
            }
            break;
    }
}
add_action('manage_news_posts_custom_column', 'neways_news_admin_column_content', 10, 2);

/**
 * Add save button above the news list
 */
function neways_add_highlight_save_button() {
    global $pagenow, $typenow;
    if ($pagenow === 'edit.php' && $typenow === 'news') {
        ?>
        <div style="margin: 20px 0; padding: 15px; background: #f1f1f1; border: 1px solid #ccc; border-radius: 4px;">
            <h3 style="margin: 0 0 10px 0;">Highlight News</h3>
            <p style="margin: 0 0 15px 0;">Select one news item to highlight, then click Save to apply changes.</p>
            <button type="button" id="save-highlight-btn" class="button button-primary" onclick="saveHighlightSelection()">
                <span class="save-text">Save Highlight Selection</span>
                <span class="loading-text" style="display: none;">Saving...</span>
            </button>
            <div id="highlight-status" style="margin-left: 10px; display: inline-block;"></div>
        </div>
        <?php
    }
}
add_action('all_admin_notices', 'neways_add_highlight_save_button');

/**
 * Add JavaScript for highlight functionality
 */
function neways_news_admin_scripts($hook) {
    // Debug: Check if we're on the right page
    global $pagenow, $typenow;
    if (($hook === 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] === 'news') || 
        ($pagenow === 'edit.php' && $typenow === 'news')) {
        ?>
        <script type="text/javascript">
        console.log('=== Neways Highlight Script Loading ===');
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
            const radios = document.querySelectorAll('input[name="highlight_news"]');
            
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
            const selectedRadio = document.querySelector('input[name="highlight_news"]:checked');
            const selectedPostId = selectedRadio ? selectedRadio.value : null;
            
            // Show loading state
            saveBtn.disabled = true;
            saveText.style.display = 'none';
            loadingText.style.display = 'inline';
            statusDiv.innerHTML = '';
            
            // Send AJAX request
            const data = new FormData();
            data.append('action', 'neways_save_highlight');
            data.append('post_id', selectedPostId || '');
            data.append('nonce', '<?php echo wp_create_nonce('neways_highlight_save_nonce'); ?>');
            
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
            const radios = document.querySelectorAll('input[name="highlight_news"]');
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
add_action('admin_head', 'neways_news_admin_scripts');

/**
 * Alternative method to ensure JavaScript is loaded
 */
function neways_news_admin_scripts_alternative() {
    global $pagenow, $typenow;
    if ($pagenow === 'edit.php' && $typenow === 'news') {
        ?>
        <script type="text/javascript">
        // Ensure functions are available even if main script didn't load
        if (typeof window.handleRadioClick === 'undefined') {
            console.log('Loading fallback highlight functions');
            let lastChecked = null;
            
            window.handleRadioClick = function(clickedRadio) {
                console.log('Fallback handleRadioClick called');
                if (clickedRadio.checked && lastChecked === clickedRadio) {
                    clickedRadio.checked = false;
                    lastChecked = null;
                    updateRadioVisualState(clickedRadio);
                } else {
                    lastChecked = clickedRadio;
                    updateRadioVisualState(clickedRadio);
                }
            };
            
            window.updateRadioVisualState = function(clickedRadio) {
                console.log('Fallback updateRadioVisualState called');
                const radios = document.querySelectorAll('input[name="highlight_news"]');
                
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
            
            window.saveHighlightSelection = function() {
                console.log('Fallback saveHighlightSelection called');
                const saveBtn = document.getElementById('save-highlight-btn');
                const statusDiv = document.getElementById('highlight-status');
                const saveText = saveBtn.querySelector('.save-text');
                const loadingText = saveBtn.querySelector('.loading-text');
                
                const selectedRadio = document.querySelector('input[name="highlight_news"]:checked');
                const selectedPostId = selectedRadio ? selectedRadio.value : null;
                
                saveBtn.disabled = true;
                saveText.style.display = 'none';
                loadingText.style.display = 'inline';
                statusDiv.innerHTML = '';
                
                const data = new FormData();
                data.append('action', 'neways_save_highlight');
                data.append('post_id', selectedPostId || '');
                data.append('nonce', '<?php echo wp_create_nonce('neways_highlight_save_nonce'); ?>');
                
                fetch(ajaxurl, {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    saveBtn.disabled = false;
                    saveText.style.display = 'inline';
                    loadingText.style.display = 'none';
                    
                    if (result.success) {
                        statusDiv.innerHTML = '<span style="color: #46b450;">✓ Saved successfully!</span>';
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        statusDiv.innerHTML = '<span style="color: #dc3232;">Error: ' + (result.data || 'Unknown error') + '</span>';
                    }
                })
                .catch(error => {
                    saveBtn.disabled = false;
                    saveText.style.display = 'inline';
                    loadingText.style.display = 'none';
                    
                    console.error('AJAX Error:', error);
                    statusDiv.innerHTML = '<span style="color: #dc3232;">Error saving. Please try again.</span>';
                });
            };
        }
        </script>
        <?php
    }
}
add_action('admin_footer', 'neways_news_admin_scripts_alternative');

/**
 * AJAX handler for highlight toggle
 */
function neways_ajax_toggle_highlight() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'neways_highlight_nonce')) {
        wp_die('Security check failed');
    }
    
    // Check permissions
    if (!current_user_can('edit_posts')) {
        wp_die('Insufficient permissions');
    }
    
    $post_id = intval($_POST['post_id']);
    $highlighted = $_POST['highlighted'] === '1';
    
    // Get current categories
    $current_terms = wp_get_post_terms($post_id, 'news_category', array('fields' => 'ids'));
    $highlighted_term = get_term_by('slug', 'highlighted', 'news_category');
    
    if (!$highlighted_term) {
        // Create highlighted category if it doesn't exist
        $highlighted_term = wp_insert_term('Highlighted', 'news_category', array('slug' => 'highlighted'));
        if (is_wp_error($highlighted_term)) {
            wp_send_json_error('Failed to create highlighted category');
            return;
        }
        $highlighted_term_id = $highlighted_term['term_id'];
    } else {
        $highlighted_term_id = $highlighted_term->term_id;
    }
    
    // Remove highlighted category from all other news posts first
    $all_news = get_posts(array(
        'post_type' => 'news',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));
    
    foreach ($all_news as $news_id) {
        if ($news_id != $post_id) {
            wp_remove_object_terms($news_id, $highlighted_term_id, 'news_category');
        }
    }
    
    // Update the selected post
    if ($highlighted) {
        // Add highlighted category
        $new_terms = array_merge($current_terms, array($highlighted_term_id));
        wp_set_post_terms($post_id, array_unique($new_terms), 'news_category');
    } else {
        // Remove highlighted category
        wp_remove_object_terms($post_id, $highlighted_term_id, 'news_category');
    }
    
    wp_send_json_success('Highlight status updated successfully');
}
add_action('wp_ajax_neways_toggle_highlight', 'neways_ajax_toggle_highlight');

/**
 * AJAX handler for save highlight button
 */
function neways_ajax_save_highlight() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'neways_highlight_save_nonce')) {
        wp_send_json_error('Security check failed');
        return;
    }
    
    // Check permissions
    if (!current_user_can('edit_posts')) {
        wp_send_json_error('Insufficient permissions');
        return;
    }
    
    $post_id = !empty($_POST['post_id']) ? intval($_POST['post_id']) : null;
    
    // Get or create highlighted category
    $highlighted_term = get_term_by('slug', 'highlighted', 'news_category');
    if (!$highlighted_term) {
        // Create highlighted category if it doesn't exist
        $highlighted_term = wp_insert_term('Highlighted', 'news_category', array('slug' => 'highlighted'));
        if (is_wp_error($highlighted_term)) {
            wp_send_json_error('Failed to create highlighted category');
            return;
        }
        $highlighted_term_id = $highlighted_term['term_id'];
    } else {
        $highlighted_term_id = $highlighted_term->term_id;
    }
    
    // Remove highlight from all news posts first (both meta and category)
    $all_news = get_posts(array(
        'post_type' => 'news',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));
    
    foreach ($all_news as $news_id) {
        // Remove meta field
        delete_post_meta($news_id, '_is_highlighted');
        // Remove highlighted category
        wp_remove_object_terms($news_id, $highlighted_term_id, 'news_category');
    }
    
    // If a post was selected, mark it as highlighted (both meta and category)
    if ($post_id) {
        // Set meta field
        update_post_meta($post_id, '_is_highlighted', '1');
        // Add highlighted category
        wp_set_post_terms($post_id, array($highlighted_term_id), 'news_category', true);
        $message = 'News highlighted successfully';
    } else {
        $message = 'All highlights cleared successfully';
    }
    
    wp_send_json_success($message);
}
add_action('wp_ajax_neways_save_highlight', 'neways_ajax_save_highlight');

/**
 * Sync highlighted posts - ensure meta field and category are in sync
 * This function can be called manually or automatically
 */
function neways_sync_highlighted_posts() {
    // Get or create highlighted category
    $highlighted_term = get_term_by('slug', 'highlighted', 'news_category');
    if (!$highlighted_term) {
        $highlighted_term = wp_insert_term('Highlighted', 'news_category', array('slug' => 'highlighted'));
        if (is_wp_error($highlighted_term)) {
            return false;
        }
        $highlighted_term_id = $highlighted_term['term_id'];
    } else {
        $highlighted_term_id = $highlighted_term->term_id;
    }
    
    // Get all news posts with _is_highlighted meta
    $highlighted_posts = get_posts(array(
        'post_type' => 'news',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_is_highlighted',
                'value' => '1',
                'compare' => '='
            )
        )
    ));
    
    // Remove highlighted category from all news posts first
    $all_news = get_posts(array(
        'post_type' => 'news',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'fields' => 'ids'
    ));
    
    foreach ($all_news as $news_id) {
        wp_remove_object_terms($news_id, $highlighted_term_id, 'news_category');
    }
    
    // Add highlighted category to posts that have the meta field
    foreach ($highlighted_posts as $post) {
        wp_set_post_terms($post->ID, array($highlighted_term_id), 'news_category', true);
    }
    
    return true;
}

/**
 * Auto-sync highlighted posts on admin init
 */
function neways_auto_sync_highlighted_posts() {
    if (is_admin() && current_user_can('edit_posts')) {
        neways_sync_highlighted_posts();
    }
}
add_action('admin_init', 'neways_auto_sync_highlighted_posts');

/**
 * Debug function to check highlight system status
 * Add ?debug_highlight_system=1 to any URL
 */
function neways_debug_highlight_system() {
    if (isset($_GET['debug_highlight_system']) && current_user_can('manage_options')) {
        echo '<div style="background: #fff; padding: 20px; margin: 20px; border: 1px solid #ccc; position: fixed; top: 0; left: 0; z-index: 9999; max-width: 800px;">';
        echo '<h3>Highlight System Debug Info</h3>';
        
        // Check highlighted category
        $highlighted_term = get_term_by('slug', 'highlighted', 'news_category');
        if ($highlighted_term) {
            echo '<p><strong>✅ Highlighted category exists:</strong> ' . $highlighted_term->name . ' (ID: ' . $highlighted_term->term_id . ')</p>';
        } else {
            echo '<p><strong>❌ Highlighted category does NOT exist</strong></p>';
        }
        
        // Check posts with meta field
        $highlighted_posts_meta = get_posts(array(
            'post_type' => 'news',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => '_is_highlighted',
                    'value' => '1',
                    'compare' => '='
                )
            )
        ));
        
        echo '<p><strong>Posts with _is_highlighted meta field:</strong> ' . count($highlighted_posts_meta) . '</p>';
        if (!empty($highlighted_posts_meta)) {
            foreach ($highlighted_posts_meta as $post) {
                echo '<p>- ' . $post->post_title . ' (ID: ' . $post->ID . ')</p>';
            }
        }
        
        // Check posts with highlighted category
        if ($highlighted_term) {
            $highlighted_posts_category = get_posts(array(
                'post_type' => 'news',
                'posts_per_page' => -1,
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'news_category',
                        'field' => 'term_id',
                        'terms' => $highlighted_term->term_id,
                    )
                )
            ));
            
            echo '<p><strong>Posts with highlighted category:</strong> ' . count($highlighted_posts_category) . '</p>';
            if (!empty($highlighted_posts_category)) {
                foreach ($highlighted_posts_category as $post) {
                    echo '<p>- ' . $post->post_title . ' (ID: ' . $post->ID . ')</p>';
                }
            }
        }
        
        // Check sync status
        $meta_posts = wp_list_pluck($highlighted_posts_meta, 'ID');
        $category_posts = $highlighted_term ? wp_list_pluck($highlighted_posts_category, 'ID') : array();
        
        if ($meta_posts === $category_posts) {
            echo '<p><strong>✅ Meta field and category are in sync</strong></p>';
        } else {
            echo '<p><strong>❌ Meta field and category are NOT in sync</strong></p>';
            echo '<p>Meta posts: ' . implode(', ', $meta_posts) . '</p>';
            echo '<p>Category posts: ' . implode(', ', $category_posts) . '</p>';
        }
        
        echo '<p><a href="' . remove_query_arg('debug_highlight_system') . '">Close Debug</a></p>';
        echo '</div>';
    }
}
add_action('wp_head', 'neways_debug_highlight_system');
add_action('admin_head', 'neways_debug_highlight_system');
