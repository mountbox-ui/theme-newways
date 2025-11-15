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
        'description' => 'We are dedicated to providing exceptional care and support services to those who need it most.',
        'image' => '',
        'image_position' => 'right', // left, right, top, bottom
        'background' => 'white', // white, gray
        'class' => '',
        'show_values' => 'true', // true, false
        'values_title' => 'Our Values'
    ), $atts);
    
    $bg_class = $atts['background'] === 'gray' ? 'bg-gray-50' : 'bg-white';
    $image_position_class = '';
    
    // Determine layout based on image position
    if ($atts['image_position'] === 'left') {
        $image_position_class = 'flex-row-reverse';
    } elseif ($atts['image_position'] === 'top') {
        $image_position_class = 'flex-col';
    } elseif ($atts['image_position'] === 'bottom') {
        $image_position_class = 'flex-col-reverse';
    } else {
        $image_position_class = 'flex-row';
    }
    
    ob_start();
    ?>
    <section class="who-we-are-section py-12 sm:py-16 lg:py-20 <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>">
        <div class="container-custom max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            
            <!-- Main Content Section -->
            <div class="flex flex-col <?php echo ($atts['image_position'] === 'top' || $atts['image_position'] === 'bottom') ? '' : 'lg:flex-row'; ?> gap-8 lg:gap-12 items-center mb-12 sm:mb-16 lg:mb-20">
                
                <!-- Image Section -->
                <?php if (!empty($atts['image'])) : ?>
                    <div class="w-full <?php echo ($atts['image_position'] === 'top' || $atts['image_position'] === 'bottom') ? 'lg:w-full' : 'lg:w-1/2'; ?> flex-shrink-0">
                        <div class="relative overflow-hidden rounded-xl shadow-lg">
                            <img src="<?php echo esc_url($atts['image']); ?>" 
                                 alt="<?php echo esc_attr($atts['title']); ?>" 
                                 class="w-full h-auto object-cover">
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Text Content Section -->
                <div class="w-full <?php echo (!empty($atts['image']) && ($atts['image_position'] !== 'top' && $atts['image_position'] !== 'bottom')) ? 'lg:w-1/2' : ''; ?>">
                    <div class="pb-4 sm:pb-6 lg:pb-8 text-left">
                        <h2 class="pb-2.5">
                            <?php echo esc_html($atts['title']); ?>
                        </h2>
                        <?php if (!empty($atts['subtitle'])) : ?>
                            <p class="text-lg sm:text-xl lg:text-2xl text-[#1E1D37] font-semibold mb-4 leading-relaxed font-manrope">
                                <?php echo esc_html($atts['subtitle']); ?>
                            </p>
                        <?php endif; ?>
                        <?php if (!empty($atts['description'])) : ?>
                            <div class="text-base sm:text-lg lg:text-xl text-[#1E1D37] max-w-full leading-relaxed font-manrope font-medium space-y-4">
                                <?php echo wp_kses_post(wpautop($atts['description'])); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Values Section (Optional) -->
            <?php if ($atts['show_values'] === 'true') : ?>
                <div class="values-grid mt-12 sm:mt-16 lg:mt-20">
                    <?php if (!empty($atts['values_title'])) : ?>
                        <h3 class="text-2xl sm:text-3xl font-bold text-[#1E1D37] mb-8 text-center font-manrope">
                            <?php echo esc_html($atts['values_title']); ?>
                        </h3>
                    <?php endif; ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                        <!-- Value 1: Compassion -->
                        <div class="value-card bg-white rounded-xl shadow-md p-6 sm:p-8 text-center hover:shadow-lg transition-shadow duration-300">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Compassion</h4>
                            <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                                We approach every individual with empathy, understanding, and genuine care.
                            </p>
                        </div>

                        <!-- Value 2: Excellence -->
                        <div class="value-card bg-white rounded-xl shadow-md p-6 sm:p-8 text-center hover:shadow-lg transition-shadow duration-300">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                                </svg>
                            </div>
                            <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Excellence</h4>
                            <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                                We strive for the highest standards in everything we do.
                            </p>
                        </div>

                        <!-- Value 3: Integrity -->
                        <div class="value-card bg-white rounded-xl shadow-md p-6 sm:p-8 text-center hover:shadow-lg transition-shadow duration-300">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Integrity</h4>
                            <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                                We act with honesty, transparency, and ethical principles.
                            </p>
                        </div>

                        <!-- Value 4: Respect -->
                        <div class="value-card bg-white rounded-xl shadow-md p-6 sm:p-8 text-center hover:shadow-lg transition-shadow duration-300">
                            <div class="mb-4">
                                <svg class="w-12 h-12 mx-auto text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <h4 class="text-lg sm:text-xl font-bold text-gray-900 mb-3">Respect</h4>
                            <p class="text-sm sm:text-base text-gray-700 leading-relaxed">
                                We honor the dignity and individuality of every person we serve.
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('who_we_are_section', 'neways_who_we_are_section_shortcode');

