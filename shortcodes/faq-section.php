<?php
/**
 * FAQ Section Shortcodes
 * 
 * Provides nested shortcodes for creating FAQ sections with collapsible answers
 * 
 * Usage:
 * [faq_section title="Frequently Asked Questions" subtitle="Find answers to common questions"]
 *     [faq_item question="What is your service?" answer="Our service provides..."]
 *     [faq_item question="How much does it cost?" answer="The pricing varies..."]
 * [/faq_section]
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Main FAQ Section Shortcode
 * 
 * @param array $atts Shortcode attributes
 * @param string $content Shortcode content (contains faq_item shortcodes)
 * @return string HTML output
 */
function neways_faq_section_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'title' => 'Frequently Asked Questions',
        'subtitle' => 'Find answers to common questions',
        'style' => 'modern', // modern, minimal, card
        'layout' => 'accordion', // accordion, grid
        'show_icons' => 'true',
        'animation' => 'true',
        'dark_mode' => 'false', // Enable dark mode
        'class' => '',
        'id' => ''
    ), $atts);
    
    // Generate unique ID for the FAQ section
    $faq_id = !empty($atts['id']) ? $atts['id'] : 'faq-section-' . uniqid();
    
    // Process the content to extract FAQ items
    $faq_items = array();
    if (!empty($content)) {
        // Extract FAQ items directly from the raw content
        $faq_items = neways_extract_faq_items_from_raw($content);
        
        // Debug mode available for troubleshooting if needed
        // Add ?debug_faq=1 to URL to enable debugging
    }
    
    // Start output buffering
    ob_start();
    ?>
    <div class="faq-section <?php echo ($atts['dark_mode'] === 'true') ? 'dark' : ''; ?> <?php echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($faq_id); ?>" 
         data-style="<?php echo esc_attr($atts['style']); ?>" 
         data-layout="<?php echo esc_attr($atts['layout']); ?>">
        
        <div class="faq-container-wrapper">
            <div class="faq-inner-container">
                <!-- FAQ Header -->
                <?php if (!empty($atts['title']) || !empty($atts['subtitle'])): ?>
                <div class="faq-header">
                    <?php if (!empty($atts['title'])): ?>
                        <h2 class="faq-title"><?php echo esc_html($atts['title']); ?></h2>
                    <?php endif; ?>
                    
                    <?php if (!empty($atts['subtitle'])): ?>
                        <p class="faq-subtitle"><?php echo esc_html($atts['subtitle']); ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
        <!-- FAQ Items Container -->
        <dl class="faq-container" data-animation="<?php echo esc_attr($atts['animation']); ?>">
            <?php if (!empty($faq_items)): ?>
                <?php foreach ($faq_items as $index => $item): ?>
                    <?php neways_render_faq_item($item, $index, $atts); ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="faq-no-items">No FAQ items found.</p>
            <?php endif; ?>
        </dl>
            </div>
        </div>
    </div>
    
    <!-- FAQ Styles - Tailwind-inspired -->
    <style>
    .faq-section {
        background: #ffffff;
        color: #111827;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    }
    
    .faq-section.dark {
        background: #111827;
        color: #f9fafb;
    }
    
    .faq-container-wrapper {
        max-width: 80rem; /* 1280px */
        margin: 0 auto;
        padding: 1.5rem 1.5rem 6rem 1.5rem; /* px-6 py-24 */
    }
    
    @media (min-width: 640px) {
        .faq-container-wrapper {
            padding: 2rem 1.5rem 8rem 1.5rem; /* sm:py-32 */
        }
    }
    
    @media (min-width: 1024px) {
        .faq-container-wrapper {
            padding: 2.5rem 2rem 10rem 2rem; /* lg:px-8 lg:py-40 */
        }
    }
    
    .faq-inner-container {
        max-width: 64rem; /* 1024px */
        margin: 0 auto;
    }
    
    .faq-header {
        margin-bottom: 4rem; /* mt-16 */
    }
    
    .faq-title {
        font-size: 2.25rem; /* text-4xl */
        font-weight: 600; /* font-semibold */
        letter-spacing: -0.025em; /* tracking-tight */
        color: #111827; /* text-gray-900 */
        margin: 0;
        line-height: 1.1;
    }
    
    .faq-section.dark .faq-title {
        color: #ffffff; /* dark:text-white */
    }
    
    @media (min-width: 640px) {
        .faq-title {
            font-size: 3rem; /* sm:text-5xl */
        }
    }
    
    .faq-subtitle {
        font-size: 1.125rem;
        color: #6b7280;
        margin: 1rem 0 0 0;
        line-height: 1.6;
    }
    
    .faq-section.dark .faq-subtitle {
        color: #d1d5db;
    }
    
    .faq-container {
        display: flex;
        flex-direction: column;
        border-top: 1px solid rgba(17, 24, 39, 0.1); /* divide-y divide-gray-900/10 */
    }
    
    .faq-section.dark .faq-container {
        border-top-color: rgba(255, 255, 255, 0.1); /* dark:divide-white/10 */
    }
    
    .faq-item {
        border-bottom: 1px solid rgba(17, 24, 39, 0.1); /* divide-y divide-gray-900/10 */
        padding: 1.5rem 0; /* py-6 */
    }
    
    .faq-section.dark .faq-item {
        border-bottom-color: rgba(255, 255, 255, 0.1); /* dark:divide-white/10 */
    }
    
    .faq-item:first-child {
        padding-top: 0; /* first:pt-0 */
    }
    
    .faq-item:last-child {
        padding-bottom: 0; /* last:pb-0 */
        border-bottom: none;
    }
    
    .faq-question {
        padding: 0;
        cursor: pointer;
        display: flex;
        width: 100%;
        align-items: flex-start;
        justify-content: space-between;
        text-align: left;
        background: transparent;
        border: none;
        color: #111827; /* text-gray-900 */
        transition: all 0.2s ease;
    }
    
    .faq-section.dark .faq-question {
        color: #ffffff; /* dark:text-white */
    }
    
    .faq-question:hover {
        color: #374151;
    }
    
    .faq-section.dark .faq-question:hover {
        color: #e5e7eb;
    }
    
    .faq-question-text {
        font-size: 1rem; /* text-base */
        line-height: 1.43; /* /7 */
        font-weight: 600; /* font-semibold */
        flex: 1;
    }
    
    .faq-icon-container {
        margin-left: 1.5rem; /* ml-6 */
        display: flex;
        height: 1.75rem; /* h-7 */
        align-items: center;
        flex-shrink: 0;
    }
    
    .faq-icon {
        width: 1.5rem; /* size-6 */
        height: 1.5rem; /* size-6 */
        stroke: currentColor;
        stroke-width: 1.5;
        fill: none;
        transition: all 0.2s ease;
    }
    
    .faq-icon-plus {
        display: block;
    }
    
    .faq-icon-minus {
        display: none;
    }
    
    .faq-question[aria-expanded="true"] .faq-icon-plus {
        display: none;
    }
    
    .faq-question[aria-expanded="true"] .faq-icon-minus {
        display: block;
    }
    
    .faq-answer {
        margin-top: 0.5rem; /* mt-2 */
        padding-right: 3rem; /* pr-12 */
        display: none;
        transition: all 0.3s ease;
    }
    
    .faq-answer.active {
        display: block;
        padding-top: 0.5rem;
    }
    
    .faq-answer-content {
        color: #4b5563; /* text-gray-600 */
        font-size: 1rem; /* text-base */
        line-height: 1.43; /* /7 */
    }
    
    .faq-section.dark .faq-answer-content {
        color: #9ca3af; /* dark:text-gray-400 */
    }
    
    .faq-answer-content p {
        margin: 0 0 1rem 0;
    }
    
    .faq-answer-content p:last-child {
        margin-bottom: 0;
    }
    
    .faq-no-items {
        text-align: center;
        color: #6b7280;
        font-style: italic;
        padding: 2.5rem 1.25rem;
    }
    
    /* Grid Layout */
    .faq-section[data-layout="grid"] .faq-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 1.5rem;
        border: none;
    }
    
    .faq-section[data-layout="grid"] .faq-item {
        border: 1px solid rgba(17, 24, 39, 0.1);
        border-radius: 0.5rem;
        padding: 1.5rem;
        background: #ffffff;
    }
    
    .faq-section.dark[data-layout="grid"] .faq-item {
        border-color: rgba(255, 255, 255, 0.1);
        background: #1f2937;
    }
    
    /* Responsive Design */
    @media (max-width: 640px) {
        .faq-container-wrapper {
            padding: 1rem 1rem 4rem 1rem;
        }
        
        .faq-title {
            font-size: 1.875rem;
        }
        
        .faq-answer {
            padding-right: 0;
        }
        
        .faq-section[data-layout="grid"] .faq-container {
            grid-template-columns: 1fr;
        }
    }
    
    /* Focus styles for accessibility */
    .faq-question:focus {
        outline: 2px solid #3b82f6;
        outline-offset: 2px;
        border-radius: 0.25rem;
    }
    
    /* Smooth transitions */
    .faq-item {
        transition: all 0.2s ease;
    }
    
    .faq-answer {
        transition: max-height 0.3s ease, margin-top 0.3s ease, opacity 0.3s ease, padding-top 0.3s ease;
    }
    
    /* Ensure answers are visible when active */
    .faq-answer.active .faq-answer-content {
        display: block;
        visibility: visible;
    }
    </style>
    
    <!-- FAQ JavaScript -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const faqSection = document.getElementById('<?php echo esc_js($faq_id); ?>');
        if (!faqSection) return;
        
        const faqQuestions = faqSection.querySelectorAll('.faq-question');
        
        faqQuestions.forEach((question) => {
            question.addEventListener('click', function() {
                const faqItem = this.closest('.faq-item');
                const answer = faqItem.querySelector('.faq-answer');
                const isExpanded = this.getAttribute('aria-expanded') === 'true';
                
                // Toggle aria-expanded attribute
                this.setAttribute('aria-expanded', !isExpanded);
                
                // Toggle active class on answer
                if (isExpanded) {
                    answer.classList.remove('active');
                } else {
                    answer.classList.add('active');
                }
                
                // Smooth scroll to FAQ item if it's opening
                if (!isExpanded && faqSection.dataset.animation === 'true') {
                    setTimeout(() => {
                        faqItem.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }, 100);
                }
            });
        });
        
        // Keyboard navigation
        faqQuestions.forEach(question => {
            question.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.click();
                }
            });
        });
    });
    </script>
    
    <?php
    return ob_get_clean();
}

