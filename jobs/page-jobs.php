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

<div class="jobs-page-container">
    <div class="container">
        <h1>Available Jobs</h1>
        
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
            <div class="jobs-filters">
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
                <select id="category-filter">
                    <option value="">All Job Category</option>
                    <?php foreach ($used_categories as $category) : ?>
                        <option value="<?php echo esc_attr(sanitize_title($category)); ?>"><?php echo esc_html($category); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="type-filter">
                    <option value="">All Job Type</option>
                    <?php foreach ($used_job_types as $job_type) : ?>
                        <option value="<?php echo esc_attr($job_type); ?>"><?php echo esc_html(ucfirst(str_replace('-', ' ', $job_type))); ?></option>
                    <?php endforeach; ?>
                </select>
                
                <select id="location-filter">
                    <option value="">All Job Location</option>
                    <?php foreach ($used_locations as $location) : ?>
                        <option value="<?php echo esc_attr($location); ?>"><?php echo esc_html($location); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="jobs-list">
                <?php while ($jobs_query->have_posts()) : $jobs_query->the_post(); ?>
                    <?php 
                    $category = get_post_meta(get_the_ID(), '_job_category', true);
                    $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                    $location = get_post_meta(get_the_ID(), '_job_location', true);
                    
                    // Get activation date or fallback to post date
                    $activation_date = get_post_meta(get_the_ID(), '_job_activation_date', true);
                    $display_date = $activation_date ? $activation_date : get_the_date('Y-m-d H:i:s');
                    ?>
                    <?php $category_slug = $category ? sanitize_title($category) : ''; ?>
                    <div class="job-item" 
                         data-category="<?php echo esc_attr($category_slug); ?>"
                         data-type="<?php echo esc_attr($job_type); ?>"
                         data-location="<?php echo esc_attr($location); ?>">
                        
                        <div class="job-header">
                            <h2><?php the_title(); ?></h2>
                            <div class="job-meta">
                                <?php if ($category) : ?>
                                    <span class="category"><?php echo ucfirst(str_replace('-', ' ', $category)); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($job_type) : ?>
                                    <span class="type"><?php echo ucfirst(str_replace('-', ' ', $job_type)); ?></span>
                                <?php endif; ?>
                                
                                <?php if ($location) : ?>
                                    <span class="location">📍 <?php echo esc_html($location); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="job-content">
                            <?php the_excerpt(); ?>
                        </div>
                        
                        <div class="job-actions">
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">View Details</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
            
        <?php else : ?>
            <div class="no-jobs">
                <p>No jobs available at the moment. Please check back later.</p>
            </div>
        <?php endif; 
        
        wp_reset_postdata();
        ?>
    </div>
</div>

<style>
.jobs-page-container {
    padding: 40px 0;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.jobs-filters {
    display: flex;
    gap: 20px;
    margin: 30px 0;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 8px;
}

.jobs-filters select {
    flex: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    background: white;
}

.jobs-list {
    display: grid;
    gap: 30px;
}

.job-item {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 30px;
    background: white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.job-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.job-header h2 {
    margin: 0 0 15px 0;
    color: #333;
    font-size: 24px;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 20px;
}

.job-meta span {
    background: #f0f0f0;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 14px;
    color: #666;
}


.job-content {
    margin-bottom: 20px;
    line-height: 1.6;
    color: #555;
}

.job-actions {
    display: flex;
    gap: 15px;
}

.btn {
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 5px;
    font-weight: 500;
    transition: background-color 0.2s ease;
}

.btn-primary {
    background: #0073aa;
    color: white;
}

.btn-primary:hover {
    background: #005a87;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.no-jobs {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

/* Filter functionality */
.job-item.hidden {
    display: none;
}

/* Responsive design */
@media (max-width: 1024px) {
    .jobs-filters {
        gap: 15px;
        flex-wrap: wrap;
    }
    
    .job-filter {
        width: 180px;
    }
}

@media (max-width: 768px) {
    .jobs-filters {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .job-filter {
        width: 100%;
        max-width: 100%;
    }
    
    .job-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 15px;
    }
    
    .job-actions {
        margin-left: 0;
        width: 100%;
    }
    
    .btn {
        justify-content: center;
        width: 100%;
        padding: 12px;
        border: 1px solid #0073aa;
        border-radius: 6px;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .jobs-listing-container {
        padding: 10px;
    }
    
    .jobs-filters {
        padding: 15px 0;
        gap: 8px;
    }
    
    .job-filter {
        height: 44px;
        font-size: 14px;
    }
    
    .job-item {
        padding: 15px;
        gap: 12px;
    }
    
    .job-title {
        font-size: 16px;
        line-height: 1.3;
    }
    
    .job-meta {
        font-size: 12px;
        margin-bottom: 8px;
    }
    
    .btn {
        padding: 10px;
        font-size: 14px;
    }
}
</style>

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
