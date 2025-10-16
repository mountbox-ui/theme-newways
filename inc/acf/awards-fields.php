<?php
/**
 * ACF Fields for Awards Template
 * 
 * This file contains the ACF field group configuration for the Awards page template.
 * You can import this into ACF or use it as a reference to create the fields manually.
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register ACF fields for Awards page template
 */
function register_awards_acf_fields() {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group(array(
        'key' => 'group_awards_template',
        'title' => 'Awards Template Fields',
        'fields' => array(
            // Awards Repeater Field
            array(
                'key' => 'field_awards_repeater',
                'label' => 'Awards',
                'name' => 'awards',
                'type' => 'repeater',
                'instructions' => 'Add awards and recognitions received by your organization',
                'required' => 0,
                'conditional_logic' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'collapsed' => 'field_award_title',
                'min' => 0,
                'max' => 0,
                'layout' => 'block',
                'button_label' => 'Add Award',
                'sub_fields' => array(
                    // Award Image
                    array(
                        'key' => 'field_award_image',
                        'label' => 'Award Image/Logo',
                        'name' => 'award_image',
                        'type' => 'image',
                        'instructions' => 'Upload an image or logo of the award (recommended size: 400x300px)',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'return_format' => 'array',
                        'preview_size' => 'medium',
                        'library' => 'all',
                        'min_width' => '',
                        'min_height' => '',
                        'min_size' => '',
                        'max_width' => '',
                        'max_height' => '',
                        'max_size' => '',
                        'mime_types' => 'jpg,jpeg,png,svg',
                    ),
                    
                    // Award Image Height
                    array(
                        'key' => 'field_award_image_height',
                        'label' => 'Image Height (px) - Adjust to change size on website',
                        'name' => 'award_image_height',
                        'type' => 'number',
                        'instructions' => 'Set the image height (100-500px). Default is 200px. See preview below.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => 'award-height-control',
                            'id' => '',
                        ),
                        'default_value' => 200,
                        'placeholder' => '200',
                        'min' => 100,
                        'max' => 500,
                        'step' => 10,
                        'prepend' => '',
                        'append' => 'px',
                    ),
                    
                    // Live Preview Message
                    array(
                        'key' => 'field_award_preview',
                        'label' => 'Website Preview - How it will look on the website',
                        'name' => 'award_preview',
                        'type' => 'message',
                        'instructions' => 'This preview shows exactly how your award will appear on the website. It updates automatically when you change the image or height.',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => 'award-preview-container',
                            'id' => '',
                        ),
                        'message' => '<div class="award-preview-wrapper">
                            <div class="award-preview-notice">📱 <strong>Live Preview:</strong> This shows exactly how your award will look on the website</div>
                            <div class="award-preview-box">
                                <div class="award-preview-card">
                                    <div class="award-preview-image-container" style="height: 230px;">
                                        <img src="" alt="Preview" class="award-preview-image" style="display:none;">
                                        <div class="award-preview-placeholder">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="width: 40px; height: 40px; margin-bottom: 10px;">
                                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                            </svg>
                                            <div>Upload an image to see preview</div>
                                        </div>
                                    </div>
                                    <div class="award-preview-content">
                                        <h3 class="award-preview-title">Your award title will appear here</h3>
                                        <p class="award-preview-org">Organization name</p>
                                        <div class="award-preview-meta">
                                            <span class="award-preview-year">2024</span>
                                            <span class="award-preview-category">Excellence</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="award-preview-info">
                                    <small>💡 Tip: Adjust the "Image Height" field above to change the image size</small>
                                </div>
                            </div>
                        </div>',
                        'new_lines' => '',
                        'esc_html' => 0,
                    ),
                    
                    // Award Title
                    array(
                        'key' => 'field_award_title',
                        'label' => 'Award Title',
                        'name' => 'award_title',
                        'type' => 'text',
                        'instructions' => 'Name of the award or recognition',
                        'required' => 1,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => 'Excellence in Care Award',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    
                    // Award Organization
                    array(
                        'key' => 'field_award_organization',
                        'label' => 'Awarding Organization',
                        'name' => 'award_organization',
                        'type' => 'text',
                        'instructions' => 'Name of the organization that gave the award',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '50',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => 'Care Quality Commission',
                        'prepend' => '',
                        'append' => '',
                        'maxlength' => '',
                    ),
                    
                    // Award Year
                    array(
                        'key' => 'field_award_year',
                        'label' => 'Award Year',
                        'name' => 'award_year',
                        'type' => 'number',
                        'instructions' => 'Year the award was received',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => '2024',
                        'min' => 1900,
                        'max' => 2100,
                        'step' => 1,
                    ),
                    
                    // Award Category
                    array(
                        'key' => 'field_award_category',
                        'label' => 'Award Category',
                        'name' => 'award_category',
                        'type' => 'select',
                        'instructions' => 'Category or type of award',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '25',
                            'class' => '',
                            'id' => '',
                        ),
                        'choices' => array(
                            'excellence' => 'Excellence',
                            'quality' => 'Quality',
                            'innovation' => 'Innovation',
                            'safety' => 'Safety',
                            'customer_service' => 'Customer Service',
                            'leadership' => 'Leadership',
                            'sustainability' => 'Sustainability',
                            'community' => 'Community',
                            'other' => 'Other',
                        ),
                        'default_value' => 'excellence',
                        'allow_null' => 1,
                        'multiple' => 0,
                        'ui' => 0,
                        'return_format' => 'value',
                        'ajax' => 0,
                        'placeholder' => '',
                    ),
                    
                    // Award Description
                    array(
                        'key' => 'field_award_description',
                        'label' => 'Award Description',
                        'name' => 'award_description',
                        'type' => 'textarea',
                        'instructions' => 'Brief description of the award or what it recognizes',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'default_value' => '',
                        'placeholder' => 'Recognized for outstanding quality of care and exceptional patient outcomes...',
                        'maxlength' => 200,
                        'rows' => 3,
                        'new_lines' => 'br',
                    ),
                ),
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'templates/page-awards.php',
                ),
            ),
        ),
        'menu_order' => 0,
        'position' => 'normal',
        'style' => 'default',
        'label_placement' => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen' => '',
        'active' => true,
        'description' => 'Fields for the Awards page template',
    ));
}

