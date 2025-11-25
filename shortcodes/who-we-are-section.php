<?php
/**
 * Who We Are Section Shortcode
 * Creates a "Who We Are" section with title, description, and optional content blocks.
 * 
 * Usage: [who_we_are_section]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_who_we_are_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Who We Are',
        'subtitle' => '',
        'description' => 'Neways Healthcare is more than a care provider—we are a community of dedicated professionals united by a common purpose: to make a positive difference in people lives every single day.',
        'image' => '',
        'image_position' => 'bottom', // left, right, top, bottom
        'background' => 'white', // white, gray
        'button_text' => '',
        'button_url' => '',
        'class' => ''
    ), $atts);
    
    $bg_class = $atts['background'] === 'gray' ? 'bg-gray-50' : 'bg-white';
    
    // Set default image from assets if not provided
    $default_image = get_template_directory_uri() . '/assets/images/who-we-are.png';
    $image_url = !empty($atts['image']) ? esc_url($atts['image']) : $default_image;
    
    ob_start();
    ?>
    <style>
        .who-we-are-section .who-we-are-button {
            border-radius: 47px;
            background: #381F75;
            box-shadow: 0 67px 80px 0 rgba(55, 52, 169, 0.07), 0 43.426px 46.852px 0 rgba(55, 52, 169, 0.05), 0 25.807px 25.481px 0 rgba(55, 52, 169, 0.04), 0 13.4px 13px 0 rgba(55, 52, 169, 0.04), 0 5.459px 6.519px 0 rgba(55, 52, 169, 0.03), 0 1.241px 3.148px 0 rgba(55, 52, 169, 0.02);
            color: white;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: opacity 0.2s ease;
        }
        .who-we-are-section .who-we-are-button > span {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .who-we-are-section .who-we-are-arrow {
            position: relative;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .who-we-are-section .who-we-are-arrow svg {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: block;
        }
        .who-we-are-section .who-we-are-arrow .who-we-are-arrow-default {
            width: 21px;
            height: 21px;
        }
        .who-we-are-section .who-we-are-arrow .who-we-are-arrow-hover {
            width: 17px;
            height: 11px;
        }
        .who-we-are-section .who-we-are-arrow .who-we-are-arrow-hover {
            display: none;
        }
        .who-we-are-section .who-we-are-button:hover .who-we-are-arrow-default {
            display: none;
        }
        .who-we-are-section .who-we-are-button:hover .who-we-are-arrow-hover {
            display: block;
        }
        .who-we-are-section .who-we-are-button:hover {
            opacity: 0.9;
        }
    </style>
    <section class="who-we-are-section  <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>">
        <div class="container-custom max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            
            <!-- Main Content Section -->
            <div class="flex flex-col <?php echo ($atts['image_position'] === 'top' || $atts['image_position'] === 'bottom') ? '' : 'lg:flex-row'; ?> items-center">
                
                <!-- Text Content Section -->
                <div class="w-full <?php echo ($atts['image_position'] !== 'top' && $atts['image_position'] !== 'bottom') ? 'lg:w-1/2' : ''; ?>">
                    <div class="text-left">
                        <h2 class="pb-2.5 text-center">
                            <?php echo esc_html($atts['title']); ?>
                        </h2>
                        <?php if (!empty($atts['subtitle'])) : ?>
                            <p class="text-lg sm:text-xl lg:text-2xl text-[#000] font-semibold text-center mb-4 leading-relaxed font-lato">
                                <?php echo esc_html($atts['subtitle']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($atts['description'])) : ?>
                            <div class="text-base sm:text-lg lg:text-lg text-[#5B5B5B] text-center max-w-2xl mx-auto leading-relaxed font-lato font-medium space-y-4 tracking-[-0.4px] shadow-[0 67px 80px 0 rgba(55, 52, 169, 0.07), 0 43.426px 46.852px 0 rgba(55, 52, 169, 0.05), 0 25.807px 25.481px 0 rgba(55, 52, 169, 0.04), 0 13.4px 13px 0 rgba(55, 52, 169, 0.04), 0 5.459px 6.519px 0 rgba(55, 52, 169, 0.03), 0 1.241px 3.148px 0 rgba(55, 52, 169, 0.02)]">
                                <?php echo wp_kses_post(wpautop($atts['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Image Section -->
                <div class="w-full <?php echo ($atts['image_position'] === 'top' || $atts['image_position'] === 'bottom') ? 'lg:w-full' : 'lg:w-1/2'; ?> flex-shrink-0">
                    <div class="relative overflow-hidden rounded-xl ">
                        <img src="<?php echo esc_url($image_url); ?>" 
                             alt="<?php echo esc_attr($atts['title']); ?>" 
                             class="w-full h-auto object-cover">
                    </div>
                </div>

                <?php if (!empty($atts['button_text']) && !empty($atts['button_url'])) : ?>
                            <div class="mt-6 sm:mt-8 text-center">
                                <a href="<?php echo esc_url($atts['button_url']); ?>" 
                                   class="who-we-are-button px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg font-medium">
                                    <span>
                                        <?php echo esc_html($atts['button_text']); ?>
                                        <span class="who-we-are-arrow">
                                            <svg class="who-we-are-arrow-default" xmlns="http://www.w3.org/2000/svg" width="21" height="21" viewBox="0 0 21 21" fill="none">
                                                <path d="M11.5246 10.4999L7.19336 6.16861L8.43148 4.93136L14 10.4999L8.43149 16.0684L7.19424 14.8311L11.5246 10.4999Z" fill="#FFFFFF"/>
                                            </svg>
                                            <svg class="who-we-are-arrow-hover" xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11" fill="none">
                                                <path d="M10.7528 1.28425C10.459 0.990463 10.459 0.514133 10.7528 0.220343C11.0465 -0.0734475 11.5229 -0.0734475 11.8167 0.220343L16.3305 4.73412C16.6243 5.02792 16.6243 5.50432 16.3305 5.79802L11.8167 10.3119C11.5229 10.6056 11.0465 10.6056 10.7528 10.3119C10.459 10.0181 10.459 9.54172 10.7528 9.24792L13.9823 6.01842H0.7606C0.34053 6.01842 0 5.68162 0 5.26612C0 4.85062 0.34053 4.51382 0.7606 4.51382H13.9823L10.7528 1.28425Z" fill="#FFFFFF"/>
                                            </svg>
                                        </span>
                                    </span>
                                </a>
                            </div>
                        <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('who_we_are_section', 'neways_who_we_are_section_shortcode');

