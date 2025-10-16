<?php
/**
 * Direct Height Field for Awards
 * Simple approach that adds height control directly to ACF
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add height field directly to ACF using WordPress hooks
 */
function add_direct_height_field_to_awards() {
    global $post;
    
    // Only run in admin
    if (!is_admin()) {
        return;
    }
    
    // Only on Awards template pages
    if (!$post || get_page_template_slug($post->ID) !== 'templates/page-awards.php') {
        return;
    }
    
    // Add JavaScript to inject height field
    add_action('admin_footer', 'inject_height_field_script');
}
add_action('admin_init', 'add_direct_height_field_to_awards');

/**
 * Inject height field script
 */
function inject_height_field_script() {
    global $post, $pagenow;
    
    // Only on post edit pages
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }
    
    if (!$post || get_page_template_slug($post->ID) !== 'templates/page-awards.php') {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // Wait for ACF to load
        function waitForACF() {
            if (typeof acf !== 'undefined') {
                addHeightFields();
            } else {
                setTimeout(waitForACF, 500);
            }
        }
        
        function addHeightFields() {
            // Find all award image fields
            $('.acf-field[data-name="award_image"]').each(function() {
                var $imageField = $(this);
                var $row = $imageField.closest('.acf-row');
                
                // Check if height field already exists
                if ($row.find('.direct-height-field').length > 0) {
                    return;
                }
                
                // Create height field HTML
                var heightFieldHtml = `
                    <div class="acf-field direct-height-field" data-name="direct_height" data-type="number">
                        <div class="acf-label">
                            <label>Image Height (px) - Adjust Size on Website</label>
                        </div>
                        <div class="acf-input">
                            <div class="acf-input-wrap">
                                <input type="number" name="direct_height[]" value="230" min="100" max="500" step="10" class="direct-height-input" style="width: 100px; padding: 8px; border: 2px solid #016A7C; border-radius: 4px; text-align: center; font-weight: bold;">
                                <span style="margin-left: 10px; color: #016A7C; font-weight: bold;">px</span>
                                <div style="margin-top: 10px; padding: 10px; background: #f0f8ff; border-radius: 4px; font-size: 12px; color: #016A7C;">
                                    <strong>💡 Live Preview:</strong> Change the number above to adjust image size on your website
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Insert after image field
                $imageField.after(heightFieldHtml);
                
                // Add preview functionality
                addPreviewToRow($row);
            });
        }
        
        function addPreviewToRow($row) {
            var $imageField = $row.find('.acf-field[data-name="award_image"]');
            var $heightField = $row.find('.direct-height-field');
            var $heightInput = $heightField.find('.direct-height-input');
            
            // Create preview container
            var previewHtml = `
                <div class="height-preview-container" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border: 2px solid #016A7C; border-radius: 8px;">
                    <div style="font-weight: bold; color: #016A7C; margin-bottom: 10px;">📱 Website Preview:</div>
                    <div class="preview-image-wrapper" style="width: 200px; height: 230px; border: 2px solid #ddd; border-radius: 6px; overflow: hidden; background: #fff; display: flex; align-items: center; justify-content: center;">
                        <img class="preview-image" src="" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                        <div class="preview-placeholder" style="color: #999; text-align: center; padding: 20px;">
                            <div style="font-size: 24px; margin-bottom: 10px;">🖼️</div>
                            <div>Upload image to see preview</div>
                        </div>
                    </div>
                    <div style="margin-top: 10px; font-size: 12px; color: #666;">
                        Current height: <span class="current-height">230</span>px
                    </div>
                </div>
            `;
            
            $heightField.append(previewHtml);
            
            // Update preview when height changes
            $heightInput.on('input change', function() {
                var height = $(this).val();
                $row.find('.preview-image-wrapper').css('height', height + 'px');
                $row.find('.current-height').text(height);
            });
            
            // Update preview when image changes
            $imageField.find('img').on('load', function() {
                var imgSrc = $(this).attr('src');
                if (imgSrc) {
                    $row.find('.preview-image').attr('src', imgSrc).show();
                    $row.find('.preview-placeholder').hide();
                }
            });
            
            // Check for existing image
            var existingImg = $imageField.find('img').attr('src');
            if (existingImg) {
                $row.find('.preview-image').attr('src', existingImg).show();
                $row.find('.preview-placeholder').hide();
            }
        }
        
        // Initialize
        setTimeout(waitForACF, 1000);
        
        // Re-initialize when new rows are added
        $(document).on('click', '.acf-repeater .acf-button', function() {
            setTimeout(function() {
                if (typeof acf !== 'undefined') {
                    addHeightFields();
                }
            }, 1000);
        });
        
        // Save height values when form is submitted
        $('form#post').on('submit', function() {
            var heights = [];
            $('.direct-height-input').each(function() {
                heights.push($(this).val());
            });
            
            // Create hidden input to save heights
            $('form#post').append('<input type="hidden" name="awards_heights" value="' + JSON.stringify(heights) + '">');
        });
        
    });
    </script>
    <?php
}

/**
 * Save height values
 */
function save_direct_height_values($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['awards_heights'])) {
        $heights = json_decode(stripslashes($_POST['awards_heights']), true);
        if (is_array($heights)) {
            update_post_meta($post_id, 'awards_heights', $heights);
        }
    }
}
add_action('save_post', 'save_direct_height_values');
?>
