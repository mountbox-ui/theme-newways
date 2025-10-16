<?php
/**
 * Image Section Shortcode
 * [image_section position="left"]
 *    [image_section_image src="image.jpg" alt="Alt text"]
 *    [image_section_content title="Title" button_text="Click" button_url="#"]Content[/image_section_content]
 * [/image_section]
 */

if (!defined('ABSPATH')) exit;

function neways_image_section_shortcode($atts, $content = null) {
    $atts = shortcode_atts(['position' => 'left'], $atts);
    
    // Parse nested shortcodes
    $pattern = get_shortcode_regex(['image_section_content', 'image_section_image']);
    preg_match_all('/'.$pattern.'/s', $content, $matches);
    
    $image_content = '';
    $text_content = '';
    
    if (!empty($matches[0])) {
        foreach ($matches[0] as $shortcode) {
            if (strpos($shortcode, 'image_section_content') !== false) {
                $text_content = do_shortcode($shortcode);
            } elseif (strpos($shortcode, 'image_section_image') !== false) {
                $image_content = do_shortcode($shortcode);
            }
        }
    }
    
    ob_start();
    ?>
    <section class="bg-navy-900 py-8 sm:py-12 md:py-16">
        <div class="container-custom mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-8 sm:gap-12 lg:gap-16 <?php echo $atts['position'] === 'right' ? 'lg:flex-row-reverse' : 'lg:flex-row'; ?> items-center justify-between">
                <!-- Text Content -->
                <div class="w-full md:w-4/5 lg:w-1/2 text-center lg:text-left">
                    <?php echo $text_content; ?>
                </div>
                <!-- Image Content -->
                <div class="w-full md:w-4/5 lg:w-1/2">
                    <div class="bg-white rounded-lg p-4 sm:p-6 shadow-lg">
                        <?php echo $image_content; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

function neways_image_section_image_shortcode($atts, $content = null) {
    $atts = shortcode_atts(['src' => '', 'alt' => ''], $atts);
    $src = empty($atts['src']) ? trim($content) : $atts['src'];
    if (empty($src)) return '';
    
    return sprintf(
        '<img src="%s" alt="%s" class="w-full h-auto rounded-lg">',
        esc_url($src),
        esc_attr($atts['alt'])
    );
}

function neways_image_section_content_shortcode($atts, $content = null) {
    $atts = shortcode_atts([
        'title' => '',
        'button_text' => '',
        'button_url' => ''
    ], $atts);
    
    ob_start();
    ?>
    <div class="content-container">
        <?php if (!empty($atts['title'])) : ?>
            <h2 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white mb-4 sm:mb-6 md:mb-8">
                <?php echo esc_html($atts['title']); ?>
            </h2>
        <?php endif; ?>
        
        <div class="text-base sm:text-lg text-navy-200 leading-relaxed mb-6 sm:mb-8 max-w-xl mx-auto lg:mx-0">
            <?php echo wp_kses_post($content); ?>
        </div>
        
        <?php if (!empty($atts['button_text']) && !empty($atts['button_url'])) : ?>
            <a href="<?php echo esc_url($atts['button_url']); ?>" 
               class="inline-block bg-blue-600 text-white px-6 sm:px-8 py-2 sm:py-3 rounded-md text-base sm:text-lg font-medium hover:bg-blue-700 transition-colors duration-200">
                <?php echo esc_html($atts['button_text']); ?>
            </a>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

add_shortcode('image_section', 'neways_image_section_shortcode');
add_shortcode('image_section_image', 'neways_image_section_image_shortcode');
add_shortcode('image_section_content', 'neways_image_section_content_shortcode');
