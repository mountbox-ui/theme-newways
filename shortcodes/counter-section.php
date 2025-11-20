<?php
/**
 * Counter Section Shortcode
 * Creates a statistics/counter section with numbers and descriptions.
 * 
 * Usage: [counter_section]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_counter_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'background' => 'white', // white, gray
        'class' => '',
        // Counter 1
        'counter1_number' => '500+',
        'counter1_text' => 'Healthcare Professionals',
        // Counter 2
        'counter2_number' => '15+',
        'counter2_text' => 'Care Facilities',
        // Counter 3
        'counter3_number' => '2000+',
        'counter3_text' => 'Lives Touched Daily',
        // Counter 4
        'counter4_number' => '25+',
        'counter4_text' => 'Years of Excellence'
    ), $atts);
    
    $bg_class = $atts['background'] === 'gray' ? 'bg-gray-50' : 'bg-white';
    
    // Extract numbers and suffixes for animation
    // Extract number and suffix (e.g., "500+" -> number: 500, suffix: "+")
    preg_match('/(\d+)(.*)/', $atts['counter1_number'], $matches1);
    $counter1_data = array(
        'number' => isset($matches1[1]) ? intval($matches1[1]) : 0,
        'suffix' => isset($matches1[2]) ? $matches1[2] : ''
    );
    
    preg_match('/(\d+)(.*)/', $atts['counter2_number'], $matches2);
    $counter2_data = array(
        'number' => isset($matches2[1]) ? intval($matches2[1]) : 0,
        'suffix' => isset($matches2[2]) ? $matches2[2] : ''
    );
    
    preg_match('/(\d+)(.*)/', $atts['counter3_number'], $matches3);
    $counter3_data = array(
        'number' => isset($matches3[1]) ? intval($matches3[1]) : 0,
        'suffix' => isset($matches3[2]) ? $matches3[2] : ''
    );
    
    preg_match('/(\d+)(.*)/', $atts['counter4_number'], $matches4);
    $counter4_data = array(
        'number' => isset($matches4[1]) ? intval($matches4[1]) : 0,
        'suffix' => isset($matches4[2]) ? $matches4[2] : ''
    );
    
    ob_start();
    ?>
    <style>
        .counter-section .counter-number {
            font-feature-settings: 'liga' off;
        }
    </style>
    <section class="counter-section py-12 sm:py-16 lg:py-20 <?php echo esc_attr($bg_class); ?> <?php echo esc_attr($atts['class']); ?>">
        <div class="container-custom max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 sm:gap-12 lg:gap-16">
                
                <!-- Counter 1 -->
                <div class="counter-item border-b border-gray-200 pb-4">
                    <div class="counter-number text-[#232361] font-manrope text-[48px] font-extrabold leading-[64px]" 
                         data-target="<?php echo esc_attr($counter1_data['number']); ?>" 
                         data-suffix="<?php echo esc_attr($counter1_data['suffix']); ?>">
                        0<?php echo esc_html($counter1_data['suffix']); ?>
                    </div>
                    <div class="counter-text text-base sm:text-lg font-manrope text-[var(--Text-Gray-900,#18191F)] font-medium text-opacity-80">
                        <?php echo esc_html($atts['counter1_text']); ?>
                    </div>
                </div>

                <!-- Counter 2 -->
                <div class="counter-item border-b border-gray-200 pb-4">
                    <div class="counter-number text-[#232361] font-manrope text-[48px] font-extrabold leading-[64px]" 
                         data-target="<?php echo esc_attr($counter2_data['number']); ?>" 
                         data-suffix="<?php echo esc_attr($counter2_data['suffix']); ?>">
                        0<?php echo esc_html($counter2_data['suffix']); ?>
                    </div>
                    <div class="counter-text text-base sm:text-lg font-manrope text-[var(--Text-Gray-900,#18191F)] font-medium text-opacity-80">
                        <?php echo esc_html($atts['counter2_text']); ?>
                    </div>
                </div>

                <!-- Counter 3 -->
                <div class="counter-item border-b border-gray-200 pb-4">
                    <div class="counter-number text-[#232361] font-manrope text-[48px] font-extrabold leading-[64px]" 
                         data-target="<?php echo esc_attr($counter3_data['number']); ?>" 
                         data-suffix="<?php echo esc_attr($counter3_data['suffix']); ?>">
                        0<?php echo esc_html($counter3_data['suffix']); ?>
                    </div>
                    <div class="counter-text text-base sm:text-lg font-manrope text-[var(--Text-Gray-900,#18191F)] font-medium text-opacity-80">
                        <?php echo esc_html($atts['counter3_text']); ?>
                    </div>
                </div>

                <!-- Counter 4 -->
                <div class="counter-item border-b border-gray-200 pb-4">
                    <div class="counter-number text-[#232361] font-manrope text-[48px] font-extrabold leading-[64px]" 
                         data-target="<?php echo esc_attr($counter4_data['number']); ?>" 
                         data-suffix="<?php echo esc_attr($counter4_data['suffix']); ?>">
                        0<?php echo esc_html($counter4_data['suffix']); ?>
                    </div>
                    <div class="counter-text text-base sm:text-lg font-manrope text-[var(--Text-Gray-900,#18191F)] font-medium text-opacity-80">
                        <?php echo esc_html($atts['counter4_text']); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
    (function() {
        const counterSection = document.querySelector('.counter-section');
        if (!counterSection) return;
        
        const counterNumbers = counterSection.querySelectorAll('.counter-number');
        let hasAnimated = false;
        
        function animateCounter(element) {
            const target = parseInt(element.getAttribute('data-target')) || 0;
            const suffix = element.getAttribute('data-suffix') || '';
            const duration = 2000; // 2 seconds
            const steps = 60;
            const increment = target / steps;
            let current = 0;
            let step = 0;
            
            const timer = setInterval(function() {
                step++;
                current = Math.min(Math.ceil(increment * step), target);
                element.textContent = current.toLocaleString() + suffix;
                
                if (current >= target) {
                    clearInterval(timer);
                    element.textContent = target.toLocaleString() + suffix;
                }
            }, duration / steps);
        }
        
        // Use Intersection Observer to trigger animation when section is visible
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting && !hasAnimated) {
                    hasAnimated = true;
                    counterNumbers.forEach(counter => {
                        animateCounter(counter);
                    });
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.3 // Trigger when 30% of the section is visible
        });
        
        observer.observe(counterSection);
    })();
    </script>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('counter_section', 'neways_counter_section_shortcode');

