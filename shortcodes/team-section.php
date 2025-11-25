<?php
/**
 * Team Section Shortcode
 * Usage: [team_section]
 */

if (!defined('ABSPATH')) exit;

/**
 * Get the highlighted team member
 */
function neways_get_highlighted_team_member() {
    $args = array(
        'post_type' => 'team',
        'posts_per_page' => 1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_is_highlighted',
                'value' => '1',
                'compare' => '='
            )
        )
    );
    
    $query = new WP_Query($args);
    
    if ($query->have_posts()) {
        $query->the_post();
        $post_id = get_the_ID();
        wp_reset_postdata();
        return $post_id;
    }
    
    return false;
}

function neways_team_section_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'title' => 'Our Team',
        'subtitle' => 'Meet the amazing people behind our success',
        'posts_per_page' => 12,
        'department' => '',
        'orderby' => 'menu_order',
        'order' => 'ASC',
        'layout' => 'grid', // grid, list, carousel, admin-style
        'style' => 'modern', // modern, admin-style, minimal
        'highlighted_only' => 'false' // true, false - show only highlighted team member
    ), $atts);

    // Check if ACF is available
    $use_acf = function_exists('get_field');
    
    // Build query args
    $args = array(
        'post_type' => 'team',
        'posts_per_page' => intval($atts['posts_per_page']),
        'post_status' => 'publish',
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
    );

    // Add taxonomy filters if specified
    if (!empty($atts['department'])) {
        $args['tax_query'][] = array(
            'taxonomy' => 'team_department',
            'field'    => 'slug',
            'terms'    => $atts['department'],
        );
    }

    
    // Add highlighted filter if specified
    if ($atts['highlighted_only'] === 'true') {
        $args['meta_query'] = array(
            array(
                'key' => '_is_highlighted',
                'value' => '1',
                'compare' => '='
            )
        );
    }


    $team_query = new WP_Query($args);

    if (!$team_query->have_posts()) {
        return '<div class="text-center py-12"><p class="text-gray-500">No team members found.</p></div>';
    }

    ob_start();
    ?>
    <div class="team-section bg-white dark:bg-gray-900 py-16 sm:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                    <?php echo esc_html($atts['title']); ?>
                </h2>
                <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400">
                    <?php echo esc_html($atts['subtitle']); ?>
                </p>
            </div>
            
            <?php
            // Determine layout classes based on layout attribute
            $layout_classes = '';
            if ($atts['layout'] === 'list') {
                $layout_classes = 'space-y-8 max-w-4xl mx-auto';
            } elseif ($atts['layout'] === 'carousel') {
                $layout_classes = 'grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3';
            } elseif ($atts['layout'] === 'admin-style') {
                $layout_classes = 'grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
            } else {
                $layout_classes = 'grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4';
            }
            ?>
            
            <div class="<?php echo esc_attr($layout_classes); ?>">
                <?php while ($team_query->have_posts()) : $team_query->the_post(); ?>
                    <?php 
                    if ($atts['highlighted_only'] === 'true') {
                        // Use special highlighted card for featured member
                        neways_render_highlighted_team_card(get_the_ID(), $use_acf);
                    } elseif ($atts['layout'] === 'list') {
                        neways_render_team_list_item(get_the_ID(), $use_acf);
                    } elseif ($atts['layout'] === 'admin-style') {
                        neways_render_team_admin_style(get_the_ID(), $use_acf);
                    } else {
                        neways_render_team_card(get_the_ID(), $use_acf);
                    }
                    ?>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <?php
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('team_section', 'neways_team_section_shortcode');

/**
 * Render team member as list item (for list layout)
 */