// Register the fields when ACF is available
add_action('acf/init', 'register_awards_acf_fields');

/**
 * Register awards fields for all pages (JavaScript will show/hide based on shortcode)
 */
function add_awards_fields_to_shortcode_pages() {
    global $post;
    
    // Only run on page edit screens
    if (!$post || $post->post_type !== 'page') {
        return;
    }
    
    // Register fields for all pages - JavaScript will handle visibility
    if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_awards_shortcode',
                'title' => 'Awards Template Fields (Shortcode Page)',
                'fields' => array(
                    // Awards Repeater Field
                    array(
                        'key' => 'field_awards_repeater_shortcode',
                        'label' => 'Awards',
                        'name' => 'awards',
                        'type' => 'repeater',
                        'instructions' => 'Add awards and recognitions received by your organization',
                        'required' => 0,
                        'conditional_logic' => 0,
                        'wrapper' => array(
                            'width' => '',
                            'class' => '',
                            'id' => '',
                        ),
                        'collapsed' => 'field_award_title_shortcode',
                        'min' => 0,
                        'max' => 0,
                        'layout' => 'block',
                        'button_label' => 'Add Award',
                        'sub_fields' => array(
                            // Award Image
                            array(
                                'key' => 'field_award_image_shortcode',
                                'label' => 'Award Image/Logo',
                                'name' => 'award_image',
                                'type' => 'image',
                                'instructions' => 'Upload an image or logo of the award (recommended size: 400x300px)',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '50',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'return_format' => 'array',
                                'preview_size' => 'medium',
                                'library' => 'all',
                                'min_width' => '',
                                'min_height' => '',
                                'min_size' => '',
                                'max_width' => '',
                                'max_height' => '',
                                'max_size' => '',
                                'mime_types' => 'jpg,jpeg,png,svg',
                            ),
                            
                            // Award Image Height
                            array(
                                'key' => 'field_award_image_height_shortcode',
                                'label' => 'Image Height (px) - Adjust to change size on website',
                                'name' => 'award_image_height',
                                'type' => 'number',
                                'instructions' => 'Set the image height (100-500px). Default is 200px. See preview below.',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '50',
                                    'class' => 'award-height-control',
                                    'id' => '',
                                ),
                                'default_value' => 200,
                                'placeholder' => '200',
                                'min' => 100,
                                'max' => 500,
                                'step' => 10,
                                'prepend' => '',
                                'append' => 'px',
                            ),
                            
                            // Live Preview Message
                            array(
                                'key' => 'field_award_preview_shortcode',
                                'label' => 'Website Preview - How it will look on the website',
                                'name' => 'award_preview',
                                'type' => 'message',
                                'instructions' => 'This preview shows exactly how your award will appear on the website. It updates automatically when you change the image or height.',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => 'award-preview-container',
                                    'id' => '',
                                ),
                                'message' => '<div class="award-preview-wrapper">
                                    <div class="award-preview-notice">📱 <strong>Live Preview:</strong> This shows exactly how your award will look on the website</div>
                                    <div class="award-preview-box">
                                        <div class="award-preview-card">
                                            <div class="award-preview-image-container" style="height: 230px;">
                                                <img src="" alt="Preview" class="award-preview-image" style="display:none;">
                                                <div class="award-preview-placeholder">
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" style="width: 40px; height: 40px; margin-bottom: 10px;">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                    <div>Upload an image to see preview</div>
                                                </div>
                                            </div>
                                            <div class="award-preview-content">
                                                <h3 class="award-preview-title">Your award title will appear here</h3>
                                                <p class="award-preview-org">Organization name</p>
                                                <div class="award-preview-meta">
                                                    <span class="award-preview-year">2024</span>
                                                    <span class="award-preview-category">Excellence</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="award-preview-info">
                                            <small>💡 Tip: Adjust the "Image Height" field above to change the image size</small>
                                        </div>
                                    </div>
                                </div>',
                                'new_lines' => '',
                                'esc_html' => 0,
                            ),
                            
                            // Award Title
                            array(
                                'key' => 'field_award_title_shortcode',
                                'label' => 'Award Title',
                                'name' => 'award_title',
                                'type' => 'text',
                                'instructions' => 'Name of the award or recognition',
                                'required' => 1,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '50',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => 'Excellence in Care Award',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            
                            // Award Organization
                            array(
                                'key' => 'field_award_organization_shortcode',
                                'label' => 'Awarding Organization',
                                'name' => 'award_organization',
                                'type' => 'text',
                                'instructions' => 'Name of the organization that gave the award',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '50',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => 'Care Quality Commission',
                                'prepend' => '',
                                'append' => '',
                                'maxlength' => '',
                            ),
                            
                            // Award Year
                            array(
                                'key' => 'field_award_year_shortcode',
                                'label' => 'Award Year',
                                'name' => 'award_year',
                                'type' => 'number',
                                'instructions' => 'Year the award was received',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '25',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => '2024',
                                'min' => 1900,
                                'max' => 2100,
                                'step' => 1,
                            ),
                            
                            // Award Category
                            array(
                                'key' => 'field_award_category_shortcode',
                                'label' => 'Award Category',
                                'name' => 'award_category',
                                'type' => 'select',
                                'instructions' => 'Category or type of award',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '25',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'choices' => array(
                                    'excellence' => 'Excellence',
                                    'quality' => 'Quality',
                                    'innovation' => 'Innovation',
                                    'safety' => 'Safety',
                                    'customer_service' => 'Customer Service',
                                    'leadership' => 'Leadership',
                                    'sustainability' => 'Sustainability',
                                    'community' => 'Community',
                                    'other' => 'Other',
                                ),
                                'default_value' => 'excellence',
                                'allow_null' => 1,
                                'multiple' => 0,
                                'ui' => 0,
                                'return_format' => 'value',
                                'ajax' => 0,
                                'placeholder' => '',
                            ),
                            
                            // Award Description
                            array(
                                'key' => 'field_award_description_shortcode',
                                'label' => 'Award Description',
                                'name' => 'award_description',
                                'type' => 'textarea',
                                'instructions' => 'Brief description of the award or what it recognizes',
                                'required' => 0,
                                'conditional_logic' => 0,
                                'wrapper' => array(
                                    'width' => '',
                                    'class' => '',
                                    'id' => '',
                                ),
                                'default_value' => '',
                                'placeholder' => 'Recognized for outstanding quality of care and exceptional patient outcomes...',
                                'maxlength' => 200,
                                'rows' => 3,
                                'new_lines' => 'br',
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'page',
                        ),
                    ),
                ),
                'menu_order' => 0,
                'position' => 'normal',
                'style' => 'default',
                'label_placement' => 'top',
                'instruction_placement' => 'label',
                'hide_on_screen' => '',
                'active' => true,
                'description' => 'Awards fields for pages using the [awards] shortcode',
            ));
    }
}
add_action('acf/init', 'add_awards_fields_to_shortcode_pages', 20);

