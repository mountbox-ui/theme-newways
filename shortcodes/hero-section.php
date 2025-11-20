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
        <div id="hero-section" class="<?php echo esc_attr($atts['class']); ?> max-h-[625px] mb-[80px]">
            <div class="relative isolate overflow-hidden text-white">
                <div
                    class="absolute inset-0 bg-cover bg-center"
                    <?php echo $background ? 'style="background-image:url(\'' . esc_url($background) . '\');"' : ''; ?>
                ></div>

                <div class="relative z-10 mx-auto flex w-full max-w-[1220px] items-center px-6 pt-16 sm:px-1 sm:pt-24 pb-8 sm:pb-12 md:pb-8 lg:px-1">
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

        return '<h6 class="hero-section__badge inline-flex items-center  text-[#f9d463]  pt-20 ">'
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

        return '<p class="hero-section__description font-manrope font-medium pb-9 text-lg leading-6 text-[rgba(255,255,255,0.74)] md:text-lg sm:text-base sm:leading-[1.9]">'
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

        if (empty($atts['primary_text']) || empty($atts['primary_url'])) {
            return '';
        }

        $button = '<a href="' . esc_url($atts['primary_url']) . '" class="hero-section__cta inline-flex items-center justify-center rounded-full btn-primary text-black bg-[#FFB64D] shadow-[0_4px_4px_0_rgba(0,0,0,0.25),0_67px_80px_0_rgba(55,52,169,0.07),0_43.426px_46.852px_0_rgba(55,52,169,0.05),0_25.807px_25.481px_0_rgba(55,52,169,0.04),0_13.4px_13px_0_rgba(55,52,169,0.04),0_5.459px_6.519px_0_rgba(55,52,169,0.03),0_1.241px_3.148px_0_rgba(55,52,169,0.02) transition duration-700 ease-in-out]">'
            . esc_html($atts['primary_text']) . '</a>';

        return '<div class="mt-4 flex flex-wrap items-center gap-4">' . $button . '</div>';
    }
    add_shortcode('hero_buttons', 'hero_section_buttons_shortcode');
}
