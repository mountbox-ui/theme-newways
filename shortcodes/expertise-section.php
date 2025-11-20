<?php
/**
 * Expertise Section Shortcode
 * Creates an "Our expertise" section with four expertise cards displayed in a grid.
 * 
 * Usage: [expertise_section]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_expertise_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Our expertise',
        'intro' => 'Specialized care services delivered by trained professionals who understand your unique needs',
        'background' => 'white', // white, gray
        'class' => ''
    ), $atts);
    
    $bg_class = $atts['background'] === 'gray' ? 'bg-gray-50' : 'bg-white';
    
    ob_start();
    ?>
    <section class="expertise-section py-12 sm:py-16 lg:py-20 <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>">
        <div class="container-custom max-w-[1200px] mx-auto ">
            <div class="py-12 sm:py-16 lg:py-12 text-left">
                <h2 class="pb-2.5">
                    <?php echo esc_html($atts['title']); ?>
                </h2>
                <?php if (!empty($atts['intro'])) : ?>
                    <p class="text-base sm:text-lg lg:text-xl text-[#1E1D37] max-w-full sm:max-w-lg leading-relaxed font-manrope font-medium">
                        <?php echo esc_html($atts['intro']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8 lg:gap-12 pb-12 sm:pb-16 lg:pb-12">
                <!-- Expertise 1: Dementia care -->
                <div class="expertise-card bg-white rounded-xl  overflow-hidden">
                    <div class="w-full h-48 sm:h-56 lg:h-64 overflow-hidden rounded-lg">
                        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service1.png" 
                             alt="Dementia care" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="py-3 sm:py-3">
                        <h3 class="pb-2 sm:pb-3 text-[#1C1A1D]">Dementia care</h3>
                        <p class="text-[var(--Body-Text-Color,#757575)] leading-5 sm:leading-6 font-manrope text-base sm:text-lg md:text-lg font-medium py-0">
                            Specialized support for individuals living with dementia and memory-related conditions.
                        </p>
                    </div>
                </div>

                <!-- Expertise 2: Parkinson's Care -->
                <div class="expertise-card bg-white rounded-xl  overflow-hidden">
                    <div class="w-full h-48 sm:h-56 lg:h-64 overflow-hidden rounded-lg">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service2.jpg" 
                             alt="Parkinson's Care" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="py-3 sm:py-3">
                        <h3 class="pb-2 sm:pb-3 text-[#1C1A1D]">Parkinson's Care</h3>
                        <p class="text-[var(--Body-Text-Color,#757575)] leading-5 sm:leading-6 font-manrope text-base sm:text-lg md:text-lg font-medium">
                        Expert care tailored to the unique needs of those with Parkinson's disease.
                        </p>
                    </div>
                </div>

                <!-- Expertise 3: End of Life Care -->
                <div class="expertise-card bg-white rounded-xl  overflow-hidden">
                    <div class="w-full h-48 sm:h-56 lg:h-64 overflow-hidden rounded-lg">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service3.jpg" 
                             alt="End of Life Care" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="py-3 sm:py-3">
                        <h3 class="pb-2 sm:pb-3 text-[#1C1A1D]">End of Life Care</h3>
                        <p class="text-[var(--Body-Text-Color,#757575)] leading-5 sm:leading-6 font-manrope text-base sm:text-lg md:text-lg font-medium">
                        Compassionate palliative and end-of-life support with dignity and respect.
                        </p>
                    </div>
                </div>

                <!-- Expertise 4: Respite care -->
                <div class="expertise-card bg-white rounded-xl  overflow-hidden">
                    <div class="w-full h-48 sm:h-56 lg:h-64 overflow-hidden rounded-lg">
                    <img src="<?php echo get_template_directory_uri(); ?>/assets/images/service4.png"  
                             alt="Respite care" 
                             class="w-full h-full object-cover">
                    </div>
                    <div class="py-3 sm:py-3">
                        <h3 class="pb-2 sm:pb-3 text-[#1C1A1D]">Respite care</h3>
                        <p class="text-[var(--Body-Text-Color,#757575)] leading-5 sm:leading-6 font-manrope text-base sm:text-lg md:text-lg font-medium">
                        Temporary care services providing relief and support for family caregivers.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('expertise_section', 'neways_expertise_section_shortcode');