/**
 * Force refresh ACF fields cache
 */
function refresh_awards_acf_cache() {
    if (function_exists('acf_get_field_group')) {
        // Delete the cached field group to force refresh
        wp_cache_delete('group_awards_template', 'acf');
        delete_transient('acf_field_groups');
        
        // Force ACF to reload
        if (function_exists('acf_reset_local')) {
            acf_reset_local();
        }
    }
}
add_action('admin_init', 'refresh_awards_acf_cache');

/**
 * Add admin notice with manual instructions
 */
function awards_field_admin_notice() {
    global $post, $pagenow;
    
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return;
    }
    
    if (!$post || $post->post_type !== 'page') {
        return;
    }
    
    // Only show notice on pages that contain the awards shortcode
    if (!has_shortcode($post->post_content, 'awards') && get_page_template_slug($post->ID) !== 'templates/page-awards.php') {
        return;
    }
    
    // Check if the field exists
    if (!function_exists('acf_get_field_group')) {
        return;
    }
    
    $field_group = acf_get_field_group('group_awards_template');
    if (!$field_group) {
        ?>
        <div class="notice notice-warning">
            <p><strong>Awards Height Control:</strong> If you don't see the "Image Height" field, please go to <a href="<?php echo admin_url('edit.php?post_type=acf-field-group'); ?>">Custom Fields → Awards Template Fields</a> and refresh the page.</p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'awards_field_admin_notice');

/**
 * Check if we're on a page edit screen with awards shortcode
 */
function is_awards_page_edit_screen() {
    global $post, $pagenow;
    
    if (!in_array($pagenow, array('post.php', 'post-new.php'))) {
        return false;
    }
    
    if (!$post) {
        return false;
    }
    
    // Show only on pages that contain the awards shortcode or use the awards template
    return ($post->post_type === 'page' && 
            (has_shortcode($post->post_content, 'awards') || get_page_template_slug($post->ID) === 'templates/page-awards.php'));
}

/**
 * Admin styles for Awards preview
 */
function awards_admin_preview_styles() {
    if (!is_awards_page_edit_screen()) {
        return;
    }
    ?>
    <style>
        /* Award Preview Styles */
        .award-preview-wrapper {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #016A7C;
        }
        
        .award-preview-notice {
            background: #016A7C;
            color: white;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .award-preview-box {
            background: #ffffff;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 20px;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .award-preview-card {
            background: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: auto;
            display: flex;
            flex-direction: column;
        }
        
        .award-preview-image-container {
            overflow: hidden;
            position: relative;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: height 0.3s ease;
            height: 230px;
        }
        
        .award-preview-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: all 0.3s ease;
        }
        
        .award-preview-placeholder {
            color: #ccc;
            text-align: center;
            padding: 20px;
            font-style: italic;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        
        .award-preview-content {
            padding: 24px;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .award-preview-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
            line-height: 1.3;
        }
        
        .award-preview-org {
            font-size: 1rem;
            color: #016A7C;
            font-weight: 500;
            margin: 0 0 12px 0;
        }
        
        .award-preview-meta {
            margin-bottom: 12px;
        }
        
        .award-preview-year,
        .award-preview-category {
            display: inline-block;
            background: #f0f8ff;
            color: #016A7C;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .award-preview-category {
            background: #e8f5e8;
            color: #2d5016;
        }
        
        .award-preview-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
        }
        
        .award-preview-info small {
            color: #666;
            font-style: italic;
        }
        
        /* Height Control Styles */
        .award-height-control input[type="number"] {
            width: 100px !important;
            font-size: 16px !important;
            font-weight: 600 !important;
            text-align: center !important;
        }
        
        .award-height-control .acf-label {
            font-weight: 700 !important;
            color: #016A7C !important;
        }
        
        .award-height-control .acf-input {
            position: relative;
        }
        
        /* Current Height Display */
        .current-height-display {
            display: inline-block;
            background: #016A7C;
            color: white;
            padding: 6px 14px;
            border-radius: 4px;
            font-weight: 600;
            margin-left: 10px;
            font-size: 13px;
        }
        
        /* Make the preview container more visible */
        .award-preview-container .acf-label {
            font-weight: 700 !important;
            color: #2d5016 !important;
            font-size: 15px !important;
        }
    </style>
    <?php
}

/**
 * Admin JavaScript for Awards preview and shortcode detection
 */
function awards_admin_preview_scripts() {
    global $post;
    
    // Only run on page edit screens
    if (!$post || $post->post_type !== 'page') {
        return;
    }
    ?>
    <script type="text/javascript">
    jQuery(document).ready(function($) {
        
        // Function to check if awards shortcode is present in content
        function checkForAwardsShortcode() {
            var content = '';
            
            // Try to get content from the editor
            if (typeof wp !== 'undefined' && wp.data && wp.data.select('core/editor')) {
                content = wp.data.select('core/editor').getEditedPostContent();
            } else if ($('#content').length) {
                content = $('#content').val() || '';
            } else if ($('.wp-editor-area').length) {
                content = $('.wp-editor-area').val() || '';
            }
            
            var hasShortcode = content.indexOf('[awards') !== -1;
            var $awardsFieldGroup = $('.acf-field-group[data-key="group_awards_shortcode"]');
            var $heightMetaBox = $('#awards_height_control');
            
            // Show/hide awards fields based on shortcode presence
            if (hasShortcode) {
                $awardsFieldGroup.show();
                $heightMetaBox.show();
                console.log('Awards shortcode detected - showing fields');
            } else {
                $awardsFieldGroup.hide();
                $heightMetaBox.hide();
                console.log('No awards shortcode - hiding fields');
            }
        }
        
        // Check on page load
        setTimeout(checkForAwardsShortcode, 1000);
        
        // Check when content changes (Classic Editor)
        $(document).on('input keyup paste change', '#content, .wp-editor-area', function() {
            setTimeout(checkForAwardsShortcode, 300);
        });
        
        // Check when Gutenberg editor content changes
        if (typeof wp !== 'undefined' && wp.data) {
            wp.data.subscribe(function() {
                setTimeout(checkForAwardsShortcode, 200);
            });
        }
        
        // Additional check for TinyMCE editor
        if (typeof tinyMCE !== 'undefined') {
            $(document).on('tinymce-editor-init', function() {
                setTimeout(checkForAwardsShortcode, 500);
            });
        }
        
        // Function to update preview for a single repeater row
        function updateAwardPreview($row) {
            // Get the fields within this row
            var $imageField = $row.find('.acf-field[data-name="award_image"]');
            var $heightField = $row.find('.acf-field[data-name="award_image_height"]');
            var $titleField = $row.find('.acf-field[data-name="award_title"]');
            var $orgField = $row.find('.acf-field[data-name="award_organization"]');
            var $yearField = $row.find('.acf-field[data-name="award_year"]');
            var $categoryField = $row.find('.acf-field[data-name="award_category"]');
            var $previewContainer = $row.find('.award-preview-container');
            
            if (!$previewContainer.length) return;
            
            // Get values
            var imageUrl = $imageField.find('.acf-image-uploader img').attr('src') || '';
            var height = $heightField.find('input[type="number"]').val() || 230;
            var title = $titleField.find('input[type="text"]').val() || 'Your award title will appear here';
            var organization = $orgField.find('input[type="text"]').val() || 'Organization name';
            var year = $yearField.find('input[type="number"]').val() || '2024';
            var category = $categoryField.find('select option:selected').text() || 'Excellence';
            
            // Update preview
            var $previewImage = $previewContainer.find('.award-preview-image');
            var $previewPlaceholder = $previewContainer.find('.award-preview-placeholder');
            var $previewImageContainer = $previewContainer.find('.award-preview-image-container');
            var $previewTitle = $previewContainer.find('.award-preview-title');
            var $previewOrg = $previewContainer.find('.award-preview-org');
            var $previewYear = $previewContainer.find('.award-preview-year');
            var $previewCategory = $previewContainer.find('.award-preview-category');
            
            // Update image
            if (imageUrl) {
                $previewImage.attr('src', imageUrl).show();
                $previewPlaceholder.hide();
            } else {
                $previewImage.hide();
                $previewPlaceholder.show();
            }
            
            // Update height
            $previewImageContainer.css('height', height + 'px');
            
            // Update content
            $previewTitle.text(title);
            $previewOrg.text(organization);
            $previewYear.text(year);
            $previewCategory.text(category);
            
            // Add current height display next to number input
            var $heightInput = $heightField.find('input[type="number"]');
            var $heightDisplay = $heightField.find('.current-height-display');
            
            if ($heightDisplay.length === 0 && $heightInput.length > 0) {
                $heightInput.after('<span class="current-height-display">Preview below</span>');
            }
        }
        
        // Function to initialize all award previews
        function initAwardPreviews() {
            $('.acf-repeater .acf-row').each(function() {
                var $row = $(this);
                updateAwardPreview($row);
            });
        }
        
        // Initialize on page load
        setTimeout(initAwardPreviews, 1000);
        
        // Listen for image changes
        $(document).on('change', '.acf-field[data-name="award_image"] input', function() {
            var $row = $(this).closest('.acf-row');
            setTimeout(function() {
                updateAwardPreview($row);
            }, 500);
        });
        
        // Listen for image upload completion
        acf.addAction('select_attachment', function(select, attachment, field) {
            if (field.get('name') === 'award_image') {
                var $row = field.$el.closest('.acf-row');
                setTimeout(function() {
                    updateAwardPreview($row);
                }, 500);
            }
        });
        
        // Listen for image removal
        acf.addAction('remove_attachment', function(field) {
            if (field.get('name') === 'award_image') {
                var $row = field.$el.closest('.acf-row');
                setTimeout(function() {
                    updateAwardPreview($row);
                }, 100);
            }
        });
        
        // Listen for height input changes
        $(document).on('input change keyup', '.acf-field[data-name="award_image_height"] input[type="number"]', function() {
            var $row = $(this).closest('.acf-row');
            updateAwardPreview($row);
        });
        
        // Listen for title changes
        $(document).on('input', '.acf-field[data-name="award_title"] input[type="text"]', function() {
            var $row = $(this).closest('.acf-row');
            updateAwardPreview($row);
        });
        
        // Listen for organization changes
        $(document).on('input', '.acf-field[data-name="award_organization"] input[type="text"]', function() {
            var $row = $(this).closest('.acf-row');
            updateAwardPreview($row);
        });
        
        // Listen for year changes
        $(document).on('input change', '.acf-field[data-name="award_year"] input[type="number"]', function() {
            var $row = $(this).closest('.acf-row');
            updateAwardPreview($row);
        });
        
        // Listen for category changes
        $(document).on('change', '.acf-field[data-name="award_category"] select', function() {
            var $row = $(this).closest('.acf-row');
            updateAwardPreview($row);
        });
        
        // Listen for new rows being added
        acf.addAction('append', function($el) {
            if ($el.hasClass('acf-row')) {
                setTimeout(function() {
                    updateAwardPreview($el);
                }, 100);
            }
        });
        
        // Re-initialize when repeater changes
        $(document).on('click', '.acf-repeater .acf-button', function() {
            setTimeout(initAwardPreviews, 500);
        });
        
    });
    </script>
    <?php
}

// Hook the styles and scripts into admin
add_action('admin_head', 'awards_admin_preview_styles');
add_action('admin_footer', 'awards_admin_preview_scripts');

/**
 * Create sample awards data for demonstration
 * This function can be called to populate sample data
 */
function create_sample_awards_data($post_id) {
    $sample_awards = array(
        array(
            'award_image' => '',
            'award_title' => 'Excellence in Care Award',
            'award_organization' => 'Care Quality Commission',
            'award_year' => 2024,
            'award_category' => 'excellence',
            'award_description' => 'Recognized for outstanding quality of care and exceptional patient outcomes.',
        ),
        array(
            'award_image' => '',
            'award_title' => 'Innovation in Healthcare',
            'award_organization' => 'Healthcare Innovation Awards',
            'award_year' => 2023,
            'award_category' => 'innovation',
            'award_description' => 'Awarded for implementing cutting-edge technology and care methodologies.',
        ),
        array(
            'award_image' => '',
            'award_title' => 'Best Customer Service',
            'award_organization' => 'Local Business Awards',
            'award_year' => 2023,
            'award_category' => 'customer_service',
            'award_description' => 'Recognized for exceptional customer service and patient satisfaction.',
        ),
        array(
            'award_image' => '',
            'award_title' => 'Safety Excellence',
            'award_organization' => 'Health & Safety Executive',
            'award_year' => 2022,
            'award_category' => 'safety',
            'award_description' => 'Outstanding commitment to health and safety standards.',
        ),
    );
    
    // Only add sample data if no awards exist
    if (!get_field('awards', $post_id)) {
        update_field('awards', $sample_awards, $post_id);
    }
}

/**
 * Add sample data when awards page is created (optional)
 * Uncomment the following lines if you want to automatically add sample data
 */
/*
add_action('acf/save_post', function($post_id) {
    if (get_page_template_slug($post_id) === 'templates/page-awards.php') {
        create_sample_awards_data($post_id);
    }
}, 20);
*/
?>
