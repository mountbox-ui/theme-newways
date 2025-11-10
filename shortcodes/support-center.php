<?php
if (!defined('ABSPATH')) {
    exit;
}

// === Parent Shortcode ===
if (!function_exists('support_center_shortcode')) {
    function support_center_shortcode($atts, $content = null)
    {
        $defaults = array(
            'bg'              => '',
            'overlay_color'   => '#0a1735',
            'overlay_opacity' => '0.55',
            'gradient'        => 'true',
            'class'           => '',
        );

        $atts = shortcode_atts($defaults, $atts, 'support_center');

        $default_bg = get_template_directory_uri() . '/assets/images/hero-bg.jpg';
        $background = !empty($atts['bg']) ? esc_url($atts['bg']) : $default_bg;

        $overlay_color = sanitize_hex_color($atts['overlay_color']);
        if (!$overlay_color) {
            $overlay_color = '#0a1735';
        }

        $overlay_opacity = floatval($atts['overlay_opacity']);
        if ($overlay_opacity < 0 || $overlay_opacity > 1) {
            $overlay_opacity = 0.55;
        }

        $gradient_enabled = filter_var($atts['gradient'], FILTER_VALIDATE_BOOLEAN);

        ob_start();
        ?>
        <div id="support-center" class="<?php echo esc_attr($atts['class']); ?>">
            <div class="relative isolate overflow-hidden  text-white shadow-[0_40px_80px_rgba(5,13,42,0.35)]">
                <div
                    class="absolute inset-0 bg-cover bg-center"
                    <?php echo $background ? 'style="background-image:url(\'' . esc_url($background) . '\');"' : ''; ?>
                ></div>

                <div class="absolute inset-0" style="background-color: <?php echo esc_attr($overlay_color); ?>; opacity: <?php echo esc_attr($overlay_opacity); ?>;"></div>

                <div class="relative z-10 mx-auto flex w-full max-w-[1220px] items-center px-6 py-16 sm:px-12 sm:py-24 lg:px-16">
                    <div class="support-center__content w-full max-w-[420px] space-y-4 text-left sm:space-y-5">
                        <?php echo do_shortcode($content); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    add_shortcode('support_center', 'support_center_shortcode');
}

// === Pretitle Shortcode ===
if (!function_exists('support_center_pretitle_shortcode')) {
    function support_center_pretitle_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'A WARM WELCOME AWAITS YOU',
            ),
            $atts,
            'sc_pretitle'
        );

        return '<h6 class="support-center__badge inline-flex items-center rounded-full bg-white/12  py-2 text-[11px] font-semibold uppercase tracking-[0.48em] text-[#f9d463]">'
            . esc_html($atts['text']) . '</h6>';
    }
    add_shortcode('sc_pretitle', 'support_center_pretitle_shortcode');
}

// === Heading Shortcode ===
if (!function_exists('support_center_heading_shortcode')) {
    function support_center_heading_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'Default Heading',
            ),
            $atts,
            'sc_heading'
        );

        return '<h1 class="support-center__headline text-[2.6rem] font-semibold leading-[1.18] text-white sm:text-[3rem] md:text-[3.2rem]">'
            . esc_html($atts['text']) . '</h1>';
    }
    add_shortcode('sc_heading', 'support_center_heading_shortcode');
}

// === Paragraph Shortcode ===
if (!function_exists('support_center_paragraph_shortcode')) {
    function support_center_paragraph_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'text' => 'Default paragraph goes here...',
            ),
            $atts,
            'sc_paragraph'
        );

        return '<p class="support-center__description text-[15.5px] leading-[1.75] text-white/80 sm:text-base sm:leading-[1.9]">'
            . esc_html($atts['text']) . '</p>';
    }
    add_shortcode('sc_paragraph', 'support_center_paragraph_shortcode');
}

// === Buttons Row Shortcode ===
if (!function_exists('support_center_buttons_shortcode')) {
    function support_center_buttons_shortcode($atts)
    {
        $atts = shortcode_atts(
            array(
                'primary_text'   => 'Explore our services',
                'primary_url'    => '#services',
                'secondary_text' => '',
                'secondary_url'  => '',
            ),
            $atts,
            'sc_buttons'
        );

        $buttons = '';

        if (!empty($atts['primary_text']) && !empty($atts['primary_url'])) {
            $buttons .= '<a href="' . esc_url($atts['primary_url']) . '" class="support-center__cta inline-flex items-center justify-center rounded-full bg-[#f9c842] px-9 py-3 text-base font-semibold text-[#242163] shadow-[0_20px_35px_rgba(10,22,55,0.35)] transition duration-200 hover:-translate-y-0.5 hover:bg-[#FFB64D] hover:shadow-[0_25px_45px_rgba(10,22,55,0.45)]">'
                . esc_html($atts['primary_text']) . '</a>';
        }

        if (!empty($atts['secondary_text']) && !empty($atts['secondary_url'])) {
            $buttons .= '<a href="' . esc_url($atts['secondary_url']) . '" class="inline-flex items-center justify-center rounded-full border border-white/40 px-8 py-3 text-base font-semibold text-white transition-colors duration-200 hover:border-white/60 hover:bg-white/10">'
                . esc_html($atts['secondary_text']) . '</a>';
        }

        if (empty($buttons)) {
            return '';
        }

        return '<div class="mt-4 flex flex-wrap items-center gap-4">' . $buttons . '</div>';
    }
    add_shortcode('sc_buttons', 'support_center_buttons_shortcode');
}
