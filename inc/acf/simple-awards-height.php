<?php
/**
 * Simple Awards Height Control
 * A direct approach to add height control to existing ACF fields
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add height control via JavaScript injection
 */
function add_simple_awards_height_control() {
    global $post, $pagenow;
    
    // Only on post edit pages
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }
    
    // Only on Awards template pages
    if (!$post || get_page_template_slug($post->ID) !== 'templates/page-awards.php') {
        return;
    }
    
    // Add CSS and JavaScript
    add_action('admin_head', 'simple_awards_height_styles');
    add_action('admin_footer', 'simple_awards_height_scripts');
}
add_action('admin_enqueue_scripts', 'add_simple_awards_height_control');

/**
 * Add styles for height control
 */
function simple_awards_height_styles() {
    ?>
    <style>
    /* Height Control Styles */
    .awards-height-control {
        background: #f8f9fa;
        border: 2px solid #016A7C;
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
        position: relative;
    }
    
    .awards-height-control::before {
        content: "🎯 HEIGHT CONTROL";
        position: absolute;
        top: -10px;
        left: 15px;
        background: #016A7C;
        color: white;
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .height-control-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
    }
    
    .height-control-row label {
        font-weight: bold;
        color: #2d5016;
        min-width: 120px;
    }
    
    .height-control-row input {
        width: 80px;
        padding: 8px;
        border: 2px solid #016A7C;
        border-radius: 4px;
        text-align: center;
        font-weight: bold;
        font-size: 16px;
    }
    
    .height-preview {
        background: white;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px;
        text-align: center;
        font-size: 14px;
        color: #666;
    }
    
    .height-preview img {
        max-width: 200px;
        border: 2px solid #016A7C;
        border-radius: 4px;
        margin-bottom: 10px;
    }
    </style>
    <?php
}

/**
 * Add JavaScript for height control
 */
function simple_awards_height_scripts() {
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // Function to add height control to each award row
        function addHeightControlToRow($row) {
            // Check if height control already exists
            if ($row.find('.awards-height-control').length > 0) {
                return;
            }
            
            // Find the image field in this row
            var $imageField = $row.find('.acf-field[data-name="award_image"]');
            if ($imageField.length === 0) return;
            
            // Create height control HTML
            var heightControlHtml = `
                <div class="awards-height-control">
                    <div class="height-control-row">
                        <label>Image Height:</label>
                        <input type="number" class="award-height-input" value="230" min="100" max="500" step="10">
                        <span>px</span>
                    </div>
                    <div class="height-preview">
                        <div class="preview-image-container" style="height: 230px; overflow: hidden; border: 2px solid #016A7C; border-radius: 4px; display: flex; align-items: center; justify-content: center; background: #f8f9fa;">
                            <img src="" alt="Preview" class="preview-image" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                            <div class="preview-placeholder" style="color: #999; font-style: italic;">Upload image to see preview</div>
                        </div>
                        <div style="margin-top: 8px; font-size: 12px; color: #016A7C;">
                            <strong>Live Preview:</strong> This shows how your award will look on the website
                        </div>
                    </div>
                </div>
            `;
            
            // Insert height control after image field
            $imageField.after(heightControlHtml);
            
            // Set up event handlers
            var $heightInput = $row.find('.award-height-input');
            var $previewContainer = $row.find('.preview-image-container');
            var $previewImage = $row.find('.preview-image');
            var $previewPlaceholder = $row.find('.preview-placeholder');
            
            // Update preview when height changes
            $heightInput.on('input change', function() {
                var height = $(this).val();
                $previewContainer.css('height', height + 'px');
            });
            
            // Update preview when image changes
            $imageField.find('img').on('load', function() {
                var imgSrc = $(this).attr('src');
                if (imgSrc) {
                    $previewImage.attr('src', imgSrc).show();
                    $previewPlaceholder.hide();
                }
            });
            
            // Check for existing image
            var existingImg = $imageField.find('img').attr('src');
            if (existingImg) {
                $previewImage.attr('src', existingImg).show();
                $previewPlaceholder.hide();
            }
        }
        
        // Function to initialize all height controls
        function initHeightControls() {
            $('.acf-repeater .acf-row').each(function() {
                addHeightControlToRow($(this));
            });
        }
        
        // Initialize on page load
        setTimeout(initHeightControls, 1000);
        
        // Initialize when new rows are added
        $(document).on('click', '.acf-repeater .acf-button', function() {
            setTimeout(initHeightControls, 500);
        });
        
        // Initialize when ACF fields are updated
        acf.addAction('ready', function() {
            setTimeout(initHeightControls, 1000);
        });
        
        // Listen for image uploads
        acf.addAction('select_attachment', function(select, attachment, field) {
            if (field.get('name') === 'award_image') {
                var $row = field.$el.closest('.acf-row');
                setTimeout(function() {
                    var $heightControl = $row.find('.awards-height-control');
                    if ($heightControl.length > 0) {
                        var $previewImage = $heightControl.find('.preview-image');
                        var $previewPlaceholder = $heightControl.find('.preview-placeholder');
                        var imgSrc = attachment.url;
                        
                        $previewImage.attr('src', imgSrc).show();
                        $previewPlaceholder.hide();
                    }
                }, 100);
            }
        });
        
    });
    </script>
    <?php
}

/**
 * Save height values to post meta
 */
function save_awards_height_values($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Save height values from the height controls
    if (isset($_POST['awards_heights'])) {
        update_post_meta($post_id, 'awards_heights', $_POST['awards_heights']);
    }
}
add_action('save_post', 'save_awards_height_values');
?>
