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

<div class="job-detail-container">
    <!-- Small div with 20vh height
    <div class="small-div-20vh"></div> -->
    
    <div class="job-content-wrapper">
        <div class="job-main-content">
            <?php while (have_posts()) : the_post(); ?>
                <?php 
                $category = get_post_meta(get_the_ID(), '_job_category', true);
                $job_type = get_post_meta(get_the_ID(), '_job_type', true);
                $location = get_post_meta(get_the_ID(), '_job_location', true);
                $salary = get_post_meta(get_the_ID(), '_job_salary', true);
                $company = get_post_meta(get_the_ID(), '_job_company', true);
                ?>
                
                <!-- Job Details with Model Line -->
                <div class="job-header-section">
                    <h1 class="job-title"><?php the_title(); ?></h1>
                    
                    <div class="job-metadata">
                        <div class="metadata-row">
                            <span class="metadata-label">Job Category<span class="colon">:</span> </span>
                            <span class="metadata-value"><?php echo esc_html($category); ?></span>
                        </div>
                        <div class="metadata-row">
                            <span class="metadata-label">Job Type<span class="colon">:</span> </span>
                            <span class="metadata-value"><?php echo ucfirst(str_replace('-', ' ', $job_type)); ?></span>
                        </div>
                        <div class="metadata-row">
                            <span class="metadata-label">Job Location<span class="colon">:</span> </span>
                            <span class="metadata-value"><?php echo esc_html($location); ?></span>
                        </div>
                    </div>
                    
                    <!-- Model Line Separator -->
                    <hr class="model-line">
                </div>
                
                <div class="job-description">
                    <div class="job-content">
                        <?php 
                        // Get the raw content before WordPress processing
                        global $post;
                        $content = $post->post_content;
                        
                        // Remove HTML tags temporarily for pattern matching
                        $plain_content = strip_tags($content);
                        $plain_content = html_entity_decode($plain_content, ENT_QUOTES, 'UTF-8');
                        
                        // Split content by lines (handle both \n and \r\n)
                        $lines = preg_split('/\r?\n/', $plain_content);
                        $formatted_content = '';
                        $has_formatted_fields = false;
                        
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) {
                                continue;
                            }
                            
                            // Check if line matches "Label : Value" pattern (more flexible)
                            if (preg_match('/^([^:]+?)\s*:\s*(.+)$/u', $line, $matches)) {
                                $label = trim($matches[1]);
                                $value = trim($matches[2]);
                                
                                // Only format if both label and value exist and are meaningful
                                if (!empty($label) && !empty($value) && strlen($label) > 0 && strlen($value) > 0) {
                                    $formatted_content .= '<div class="job-field"><strong class="job-field-label">' . esc_html($label) . ':</strong> <span class="job-field-value">' . esc_html($value) . '</span></div>';
                                    $has_formatted_fields = true;
                                } else {
                                    // Regular paragraph
                                    $formatted_content .= '<p class="job-content-text">' . esc_html($line) . '</p>';
                                }
                            } else {
                                // Regular paragraph for non-matching lines
                                if (!empty($line)) {
                                    $formatted_content .= '<p class="job-content-text">' . esc_html($line) . '</p>';
                                }
                            }
                        }
                        
                        // Output formatted content
                        if ($has_formatted_fields && !empty($formatted_content)) {
                            echo $formatted_content;
                        } else {
                            // Fallback to default WordPress content formatting
                            $content = apply_filters('the_content', $content);
                            $content = str_replace(']]>', ']]&gt;', $content);
                            echo $content;
                        }
                        ?>
                    </div>
                </div>
                
                
            <?php endwhile; ?>
        </div>
        
        <div class="job-application-form">
            <div class="form-container">
                <h2>Apply Now</h2>
                <form id="job-application-form" method="post" enctype="multipart/form-data">
                    <?php wp_nonce_field('job_application_nonce', 'job_application_nonce_field'); ?>
                    <input type="hidden" name="job_id" value="<?php echo get_the_ID(); ?>">
                    
                    <div class="form-group">
                        <input type="text" id="applicant_name" name="applicant_name" placeholder="Name" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="email" id="applicant_email" name="applicant_email" placeholder="Email" required>
                    </div>
                    
                    <div class="form-group">
                        <input type="tel" id="applicant_phone" name="applicant_phone" placeholder="Phone" required>
                    </div>
                    
                    <div class="form-group">
                        <textarea id="cover_letter" name="cover_letter" rows="4" placeholder="Cover Letter"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <input type="file" id="resume_upload" name="resume_upload" accept=".pdf,.doc,.docx" required>
                    </div>
                    
                    <div class="form-group checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="privacy_policy" required>
                            <span class="checkmark"></span>
                            I accept the Privacy Policy
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Thank You Modal -->
<div id="thankYouModal" class="modal-overlay" style="display: none;">
    <div class="modal-card">
        <div class="modal-icon">
            <div class="icon-circle">
                <svg class="checkmark" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 12L11 14L15 10" stroke="#FF6B6B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>
        <h2 class="modal-title">Thank you</h2>
        <p class="modal-message">your message has been received,<br>we will update you shortly.</p>
        <button class="modal-button" onclick="closeThankYouModal()">Go further</button>
    </div>
