<?php
// === Parent Shortcode ===
function support_center_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'bg' => '', // background image
    ), $atts, 'support_center');

    $bg = esc_url($atts['bg']);

    ob_start();
    ?>
    <div id="support-center" class="relative isolate overflow-hidden bg-[#016a7c] py-24 sm:py-32 dark:bg-gray-900">
        <?php if($bg): ?>
            <img src="<?php echo $bg; ?>" alt="" class="absolute inset-0 -z-10 size-full object-cover opacity-30 dark:hidden" />
        <?php endif; ?>

        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:mx-0">
                <?php echo do_shortcode($content); ?>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('support_center', 'support_center_shortcode');


// === Heading Shortcode ===
function support_center_heading_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => 'Default Heading',
    ), $atts, 'sc_heading');

    return '<h2 class="text-5xl font-semibold tracking-tight text-white sm:text-7xl dark:text-white">'
            . esc_html($atts['text']) . '</h2>';
}
add_shortcode('sc_heading', 'support_center_heading_shortcode');


// === Paragraph Shortcode ===
function support_center_paragraph_shortcode($atts) {
    $atts = shortcode_atts(array(
        'text' => 'Default paragraph goes here...',
    ), $atts, 'sc_paragraph');

    return '<p class="mt-8 text-pretty text-lg font-medium text-white sm:text-xl/8 dark:text-gray-400">'
            . esc_html($atts['text']) . '</p>';
}
add_shortcode('sc_paragraph', 'support_center_paragraph_shortcode');