function neways_render_team_list_item($post_id, $use_acf = false) {
    // Get team member details from ACF fields
    $name = '';
    $designation = '';
    $image = '';
    $message = '';
    $message_visibility = true;
    
    // Get ACF fields using your specific field names
    if ($use_acf && function_exists('get_field')) {
        $name = get_field('name', $post_id);
        $designation = get_field('designation', $post_id);
        $image = get_field('image', $post_id);
        $message = get_field('message', $post_id);
        $message_visibility = get_field('message_visibility', $post_id);
    }
    
    // Use post title as fallback for name
    if (empty($name)) {
        $name = get_the_title($post_id);
    }
    ?>
    <div class="team-member-list-item flex items-center space-x-6 p-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex-shrink-0">
            <div class="relative w-20 h-20">
                <?php 
                // Use ACF image if available, otherwise fallback to featured image
                if (!empty($image) && is_array($image)) {
                    $image_url = $image['sizes']['thumbnail'] ?? $image['url'];
                    $image_alt = $image['alt'] ?? $name;
                    echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" class="w-full h-full rounded-full object-cover" />';
                } elseif (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, 'thumbnail', array('class' => 'w-full h-full rounded-full object-cover'));
                } else {
                    ?>
                    <div class="w-full h-full rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        
        <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                <?php echo esc_html($name); ?>
            </h3>
            
            <?php if (!empty($designation)) : ?>
                <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                    <?php echo esc_html($designation); ?>
                </p>
            <?php endif; ?>
            
            <?php if (!empty($message) && $message_visibility) : ?>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 italic">
                    "<?php echo esc_html($message); ?>"
                </p>
            <?php endif; ?>
        </div>
    </div>
    <?php
}

/**
 * Render highlighted team member with special styling
 */
function neways_render_highlighted_team_card($post_id, $use_acf = false) {
    // Get team member details from ACF fields
    $name = '';
    $designation = '';
    $image = '';
    $message = '';
    $message_visibility = true;
    
    // Get ACF fields using your specific field names
    if ($use_acf && function_exists('get_field')) {
        $name = get_field('name', $post_id);
        $designation = get_field('designation', $post_id);
        $image = get_field('image', $post_id);
        $message = get_field('message', $post_id);
        $message_visibility = get_field('message_visibility', $post_id);
    }
    
    // Use post title as fallback for name
    if (empty($name)) {
        $name = get_the_title($post_id);
    }
    
    // Get departments
    $departments = get_the_terms($post_id, 'team_department');
    $department_names = array();
    if ($departments && !is_wp_error($departments)) {
        foreach ($departments as $dept) {
            $department_names[] = $dept->name;
        }
    }
    ?>
    <div class="highlighted-team-card bg-gradient-to-br from-yellow-50 to-orange-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl shadow-xl border-2 border-yellow-200 dark:border-yellow-600 p-8 relative overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-100 dark:bg-yellow-900 rounded-full -translate-y-16 translate-x-16 opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-orange-100 dark:bg-orange-900 rounded-full translate-y-12 -translate-x-12 opacity-20"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row items-center lg:items-start space-y-6 lg:space-y-0 lg:space-x-8">
            <!-- Image Section -->
            <div class="flex-shrink-0">
                <div class="relative w-48 h-48 lg:w-56 lg:h-56">
                    <?php 
                    // Use ACF image if available, otherwise fallback to featured image
                    if (!empty($image) && is_array($image)) {
                        $image_url = $image['sizes']['large'] ?? $image['url'];
                        $image_alt = $image['alt'] ?? $name;
                        echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" class="w-full h-full rounded-2xl object-cover border-4 border-yellow-300 dark:border-yellow-500 shadow-lg" />';
                    } elseif (has_post_thumbnail($post_id)) {
                        echo get_the_post_thumbnail($post_id, 'large', array('class' => 'w-full h-full rounded-2xl object-cover border-4 border-yellow-300 dark:border-yellow-500 shadow-lg'));
                    } else {
                        ?>
                        <div class="w-full h-full rounded-2xl bg-gray-200 dark:bg-gray-600 flex items-center justify-center border-4 border-yellow-300 dark:border-yellow-500 shadow-lg">
                            <svg class="w-24 h-24 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <?php
                    }
                    ?>
                    
                </div>
            </div>
            
            <!-- Content Section -->
            <div class="flex-1 text-center lg:text-left">
                <h3 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-2">
                    <?php echo esc_html($name); ?>
                </h3>
                
                <?php if (!empty($designation)) : ?>
                    <p class="text-xl text-yellow-600 dark:text-yellow-400 font-semibold mb-4">
                        <?php echo esc_html($designation); ?>
                    </p>
                <?php endif; ?>
                
                <?php if (!empty($department_names)) : ?>
                    <div class="flex flex-wrap justify-center lg:justify-start gap-2 mb-6">
                        <?php foreach ($department_names as $dept_name) : ?>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-200 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-200">
                                <?php echo esc_html($dept_name); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($message) && $message_visibility) : ?>
                    <div class="text-lg text-gray-700 dark:text-gray-300 mb-6 max-w-2xl mx-auto lg:mx-0">
                        <blockquote class="border-l-4 border-yellow-400 pl-6 italic">
                            "<?php echo esc_html($message); ?>"
                        </blockquote>
                    </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
    <?php
}

