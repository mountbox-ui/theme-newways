<?php
/**
 * Text Section Shortcode
 * Creates a text-only section without images.
 * 
 * Usage: [text_section title="..." content="..." align="center" background="white" button_text="..." button_url="..."]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

function neways_text_section_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => '',
        'content' => '',
        'align' => 'left', // left, center, right
        'background' => 'transparent', // transparent, navy, white
        'button_text' => '',
        'button_url' => ''
    ), $atts);
    
    $section_classes = 'text-section py-16';
    if ($atts['background'] === 'navy') {
        $section_classes .= ' bg-navy-800';
    } elseif ($atts['background'] === 'white') {
        $section_classes .= ' bg-white text-gray-900';
    }
    
    $text_color = $atts['background'] === 'white' ? 'text-gray-900' : 'text-white';
    $subtitle_color = $atts['background'] === 'white' ? 'text-gray-600' : 'text-navy-200';
    
    $align_class = '';
    switch ($atts['align']) {
        case 'center':
            $align_class = 'text-center';
            break;
        case 'right':
            $align_class = 'text-right';
            break;
        default:
            $align_class = 'text-left';
    }
    
    ob_start();
    ?>
    <section class="<?php echo esc_attr($section_classes); ?>">
        <div class="container-custom">
            <div class="max-w-4xl <?php echo $atts['align'] === 'center' ? 'mx-auto' : ''; ?> <?php echo $align_class; ?>">
                <?php if (!empty($atts['title'])) : ?>
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 <?php echo $text_color; ?>">
                        <?php echo esc_html($atts['title']); ?>
                    </h2>
                <?php endif; ?>
                <?php if (!empty($atts['content'])) : ?>
                    <p class="text-lg mb-6 <?php echo $subtitle_color; ?> leading-relaxed">
                        <?php echo esc_html($atts['content']); ?>
                    </p>
                <?php endif; ?>
                <?php if (!empty($atts['button_text']) && !empty($atts['button_url'])) : ?>
                    <a href="<?php echo esc_url($atts['button_url']); ?>" class="btn btn-primary">
                        <?php echo esc_html($atts['button_text']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </section>
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('text_section', 'neways_text_section_shortcode');
