<?php
/**
 * Services Section Shortcode
 * Creates an "Our services" section with three service cards displayed horizontally.
 * 
 * Usage: [services_section]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_services_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Our services',
        'intro' => 'Comprehensive care solutions tailored to meet diverse needs across the healthcare spectrum.',
        'background' => 'gradient', // gradient, white, gray
        'class' => ''
    ), $atts);
    
    // Background classes and styles
    $bg_class = '';
    $bg_style = '';
    if ($atts['background'] === 'gradient') {
        // Light purple background as per design
        $bg_style = 'background: #F7F5FE;'; // Light purple background
    } elseif ($atts['background'] === 'gray') {
        $bg_class = 'bg-gray-50';
    } else {
        $bg_class = 'bg-white';
    }
    
    // Service data
    $services = array(
        array(
            'title' => 'Care Home Services',
            'image' => get_template_directory_uri() . '/assets/images/neways-services1.jpg',
            'items' => array('Residential Care', 'Nursing Care', '24/7 Support'),
            'link' => '#'
        ),
        array(
            'title' => 'Home Care Services',
            'image' => get_template_directory_uri() . '/assets/images/newways-services2.jpg',
            'items' => array('Domiciliary Care', 'Live-in Care', 'Personal Care'),
            'link' => '#'
        ),
        array(
            'title' => 'Professional Services',
            'image' => get_template_directory_uri() . '/assets/images/newwaysservices3.jpg',
            'items' => array('Healthcare Recruitment', 'Strategic Consulting', 'Training & Development'),
            'link' => '#'
        )
    );
    
    ob_start();
    ?>
    <section class="services-section py-12 sm:py-16 lg:py-16 <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>"<?php echo !empty($bg_style) ? ' style="' . esc_attr($bg_style) . '"' : ''; ?>>
        <div class="container-custom max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0 ">
            <div class="pb-4 sm:pb-6 lg:pb-12">
                <h2 class="pb-3">
                    <?php echo esc_html($atts['title']); ?>
                </h2>
                <?php if (!empty($atts['intro'])) : ?>
                    <p class="text-base sm:text-lg lg:text-xl text-[#1E1D37] max-w-full sm:max-w-2xl lg:max-w-lg leading-relaxed font-lato font-medium">
                        <?php echo esc_html($atts['intro']); ?>
                    </p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                <?php foreach ($services as $service) : ?>
                    <a href="<?php echo esc_url($service['link']); ?>" class="service-card bg-white  overflow-hidden shadow-md hover:shadow-2xl hover:-translate-y-2 transition-all duration-300 flex flex-col cursor-pointer block">
                        <!-- Service Image -->
                        <?php if (!empty($service['image'])) : ?>
                            <div class="service-image-wrapper w-full h-48 sm:h-56 lg:h-64 overflow-hidden">
                                <img src="<?php echo esc_url($service['image']); ?>" 
                                     alt="<?php echo esc_attr($service['title']); ?>" 
                                     class="w-full h-full object-cover">
                            </div>
                        <?php else : ?>
                            <!-- Placeholder for image - can be replaced with actual images -->
                            <div class="service-image-wrapper w-full h-48 sm:h-56 lg:h-64 bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center">
                                <svg class="w-24 h-24 text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Service Content -->
                        <div class="p-4 sm:p-6 lg:p-8 flex-1 flex flex-col">
                            <h5 class=" text-[#1C1A1D] pb-4 sm:pb-6 ">
                                <?php echo esc_html($service['title']); ?>
                            </h5>
                            
                            <!-- Service Items List -->
                            <ul class="space-y-3 sm:space-y-4 mb-4 sm:mb-6 flex-1">
                                <?php foreach ($service['items'] as $item) : ?>
                                    <li class="flex items-center gap-3">
                                        <!-- Gold Checkmark Icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                        <path d="M8.33333 16.6667C12.9358 16.6667 16.6667 12.9358 16.6667 8.33333C16.6667 3.73083 12.9358 0 8.33333 0C3.73083 0 0 3.73083 0 8.33333C0 12.9358 3.73083 16.6667 8.33333 16.6667ZM12.8808 6.21417L7.5 11.595L3.99417 8.08917L5.1725 6.91083L7.5 9.23833L11.7025 5.03583L12.8808 6.21417Z" fill="#CDAC79"/>
                                        </svg>
                                        <span class="text-[#2A2A2A] text-sm sm:text-base lg:text-xl font-lato font-normal leading-relaxed">
                                            <?php echo esc_html($item); ?>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            
                            <!-- Learn More Text -->
                            <div class="btn-readmore group pl-0 ">
                                <span>Learn more</span>
                                <span class="btn-readmore-arrow">
                            <svg viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <!-- Arrow head -->
                            <g class="btn-readmore-arrow-head">
                                <path d="M1 1C4.5 4 5 4.38484 5 4.5C5 4.61516 4.5 5 1 8" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <!-- Arrow body -->
                            <g class="btn-readmore-arrow-body">
                                <path d="M3.5 4.5H0" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                            </g>
                        </svg>
                            </span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('services_section', 'neways_services_section_shortcode');

