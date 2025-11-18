<?php
/**
 * Awards Section Shortcode
 * 
 * Display awards with ACF integration and fixed height control
 * 
 * Usage:
 * [awards] - Display awards from current post
 * [awards post_id="123"] - Display awards from specific post
 * [awards show_title="yes"] - Show page title
 * [awards show_title="no"] - Hide page title (default)
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register Awards Shortcode
 */
function neways_awards_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(), // Default to current post
        'show_title' => 'no', // Show title or not
    ), $atts, 'awards');
    
    // Get the post ID
    $post_id = intval($atts['post_id']);
    
    // Start output buffering
    ob_start();
    
    // Get awards - try multiple methods
    $awards = get_field('awards', $post_id);
    
    // Make sure $awards is an array (ACF can return false/null)
    if (!is_array($awards)) {
        $awards = array();
    }
    
    // If no awards from repeater, try individual fields (fallback)
    if (empty($awards)) {
        for ($i = 1; $i <= 10; $i++) {
            $award_field = get_field('award' . $i, $post_id);
            if (!empty($award_field)) {
                $awards[] = $award_field;
            }
        }
    }
    
    // Get manual heights
    $manual_heights = get_post_meta($post_id, 'manual_awards_heights', true);
    if (!is_array($manual_heights)) {
        $manual_heights = array();
    }
    
    ?>
    <section class="bg-[#FAF8F4] py-8 sm:py-12 lg:py-16 awards-section-responsive">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <?php if ($atts['show_title'] === 'yes'): ?>
                <div class="text-center mb-8 sm:mb-10">
                    <h1 class="text-2xl sm:text-3xl font-bold text-[#273A29] m-0"><?php echo get_the_title($post_id); ?></h1>
                </div>
            <?php endif; ?>

            <h4 class="text-center text-[#273A29]/70 text-sm sm:text-base md:text-[20px] tracking-[0.2em] sm:tracking-[0.3em] uppercase mb-6 sm:mb-[24px] px-2 break-words">AWARDS/RECOGNITIONS/ PARTNERSHIPS</h4>

            <div class="flex flex-wrap items-center justify-center gap-x-4 gap-y-6 sm:gap-x-8 sm:gap-y-8 md:gap-x-14 md:gap-y-10">
                <?php
                if (!empty($awards) && is_array($awards)):
                    $award_index = 0;
                    foreach ($awards as $award):
                        // Handle direct image objects (your current setup)
                        $award_image = null;
                        $award_title = 'Award ' . ($award_index + 1);
                        $award_organization = '';
                        $award_year = '';
                        $award_category = '';
                        $award_description = '';
                        
                        if (is_array($award)) {
                            // Check if this is a direct image object
                            if (isset($award['url']) && isset($award['ID'])) {
                                // This is a direct image object
                                $award_image = $award;
                                $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($award_index + 1);
                            } 
                            // Check if this is an award with image subfield (repeater structure)
                            elseif (isset($award['award_image'])) {
                                // Handle different ACF return formats: array, ID, or URL
                                $award_image_raw = $award['award_image'];
                                
                                // If it's an image ID, get the full image array
                                if (is_numeric($award_image_raw)) {
                                    $award_image = acf_get_attachment($award_image_raw);
                                } 
                                // If it's already an array
                                elseif (is_array($award_image_raw)) {
                                    $award_image = $award_image_raw;
                                }
                                // If it's a URL string
                                elseif (filter_var($award_image_raw, FILTER_VALIDATE_URL)) {
                                    $award_image = $award_image_raw;
                                } else {
                                    $award_image = null;
                                }
                                
                                $award_title = isset($award['award_title']) ? $award['award_title'] : 'Award ' . ($award_index + 1);
                                $award_organization = isset($award['award_organization']) ? $award['award_organization'] : '';
                                $award_year = isset($award['award_year']) ? $award['award_year'] : '';
                                $award_category = isset($award['award_category']) ? $award['award_category'] : '';
                                $award_description = isset($award['award_description']) ? $award['award_description'] : '';
                            }
                            // Check other possible structures
                            elseif (isset($award['image'])) {
                                $award_image = $award['image'];
                                $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($award_index + 1);
                            }
                        }
                        
                        // Skip if no image found - but log it for debugging
                        if (empty($award_image)) {
                            // Debug: uncomment to see which awards are being skipped
                            // error_log('Award ' . ($award_index + 1) . ' skipped - no image found. Award data: ' . print_r($award, true));
                            $award_index++;
                            continue;
                        }
                        
                        // Get height from manual height control
                        $award_image_height = 230; // Default
                        if (isset($manual_heights[$award_index])) {
                            $award_image_height = intval($manual_heights[$award_index]);
                        }
                        
                        // Get image URL and dimensions
                        $img_url = '';
                        $img_alt = '';
                        $img_width = 0;
                        $img_height = 0;
                        
                        if (is_array($award_image)) {
                            $img_url = isset($award_image['url']) ? $award_image['url'] : '';
                            $img_alt = isset($award_image['alt']) ? $award_image['alt'] : $award_title;
                            $img_width = isset($award_image['width']) ? intval($award_image['width']) : 0;
                            $img_height = isset($award_image['height']) ? intval($award_image['height']) : 0;
                        } elseif (filter_var($award_image, FILTER_VALIDATE_URL)) {
                            $img_url = $award_image;
                            $img_alt = $award_title;
                        }
                        
                        // Calculate proportional width based on the height you set
                        $award_image_width = 200; // Default width
                        if ($img_width > 0 && $img_height > 0) {
                            $aspect_ratio = $img_width / $img_height;
                            $award_image_width = round($award_image_height * $aspect_ratio);
                        }
                        
                        // Display image - responsive height calculation
                        // On mobile (< 412px), reduce height to 60% of desktop size
                        $mobile_height = round($award_image_height * 0.6);
                        if ($mobile_height < 40) {
                            $mobile_height = 40; // Minimum height
                        }
                        if (!empty($img_url)): ?>
                            <img src="<?php echo esc_url($img_url); ?>" 
                                 alt="<?php echo esc_attr($img_alt); ?>" 
                                 class="award-image-responsive opacity-70 hover:opacity-100 grayscale hover:grayscale-0 transition w-auto max-w-full object-contain" 
                                 style="--mobile-h: <?php echo esc_attr($mobile_height); ?>px; --desktop-h: <?php echo esc_attr($award_image_height); ?>px; height: var(--mobile-h); width: auto;">
                        <?php endif;
                        
                        $award_index++;
                    endforeach;
                else:
                    ?>
                    <div class="no-awards">
                        <div class="no-awards-icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                        </div>
                        <h3>No Awards Yet</h3>
                        <p>Please add awards using the ACF fields.</p>
                    </div>
                    <?php
                endif;
                ?>
            </div>

        </div>
    </section>
    
    <style>
    /* Responsive awards images - mobile under 412px uses smaller height */
    @media (max-width: 411px) {
        .awards-section-responsive .award-image-responsive {
            height: var(--mobile-h) !important;
        }
    }
    
    @media (min-width: 412px) {
        .awards-section-responsive .award-image-responsive {
            height: var(--desktop-h) !important;
        }
    }
    </style>
    <?php
    
    // Return the buffered content
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('awards', 'neways_awards_shortcode');

/**
 * Optional: Add TinyMCE button for inserting awards shortcode (Classic Editor)
 * Uncomment the functions below if you want to add a button to insert the shortcode
 */
/*
function add_awards_shortcode_button() {
    if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
        return;
    }
    
    if (get_user_option('rich_editing') !== 'true') {
        return;
    }
    
    add_filter('mce_external_plugins', 'add_awards_tinymce_plugin');
    add_filter('mce_buttons', 'register_awards_button');
}
add_action('admin_head', 'add_awards_shortcode_button');

function add_awards_tinymce_plugin($plugin_array) {
    $plugin_array['awards_button'] = get_template_directory_uri() . '/assets/js/awards-tinymce.js';
    return $plugin_array;
}

function register_awards_button($buttons) {
    array_push($buttons, 'awards_button');
    return $buttons;
}
*/

