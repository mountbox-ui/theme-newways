<?php
/**
 * Manual Height Control - Simple Text Field Approach
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add manual height field using WordPress meta boxes
 */
function add_manual_height_meta_box() {
    global $post;
    
    // Show on all pages - JavaScript will handle hiding/showing based on shortcode
    if ($post && $post->post_type === 'page') {
        add_meta_box(
            'awards_height_control',
            '🎯 Awards Height Control - Adjust Image Sizes',
            'manual_height_meta_box_callback',
            'page',
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'add_manual_height_meta_box');

/**
 * Meta box callback
 */
function manual_height_meta_box_callback($post) {
    // Get saved heights
    $heights = get_post_meta($post->ID, 'manual_awards_heights', true);
    if (!is_array($heights)) {
        $heights = array();
    }
    
    ?>
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #016A7C;">
        <h3 style="margin-top: 0; color: #016A7C;">📏 Image Height Control</h3>
        <p style="color: #666; margin-bottom: 20px;">
            Set the height for each award image. These values will be used on the website.
            Default is 230px. Range: 100px - 500px
        </p>
        
        <div id="height-controls">
            <?php
            // Get awards data - try multiple methods
            $awards = get_field('awards', $post->ID);
            
            // If no awards from ACF, try individual fields
            if (empty($awards)) {
                $awards = array();
                for ($i = 1; $i <= 3; $i++) {
                    $award_field = get_field('award' . $i, $post->ID);
                    if (!empty($award_field)) {
                        $awards[] = $award_field;
                    }
                }
            }
            
            // Show success message
            echo '<div style="margin-bottom: 10px; padding: 10px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 4px; font-size: 12px; color: #155724;">';
            echo '<strong>✅ Success!</strong> Found ' . count($awards) . ' awards with images. You can now adjust their heights below.';
            echo '</div>';
            
            if (!empty($awards) && is_array($awards)) {
                foreach ($awards as $index => $award) {
                    $current_height = isset($heights[$index]) ? $heights[$index] : 230;
                    
                    // Handle direct image objects (your current setup)
                    $img_url = '';
                    $img_alt = '';
                    $award_title = 'Award ' . ($index + 1);
                    
                    if (is_array($award)) {
                        // Check if this is a direct image object
                        if (isset($award['url']) && isset($award['ID'])) {
                            // This is a direct image object
                            $img_url = $award['url'];
                            $img_alt = isset($award['alt']) ? $award['alt'] : (isset($award['title']) ? $award['title'] : $award_title);
                            $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($index + 1);
                        } 
                        // Check if this is an award with image subfield
                        elseif (isset($award['award_image']['url'])) {
                            $img_url = $award['award_image']['url'];
                            $img_alt = isset($award['award_image']['alt']) ? $award['award_image']['alt'] : $award_title;
                            $award_title = isset($award['award_title']) ? $award['award_title'] : 'Award ' . ($index + 1);
                        }
                        // Check other possible structures
                        elseif (isset($award['image']['url'])) {
                            $img_url = $award['image']['url'];
                            $img_alt = isset($award['image']['alt']) ? $award['image']['alt'] : $award_title;
                            $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($index + 1);
                        } elseif (isset($award['url'])) {
                            $img_url = $award['url'];
                            $img_alt = isset($award['alt']) ? $award['alt'] : $award_title;
                        }
                    } elseif (filter_var($award, FILTER_VALIDATE_URL)) {
                        $img_url = $award;
                        $img_alt = $award_title;
                    }
                    
                    ?>
                    <div style="margin-bottom: 15px; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 6px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="flex: 1;">
                                <strong><?php echo esc_html($award_title); ?></strong>
                                <?php if (!empty($img_url)): ?>
                                    <span style="color: #28a745; font-size: 12px;">✅ Image found</span>
                                <?php else: ?>
                                    <span style="color: #dc3545; font-size: 12px;">❌ No image</span>
                                <?php endif; ?>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <label>Height:</label>
                                <input type="number" 
                                       name="manual_height[<?php echo $index; ?>]" 
                                       value="<?php echo esc_attr($current_height); ?>" 
                                       min="100" 
                                       max="500" 
                                       step="10"
                                       style="width: 80px; padding: 8px; border: 2px solid #016A7C; border-radius: 4px; text-align: center; font-weight: bold;">
                                <span style="color: #016A7C; font-weight: bold;">px</span>
                            </div>
                        </div>
                        
                        <?php if (!empty($img_url)): ?>
                            <div style="margin-top: 10px;">
                                <div class="preview-container" style="display: inline-block; width: 200px; height: <?php echo $current_height; ?>px; border: 2px solid #016A7C; border-radius: 4px; overflow: hidden; background: #f8f9fa; transition: all 0.3s ease;">
                                    <img src="<?php echo esc_url($img_url); ?>" 
                                         alt="<?php echo esc_attr($img_alt); ?>" 
                                         class="preview-image"
                                         style="width: 100%; height: 100%; object-fit: contain; display: none;"
                                         onload="this.style.display='block'; this.parentNode.querySelector('.image-dimensions').style.display='block';"
                                         onerror="this.style.display='none';">
                                    <div class="image-dimensions" style="display: none; position: absolute; bottom: 5px; right: 5px; background: rgba(0,0,0,0.7); color: white; padding: 2px 6px; border-radius: 3px; font-size: 10px;"></div>
                                </div>
                                <div class="preview-text" style="margin-top: 5px; font-size: 12px; color: #666;">
                                    Preview at <?php echo $current_height; ?>px height (width auto-adjusts)
                                </div>
                            </div>
                        <?php else: ?>
                            <div style="margin-top: 10px; padding: 10px; background: #f8f9fa; border-radius: 4px; color: #666; font-size: 12px;">
                                No image found for this award. Please upload an image in the ACF fields below.
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php
                }
            } else {
                ?>
                <div style="padding: 20px; text-align: center; color: #666; background: white; border: 1px solid #ddd; border-radius: 6px;">
                    <div style="font-size: 24px; margin-bottom: 10px;">📝</div>
                    <div>No awards found. Please add awards using the ACF fields below first.</div>
                    <div style="margin-top: 10px; font-size: 12px; color: #999;">
                        Make sure to save your page after adding awards so they appear here.
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        
        <div style="margin-top: 20px; padding: 15px; background: #e8f5e8; border-radius: 6px; border-left: 4px solid #2d5016;">
            <strong style="color: #2d5016;">💡 Instructions:</strong>
            <ul style="margin: 10px 0; color: #2d5016;">
                <li>Set the height for each award image (100-500px)</li>
                <li>Default height is 230px</li>
                <li>Changes will be applied when you save this page</li>
                <li>The preview shows how the image will look on your website</li>
            </ul>
        </div>
    </div>
    
    <script>
    // Update preview when height changes
    jQuery(document).ready(function($) {
        function updatePreview($input) {
            var height = parseInt($input.val()) || 230;
            var $awardContainer = $input.closest('div[style*="margin-bottom: 15px"]');
            var $previewContainer = $awardContainer.find('.preview-container');
            var $previewImage = $previewContainer.find('.preview-image');
            var $previewText = $awardContainer.find('.preview-text');
            var $dimensions = $previewContainer.find('.image-dimensions');
            
            console.log('Updating height to:', height);
            
            if ($previewContainer.length > 0) {
                // Get original image dimensions
                var originalWidth = $previewImage.data('original-width');
                var originalHeight = $previewImage.data('original-height');
                
                if (originalWidth && originalHeight) {
                    // Calculate proportional width
                    var aspectRatio = originalWidth / originalHeight;
                    var newWidth = Math.round(height * aspectRatio);
                    
                    // Set both height and width
                    $previewContainer.css({
                        'height': height + 'px',
                        'width': newWidth + 'px'
                    });
                    
                    console.log('Updated to:', height + 'px height, ' + newWidth + 'px width');
                    
                    // Update dimensions display
                    if ($dimensions.length > 0) {
                        $dimensions.text(newWidth + '×' + height);
                    }
                } else {
                    // Fallback: just update height
                    $previewContainer.css('height', height + 'px');
                    console.log('Height updated to:', height + 'px (no original dimensions)');
                }
            }
            
            if ($previewText.length > 0) {
                $previewText.text('Preview at ' + height + 'px height (width auto-adjusts)');
            }
        }
        
        // Store original image dimensions when images load
        $('.preview-image').each(function() {
            var $img = $(this);
            if ($img[0].complete && $img[0].naturalWidth > 0) {
                $img.data('original-width', $img[0].naturalWidth);
                $img.data('original-height', $img[0].naturalHeight);
                console.log('Stored dimensions:', $img[0].naturalWidth, '×', $img[0].naturalHeight);
            } else {
                $img.on('load', function() {
                    $img.data('original-width', this.naturalWidth);
                    $img.data('original-height', this.naturalHeight);
                    console.log('Stored dimensions on load:', this.naturalWidth, '×', this.naturalHeight);
                });
            }
        });
        
        // Bind events to existing inputs
        $('input[name^="manual_height"]').each(function() {
            $(this).on('input change keyup', function() {
                updatePreview($(this));
            });
        });
        
        // Also bind to any dynamically added inputs
        $(document).on('input change keyup', 'input[name^="manual_height"]', function() {
            updatePreview($(this));
        });
        
        console.log('Height control script loaded. Found', $('input[name^="manual_height"]').length, 'height inputs');
    });
    </script>
    <?php
}

/**
 * Save manual height values
 */
function save_manual_height_values($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['manual_height']) && is_array($_POST['manual_height'])) {
        $heights = array_map('intval', $_POST['manual_height']);
        update_post_meta($post_id, 'manual_awards_heights', $heights);
    }
}
add_action('save_post', 'save_manual_height_values');
?>