</div>

<!-- <?php echo do_shortcode('[hfe_template id="8975"]'); ?> -->

<style>
/* Reset and Base Styles */
* {
    box-sizing: border-box;
}

/* Remove any gaps from WordPress admin bar and theme */
body {
    margin: 0;
    padding: 0;
}

/* Ensure no gaps at the top */
html, body {
    margin: 0;
    padding: 0;
}

/* Remove any default margins from the page */
.page {
    margin: 0;
    padding: 0;
}

.job-detail-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 40px 20px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: #ffffff;
    min-height: 100vh;
}

/* Small div with 20vh height - Full Width Black */
.small-div-20vh {
    height: 90px;
    background: #00323F;
    margin: 0;
    padding: 0;
    width: 100vw;
    position: relative;
    left: 50%;
    right: 50%;
    margin-left: -50vw;
    margin-right: -50vw;
    margin-top: 0;
}

.job-content-wrapper {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 120px ;
    align-items: start;
    padding-top: 54px;
}

.job-main-content {
    background: #ffffff;
    padding: 0;
    overflow-wrap: break-word;
    word-wrap: break-word;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
}

/* Job Header Section */
.job-header-section {
    background: #ffffff;
    padding: 0;
    margin-bottom: 30px;
}

.job-title {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 32px;
    font-weight: 700;
    color: #00455E !important;
    line-height: 38.4px;
    word-wrap: break-word;
    margin: 0px !important;
}

/* Job Metadata Styling */
.job-metadata {
    margin-bottom: 23px;
    margin-top: 23px;
}

.metadata-row {
    display: flex;
    margin-bottom: 8px;
    align-items: center;
}

.metadata-label {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-weight: 400;
    color: rgba(60, 74, 78, 0.8);
    font-size: 16px;
    min-width: 120px;
    text-align: left;
    white-space: nowrap;
    padding-right: 0;
}

.metadata-value {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    color: rgba(60, 74, 78, 0.8);
    font-size: 16px;
    font-weight: 400;
}

/* Model Line Separator */
.model-line {
    border: none;
    height: 1px;
    background: #000;
    opacity: 0.2;
    margin-top: 23px;
    margin-bottom: 23px;
    width: 598px;
}

.colon {
    margin-left: 0px;
    margin-right: 8px;
    font-weight: 400;
}

.job-description {
    margin-bottom: 30px;
    word-wrap: break-word;
}

.job-description h2 {
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 600 !important;
    color: #00455E;
    margin: 0 0 15px 0;
    text-transform: none;
    padding-top: 32px;
    padding-bottom: 10px;
}


