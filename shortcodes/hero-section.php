<?php
if (!defined('ABSPATH')) {
    exit;
}

// === Parent Shortcode ===
if (!function_exists('hero_section_shortcode')) {
    function hero_section_shortcode($atts, $content = null)
    {
        $defaults = array(
            'bg'              => '',
            'overlay_color'   => '#0a1735',
            'overlay_opacity' => '0.55',
            'gradient'        => 'true',
            'class'           => '',
        );

        $atts = shortcode_atts($defaults, $atts, 'hero_section');

        $default_bg = get_template_directory_uri() . '/assets/images/hero-bg.jpg';
        $background = !empty($atts['bg']) ? esc_url($atts['bg']) : $default_bg;

        // $overlay_color = sanitize_hex_color($atts['overlay_color']);
        // if (!$overlay_color) {
        //     $overlay_color = '#0a1735';
        // }

        // $overlay_opacity = floatval($atts['overlay_opacity']);
        // if ($overlay_opacity < 0 || $overlay_opacity > 1) {
        //     $overlay_opacity = 0.55;
        // }

        // $gradient_enabled = filter_var($atts['gradient'], FILTER_VALIDATE_BOOLEAN);

        ob_start();
        ?>
        <div id="hero-section" class="<?php echo esc_attr($atts['class']); ?> max-h-[625px] mb-0 sm:mb-0 md:mb-0 xl:mb-[80px]">
            <div class="isolate overflow-hidden text-white">
                <div
                    class="absolute inset-0 bg-cover bg-center h-[620px] sm:h-[700px] md:h-[650px] xl:h-[650px]"
                    <?php echo $background ? 'style="background-image:url(\'' . esc_url($background) . '\');"' : ''; ?>
                ></div>

                <div class="relative z-10 mx-auto flex w-full max-w-[1220px] items-center px-6 pt-16 pb-8  sm:pt-16sm:pb-12 md:px-8 md:pb-8 xl:px-0">
                    <div class="hero-section__content w-full max-w-[500px] text-left">
                        <?php echo do_shortcode($content); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    add_shortcode('hero_section', 'hero_section_shortcode');
}

// === Pretitle Shortcode ===
if (!function_exists('hero_section_pretitle_shortcode')) {
    function hero_section_pretitle_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'A WARM WELCOME AWAITS YOU',
            ),
            $atts,
            'hero_pretitle'
        );

        return '<h6 class="hero-section__badge inline-flex items-center  text-[#f9d463] ">'
            . esc_html($atts['text']) . '</h6>';
    }
    add_shortcode('hero_pretitle', 'hero_section_pretitle_shortcode');
}

// === Heading Shortcode ===
if (!function_exists('hero_section_heading_shortcode')) {
    function hero_section_heading_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'Default Heading',
            ),
            $atts,
            'hero_heading'
        );

        return '<h1 class="pb-5">'
            . esc_html($atts['text']) . '</h1>';
    }
    add_shortcode('hero_heading', 'hero_section_heading_shortcode');
}

// === Paragraph Shortcode ===
if (!function_exists('hero_section_paragraph_shortcode')) {
    function hero_section_paragraph_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'Default paragraph goes here...',
            ),
            $atts,
            'hero_paragraph'
        );

        return '<p class="hero-section__description font-lato font-medium pb-[30px] text-lg leading-[26px] text-[rgba(255,255,255,0.74)] md:text-lg sm:text-base sm:leading-[1.9]">'
            . esc_html($atts['text']) . '</p>';
    }
    add_shortcode('hero_paragraph', 'hero_section_paragraph_shortcode');
}

// === Buttons Row Shortcode ===

if (!function_exists('hero_section_buttons_shortcode')) {
    function hero_section_buttons_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'primary_text' => 'Explore our services',
                'primary_url'  => '#services',
            ),
            $atts,
            'hero_buttons'
        );

        // Return empty if either text or URL is missing
        if (empty($atts['primary_text']) || empty($atts['primary_url'])) {
            return '';
        }

        // Button HTML with reusable btn-hero class and inline background color
        $button = '<a href="' . esc_url($atts['primary_url']) . '" class="btn-hero group" >
                    <span>' . esc_html($atts['primary_text']) . '</span>
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
                </a>';

                
                

        // Wrap the button in a flex container for spacing
        return '<div class="mt-4 flex flex-wrap items-center gap-4">' . $button . '</div>';
    }

    add_shortcode('hero_buttons', 'hero_section_buttons_shortcode');
}


