<?php
/**
 * Template Name: Jobs Listing Page
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your theme folder
 * 2. Rename it to "page-jobs.php"
 * 3. Create a page called "Jobs" and assign this template
 * 4. Or use the shortcode [jobs_listing] on any page
 */

get_header(); ?>

<div class="min-h-screen py-10 lg:py-16">
    <div class="max-w-[1280px] mx-auto px-4 py-16 sm:px-6 relative z-10">
        <div class="text-left mb-14 mx-auto max-w-2xl lg:mx-0">
                <h2>
                    Available Jobs
                </h2>
                <p class="paragraph">
                    Explore current openings at TOV and find a role that matches your skills. Join us and be part of a growing, creative, and collaborative team.
                </p>
            </div>
        
        <?php
        // Get all published jobs (only active ones)
        $jobs_query = new WP_Query(array(
            'post_type' => 'jobs',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_job_status',
                        'value' => 'active',
                        'compare' => '='
                    ),
                    array(
                        'key' => '_job_status',
                        'compare' => 'NOT EXISTS'
                    ),
                    array(
                        'key' => '_job_status',
                        'value' => '',
                        'compare' => '='
                    )
                )
            )
        ));
        
        // Manual filtering as backup - remove any inactive jobs that might have slipped through
        $filtered_posts = array();
        if ($jobs_query->posts) {
            foreach ($jobs_query->posts as $post) {
                $job_status = get_post_meta($post->ID, '_job_status', true);
                
                // Only include jobs that are not explicitly inactive
                if ($job_status !== 'inactive') {
                    $filtered_posts[] = $post;
                }
            }
        }
        
        // Replace the query posts with our filtered results
        $jobs_query->posts = $filtered_posts;
        $jobs_query->post_count = count($filtered_posts);
        
        if ($jobs_query->have_posts()) : ?>
            <!-- Filters -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-5 mb-6 rounded-lg w-full sm:w-auto md:w-[600px]">
                <?php 
                // Get unique categories, locations, and job types from actual job posts
                $used_categories = array();
                $used_locations = array();
                $used_job_types = array();
                
                if ($jobs_query->posts) {
                    foreach ($jobs_query->posts as $post) {
                        $category = get_post_meta($post->ID, '_job_category', true);
                        $location = get_post_meta($post->ID, '_job_location', true);
                        $job_type = get_post_meta($post->ID, '_job_type', true);
                        
                        if ($category && !in_array($category, $used_categories)) {
                            $used_categories[] = $category;
                        }
                        
                        if ($location && !in_array($location, $used_locations)) {
                            $used_locations[] = $location;
                        }
                        
                        if ($job_type && !in_array($job_type, $used_job_types)) {
                            $used_job_types[] = $job_type;
                        }
                    }
                }
                
                // Sort arrays for better display
                sort($used_categories);
                sort($used_locations);
                sort($used_job_types);
                ?>
                <select id="category-filter" class="w-full sm:w-[250px] px-4 py-3 border border-gray-300 rounded-md bg-white font-lato text-base focus:outline-none focus:ring-2 focus:ring-[#016A7C] focus:border-transparent transition-all">
                    <option value="">All Job Category</option>
                    <?php foreach ($used_categories as $category) : ?>
                        <option value="<?php echo esc_attr(sanitize_title($category)); ?>"><?php echo esc_html($category); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="type-filter" class="w-full sm:w-[250px] px-4 py-3 border border-gray-300 rounded-md bg-white font-lato text-base focus:outline-none focus:ring-2 focus:ring-[#016A7C] focus:border-transparent transition-all">
                    <option value="">All Job Type</option>
                    <?php foreach ($used_job_types as $job_type) : ?>
                        <option value="<?php echo esc_attr($job_type); ?>"><?php echo esc_html(ucfirst(str_replace('-', ' ', $job_type))); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="location-filter" class="w-full sm:w-[250px] px-4 py-3 border border-gray-300 rounded-md bg-white font-lato text-base focus:outline-none focus:ring-2 focus:ring-[#016A7C] focus:border-transparent transition-all">
                    <option value="">All Job Location</option>
                    <?php foreach ($used_locations as $location) : ?>
                        <option value="<?php echo esc_attr($location); ?>"><?php echo esc_html($location); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Job Listings -->
            <ul role="list" class="divide-y divide-gray-100 border-b border-[#232361] border-opacity-20">
                <?php while ($jobs_query->have_posts()) : $jobs_query->the_post(); ?>
                    <?php 
                    $category = get_post_meta(get_the_ID(), '_job_category', true);
                    $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                    $location = get_post_meta(get_the_ID(), '_job_location', true);
                    $short_description = get_post_meta(get_the_ID(), '_job_short_description', true);
                    
                    // Get activation date or fallback to post date
                    $activation_date = get_post_meta(get_the_ID(), '_job_activation_date', true);
                    $display_date = $activation_date ? $activation_date : get_the_date('Y-m-d H:i:s');
                    ?>
                    <?php $category_slug = $category ? sanitize_title($category) : ''; ?>
                    <li class="job-item flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-6 cursor-pointer  transition-colors duration-200 rounded-lg px-4 -mx-4" 
                         data-category="<?php echo esc_attr($category_slug); ?>"
                         data-type="<?php echo esc_attr($job_type); ?>"
                         data-location="<?php echo esc_attr($location); ?>"
                         data-url="<?php echo esc_url(get_permalink()); ?>"
                         onclick="window.location.href='<?php echo esc_url(get_permalink()); ?>'">
                        <div class="min-w-0 flex-1">
                            <div class="mb-2">
                                <h4><?php the_title(); ?></h4>
                            </div>
                            
                            <?php if (!empty($short_description)) : ?>
                                <p class="paragraph pb-[12px]" style="width: 715px; max-width: 100%;"><?php echo esc_html($short_description); ?></p>
                            <?php endif; ?>
                            
                            <div class="flex flex-wrap items-center gap-2 mt-[12px]">
                                <?php if ($category) : ?>
                                    <span class="flex-none rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-black/80 "><?php echo esc_html($category); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($job_type) : ?>
                                    <span class="flex-none rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-black/80 "><?php echo ucfirst(str_replace('-', ' ', $job_type)); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($location) : ?>
                                    <span class="flex-none rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-black/80 "><?php echo esc_html($location); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary bt-1 px-4 py-2 hover:text-black" onclick="event.stopPropagation();">
                                View Details
                            </a>
                        </div>
                    </li>
                <?php endwhile; ?>
            </ul>
            
        <?php else : ?>
            <div class="text-center py-16">
                <p class="paragraph">No jobs available at the moment. Please check back later.</p>
            </div>
        <?php endif; 
        
        wp_reset_postdata();
        ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('category-filter');
    const typeFilter = document.getElementById('type-filter');
    const locationFilter = document.getElementById('location-filter');
    const jobItems = document.querySelectorAll('.job-item');
    
    function filterJobs() {
        const selectedCategory = categoryFilter.value;
        const selectedType = typeFilter.value;
        const selectedLocation = locationFilter.value;
        
        jobItems.forEach(item => {
            const itemCategory = item.dataset.category;
            const itemType = item.dataset.type;
            const itemLocation = item.dataset.location;
            
            const categoryMatch = !selectedCategory || itemCategory === selectedCategory;
            const typeMatch = !selectedType || itemType === selectedType;
            const locationMatch = !selectedLocation || itemLocation.toLowerCase().includes(selectedLocation.toLowerCase());
            
            if (categoryMatch && typeMatch && locationMatch) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }
    
    categoryFilter.addEventListener('change', filterJobs);
    typeFilter.addEventListener('change', filterJobs);
    locationFilter.addEventListener('change', filterJobs);
});
</script>

<?php get_footer(); ?>