/**
 * Individual FAQ Item Shortcode
 * 
 * @param array $atts Shortcode attributes
 * @param string $content Shortcode content (the answer)
 * @return string HTML output
 */
function neways_faq_item_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'question' => '',
        'answer' => '',
        'icon' => 'plus', // plus, chevron, arrow
        'class' => '',
        'id' => ''
    ), $atts);
    
    // Use content as answer if answer attribute is not provided
    $answer = !empty($content) ? trim($content) : $atts['answer'];
    
    if (empty($atts['question']) || empty($answer)) {
        return '<p class="faq-error">FAQ item missing question or answer.</p>';
    }
    
    // Generate unique ID for the FAQ item
    $item_id = !empty($atts['id']) ? $atts['id'] : 'faq-item-' . uniqid();
    
    // Get the icon HTML
    $icon_html = neways_get_faq_icon($atts['icon']);
    
    ob_start();
    ?>
    <div class="faq-item <?php echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($item_id); ?>">
        <dt>
            <button class="faq-question" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($item_id); ?>-answer">
                <span class="faq-question-text"><?php echo esc_html($atts['question']); ?></span>
                <span class="faq-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="faq-icon faq-icon-plus" aria-hidden="true">
                        <path d="M12 6v12m6-6H6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="faq-icon faq-icon-minus" aria-hidden="true">
                        <path d="M18 12H6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </button>
        </dt>
        <dd class="faq-answer" id="<?php echo esc_attr($item_id); ?>-answer">
            <div class="faq-answer-content">
                <?php echo wpautop(do_shortcode($answer)); ?>
            </div>
        </dd>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Extract FAQ items from raw content (before shortcode processing)
 * 
 * @param string $content Raw shortcode content
 * @return array Array of FAQ items
 */