.job-content {
    font-size: 16px;
    line-height: 1.6;
    color: #333;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

/* Style lists in job content with tick marks */
.job-content ul {
    list-style: none;
    padding: 0;
    margin: 0px !important;
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

/* Style for job field labels and values */
.job-field {
    margin-bottom: 24px;
    padding-bottom: 20px;
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
}

.job-field:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.job-field-label {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: #00455E;
    display: block;
    margin-bottom: 8px;
}

.job-field-value {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: rgba(60, 74, 78, 0.80);
    line-height: 24px;
    display: block;
}

.job-content-text {
    font-family: 'Inter', sans-serif;
    font-size: 16px;
    font-weight: 400;
    color: rgba(60, 74, 78, 0.80);
    line-height: 24px;
    margin-bottom: 15px;
}




/* Application Form Styles */
.job-application-form {
    position: sticky;
    top: 20px;
    z-index: 10;
    padding-bottom: 10px;
}

.form-container {
    padding: 32px;
    width: 422px;
    max-width: 100%;
    border-radius: 8px;
    border: 1px solid rgba(0, 69, 94, 0.10);
    background: rgba(0, 69, 94, 0.05);
    box-sizing: border-box;
}

.form-container h2 {
    color: #00455E;
    font-family: 'Poppins', sans-serif;
    font-size: 20px;
    font-weight: 700;
    line-height: 38.4px;
    margin: 0px !important;
    opacity: 0.8;
}

.form-group {
    margin-bottom: 0px !important;
    padding-top: 12px;
}

.form-group label {
    display: block;
    font-weight: 500;
    color: #333;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d0d0d0;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s ease;
    box-sizing: border-box;
    background: #ffffff;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #1a365d;
    box-shadow: 0 0 0 2px rgba(26, 54, 93, 0.1);
}

/* Style asterisks in placeholders */
.form-group input::placeholder,
.form-group textarea::placeholder {
    color: #999;
}

/* Style for required fields with red asterisks */
/*.form-group {*/
/*    position: relative;*/
/*}*/

/*.form-group input[required]::after,*/
/*.form-group textarea[required]::after {*/
/*    content: "*";*/
/*    color: #FF3336;*/
/*    position: absolute;*/
/*    right: 15px;*/
/*    top: 50%;*/
/*    transform: translateY(-50%);*/
/*    font-weight: bold;*/
/*    pointer-events: none;*/
/*    z-index: 1;*/
/*}*/

.checkbox-group {
    margin-bottom: 25px;
}

.checkbox-label {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    font-size: 14px;
    line-height: 1.4;
}

.checkbox-label input[type="checkbox"] {
    width: auto;
    margin: 0;
    flex-shrink: 0;
}

.submit-btn {
    width: 100%;
    background: #00323F !important;
    color: white !important;
    border: none;
    padding: 17px 153px;
    border-radius: 100px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.submit-btn:hover {
    background: #153a5e;
}

.submit-btn:active {
    transform: translateY(1px);
}

/* Header Navigation Fix */
.site-header {
    overflow: visible;
}

.site-header .container {
    max-width: 100%;
    overflow: visible;
}

.site-header nav {
    white-space: nowrap;
    overflow: visible;
}

.site-header nav ul {
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.site-header nav li {
    flex-shrink: 0;
    white-space: nowrap;
    position: relative;
}

/* Dropdown Menu Styles */
.site-header nav li:hover ul,
.site-header nav li:focus-within ul {
    display: block;
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.site-header nav ul ul {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    border-radius: 4px;
    min-width: 200px;
    z-index: 1000;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    flex-direction: column;
    padding: 8px 0;
}

.site-header nav ul ul li {
    white-space: normal;
    flex-shrink: 1;
}

.site-header nav ul ul li a {
    display: block;
    padding: 10px 20px;
    color: #333;
    text-decoration: none;
    transition: background-color 0.2s ease;
}

.site-header nav ul ul li a:hover {
    background-color: #f8f9fa;
}

/* Logo and Brand Fix */
.site-branding {
    display: flex;
    align-items: center;
    gap: 10px;
}

.site-branding .logo {
    max-width: 50px;
    height: auto;
}

.site-branding .site-title {
    font-size: 18px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 1250px) {
    .job-content-wrapper {
        gap: 80px;
    }
    
    .job-detail-container {
        padding: 0 15px 40px 15px;
    }
}

@media (max-width: 1100px) {
    .job-content-wrapper {
        gap: 60px;
    }
    
    .form-container {
        width: 380px;
    }
}

@media (max-width: 1024px) {
    .job-content-wrapper {
        gap: 30px;
        grid-template-columns: 2fr;
    }
    
    .job-detail-container {
        padding: 0 15px 30px 15px;
    }
    
    .job-main-content {
        padding: 0;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .job-title {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 28px;
        font-weight: 700;
    }
    
    .job-description {
        font-size: 15px;
        line-height: 22px;
    }
    
    .metadata-row {
        flex-direction: row;
        align-items: center;
        gap: 0;
    }
    
    .metadata-label {
        min-width: 120px;
        text-align: left;
        padding-right: 0;
        font-size: 14px;
    }
    
    .metadata-value {
        font-size: 14px;
        margin-left: 0;
    }
    
    .form-container {
        width: 100%;
        max-width: 100%;
        padding: 24px;
    }
}

@media (max-width: 768px) {
    .job-content-wrapper {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .job-detail-container {
        padding: 0 10px 20px 10px;
    }
    
    .job-main-content {
        padding: 0;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    
    .job-application-form {
        position: static;
    }
    
    .job-title {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 26px;
        font-weight: 700;
    }
    
    .job-description {
        font-size: 14px;
        line-height: 20px;
    }
    
    .metadata-row {
        flex-direction: row;
        align-items: center;
        gap: 0;
        margin-bottom: 12px;
    }
    
    .metadata-label {
        min-width: 120px;
        text-align: left;
        padding-right: 0;
        font-size: 14px;
        margin-bottom: 0;
    }
    
    .metadata-value {
        font-size: 14px;
        margin-left: 0;
    }
    
    .form-container {
        padding: 20px;
        width: fit-content;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .form-container h2 {
        font-size: 20px;
    }
}

@media (max-width: 480px) {
    .job-detail-container {
        padding: 0 10px 15px 10px;
    }
    
    .job-main-content {
        padding: 0;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    
    .job-title {
        font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 22px;
        font-weight: 700;
        line-height: 26px;
    }
    
    .job-description {
        font-size: 13px;
        line-height: 18px;
        margin-bottom: 20px;
    }
    
    .metadata-row {
        flex-direction: row;
        align-items: center;
        gap: 0;
        margin-bottom: 10px;
    }
    
    .metadata-label {
        min-width: 120px;
        text-align: left;
        padding-right: 0;
        font-size: 13px;
        margin-bottom: 0;
    }
    
    .metadata-value {
        font-size: 13px;
        margin-left: 0;
    }
    
    .form-container {
        padding: 15px;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
    }
    
    .form-container h2 {
        font-size: 18px;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        font-size: 14px;
        padding: 12px;
    }
    
    .submit-btn {
        padding: 12px 24px;
        font-size: 14px;
    }
}

/* Extra small devices (320px and below) */
@media (max-width: 320px) {
    .job-detail-container {
        padding: 0 5px 10px 5px;
    }
    
    .job-main-content {
        padding: 0;
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
        box-sizing: border-box;
    }
    
    .job-title {
        font-size: 20px;
        line-height: 24px;
    }
    
    .job-description {
        font-size: 12px;
        line-height: 16px;
    }
    
    .form-container {
        padding: 12px;
    }
    
    .form-container h2 {
        font-size: 16px;
    }
    
    .form-group input,
    .form-group textarea,
    .form-group select {
        font-size: 13px;
        padding: 10px;
    }
    
    .submit-btn {
        padding: 10px 20px;
        font-size: 13px;
    }
}

/* Success/Error Messages */
.application-message {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}

.application-message.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.application-message.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

/* Fix for text overflow issues */
.job-content p,
.job-content div,
.job-content span {
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
}

/* Ensure proper spacing */
.job-main-content > * {
    margin-bottom: 0px;
}

.job-main-content > *:last-child {
    margin-bottom: 0;
}

/* Thank You Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 10000;
    backdrop-filter: blur(5px);
}

.modal-card {
    background: white;
    border-radius: 16px;
    padding: 40px;
    text-align: center;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    animation: modalSlideIn 0.3s ease-out;
}

@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.modal-icon {
    margin-bottom: 24px;
}

.icon-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 2px solid white;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto;
    background: #00323F;
}

.checkmark {
    width: 40px;
    height: 40px;
}

.modal-title {
    font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 16px 0;
}

.modal-message {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    color: #718096;
    line-height: 1.5;
    margin: 0 0 32px 0;
}

.modal-button {
    background: #00323F !important;
    color: white !important;
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 140px;
}

.modal-button:hover {
    background: #00323F;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(61, 213, 243, 0.3);
}

.modal-button:active {
    transform: translateY(0);
}

/* Modal Responsive Design */
@media (max-width: 768px) {
    .modal-card {
        padding: 30px 20px;
        max-width: 90%;
        margin: 20px;
    }
    
    .modal-title {
        font-size: 24px;
    }
    
    .modal-message {
        font-size: 14px;
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
    }
    
    .checkmark {
        width: 30px;
        height: 30px;
    }
}

@media (max-width: 480px) {
    .modal-card {
        padding: 25px 15px;
        max-width: 95%;
        margin: 10px;
    }
    
    .modal-title {
        font-size: 20px;
    }
    
    .modal-message {
        font-size: 13px;
    }
    
    .modal-button {
        padding: 12px 24px;
        font-size: 14px;
        min-width: 120px;
    }
    
    .icon-circle {
        width: 50px;
        height: 50px;
    }
    
    .checkmark {
        width: 25px;
        height: 25px;
    }
}
</style>

<script>
// Modal functions
function showThankYouModal() {
    document.getElementById('thankYouModal').style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function closeThankYouModal() {
    document.getElementById('thankYouModal').style.display = 'none';
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