/**
 * Team Member Single Shortcode
 * Usage: [team_member id="123"]
 */
function neways_team_member_shortcode($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
        'name' => '',
        'use_acf' => 'true'
    ), $atts);

    if (empty($atts['id']) && empty($atts['name'])) {
        return '<p class="text-red-500">Error: Team member ID or name is required.</p>';
    }

    // Check if ACF is available
    $use_acf = $atts['use_acf'] === 'true' && function_exists('get_field');

    $args = array(
        'post_type' => 'team',
        'post_status' => 'publish',
        'posts_per_page' => 1,
    );

    if (!empty($atts['id'])) {
        $args['p'] = intval($atts['id']);
    } else {
        $args['name'] = sanitize_text_field($atts['name']);
    }

    $team_query = new WP_Query($args);

    if (!$team_query->have_posts()) {
        return '<p class="text-red-500">Team member not found.</p>';
    }

    ob_start();
    while ($team_query->have_posts()) {
        $team_query->the_post();
        neways_render_team_card(get_the_ID(), $use_acf);
    }
    wp_reset_postdata();
    
    return ob_get_clean();
}
add_shortcode('team_member', 'neways_team_member_shortcode');

/**
 * Team Department Filter Shortcode
 * Usage: [team_departments]
 */
function neways_team_departments_shortcode($atts) {
    $atts = shortcode_atts(array(
        'show_count' => 'true',
        'style' => 'list' // list, buttons, dropdown
    ), $atts);

    $departments = get_terms(array(
        'taxonomy' => 'team_department',
        'hide_empty' => true,
    ));

    if (empty($departments) || is_wp_error($departments)) {
        return '<p class="text-gray-500">No departments found.</p>';
    }

    ob_start();
    
    if ($atts['style'] === 'buttons') {
        echo '<div class="flex flex-wrap gap-2">';
        foreach ($departments as $department) {
            $count = $atts['show_count'] === 'true' ? ' (' . $department->count . ')' : '';
        echo '<a href="' . esc_url(get_term_link($department)) . '" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors duration-200">';
            echo esc_html($department->name) . $count;
            echo '</a>';
        }
        echo '</div>';
    } elseif ($atts['style'] === 'dropdown') {
        echo '<select class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">';
        echo '<option value="">All Departments</option>';
        foreach ($departments as $department) {
            $count = $atts['show_count'] === 'true' ? ' (' . $department->count . ')' : '';
            echo '<option value="' . esc_url(get_term_link($department)) . '">' . esc_html($department->name) . $count . '</option>';
        }
        echo '</select>';
    } else {
        echo '<ul class="space-y-2">';
        foreach ($departments as $department) {
            $count = $atts['show_count'] === 'true' ? ' (' . $department->count . ')' : '';
            echo '<li><a href="' . esc_url(get_term_link($department)) . '" class="text-blue-600 hover:text-blue-800 transition-colors duration-200">';
            echo esc_html($department->name) . $count;
            echo '</a></li>';
        }
        echo '</ul>';
    }
    
    return ob_get_clean();
}
add_shortcode('team_departments', 'neways_team_departments_shortcode');

/**
 * Highlighted Team Member Shortcode
 * Usage: [highlighted_team_member]
 */
