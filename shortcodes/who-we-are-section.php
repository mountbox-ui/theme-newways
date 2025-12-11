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
            transition: all 0.3s ease-in-out;
            transform: translateY(0);
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
            background: white;
            color: #381F75;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3), 0 5px 10px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }
        .who-we-are-section .who-we-are-button:hover .who-we-are-arrow svg path {
            fill: #381F75;
        }
    </style>
    <section class="who-we-are-section  <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>">
        <div class="container-custom max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            
            <!-- Main Content Section -->
            <div class="flex flex-col <?php echo ($atts['image_position'] === 'top' || $atts['image_position'] === 'bottom') ? '' : 'lg:flex-row'; ?> items-center">
                
                <!-- Text Content Section -->
                <div class="w-full <?php echo ($atts['image_position'] !== 'top' && $atts['image_position'] !== 'bottom') ? 'lg:w-1/2' : ''; ?>">
                    <div class="text-left">
                        <h2 class="pb-2.5 text-left sm:text-left md:text-center">
                            <?php echo esc_html($atts['title']); ?>
                        </h2>
                        <?php if (!empty($atts['subtitle'])) : ?>
                            <p class="text-lg sm:text-xl lg:text-2xl text-[#000] font-semibold text-left sm:text-left md:text-center mb-4 leading-relaxed font-lato">
                                <?php echo esc_html($atts['subtitle']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($atts['description'])) : ?>
                            <div class="text-base sm:text-lg lg:text-lg text-[#5B5B5B] text-left sm:text-left md:text-center max-w-2xl md:mx-auto leading-relaxed font-lato font-medium space-y-4 tracking-[-0.4px] shadow-[0 67px 80px 0 rgba(55, 52, 169, 0.07), 0 43.426px 46.852px 0 rgba(55, 52, 169, 0.05), 0 25.807px 25.481px 0 rgba(55, 52, 169, 0.04), 0 13.4px 13px 0 rgba(55, 52, 169, 0.04), 0 5.459px 6.519px 0 rgba(55, 52, 169, 0.03), 0 1.241px 3.148px 0 rgba(55, 52, 169, 0.02)]">
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
                                   class="btn-hero group bg-[#381F75] text-white hover:border-solid hover:border-[1px] hover:border-[#381F75]">
                                    <span>
                                        <?php echo esc_html($atts['button_text']); ?>
                                        <span class="btn-hero-arrow">
                                        <svg viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <!-- Arrow head -->
                            <g class="btn-hero-arrow-head">
                                <path d="M1 1C4.5 4 5 4.38484 5 4.5C5 4.61516 4.5 5 1 8" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <!-- Arrow body -->
                            <g class="btn-hero-arrow-body">
                                <path d="M3.5 4.5H0" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </g>
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

