<?php
/**
 * Testimonials Section Shortcode
 * Displays a testimonial block with large quote accent and stacked cards.
 *
 * Usage: [testimonials_section]
 * Optional attributes:
 * - title="Real Stories from Real Customers"
 * - subtitle="Get inspired by these stories."
 * - background="#E5F0FF"
 * - class="custom-class"
 * - testimonials='[{"quote":"...", "name":"...", "role":"..."}]'
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_testimonials_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Real Stories from Real Customers',
        'subtitle' => 'Get inspired by these stories.',
        'background' => '#E0E8F7',
        'class' => '',
        'testimonials' => ''
    ), $atts, 'testimonials_section');

    // Default testimonials data (matching provided design)
    $testimonials = array(
        array(
            'quote' => 'Working with Neways Healthcare has been a pleasure. Their commitment to quality care and professional development is truly outstanding.',
            'name'  => 'Jane Cooper',
            'role'  => 'CEO, Airbnb',
            'layout_class' => 'lg:absolute lg:top-0 lg:right-5 lg:w-[420px]',
            'style' => ''
        ),
        array(
            'quote' => 'The care my mother receives at Neways is exceptional. The staff are kind, professional, and treat her with the dignity she deserves.',
            'name'  => 'Floyd Miles',
            'role'  => 'Vice President, GoPro',
            'layout_class' => 'lg:absolute lg:top-36 lg:left-[-220px] lg:w-[350px]',
            'style' => ''
        ),
        array(
            'quote' => 'Landify saved our time in designing my company page.',
            'name'  => 'Kristin Watson',
            'role'  => 'Co-Founder, BookMyShow',
            'layout_class' => 'lg:absolute lg:top-[300px] lg:right-8 lg:w-[360px]',
            'style' => ''
        ),
    );

    // Allow overriding testimonials via JSON attribute
    if (!empty($atts['testimonials'])) {
        $decoded = json_decode(html_entity_decode($atts['testimonials']), true);
        if (is_array($decoded) && !empty($decoded)) {
                    $custom_items = array();
            foreach ($decoded as $item) {
                if (!empty($item['quote']) && !empty($item['name'])) {
                    $custom_items[] = array(
                        'quote' => wp_kses_post($item['quote']),
                        'name'  => sanitize_text_field($item['name']),
                                'role'  => isset($item['role']) ? sanitize_text_field($item['role']) : '',
                                'layout_class' => isset($item['layout_class']) ? sanitize_text_field($item['layout_class']) : '',
                                'style' => isset($item['style']) ? sanitize_text_field($item['style']) : ''
                    );
                }
            }
            if (!empty($custom_items)) {
                $testimonials = $custom_items;
            }
        }
    }

    ob_start();
    ?>
    <section class="testimonials-section h-[700px] <?php echo esc_attr($atts['class']); ?>" style="background: <?php echo esc_attr($atts['background']); ?>;">
        <div class="max-w-7xl mx-auto px-6 py-16 lg:py-24">
            <div class="grid gap-12 lg:grid-cols-2 items-start">
                <!-- Left Section: Quote Icon + Text -->
                <div class="relative">
                    <!-- Large Quote Icon - Overlapping -->
                    <div class="absolute -left-4 -top-4 lg:-left-8 lg:-top-8 z-0">
                        <svg xmlns="http://www.w3.org/2000/svg" width="143" height="120" viewBox="0 0 143 120" fill="none" class="w-24 h-20 sm:w-32 sm:h-28 lg:w-40 lg:h-32">
                            <g opacity="0.3" clip-path="url(#clip0_72_1092)">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M142.373 18.7419C121.049 29.1541 110.387 41.3882 110.387 55.4447C119.476 56.4859 126.992 60.1735 132.934 66.5076C138.877 72.8417 141.849 80.1735 141.849 88.5033C141.849 97.3536 138.965 104.816 133.197 110.889C127.428 116.963 120.175 120 111.435 120C101.647 120 93.1701 116.052 86.0037 108.156C78.8374 100.26 75.2542 90.6725 75.2542 79.3926C75.2542 45.553 94.306 19.089 132.41 0L142.373 18.7419ZM67.1186 18.7419C45.6196 29.1541 34.8702 41.3882 34.8702 55.4447C44.134 56.4859 51.7373 60.1735 57.6801 66.5076C63.6229 72.8417 66.5943 80.1735 66.5943 88.5033C66.5943 97.3536 63.6666 104.816 57.8112 110.889C51.9557 116.963 44.6584 120 35.919 120C26.1308 120 17.6973 116.052 10.6184 108.156C3.53942 100.26 0 90.6725 0 79.3926C0 45.553 18.9643 19.089 56.8935 0L67.1186 18.7419Z" fill="#A8C0F0"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_72_1092">
                                    <rect width="143" height="120" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
                    <!-- Text Content -->
                    <div class="relative z-10 pt-8 lg:pt-12 pl-8 lg:pl-12">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-normal text-[#333333] font-marcellus leading-tight mb-4 max-w-md">
                            <?php echo esc_html($atts['title']); ?>
                        </h2>
                        <?php if (!empty($atts['subtitle'])) : ?>
                            <p class="text-base text-[#555555] font-manrope font-normal">
                                <?php echo esc_html($atts['subtitle']); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Section: Testimonial Cards -->
                <div class="relative flex flex-col gap-6 lg:min-h-[420px]">
                    <?php foreach ($testimonials as $index => $item) : ?>
                        <?php
                        $card_classes = 'bg-white rounded-lg shadow-[0 10px 20px 0 rgba(41, 41, 42, 0.07)] p-6 sm:p-7 flex flex-col gap-2 transition-transform duration-200 mx-auto lg:mx-0 w-full max-w-md';

                        // Support custom layout classes and per-card positioning
                        if (!empty($item['layout_class'])) {
                            $card_classes .= ' ' . esc_attr($item['layout_class']);
                        } else {
                            if ($index === 0) {
                                $card_classes .= ' lg:absolute lg:top-0 lg:right-0 lg:w-[420px]';
                            } elseif ($index === 1) {
                                $card_classes .= ' lg:absolute lg:top-36 lg:left-0 lg:w-[460px]';
                            } elseif ($index === 2) {
                                $card_classes .= ' lg:absolute lg:bottom-0 lg:right-8 lg:w-[360px]';
                            }
                        }
                        ?>
                        <article class="<?php echo esc_attr($card_classes); ?>"<?php echo !empty($item['style']) ? ' style="' . esc_attr($item['style']) . '"' : ''; ?>>
                            <!-- Quote Icon in Card -->
                            <div class="mb-3 lg:mb-0">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none">
                                    <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.996 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.984zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.432.917-3.995 3.638-3.995 5.849h3.983v10h-9.984z" fill="#A8C0F0"/>
                                </svg>
                            </div>
                            <p class="text-base sm:text-lg text-[#3C3C3C] font-normal leading-relaxed  flex-grow">
                                <?php echo esc_html($item['quote']); ?>
                            </p>
                            <div>
                                <p class="text-base font-semibold text-[#1F1F1F]">
                                    <?php echo esc_html($item['name']); ?>
                                </p>
                                <?php if (!empty($item['role'])) : ?>
                                    <p class="text-sm text-[#7B8BAB] font-normal">
                                        <?php echo esc_html($item['role']); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

add_shortcode('testimonials_section', 'neways_testimonials_section_shortcode');