function neways_highlighted_team_member_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Team Member Spotlight',
        'subtitle' => 'Meet our team member',
        'layout' => 'card', // card, list, admin-style
        'style' => 'modern', // modern, admin-style, minimal
        'show_title' => 'true',
        'show_subtitle' => 'true'
    ), $atts);
    
    // Get the highlighted team member
    $highlighted_id = neways_get_highlighted_team_member();
    
    if (!$highlighted_id) {
        return '<div class="text-center py-8"><p class="text-gray-500">No featured team member selected.</p></div>';
    }
    
    // Check if ACF is available
    $use_acf = function_exists('get_field');
    
    ob_start();
    ?>
    <div class="highlighted-team-member bg-white dark:bg-gray-900 py-16 sm:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <?php if ($atts['show_title'] === 'true') : ?>
                <div class="text-center mb-12">
                    <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-4xl">
                        <?php echo esc_html($atts['title']); ?>
                    </h2>
                    <?php if ($atts['show_subtitle'] === 'true') : ?>
                        <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400">
                            <?php echo esc_html($atts['subtitle']); ?>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
            
            <?php
            // Render based on layout
            if ($atts['layout'] === 'list') {
                neways_render_team_list_item($highlighted_id, $use_acf);
            } elseif ($atts['layout'] === 'admin-style') {
                neways_render_team_admin_style($highlighted_id, $use_acf);
            } else {
                // Enhanced card layout for highlighted member
                neways_render_highlighted_team_card($highlighted_id, $use_acf);
            }
            ?>
        </div>
    </div>
    <?php
    
    return ob_get_clean();
}
add_shortcode('highlighted_team_member', 'neways_highlighted_team_member_shortcode');

/**
 * Render team member in admin-style layout
 */
function neways_render_team_admin_style($post_id, $use_acf = false) {
    // Get team member details from ACF fields
    $name = '';
    $designation = '';
    $image = '';
    $message = '';
    $message_visibility = true;
    
    // Get ACF fields using your specific field names
    if ($use_acf && function_exists('get_field')) {
        $name = get_field('name', $post_id);
        $designation = get_field('designation', $post_id);
        $image = get_field('image', $post_id);
        $message = get_field('message', $post_id);
        $message_visibility = get_field('message_visibility', $post_id);
    }
    
    // Use post title as fallback for name
    if (empty($name)) {
        $name = get_the_title($post_id);
    }
    
    // Get departments
    $departments = get_the_terms($post_id, 'team_department');
    $department_names = array();
    if ($departments && !is_wp_error($departments)) {
        foreach ($departments as $dept) {
            $department_names[] = $dept->name;
        }
    }
    ?>
    <div class="team-member-admin-card bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow duration-200">
        <!-- Header with image and name -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0">
                    <div class="w-16 h-16 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <?php 
                        // Use ACF image if available, otherwise fallback to featured image
                        if (!empty($image) && is_array($image)) {
                            $image_url = $image['sizes']['thumbnail'] ?? $image['url'];
                            $image_alt = $image['alt'] ?? $name;
                            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" class="w-full h-full object-cover" />';
                        } elseif (has_post_thumbnail($post_id)) {
                            echo get_the_post_thumbnail($post_id, 'thumbnail', array('class' => 'w-full h-full object-cover'));
                        } else {
                            ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white truncate">
                        <?php echo esc_html($name); ?>
                    </h3>
                    <?php if (!empty($designation)) : ?>
                        <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">
                            <?php echo esc_html($designation); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Content section -->
        <div class="p-6">
            <?php if (!empty($message) && $message_visibility) : ?>
                <div class="mb-4">
                    <blockquote class="text-sm text-gray-600 dark:text-gray-400 italic border-l-4 border-blue-500 pl-4">
                        "<?php echo esc_html($message); ?>"
                    </blockquote>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($department_names)) : ?>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($department_names as $dept_name) : ?>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            <?php echo esc_html($dept_name); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Footer with action buttons -->
        <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    <?php echo get_the_date('M j, Y', $post_id); ?>
                </span>
                <div class="flex space-x-2">
                    <a href="<?php echo get_permalink($post_id); ?>" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                        View Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}