function neways_extract_faq_items_from_raw($content) {
    $faq_items = array();
    
    // Extract all faq_item shortcodes
    $pattern = '/\[faq_item\s+([^\]]+)\]([^\[]*(?:\[[^\]]*\][^\[]*)*)\[\/faq_item\]/s';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    
    if (empty($matches)) {
        // Try self-closing tags
        $pattern = '/\[faq_item\s+([^\]]+)\]/';
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    }
    
    foreach ($matches as $match) {
        $attributes_string = isset($match[1]) ? $match[1] : '';
        $content_part = isset($match[2]) ? $match[2] : '';
        
        // Parse attributes
        $attributes = shortcode_parse_atts($attributes_string);
        
        $question = isset($attributes['question']) ? $attributes['question'] : '';
        $answer = '';
        
        // Check if answer is in attributes or content
        if (isset($attributes['answer'])) {
            $answer = $attributes['answer'];
        } elseif (!empty($content_part)) {
            $answer = trim($content_part);
        }
        
        if (!empty($question)) {
            $faq_items[] = array(
                'question' => $question,
                'answer' => $answer,
                'icon' => isset($attributes['icon']) ? $attributes['icon'] : 'plus',
                'class' => isset($attributes['class']) ? $attributes['class'] : '',
                'id' => isset($attributes['id']) ? $attributes['id'] : ''
            );
        }
    }
    
    return $faq_items;
}

