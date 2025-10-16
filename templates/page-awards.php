<?php
/**
 * Template Name: Awards
 * 
 * Template for displaying awards with ACF integration and fixed height
 */

get_header(); ?>

<div class="awards-page">
    <div class="awards-container">
        <!-- Page Header -->
        <div class="awards-header">
            <h1 class="awards-title"><?php the_title(); ?></h1>
        </div>

        <!-- Awards Grid -->
        <div class="awards-grid">
            <?php
            // Get awards - try multiple methods
            $awards = get_field('awards');
            
            // If no awards from repeater, try individual fields
            if (empty($awards)) {
                $awards = array();
                for ($i = 1; $i <= 3; $i++) {
                    $award_field = get_field('award' . $i);
                    if (!empty($award_field)) {
                        $awards[] = $award_field;
                    }
                }
            }
            
            // Get manual heights
            $manual_heights = get_post_meta(get_the_ID(), 'manual_awards_heights', true);
            if (!is_array($manual_heights)) {
                $manual_heights = array();
            }

            // Debug info (remove this after testing)
            echo '<!-- DEBUG: Found ' . count($awards) . ' awards -->';
            if (!empty($awards)) {
                echo '<!-- First award: ' . print_r(array_keys($awards[0]), true) . ' -->';
            }
            
            if (!empty($awards) && is_array($awards)):
                $award_index = 0;
                foreach ($awards as $award):
                    // Handle direct image objects (your current setup)
                    $award_image = null;
                    $award_title = 'Award ' . ($award_index + 1);
                    $award_organization = '';
                    $award_year = '';
                    $award_category = '';
                    $award_description = '';
                    
                    if (is_array($award)) {
                        // Check if this is a direct image object
                        if (isset($award['url']) && isset($award['ID'])) {
                            // This is a direct image object
                            $award_image = $award;
                            $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($award_index + 1);
                        } 
                        // Check if this is an award with image subfield
                        elseif (isset($award['award_image'])) {
                            $award_image = $award['award_image'];
                            $award_title = isset($award['award_title']) ? $award['award_title'] : 'Award ' . ($award_index + 1);
                            $award_organization = isset($award['award_organization']) ? $award['award_organization'] : '';
                            $award_year = isset($award['award_year']) ? $award['award_year'] : '';
                            $award_category = isset($award['award_category']) ? $award['award_category'] : '';
                            $award_description = isset($award['award_description']) ? $award['award_description'] : '';
                        }
                        // Check other possible structures
                        elseif (isset($award['image'])) {
                            $award_image = $award['image'];
                            $award_title = isset($award['title']) ? $award['title'] : 'Award ' . ($award_index + 1);
                        }
                    }
                    
                    // Skip if no image found
                    if (empty($award_image)) {
                        $award_index++;
                        continue;
                    }
                    
                    // Get height from manual height control
                    $award_image_height = 230; // Default
                    if (isset($manual_heights[$award_index])) {
                        $award_image_height = intval($manual_heights[$award_index]);
                    }
                    
                    // Get image URL and dimensions
                    $img_url = '';
                    $img_alt = '';
                    $img_width = 0;
                    $img_height = 0;
                    
                    if (is_array($award_image)) {
                        $img_url = isset($award_image['url']) ? $award_image['url'] : '';
                        $img_alt = isset($award_image['alt']) ? $award_image['alt'] : $award_title;
                        $img_width = isset($award_image['width']) ? intval($award_image['width']) : 0;
                        $img_height = isset($award_image['height']) ? intval($award_image['height']) : 0;
                    } elseif (filter_var($award_image, FILTER_VALIDATE_URL)) {
                        $img_url = $award_image;
                        $img_alt = $award_title;
                    }
                    
                    // Calculate proportional width based on the height you set
                    $award_image_width = 200; // Default width
                    if ($img_width > 0 && $img_height > 0) {
                        $aspect_ratio = $img_width / $img_height;
                        $award_image_width = round($award_image_height * $aspect_ratio);
                    }
                    
                    // Display image directly without any div containers
                    if (!empty($img_url)): ?>
                        <img src="<?php echo esc_url($img_url); ?>" 
                             alt="<?php echo esc_attr($img_alt); ?>" 
                             class="award-image" 
                             style="height: <?php echo esc_attr($award_image_height); ?>px; width: <?php echo esc_attr($award_image_width); ?>px; object-fit: contain; margin: 10px;">
                    <?php endif;
                    
                    $award_index++;
                endforeach;
            else:
                ?>
                <div class="no-awards">
                    <div class="no-awards-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <h3>No Awards Yet</h3>
                    <p>Please add awards using the ACF fields below.</p>
                </div>
                <?php
            endif;
            ?>
        </div>

    </div>
</div>

<style>
.awards-page {
    min-height: 100vh;
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    padding: 40px 20px;
}

.awards-container {
    max-width: 1200px;
    margin: 0 auto;
}

/* Header Styles */
.awards-header {
    text-align: center;
    margin-bottom: 60px;
}

.awards-title {
    font-size: 3rem;
    font-weight: 700;
    color: #2d5016;
    margin: 0 0 20px 0;
    line-height: 1.2;
}


/* Awards Grid */
.awards-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 60px;
    padding: 20px;
}

.award-image {
    transition: transform 0.3s ease;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.award-image:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

/* No Awards State */
.no-awards {
    grid-column: 1 / -1;
    text-align: center;
    padding: 80px 20px;
    color: #666;
}

.no-awards-icon {
    width: 80px;
    height: 80px;
    color: #ddd;
    margin: 0 auto 20px auto;
}

.no-awards-icon svg {
    width: 100%;
    height: 100%;
}

.no-awards h3 {
    font-size: 1.5rem;
    margin: 0 0 10px 0;
    color: #333;
}

.no-awards p {
    font-size: 1rem;
    margin: 0;
}


/* Responsive Design */
@media (max-width: 768px) {
    .awards-page {
        padding: 20px 15px;
    }
    
    .awards-title {
        font-size: 2.25rem;
    }
    
    .awards-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .award-item {
        height: auto; /* Allow flexible height on mobile */
        min-height: 350px;
    }
    
    .award-image-container {
        height: 180px;
    }
    
    .award-content {
        padding: 20px;
    }
    
}

@media (max-width: 480px) {
    .awards-title {
        font-size: 2rem;
    }
    
    .award-item {
        min-height: 320px;
    }
    
    .award-image-container {
        height: 160px;
    }
    
    .award-content {
        padding: 16px;
    }
    
}
</style>

<?php get_footer(); ?>
