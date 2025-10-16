<?php
/**
 * Gallery Management System
 * 
 * Handles the backend logic for the custom gallery management system.
 * Allows uploading and managing unlimited photos for the gallery (max 2.5MB per image).
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Add Gallery menu to WordPress admin
 */
function neways_add_gallery_menu() {
    add_menu_page(
        __('Gallery Management', 'neways'),
        __('Gallery', 'neways'),
        'manage_options',
        'neways-gallery',
        'neways_render_gallery_page',
        'dashicons-images-alt2',
        30
    );
}
add_action('admin_menu', 'neways_add_gallery_menu');

/**
 * Render the gallery management page
 */
function neways_render_gallery_page() {
    // Handle form submissions
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'upload_images' && wp_verify_nonce($_POST['neways_gallery_nonce'], 'neways_gallery_upload')) {
            neways_handle_gallery_upload();
        } elseif ($_POST['action'] === 'remove_image' && wp_verify_nonce($_POST['neways_gallery_nonce'], 'neways_gallery_remove')) {
            neways_handle_gallery_remove();
        } elseif ($_POST['action'] === 'reorder_images' && wp_verify_nonce($_POST['neways_gallery_nonce'], 'neways_gallery_reorder')) {
            neways_handle_gallery_reorder();
        }
    }
    
    $gallery_images = get_option('neways_gallery_images', array());
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('Gallery Management', 'neways'); ?></h1>
        <p><?php echo esc_html__('Upload and manage unlimited photos for your gallery. Each image must be less than 2.5MB. Use the [neways_gallery] shortcode to display the gallery on your website.', 'neways'); ?></p>
        
        
        <!-- Upload Form -->
        <div class="card" style="max-width: 800px;">
            <h2><?php echo esc_html__('Upload Images', 'neways'); ?></h2>
            <form method="post" enctype="multipart/form-data" id="gallery-upload-form">
                <?php wp_nonce_field('neways_gallery_upload', 'neways_gallery_nonce'); ?>
                <input type="hidden" name="action" value="upload_images">
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gallery_images"><?php echo esc_html__('Select Images', 'neways'); ?></label>
                        </th>
                        <td>
                            <input type="file" id="gallery_images" name="gallery_images[]" multiple accept="image/*">
                            <p class="description">
                                <?php echo esc_html__('You can select multiple images at once. Each image must be less than 2.5MB.', 'neways'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo esc_attr__('Upload Images', 'neways'); ?>">
                </p>
            </form>
        </div>
        
        <!-- Current Gallery Images -->
        <div class="card" style="max-width: 1000px;">
            <h2><?php echo esc_html__('Current Gallery Images', 'neways'); ?> (<?php echo count($gallery_images); ?>)</h2>
            
            <?php if (empty($gallery_images)): ?>
                <p><?php echo esc_html__('No images uploaded yet.', 'neways'); ?></p>
            <?php else: ?>
                <div id="gallery-images-sortable" class="gallery-images-container">
                    <?php foreach ($gallery_images as $index => $image): ?>
                        <div class="gallery-image-item" data-image-id="<?php echo esc_attr($index); ?>">
                            <div class="gallery-image-preview">
                                <img src="<?php echo esc_url($image['url']); ?>" alt="<?php echo esc_attr($image['alt']); ?>" style="width: 150px; height: 150px; object-fit: cover;">
                            </div>
                            <div class="gallery-image-info">
                                <p><strong><?php echo esc_html__('Title:', 'neways'); ?></strong> <?php echo esc_html($image['title']); ?></p>
                                <p><strong><?php echo esc_html__('Alt Text:', 'neways'); ?></strong> <?php echo esc_html($image['alt']); ?></p>
                                <p><strong><?php echo esc_html__('Uploaded:', 'neways'); ?></strong> <?php echo esc_html($image['uploaded']); ?></p>
                            </div>
                            <div class="gallery-image-actions">
                                <form method="post" style="display: inline;">
                                    <?php wp_nonce_field('neways_gallery_remove', 'neways_gallery_nonce'); ?>
                                    <input type="hidden" name="action" value="remove_image">
                                    <input type="hidden" name="image_index" value="<?php echo esc_attr($index); ?>">
                                    <input type="submit" class="button button-secondary" value="<?php echo esc_attr__('Remove', 'neways'); ?>" onclick="return confirm('<?php echo esc_js(__('Are you sure you want to remove this image?', 'neways')); ?>');">
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <style>
                .gallery-images-container {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                    margin-top: 20px;
                }
                .gallery-image-item {
                    border: 1px solid #ddd;
                    padding: 15px;
                    border-radius: 5px;
                    background: #f9f9f9;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                }
                .gallery-image-preview {
                    margin-bottom: 15px;
                }
                .gallery-image-info {
                    margin-bottom: 15px;
                    font-size: 14px;
                }
                .gallery-image-info p {
                    margin: 5px 0;
                }
                .gallery-image-actions {
                    margin-top: auto;
                }
                </style>
                
                <script>
                jQuery(document).ready(function($) {
                    // Make images sortable
                    $("#gallery-images-sortable").sortable({
                        placeholder: "ui-state-highlight",
                        update: function(event, ui) {
                            var newOrder = [];
                            $("#gallery-images-sortable .gallery-image-item").each(function() {
                                newOrder.push($(this).data('image-id'));
                            });
                            
                            // Send AJAX request to update order
                            $.post(ajaxurl, {
                                action: 'neways_reorder_gallery_images',
                                nonce: '<?php echo wp_create_nonce('neways_gallery_reorder'); ?>',
                                order: newOrder
                            });
                        }
                    });
                    
                    // File size validation before upload
                    $('#gallery_images').on('change', function() {
                        var files = this.files;
                        var maxSize = 2.5 * 1024 * 1024; // 2.5MB in bytes
                        var validFiles = [];
                        var invalidFiles = [];
                        
                        for (var i = 0; i < files.length; i++) {
                            if (files[i].size > maxSize) {
                                invalidFiles.push(files[i].name + ' (' + (files[i].size / (1024 * 1024)).toFixed(2) + ' MB)');
                            } else {
                                validFiles.push(files[i]);
                            }
                        }
                        
                        if (invalidFiles.length > 0) {
                            alert('The following files are too large (maximum 2.5MB):\n' + invalidFiles.join('\n'));
                        }
                        
                        // Replace the file input with only valid files
                        if (invalidFiles.length > 0 && validFiles.length > 0) {
                            var dt = new DataTransfer();
                            for (var i = 0; i < validFiles.length; i++) {
                                dt.items.add(validFiles[i]);
                            }
                            this.files = dt.files;
                        } else if (invalidFiles.length > 0 && validFiles.length === 0) {
                            this.value = '';
                        }
                    });
                });
                </script>
            <?php endif; ?>
        </div>
        
        <!-- Shortcode Usage -->
        <div class="card" style="max-width: 800px;">
            <h2><?php echo esc_html__('How to Use', 'neways'); ?></h2>
            <p><?php echo esc_html__('To display the gallery on your website, use this shortcode:', 'neways'); ?></p>
            <code>[neways_gallery]</code>
            <p><?php echo esc_html__('You can also use these parameters:', 'neways'); ?></p>
            <ul>
                <li><code>[neways_gallery columns="2"]</code> - <?php echo esc_html__('Set number of columns (1-3)', 'neways'); ?></li>
                <li><code>[neways_gallery lightbox="true"]</code> - <?php echo esc_html__('Enable lightbox (true/false)', 'neways'); ?></li>
                <li><code>[neways_gallery size="medium"]</code> - <?php echo esc_html__('Image size (thumbnail, medium, large, full)', 'neways'); ?></li>
            </ul>
        </div>
    </div>
    <?php
}

/**
 * Handle gallery image uploads
 */
function neways_handle_gallery_upload() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    $gallery_images = get_option('neways_gallery_images', array());
    $uploaded_count = 0;
    $skipped_count = 0;
    $max_file_size = 2.5 * 1024 * 1024; // 2.5MB in bytes
    
    if (!empty($_FILES['gallery_images']['name'][0])) {
        $files = $_FILES['gallery_images'];
        
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                // Check file size
                if ($files['size'][$i] > $max_file_size) {
                    $skipped_count++;
                    add_action('admin_notices', function() use ($files, $i) {
                        $file_size_mb = round($files['size'][$i] / (1024 * 1024), 2);
                        echo '<div class="notice notice-warning"><p>' . sprintf(esc_html__('File "%s" (%s MB) is too large. Maximum file size is 2.5MB.', 'neways'), esc_html($files['name'][$i]), $file_size_mb) . '</p></div>';
                    });
                    continue;
                }
                
                $file = array(
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i]
                );
                
                $upload = wp_handle_upload($file, array('test_form' => false));
                
                if (!isset($upload['error'])) {
                    $gallery_images[] = array(
                        'url' => $upload['url'],
                        'title' => sanitize_text_field($file['name']),
                        'alt' => sanitize_text_field($file['name']),
                        'uploaded' => current_time('mysql')
                    );
                    $uploaded_count++;
                } else {
                    add_action('admin_notices', function() use ($upload, $files, $i) {
                        echo '<div class="notice notice-error"><p>' . sprintf(esc_html__('Upload Error for "%s": %s', 'neways'), esc_html($files['name'][$i]), esc_html($upload['error'])) . '</p></div>';
                    });
                }
            }
        }
        
        update_option('neways_gallery_images', $gallery_images);
        
        if ($uploaded_count > 0) {
            add_action('admin_notices', function() use ($uploaded_count) {
                echo '<div class="notice notice-success"><p>' . sprintf(esc_html__('%d image(s) uploaded successfully.', 'neways'), $uploaded_count) . '</p></div>';
            });
        }
        
        if ($skipped_count > 0) {
            add_action('admin_notices', function() use ($skipped_count) {
                echo '<div class="notice notice-warning"><p>' . sprintf(esc_html__('%d image(s) were skipped due to file size exceeding 2.5MB.', 'neways'), $skipped_count) . '</p></div>';
            });
        }
    }
}

/**
 * Handle gallery image removal
 */
function neways_handle_gallery_remove() {
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    $image_index = intval($_POST['image_index']);
    $gallery_images = get_option('neways_gallery_images', array());
    
    if (isset($gallery_images[$image_index])) {
        unset($gallery_images[$image_index]);
        $gallery_images = array_values($gallery_images); // Re-index array
        update_option('neways_gallery_images', $gallery_images);
        
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success"><p>' . esc_html__('Image removed successfully.', 'neways') . '</p></div>';
        });
    }
}

/**
 * Handle gallery image reordering via AJAX
 */
function neways_handle_gallery_reorder_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'neways_gallery_reorder')) {
        wp_die('Security check failed');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    $new_order = $_POST['order'];
    $gallery_images = get_option('neways_gallery_images', array());
    $reordered_images = array();
    
    foreach ($new_order as $index) {
        if (isset($gallery_images[$index])) {
            $reordered_images[] = $gallery_images[$index];
        }
    }
    
    update_option('neways_gallery_images', $reordered_images);
    wp_send_json_success();
}
add_action('wp_ajax_neways_reorder_gallery_images', 'neways_handle_gallery_reorder_ajax');
