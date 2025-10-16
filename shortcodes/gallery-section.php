<?php
/**
 * Gallery Section Shortcode
 * 
 * Displays a responsive gallery with up to 6 images.
 * Usage: [neways_gallery columns="2" lightbox="true" size="medium"]
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Gallery shortcode function
 */
function neways_gallery_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(array(
        'columns' => '2',
        'lightbox' => 'true',
        'size' => 'medium'
    ), $atts);
    
    // Get gallery images
    $gallery_images = get_option('neways_gallery_images', array());
    
    if (empty($gallery_images)) {
        return '<p>' . esc_html__('No gallery images found.', 'neways') . '</p>';
    }
    
    // Reverse the array so the last uploaded image shows first
    $gallery_images = array_reverse($gallery_images);
    
    // Validate columns
    $columns = intval($atts['columns']);
    if ($columns < 1) $columns = 1;
    if ($columns > 3) $columns = 3;
    
    // Validate lightbox
    $lightbox = ($atts['lightbox'] === 'true');
    
    // Validate size
    $valid_sizes = array('thumbnail', 'medium', 'large', 'full');
    $size = in_array($atts['size'], $valid_sizes) ? $atts['size'] : 'medium';
    
    // Generate unique ID for this gallery instance
    $gallery_id = 'gallery-' . uniqid();

    ob_start();
    ?>
    <div class="neways-gallery-container" id="<?php echo esc_attr($gallery_id); ?>">
        <div class="gallery-grid gallery-columns-<?php echo esc_attr($columns); ?>">
            <?php foreach ($gallery_images as $index => $image): ?>
                <div class="gallery-item">
                    <?php if ($lightbox): ?>
                        <a href="<?php echo esc_url($image['url']); ?>" 
                           class="gallery-lightbox" 
                           data-gallery="<?php echo esc_attr($gallery_id); ?>" 
                           data-title="<?php echo esc_attr($image['title']); ?>">
                    <?php endif; ?>
                    
                    <img src="<?php echo esc_url($image['url']); ?>" 
                         alt="<?php echo esc_attr($image['alt']); ?>" 
                         class="gallery-image"
                         loading="lazy">
                    
                    <?php if ($lightbox): ?>
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <svg class="gallery-zoom-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        </div>
                        </a>
                        <?php endif; ?>
                </div>
            <?php endforeach; ?>
                        </div>
                    </div>
                    
    <style>
    .neways-gallery-container {
        margin: 20px 0;
        max-width: 1200px;
        margin-left: auto;
        margin-right: auto;
    }
    
    .gallery-grid {
        display: grid;
        gap: 20px;
        grid-template-columns: repeat(<?php echo esc_attr($columns); ?>, 1fr);
    }
    
    .gallery-columns-1 {
        grid-template-columns: 1fr;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .gallery-columns-2 {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .gallery-columns-3 {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .gallery-item:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
    }
    
    .gallery-image {
        width: 100%;
        height: 250px;
        object-fit: cover;
        display: block;
        transition: transform 0.3s ease;
    }
    
    .gallery-item:hover .gallery-image {
        transform: scale(1.05);
    }
    
    .gallery-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .gallery-item:hover .gallery-overlay {
        opacity: 1;
    }
    
    .gallery-zoom-icon {
        width: 32px;
        height: 32px;
        color: white;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .gallery-columns-2,
        .gallery-columns-3 {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .gallery-image {
            height: 200px;
        }
    }
    
    @media (max-width: 480px) {
        .gallery-columns-1,
        .gallery-columns-2,
        .gallery-columns-3 {
            grid-template-columns: 1fr;
        }
        
        .gallery-image {
            height: 250px;
        }
    }
    </style>
    
    <?php if ($lightbox): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize lightbox for this gallery
        initGalleryLightbox('<?php echo esc_js($gallery_id); ?>');
    });
    
    function initGalleryLightbox(galleryId) {
        const gallery = document.getElementById(galleryId);
        if (!gallery) return;
        
        const lightboxLinks = gallery.querySelectorAll('.gallery-lightbox');
        let lightboxOverlay = document.querySelector('.gallery-lightbox-overlay');
        
        // Create lightbox if it doesn't exist
        if (!lightboxOverlay) {
            lightboxOverlay = document.createElement('div');
            lightboxOverlay.className = 'gallery-lightbox-overlay';
            lightboxOverlay.innerHTML = `
                <div class="gallery-lightbox-content">
                    <button class="gallery-lightbox-close">&times;</button>
                    <button class="gallery-lightbox-prev">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                            </button>
                    <button class="gallery-lightbox-next">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
            </button>
                    <img src="" alt="">
                    <div class="gallery-lightbox-caption"></div>
        </div>
            `;
            document.body.appendChild(lightboxOverlay);
            
            // Add lightbox styles
            const style = document.createElement('style');
            style.textContent = `
                .gallery-lightbox-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.9);
                    z-index: 999999;
                    display: none;
                    align-items: center;
                    justify-content: center;
                }
                
                .gallery-lightbox-content {
                    position: relative;
                    max-width: 90%;
                    max-height: 90%;
                    text-align: center;
                }
                
                .gallery-lightbox-content img {
                    max-width: 100%;
                    max-height: 80vh;
                    object-fit: contain;
                }
                
                .gallery-lightbox-close {
                    position: absolute;
                    top: -40px;
                    right: 0;
                    background: none;
                    border: none;
                    color: white;
                    font-size: 30px;
                    cursor: pointer;
                    z-index: 1000000;
                }
                
                .gallery-lightbox-prev,
                .gallery-lightbox-next {
                    position: absolute;
                    top: 50%;
                    transform: translateY(-50%);
                    background: rgba(0, 0, 0, 0.5);
                    border: none;
                    color: white;
                    padding: 10px;
                    cursor: pointer;
                    border-radius: 50%;
                }
                
                .gallery-lightbox-prev {
                    left: -50px;
                }
                
                .gallery-lightbox-next {
                    right: -50px;
                }
                
                .gallery-lightbox-caption {
                    color: white;
                    margin-top: 15px;
                    font-size: 16px;
                }
            `;
            document.head.appendChild(style);
        }
        
        const lightboxImg = lightboxOverlay.querySelector('img');
        const lightboxCaption = lightboxOverlay.querySelector('.gallery-lightbox-caption');
        const closeBtn = lightboxOverlay.querySelector('.gallery-lightbox-close');
        const prevBtn = lightboxOverlay.querySelector('.gallery-lightbox-prev');
        const nextBtn = lightboxOverlay.querySelector('.gallery-lightbox-next');
        
        let currentGalleryImages = [];
        let currentImageIndex = 0;
        
        function showLightbox(imageSrc, caption = '', galleryImages = []) {
            currentGalleryImages = galleryImages;
            currentImageIndex = galleryImages.findIndex(img => img.src === imageSrc);
            
            if (currentImageIndex === -1) {
                currentImageIndex = 0;
                currentGalleryImages = [{src: imageSrc, caption: caption}];
            }
            
            lightboxOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            updateLightboxImage();
        }
        
        function hideLightbox() {
            lightboxOverlay.style.display = 'none';
            document.body.style.overflow = '';
        }
        
        function updateLightboxImage() {
            if (currentGalleryImages.length === 0) return;
            
            const currentImage = currentGalleryImages[currentImageIndex];
            lightboxImg.src = currentImage.src;
            lightboxImg.alt = currentImage.caption || '';
            lightboxCaption.textContent = currentImage.caption || '';
            
            // Show/hide navigation buttons
            prevBtn.style.display = currentGalleryImages.length > 1 ? 'block' : 'none';
            nextBtn.style.display = currentGalleryImages.length > 1 ? 'block' : 'none';
        }
        
        function nextImage() {
            if (currentGalleryImages.length > 1) {
                currentImageIndex = (currentImageIndex + 1) % currentGalleryImages.length;
                updateLightboxImage();
            }
        }
        
        function prevImage() {
            if (currentGalleryImages.length > 1) {
                currentImageIndex = (currentImageIndex - 1 + currentGalleryImages.length) % currentGalleryImages.length;
                updateLightboxImage();
            }
        }
        
        // Event listeners
        closeBtn.addEventListener('click', hideLightbox);
        nextBtn.addEventListener('click', nextImage);
        prevBtn.addEventListener('click', prevImage);
        
        lightboxOverlay.addEventListener('click', function(e) {
            if (e.target === lightboxOverlay) {
                hideLightbox();
            }
        });
        
        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (lightboxOverlay.style.display === 'flex') {
                if (e.key === 'Escape') {
                    hideLightbox();
                } else if (e.key === 'ArrowRight') {
                    nextImage();
                } else if (e.key === 'ArrowLeft') {
                    prevImage();
                }
            }
        });
        
        // Initialize lightbox links
        lightboxLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                const imageSrc = this.href;
                const caption = this.getAttribute('data-title') || '';
                
                // Collect all images from the same gallery
                const galleryLinks = gallery.querySelectorAll('.gallery-lightbox');
                const galleryImages = Array.from(galleryLinks).map(link => ({
                    src: link.href,
                    caption: link.getAttribute('data-title') || ''
                }));
                
                showLightbox(imageSrc, caption, galleryImages);
            });
        });
    }
    </script>
                <?php endif; ?>
                
    <?php
    return ob_get_clean();
}

// Register the shortcode
add_shortcode('neways_gallery', 'neways_gallery_shortcode');
