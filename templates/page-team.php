<?php
/**
 * Template Name: Team Page
 * 
 * This template displays team members in a responsive layout
 */

get_header(); ?>

<div class="bg-white py-24 sm:py-32 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="mx-auto max-w-2xl lg:mx-0">
            <h2 class="text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl dark:text-white">Our team</h2>
            <p class="mt-6 text-lg/8 text-gray-600 dark:text-gray-400">We're a dynamic group of individuals who are passionate about what we do and dedicated to delivering the best results for our clients.</p>
        </div>
        
        <?php
        // Query team members
        $team_query = new WP_Query(array(
            'post_type' => 'team',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ));
        
        if ($team_query->have_posts()) :
                ?>
                <ul role="list" class="mx-auto mt-20 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-14 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3 xl:grid-cols-4">
                    <?php
                    while ($team_query->have_posts()) : $team_query->the_post();
                        // Get ACF fields
                        $name = '';
                        $designation = '';
                        $image = '';
                        $show_on_homepage = false;
                        
                        if (function_exists('get_field')) {
                            $name = get_field('name', get_the_ID());
                            $designation = get_field('designation', get_the_ID());
                            $image = get_field('image', get_the_ID());
                            $show_on_homepage = get_field('show_on_homepage', get_the_ID());
                        }
                        
                        // Use post title as fallback for name
                        if (empty($name)) {
                            $name = get_the_title();
                        }
                        ?>
                        <li>
                            <?php 
                            // Use ACF image if available, otherwise fallback to featured image
                            if (!empty($image) && is_array($image)) {
                                $image_url = $image['sizes']['large'] ?? $image['url'];
                                $image_alt = $image['alt'] ?? $name;
                                echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($image_alt) . '" class="aspect-[14/13] w-full rounded-2xl object-cover outline outline-1 -outline-offset-1 outline-black/5 dark:outline-white/10" />';
                            } elseif (has_post_thumbnail()) {
                                echo get_the_post_thumbnail(get_the_ID(), 'large', array('class' => 'aspect-[14/13] w-full rounded-2xl object-cover outline outline-1 -outline-offset-1 outline-black/5 dark:outline-white/10'));
                            } else {
                                ?>
                                <div class="aspect-[14/13] w-full rounded-2xl bg-gray-200 dark:bg-gray-700 flex items-center justify-center outline outline-1 -outline-offset-1 outline-black/5 dark:outline-white/10">
                                    <svg class="w-16 h-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <?php
                            }
                            ?>
                            <h3 class="mt-6 text-lg/8 font-semibold tracking-tight text-gray-900 dark:text-white">
                                <?php echo esc_html($name); ?>
                            </h3>
                            <p class="text-base/7 text-gray-600 dark:text-gray-300">
                                <?php echo esc_html($designation); ?>
                            </p>
                        </li>
                        <?php
                    endwhile;
                    wp_reset_postdata();
                    ?>
                </ul>
                <?php
        else :
            ?>
            <div class="mt-20 text-center">
                <p class="text-gray-500">No team members found.</p>
            </div>
            <?php
        endif;
        ?>
    </div>
</div>

<?php get_footer(); ?>
