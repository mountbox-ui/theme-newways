<?php
/**
 * Template Name: Single Job Template
 * 
 * INSTRUCTIONS:
 * 1. Upload this file to your theme folder
 * 2. Rename it to "single-jobs.php"
 * 3. This template will automatically be used for individual job pages
 */

get_header(); 

// Check if we're on a job post
if (!is_singular('jobs')) {
    // Debug: Let's see what post type we're actually on
    global $post;
    if ($post) {
        echo '<!-- Debug: Post type is: ' . $post->post_type . ' -->';
    }
    // If not a job post, show 404
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit;
}

// Check if job is active (but don't block if status is not set)
$job_status = get_post_meta(get_the_ID(), '_job_status', true);

// Only show 404 if job is explicitly set to inactive
if ($job_status === 'inactive') {
    global $wp_query;
    $wp_query->set_404();
    status_header(404);
    get_template_part('404');
    exit;
}
?>


<div class="min-h-screen bg-white py-8 lg:py-24">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6 sm:py-4 md:py-10 relative z-10">
        <div class="flex flex-col lg:flex-row gap-12 lg:gap-24">
           <!-- Left Column - Job Details -->
            <div class="flex-1 min-w-0">
                <?php while (have_posts()) : the_post(); ?>
                    <?php 
                    $category = get_post_meta(get_the_ID(), '_job_category', true);
                    $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                    $location = get_post_meta(get_the_ID(), '_job_location', true);
                    $short_description = get_post_meta(get_the_ID(), '_job_short_description', true);
                    ?>
                    
                    <!-- Job Title -->
                    <h1 class="text-[#00455E] font-poppins text-3xl md:text-4xl font-semibold mb-6 leading-tight"><?php the_title(); ?></h1>
                    
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
                            <div class="border-b border-gray-200 my-6"></div>
                            
                            <!-- Job Description -->
                            <div class="paragraph-text prose prose-sm lg:prose-base max-w-none text-gray-700 ">
                                <?php the_content(); ?>
                            </div>
                            
                        <?php endwhile; ?>
            </div>
            
            <!-- Right Column - Apply Form -->
            <div class="w-full lg:flex-none right-column-fixed">
                <div class="bg-gray-50 rounded-lg p-6 lg:p-8 border border-gray-200 lg:sticky lg:top-6 w-full">
                    <h2 class="text-xl lg:text-2xl font-bold text-gray-900 mb-6">Apply Now</h2>
                    
                    <form id="job-application-form" method="post" enctype="multipart/form-data" class="space-y-4">
                        <?php wp_nonce_field('job_application_nonce', 'job_application_nonce_field'); ?>
                        <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
                        
                        <div>
                            <input type="text" id="applicant_name" name="applicant_name" placeholder="Name*" required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all">
                        </div>
                        
                        <div>
                            <input type="email" id="applicant_email" name="applicant_email" placeholder="Email*" required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all">
                        </div>
                        
                        <div>
                            <input type="tel" id="applicant_phone" name="applicant_phone" placeholder="Phone*" required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition-all">
                        </div>
                        
                        <div>
                            <textarea id="cover_letter" name="cover_letter" rows="4" placeholder="Cover Letter" class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white resize-none transition-all"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Resume</label>
                            <input type="file" id="resume_upload" name="resume_upload" accept=".pdf,.doc,.docx" required class="w-full px-4 py-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer transition-all">
                            <p class="text-xs text-gray-500 mt-2">Allowed Type(s): .pdf, .doc, .docx</p>
                        </div>
                        
                        <div class="flex items-start pt-2">
                            <input type="checkbox" name="privacy_policy" required id="privacy_policy" class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded cursor-pointer">
                            <label for="privacy_policy" class="ml-3 text-sm text-gray-700 cursor-pointer select-none">I accept the Privacy Policy</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary bt-1 w-full bg-[#232361]">
                            Submit
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom width for the right column to ensure 422px on large screens */
@media (min-width: 1024px) {
    .right-column-fixed {
        width: 422px !important;
        flex: none !important;
    }
}
</style>


<!-- Thank You Modal -->
<div id="thankYouModal" class="hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
  <!-- Background backdrop -->
  <div class="modal-backdrop"></div>

  <div class="modal-flex-container">
    <!-- Modal panel -->
    <div class="modal-panel">
      <div>
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100 mb-4">
          <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
          </svg>
        </div>
        <div class="mt-3 text-center sm:mt-5">
          <h3 class="text-xl font-semibold leading-6 text-gray-900 mb-2" id="modal-title">Application Received</h3>
          <div class="mt-2">
            <p class="text-sm text-gray-500">Your application has been successfully submitted. We will review your information and get back to you shortly.</p>
          </div>
          </div>
      <div class="mt-6">
        <button type="button" class="btn btn-primary bt-1 w-full bg-[#232361]" onclick="closeThankYouModal()" href="/home">Back to Home</button>
      </div>
    </div>
  </div>
</div>

<style>
/* Robust Modal Styles */
#thankYouModal {
    position: fixed;
    inset: 0;
    z-index: 10000;
    overflow-y: auto;
}

#thankYouModal.hidden {
    display: none !important;
}

.modal-backdrop {
    position: fixed;
    inset: 0;
    background-color: rgba(107, 114, 128, 0.75);
    backdrop-filter: blur(4px);
}

.modal-flex-container {
    display: flex;
    min-height: 100%;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    text-align: center;
    position: relative;
}

.modal-panel {
    position: relative;
    background-color: white;
    border-radius: 0.5rem;
    padding: 1.5rem;
    text-align: center;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    width: 100%;
    max-width: 24rem; /* 384px */
    margin: 2rem auto;
}

/* Legacy WordPress content styles - keep for backward compatibility */
.job-content ul {
    list-style: none;
    padding: 0;
    margin: 0 !important;
}

.job-content ul li {
    padding: 3px 0;
    display: flex;
    align-items: flex-start;
    word-wrap: break-word;
    position: relative;
    padding-left: 30px;
    color: rgba(60, 74, 78, 0.80);
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
}

.job-content ul li::before {
    content: "✓";
    color: #19A1AF;
    font-weight: bold;
    font-size: 14px;
    position: absolute;
    left: 0;
    top: 8px;
    width: 15px;
    height: 15px;
    border-radius: 50%;
    background: rgba(25, 161, 175, 0.13);
    display: flex;
    align-items: center;
    justify-content: center;
}

.job-content p {
    margin-bottom: 15px;
    word-wrap: break-word;
    color: rgba(60, 74, 78, 0.80);
    font-family: Inter;
    font-size: 16px;
    font-weight: 400;
    line-height: 24px;
}

.job-content h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 600 !important;
    color: #00455E;
    margin: 0 0 15px 0;
    text-transform: none;
    padding-top: 32px;
    padding-bottom: 10px;
}
</style>

<script>
// Modal functions
function showThankYouModal() {
    const modal = document.getElementById('thankYouModal');
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent scrolling
}

function closeThankYouModal() {
    const modal = document.getElementById('thankYouModal');
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto'; // Restore scrolling
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('thankYouModal');
    if (event.target === modal) {
        closeThankYouModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeThankYouModal();
    }
});
</script>

<?php get_footer(); ?>