/**
 * Extract FAQ items from content (legacy function)
 * 
 * @param string $content Shortcode content
 * @return array Array of FAQ items
 */
function neways_extract_faq_items($content) {
    $faq_items = array();
    
    // Pattern to match [faq_item] shortcodes
    $pattern = '/\[faq_item\s+([^\]]+)\]([^\[]*(?:\[[^\]]*\][^\[]*)*)\[\/faq_item\]/';
    preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);
    
    foreach ($matches as $match) {
        $attributes = shortcode_parse_atts($match[1]);
        $answer = $match[2];
        
        $faq_items[] = array(
            'question' => isset($attributes['question']) ? $attributes['question'] : '',
            'answer' => $answer,
            'icon' => isset($attributes['icon']) ? $attributes['icon'] : 'plus',
            'class' => isset($attributes['class']) ? $attributes['class'] : '',
            'id' => isset($attributes['id']) ? $attributes['id'] : ''
        );
    }
    
    return $faq_items;
}

/**
 * Render individual FAQ item
 * 
 * @param array $item FAQ item data
 * @param int $index Item index
 * @param array $section_atts Section attributes
 * @return void
 */
function neways_render_faq_item($item, $index, $section_atts) {
    $item_id = !empty($item['id']) ? $item['id'] : 'faq-item-' . uniqid();
    
    ?>
    <div class="faq-item <?php echo esc_attr($item['class']); ?>" id="<?php echo esc_attr($item_id); ?>">
        <dt>
            <button class="faq-question" type="button" aria-expanded="false" aria-controls="<?php echo esc_attr($item_id); ?>-answer">
                <span class="faq-question-text"><?php echo esc_html($item['question']); ?></span>
                <span class="faq-icon-container">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="faq-icon faq-icon-plus" aria-hidden="true">
                        <path d="M12 6v12m6-6H6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="faq-icon faq-icon-minus" aria-hidden="true">
                        <path d="M18 12H6" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </button>
        </dt>
        <dd class="faq-answer" id="<?php echo esc_attr($item_id); ?>-answer">
            <div class="faq-answer-content">
                <?php echo wpautop(do_shortcode($item['answer'])); ?>
            </div>
        </dd>
    </div>
    <?php
}

/**
 * Get FAQ icon HTML
 * 
 * @param string $icon_type Icon type
 * @return string Icon HTML
 */
function neways_get_faq_icon($icon_type = 'plus') {
    switch ($icon_type) {
        case 'chevron':
            return '<svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6,9 12,15 18,9"></polyline>
                    </svg>';
        
        case 'arrow':
            return '<svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12,5 19,12 12,19"></polyline>
                    </svg>';
        
        case 'plus':
        default:
            return '<svg class="faq-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"></line>
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                    </svg>';
    }
}

// Register shortcodes
add_shortcode('faq_section', 'neways_faq_section_shortcode');
add_shortcode('faq_item', 'neways_faq_item_shortcode');

// Add shortcode to functions.php include
if (!function_exists('neways_load_faq_shortcodes')) {
    function neways_load_faq_shortcodes() {
        // This function can be used to conditionally load the shortcodes
        return true;
    }
}
?>
