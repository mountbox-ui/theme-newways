<?php
/**
 * Template Name: Book a Tour
 * 
 * Template for booking a tour with the same design as the care enquiry form
 */

get_header(); ?>

<div class="book-tour-page">
    <div class="book-tour-container">
        <!-- Left Content Section -->
        <div class="book-tour-content">
            <h1 class="book-tour-title">
                <span class="title-line-1">Start a care </span>
                <span class="title-line-2">enquiry</span>
            </h1>
            
            <p class="book-tour-description">
                Schedule your personalized tour of our facility. Select your preferred date and time, and one of our dedicated team members will be in touch shortly to confirm your visit.
            </p>
            
            <div class="book-tour-links">
                <p class="contact-link">
                    <span>Looking to contact a relative? </span>
                    <a href="#" class="blue-link">Contact the home team</a>
                </p>
                
                <p class="contact-link">
                    <span>Want to apply for a job & join our team? </span>
                    <a href="#" class="blue-link">Contact our recruitment team</a>
                    <span class="note">. Kindly note that we will not respond to any recruitment enquiries made via this form.</span>
                </p>
            </div>
        </div>
        
        <!-- Right Form Section -->
        <div class="book-tour-form-section">
            <form class="book-tour-form" id="book-tour-form" method="post" action="">
                <?php wp_nonce_field('book_tour_nonce', 'book_tour_nonce_field'); ?>
                
                <!-- Tour Type Selection -->
                <div class="form-group tour-type-group">
                    <label class="form-label">PLEASE SELECT TOUR TYPE: (REQUIRED)</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="tour_type" value="general" required>
                            <span class="radio-custom"></span>
                            <span class="radio-label">General Tour</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tour_type" value="private" required>
                            <span class="radio-custom"></span>
                            <span class="radio-label">Private Tour</span>
                        </label>
                        <label class="radio-option">
                            <input type="radio" name="tour_type" value="virtual" required checked>
                            <span class="radio-custom"></span>
                            <span class="radio-label">Virtual Tour</span>
                        </label>
                    </div>
                    <p class="tour-description">
                        Experience our facility through an immersive virtual tour from the comfort of your home.
                    </p>
                </div>
                
                <!-- Full Name -->
                <div class="form-group">
                    <label for="full_name" class="form-label">FULL NAME (REQUIRED)</label>
                    <input type="text" id="full_name" name="full_name" class="form-input" required>
                </div>
                
                <!-- Phone Number -->
                <div class="form-group">
                    <label for="phone_number" class="form-label">PHONE NUMBER (REQUIRED)</label>
                    <input type="tel" id="phone_number" name="phone_number" class="form-input" required>
                </div>
                
                <!-- Email Address -->
                <div class="form-group">
                    <label for="email_address" class="form-label">EMAIL ADDRESS</label>
                    <input type="email" id="email_address" name="email_address" class="form-input">
                </div>
                
                <!-- Care Home Selection -->
                <div class="form-group">
                    <label for="care_home" class="form-label">CARE HOME (REQUIRED)</label>
                    <select id="care_home" name="care_home" class="form-select" required>
                        <option value="">Select a care home</option>
                        <option value="otterton">Otterton</option>
                        <option value="exmouth">Exmouth</option>
                        <option value="sidmouth">Sidmouth</option>
                    </select>
                </div>
                
                <!-- Preferred Date -->
                <div class="form-group">
                    <label for="preferred_date" class="form-label">PREFERRED DATE</label>
                    <input type="date" id="preferred_date" name="preferred_date" class="form-input">
                </div>
                
                <!-- Preferred Time -->
                <div class="form-group">
                    <label for="preferred_time" class="form-label">PREFERRED TIME</label>
                    <select id="preferred_time" name="preferred_time" class="form-select">
                        <option value="">Select a time</option>
                        <option value="09:00">9:00 AM</option>
                        <option value="10:00">10:00 AM</option>
                        <option value="11:00">11:00 AM</option>
                        <option value="14:00">2:00 PM</option>
                        <option value="15:00">3:00 PM</option>
                        <option value="16:00">4:00 PM</option>
                    </select>
                </div>
                
                <!-- Message -->
                <div class="form-group">
                    <label for="message" class="form-label">MESSAGE</label>
                    <textarea id="message" name="message" class="form-textarea" rows="4"></textarea>
                </div>
                
                <!-- Checkboxes -->
                <div class="form-group checkbox-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="privacy_notice" required>
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-label">
                            I confirm I have read the <a href="#" class="blue-link">privacy notice</a> (Required)
                        </span>
                    </label>
                    
                    <label class="checkbox-option">
                        <input type="checkbox" name="newsletter">
                        <span class="checkbox-custom"></span>
                        <span class="checkbox-label">
                            I would like to be kept up-to-date with news from our homes, future offers and services
                        </span>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="submit-button">Book Tour</button>
                
                <!-- Footer Text -->
                <div class="form-footer">
                    <p class="recaptcha-notice">
                        This site is protected by reCAPTCHA and the Google 
                        <a href="#" class="blue-link">Privacy Policy</a> and 
                        <a href="#" class="blue-link">Terms of Service</a> apply. 
                        We use an application to understand the nature of your enquiry, this maybe retained for 30 days.
                    </p>
                </div>
            </form>
        </div>
    </div>
    
</div>

<!-- Success Modal -->
<div id="successModal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">✓ Booking Successful!</h2>
            <button class="modal-close" onclick="closeSuccessModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="success-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="12" r="10" fill="#28a745"/>
                    <path d="M9 12l2 2 4-4" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
            <h3>Thank You for Your Booking!</h3>
            <p>Your tour booking request has been submitted successfully. We will contact you shortly to confirm your visit and provide further details.</p>
            <div class="modal-details">
                <p><strong>What happens next?</strong></p>
                <ul>
                    <li>Our team will review your request</li>
                    <li>We'll contact you within 24 hours to confirm</li>
                    <li>You'll receive detailed information about your tour</li>
                    <li>Check your email for a confirmation message</li>
                </ul>
            </div>
        </div>
        <div class="modal-footer">
            <button class="modal-button" onclick="closeSuccessModal()">Close</button>
        </div>
    </div>
</div>

<style>
.book-tour-page {
    min-height: 100vh;
    background: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    padding: 40px 20px;
}

.book-tour-container {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

/* Left Content Section */
.book-tour-content {
    padding-right: 40px;
}

.book-tour-title {
    font-size: 3.5rem;
    font-weight: 700;
    color: #2d5016;
    line-height: 1.1;
    margin: 0 0 30px 0;
    font-family: inherit;
}

.title-line-1 {
    display: block;
}

.title-line-2 {
    display: block;
    margin-left: 20px;
}

.book-tour-description {
    font-size: 1.1rem;
    color: #4a4a4a;
    line-height: 1.6;
    margin: 0 0 40px 0;
}

.book-tour-links {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.contact-link {
    font-size: 1rem;
    color: #4a4a4a;
    line-height: 1.5;
    margin: 0;
}

.blue-link {
    color: #0066cc;
    text-decoration: underline;
    cursor: pointer;
}

.blue-link:hover {
    color: #004499;
}

.note {
    font-style: italic;
}

/* Right Form Section */
.book-tour-form-section {
    background: #ffffff;
    padding: 40px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.book-tour-form {
    display: flex;
    flex-direction: column;
    gap: 25px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4a4a4a;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 1rem;
    color: #333;
    background: #ffffff;
    transition: border-color 0.3s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #0066cc;
    box-shadow: 0 0 0 3px rgba(0, 102, 204, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

/* Radio Buttons */
.tour-type-group {
    margin-bottom: 10px;
}

.radio-group {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 1rem;
    color: #333;
}

.radio-option input[type="radio"] {
    display: none;
}

.radio-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #016A7C;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
}

.radio-option input[type="radio"]:checked + .radio-custom {
    background: #016A7C;
}

.radio-option input[type="radio"]:checked + .radio-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.tour-description {
    font-size: 0.95rem;
    color: #666;
    margin-top: 10px;
    line-height: 1.4;
}

/* Checkboxes */
.checkbox-group {
    gap: 15px;
}

.checkbox-option {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    cursor: pointer;
    font-size: 0.95rem;
    color: #333;
    line-height: 1.4;
    position: relative;
}

.checkbox-option input[type="checkbox"] {
    position: absolute;
    opacity: 0;
    width: 18px;
    height: 18px;
    margin: 0;
    cursor: pointer;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #e0e0e0;
    border-radius: 3px;
    position: relative;
    flex-shrink: 0;
    margin-top: 2px;
    transition: all 0.3s ease;
    pointer-events: none;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom {
    background: #016A7C;
    border-color: #016A7C;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

/* Submit Button */
.submit-button {
    background: #016A7C;
    color: white;
    border: none;
    padding: 16px 32px;
    font-size: 1.1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin-top: 10px;
}

.submit-button:hover {
    background: #016A7C;
}

.submit-button:active {
    transform: translateY(1px);
}

/* Form Footer */
.form-footer {
    margin-top: 20px;
}

.recaptcha-notice {
    font-size: 0.75rem;
    color: #666;
    line-height: 1.4;
    margin: 0;
}

/* Responsive Design */
@media (max-width: 968px) {
    .book-tour-container {
        grid-template-columns: 1fr;
        gap: 40px;
    }
    
    .book-tour-content {
        padding-right: 0;
        text-align: center;
    }
    
    .book-tour-title {
        font-size: 2.5rem;
    }
    
    .book-tour-form-section {
        padding: 30px 20px;
    }
}

@media (max-width: 640px) {
    .book-tour-page {
        padding: 20px 15px;
    }
    
    .book-tour-title {
        font-size: 2rem;
    }
    
    .radio-group {
        flex-direction: column;
        gap: 15px;
    }
    
    .book-tour-form-section {
        padding: 20px 15px;
    }
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    animation: fadeIn 0.3s ease;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-width: 500px;
    width: 100%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    animation: slideIn 0.3s ease;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 24px 24px 0 24px;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 20px;
}

.modal-title {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #28a745;
}

.modal-close {
    background: none;
    border: none;
    font-size: 2rem;
    color: #6c757d;
    cursor: pointer;
    padding: 0;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #495057;
}

.modal-body {
    padding: 0 24px;
    text-align: center;
}

.success-icon {
    margin-bottom: 20px;
}

.modal-body h3 {
    margin: 0 0 16px 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
}

.modal-body p {
    margin: 0 0 20px 0;
    color: #666;
    line-height: 1.6;
}

.modal-details {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 20px;
    margin: 20px 0;
    text-align: left;
}

.modal-details p {
    margin: 0 0 12px 0;
    color: #333;
    font-weight: 600;
}

.modal-details ul {
    margin: 0;
    padding-left: 20px;
    color: #666;
}

.modal-details li {
    margin-bottom: 8px;
    line-height: 1.5;
}

.modal-footer {
    padding: 20px 24px 24px 24px;
    text-align: center;
    border-top: 1px solid #e9ecef;
    margin-top: 20px;
}

.modal-button {
    background: #016A7C;
    color: white;
    border: none;
    padding: 12px 32px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.modal-button:hover {
    background: #015a6b;
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: translateY(-50px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Mobile Responsive */
@media (max-width: 640px) {
    .modal-overlay {
        padding: 10px;
    }
    
    .modal-content {
        max-height: 95vh;
    }
    
    .modal-header,
    .modal-body,
    .modal-footer {
        padding-left: 16px;
        padding-right: 16px;
    }
    
    .modal-title {
        font-size: 1.25rem;
    }
    
    .modal-body h3 {
        font-size: 1.1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('book-tour-form');
    const submitButton = form.querySelector('.submit-button');
    
    // Form submission handling
    form.addEventListener('submit', function(e) {
        // Show loading state
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        submitButton.style.opacity = '0.7';
        
        // Validate required fields
        const requiredFields = form.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            let fieldValid = true;
            
            if (field.type === 'checkbox') {
                // Special handling for checkboxes
                if (!field.checked) {
                    fieldValid = false;
                }
            } else {
                // Regular input fields
                if (!field.value.trim()) {
                    fieldValid = false;
                }
            }
            
            if (!fieldValid) {
                isValid = false;
                if (field.type === 'checkbox') {
                    // Highlight the checkbox container
                    const checkboxContainer = field.closest('.checkbox-option');
                    if (checkboxContainer) {
                        checkboxContainer.style.borderColor = '#dc3545';
                        checkboxContainer.style.borderRadius = '4px';
                        checkboxContainer.style.borderWidth = '1px';
                        checkboxContainer.style.borderStyle = 'solid';
                    }
                } else {
                    field.style.borderColor = '#dc3545';
                }
            } else {
                if (field.type === 'checkbox') {
                    const checkboxContainer = field.closest('.checkbox-option');
                    if (checkboxContainer) {
                        checkboxContainer.style.borderColor = 'transparent';
                        checkboxContainer.style.borderWidth = '0';
                    }
                } else {
                    field.style.borderColor = '#e0e0e0';
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            submitButton.disabled = false;
            submitButton.textContent = 'Book Tour';
            submitButton.style.opacity = '1';
            
            // Show error message
            showMessage('Please fill in all required fields.', 'error');
            return;
        }
        
        // If validation passes, form will submit normally
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required')) {
                if (this.type === 'checkbox') {
                    const checkboxContainer = this.closest('.checkbox-option');
                    if (checkboxContainer) {
                        if (!this.checked) {
                            checkboxContainer.style.borderColor = '#dc3545';
                            checkboxContainer.style.borderRadius = '4px';
                            checkboxContainer.style.borderWidth = '1px';
                            checkboxContainer.style.borderStyle = 'solid';
                        } else {
                            checkboxContainer.style.borderColor = 'transparent';
                            checkboxContainer.style.borderWidth = '0';
                        }
                    }
                } else if (!this.value.trim()) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#e0e0e0';
                }
            }
        });
        
        input.addEventListener('input', function() {
            if (this.type === 'checkbox') {
                const checkboxContainer = this.closest('.checkbox-option');
                if (checkboxContainer && this.checked) {
                    checkboxContainer.style.borderColor = 'transparent';
                    checkboxContainer.style.borderWidth = '0';
                }
            } else if (this.style.borderColor === 'rgb(220, 53, 69)') {
                this.style.borderColor = '#e0e0e0';
            }
        });
        
        // For checkboxes, also listen to change event
        if (input.type === 'checkbox') {
            input.addEventListener('change', function() {
                const checkboxContainer = this.closest('.checkbox-option');
                if (checkboxContainer) {
                    if (this.checked) {
                        checkboxContainer.style.borderColor = 'transparent';
                        checkboxContainer.style.borderWidth = '0';
                    } else if (this.hasAttribute('required')) {
                        checkboxContainer.style.borderColor = '#dc3545';
                        checkboxContainer.style.borderRadius = '4px';
                        checkboxContainer.style.borderWidth = '1px';
                        checkboxContainer.style.borderStyle = 'solid';
                    }
                }
            });
        }
    });
    
    // Function to show messages
    function showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
        messageDiv.style.cssText = type === 'error' 
            ? 'background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #dc3545;'
            : 'background: #d4edda; color: #155724; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #28a745;';
        
        messageDiv.innerHTML = `<p style="margin: 0;">${message}</p>`;
        
        // Remove existing messages
        const existingMessages = form.querySelectorAll('.error-message, .success-message');
        existingMessages.forEach(msg => msg.remove());
        
        // Insert message at the top of the form
        form.insertBefore(messageDiv, form.firstChild);
        
        // Scroll to message
        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Remove message after 5 seconds for non-error messages
        if (type !== 'error') {
            setTimeout(() => {
                messageDiv.remove();
            }, 5000);
        }
    }
});

// Modal functions
function showSuccessModal(customMessage = null) {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
        
        // Update message if custom message provided
        if (customMessage) {
            const messageElement = modal.querySelector('.modal-body p');
            if (messageElement) {
                messageElement.textContent = customMessage;
            }
        }
        
        // Focus on close button for accessibility
        const closeButton = modal.querySelector('.modal-close');
        if (closeButton) {
            closeButton.focus();
        }
    }
}

function closeSuccessModal() {
    const modal = document.getElementById('successModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = '';
        
        // Reset form after closing modal
        const form = document.getElementById('book-tour-form');
        if (form) {
            form.reset();
        }
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('successModal');
    if (modal && e.target === modal) {
        closeSuccessModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeSuccessModal();
    }
});
</script>

<?php
// Ensure database table exists before processing form
create_tour_bookings_table();

// Configure WordPress mail settings
function configure_tour_booking_mail() {
    // Set mail content type to HTML for better formatting
    add_filter('wp_mail_content_type', function($content_type) {
        return 'text/plain';
    });
    
    // Add mail debugging
    add_action('wp_mail_failed', function($wp_error) {
        error_log('WP Mail failed: ' . $wp_error->get_error_message());
    });
}
configure_tour_booking_mail();

// Debug email configuration (remove in production)
function debug_email_config() {
    $admin_email = get_option('admin_email');
    $site_name = get_bloginfo('name');
    $site_url = get_bloginfo('url');
    
    error_log('=== EMAIL CONFIGURATION DEBUG ===');
    error_log('- Admin Email: ' . $admin_email);
    error_log('- Site Name: ' . $site_name);
    error_log('- Site URL: ' . $site_url);
    error_log('- WordPress Version: ' . get_bloginfo('version'));
    
    // Check if mail function exists
    if (function_exists('mail')) {
        error_log('- PHP mail() function: Available');
    } else {
        error_log('- PHP mail() function: NOT AVAILABLE');
    }
    
    // Check if wp_mail function exists
    if (function_exists('wp_mail')) {
        error_log('- wp_mail() function: Available');
    } else {
        error_log('- wp_mail() function: NOT AVAILABLE');
    }
    
    // Check sendmail path
    $sendmail_path = ini_get('sendmail_path');
    error_log('- Sendmail Path: ' . ($sendmail_path ?: 'Not set'));
    
    // Check SMTP settings
    $smtp_host = ini_get('SMTP');
    $smtp_port = ini_get('smtp_port');
    error_log('- SMTP Host: ' . ($smtp_host ?: 'Not set'));
    error_log('- SMTP Port: ' . ($smtp_port ?: 'Not set'));
    
    // Check if we're on local development
    if (strpos($site_url, 'localhost') !== false || strpos($site_url, '.local') !== false) {
        error_log('- WARNING: Running on local development server - emails may not work');
        error_log('- Consider using a plugin like "Mail SMTP" for local testing');
    }
    
    // Check WordPress mail settings
    $wp_mail_from = get_option('mail_from');
    $wp_mail_from_name = get_option('mail_from_name');
    error_log('- WordPress Mail From: ' . ($wp_mail_from ?: 'Not set'));
    error_log('- WordPress Mail From Name: ' . ($wp_mail_from_name ?: 'Not set'));
    
    error_log('=== END EMAIL CONFIGURATION DEBUG ===');
}
// Email configuration debug function available for future debugging if needed
// debug_email_config();

// Test database connection and table
global $wpdb;
$table_name = $wpdb->prefix . 'tour_bookings';
$table_exists = $wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name;

if (!$table_exists) {
    error_log('Tour bookings table does not exist. Attempting to create...');
    create_tour_bookings_table();
}

// Test email functionality (only run once for debugging)
function test_tour_booking_email() {
    $admin_email = get_option('admin_email');
    $test_subject = 'Tour Booking Email Test - ' . date('Y-m-d H:i:s');
    $test_message = 'This is a test email to verify the tour booking email system is working correctly.' . "\n\n";
    $test_message .= 'Test Details:' . "\n";
    $test_message .= '- Time: ' . current_time('mysql') . "\n";
    $test_message .= '- Site: ' . get_bloginfo('name') . "\n";
    $test_message .= '- URL: ' . get_bloginfo('url') . "\n";
    $test_message .= '- WordPress Version: ' . get_bloginfo('version') . "\n";
    
    $test_headers = array(
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . get_bloginfo('name') . ' <' . $admin_email . '>',
        'Reply-To: ' . $admin_email,
        'X-Mailer: WordPress/' . get_bloginfo('version')
    );
    
    error_log('=== EMAIL TEST STARTING ===');
    error_log('Testing email to: ' . $admin_email);
    error_log('Test subject: ' . $test_subject);
    
    // Test WordPress wp_mail function
    $wp_result = wp_mail($admin_email, $test_subject . ' (WordPress)', $test_message, $test_headers);
    error_log('WordPress wp_mail result: ' . ($wp_result ? 'SUCCESS' : 'FAILED'));
    
    // Test PHP mail function
    $php_headers = implode("\r\n", $test_headers);
    $php_result = mail($admin_email, $test_subject . ' (PHP)', $test_message, $php_headers);
    error_log('PHP mail() result: ' . ($php_result ? 'SUCCESS' : 'FAILED'));
    
    error_log('=== EMAIL TEST COMPLETED ===');
    
    return $wp_result || $php_result;
}

// Email test function available for future debugging if needed
// test_tour_booking_email();

// Handle form submission
if ($_POST && isset($_POST['book_tour_nonce_field']) && wp_verify_nonce($_POST['book_tour_nonce_field'], 'book_tour_nonce')) {
    // Sanitize form data
    $tour_type = sanitize_text_field($_POST['tour_type']);
    $full_name = sanitize_text_field($_POST['full_name']);
    $phone_number = sanitize_text_field($_POST['phone_number']);
    $email_address = sanitize_email($_POST['email_address']);
    $care_home = sanitize_text_field($_POST['care_home']);
    $preferred_date = sanitize_text_field($_POST['preferred_date']);
    $preferred_time = sanitize_text_field($_POST['preferred_time']);
    $message = sanitize_textarea_field($_POST['message']);
    $privacy_notice = isset($_POST['privacy_notice']) ? 1 : 0;
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validate required fields
    $errors = array();
    if (empty($tour_type)) $errors[] = 'Tour type is required';
    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($phone_number)) $errors[] = 'Phone number is required';
    if (empty($care_home)) $errors[] = 'Care home selection is required';
    if (!$privacy_notice) $errors[] = 'Privacy notice acceptance is required';
    
    if (empty($errors)) {
        // Save to database
        global $wpdb;
        $table_name = $wpdb->prefix . 'tour_bookings';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'tour_type' => $tour_type,
                'full_name' => $full_name,
                'phone_number' => $phone_number,
                'email_address' => $email_address,
                'care_home' => $care_home,
                'preferred_date' => $preferred_date,
                'preferred_time' => $preferred_time,
                'message' => $message,
                'privacy_notice' => $privacy_notice,
                'newsletter' => $newsletter,
                'submission_date' => current_time('mysql'),
                'status' => 'pending'
            ),
            array(
                '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s'
            )
        );
        
        if ($result !== false) {
            // Get the booking ID for reference
            $booking_id = $wpdb->insert_id;
            
            // Send email notification to admin and additional recipient
            $admin_email = get_option('admin_email');
            $additional_email = 'alisha.d@mountbox.in';
            $subject = 'New Tour Booking Request - ' . $care_home . ' (ID: ' . $booking_id . ')';
            
            $email_message = "New tour booking request received:\n\n";
            $email_message .= "Tour Type: " . $tour_type . "\n";
            $email_message .= "Full Name: " . $full_name . "\n";
            $email_message .= "Phone Number: " . $phone_number . "\n";
            $email_message .= "Email Address: " . $email_address . "\n";
            $email_message .= "Care Home: " . $care_home . "\n";
            $email_message .= "Preferred Date: " . $preferred_date . "\n";
            $email_message .= "Preferred Time: " . $preferred_time . "\n";
            $email_message .= "Message: " . $message . "\n";
            $email_message .= "Newsletter Signup: " . ($newsletter ? 'Yes' : 'No') . "\n";
            $email_message .= "Submission Date: " . current_time('mysql') . "\n";
            $email_message .= "Booking ID: " . $booking_id . "\n";
            
            // Prepare email headers
            $headers = array(
                'Content-Type: text/plain; charset=UTF-8',
                'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
                'Reply-To: ' . get_option('admin_email'),
                'X-Mailer: WordPress/' . get_bloginfo('version')
            );
            
            // Send email to admin
            $admin_mail_sent = wp_mail($admin_email, $subject, $email_message, $headers);
            
            // Send email to additional recipient
            $additional_mail_sent = wp_mail($additional_email, $subject, $email_message, $headers);
            
            // Log email sending status for debugging
            error_log('Tour booking emails - Admin (' . $admin_email . '): ' . ($admin_mail_sent ? 'sent' : 'failed') . ', Additional (' . $additional_email . '): ' . ($additional_mail_sent ? 'sent' : 'failed'));
            
            // If emails failed, try alternative method
            if (!$admin_mail_sent || !$additional_mail_sent) {
                error_log('Primary email method failed, trying alternative...');
                
                // Try sending with PHP mail() directly as fallback
                if (!$admin_mail_sent) {
                    $admin_mail_sent = mail($admin_email, $subject, $email_message, implode("\r\n", $headers));
                    error_log('Alternative admin email: ' . ($admin_mail_sent ? 'sent' : 'failed'));
                }
                
                if (!$additional_mail_sent) {
                    $additional_mail_sent = mail($additional_email, $subject, $email_message, implode("\r\n", $headers));
                    error_log('Alternative additional email: ' . ($additional_mail_sent ? 'sent' : 'failed'));
                }
            }
            
            // Send confirmation email to user (if email provided)
            if (!empty($email_address)) {
                $user_subject = 'Tour Booking Confirmation - ' . get_bloginfo('name');
                $user_message = "Dear " . $full_name . ",\n\n";
                $user_message .= "Thank you for your tour booking request!\n\n";
                $user_message .= "We have received your request for a " . $tour_type . " at " . $care_home . ".\n";
                if (!empty($preferred_date)) {
                    $user_message .= "Preferred Date: " . $preferred_date . "\n";
                }
                if (!empty($preferred_time)) {
                    $user_message .= "Preferred Time: " . $preferred_time . "\n";
                }
                $user_message .= "\nOur team will contact you shortly to confirm your visit and provide further details.\n\n";
                $user_message .= "If you have any questions, please don't hesitate to contact us.\n\n";
                $user_message .= "Best regards,\n";
                $user_message .= get_bloginfo('name') . " Team";
                
                // Prepare user email headers
                $user_headers = array(
                    'Content-Type: text/plain; charset=UTF-8',
                    'From: ' . get_bloginfo('name') . ' <' . get_option('admin_email') . '>',
                    'Reply-To: ' . get_option('admin_email'),
                    'X-Mailer: WordPress/' . get_bloginfo('version')
                );
                
                $user_mail_sent = wp_mail($email_address, $user_subject, $user_message, $user_headers);
                error_log('Tour booking user email (' . $email_address . '): ' . ($user_mail_sent ? 'sent' : 'failed'));
                
                // If user email failed, try alternative method
                if (!$user_mail_sent) {
                    error_log('Primary user email method failed, trying alternative...');
                    $user_mail_sent = mail($email_address, $user_subject, $user_message, implode("\r\n", $user_headers));
                    error_log('Alternative user email: ' . ($user_mail_sent ? 'sent' : 'failed'));
                }
            }
            
            // Show success modal with email status
            $email_status_message = '';
            if ($admin_mail_sent && $additional_mail_sent) {
                $email_status_message = 'Confirmation emails have been sent to our team and to your email address.';
            } else {
                $email_status_message = 'Your booking has been submitted successfully. Our team will contact you shortly.';
                error_log('Some emails failed to send - Admin: ' . ($admin_mail_sent ? 'sent' : 'failed') . ', Additional: ' . ($additional_mail_sent ? 'sent' : 'failed') . ', User: ' . ($user_mail_sent ?? 'not sent'));
            }
            
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        showSuccessModal("' . esc_js($email_status_message) . '");
                    });
                  </script>';
            
            // Clear form data to prevent resubmission
            $_POST = array();
        } else {
            // Log database error for debugging
            error_log('Tour booking database error: ' . $wpdb->last_error);
            
            echo '<div class="error-message" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #dc3545;">
                    <h3 style="margin: 0 0 10px 0; color: #721c24;">✗ Error</h3>
                    <p style="margin: 0;">There was an error submitting your request. Please try again or contact us directly.</p>
                    <p style="margin: 10px 0 0 0; font-size: 0.9em; opacity: 0.8;">Error: ' . esc_html($wpdb->last_error) . '</p>
                  </div>';
        }
    } else {
        echo '<div class="error-message" style="background: #f8d7da; color: #721c24; padding: 20px; border-radius: 8px; margin-bottom: 30px; border-left: 4px solid #dc3545;">
                <h3 style="margin: 0 0 10px 0; color: #721c24;">✗ Please fix the following errors:</h3>
                <ul style="margin: 10px 0 0 20px;">
                    ' . implode('', array_map(function($error) { return '<li>' . esc_html($error) . '</li>'; }, $errors)) . '
                </ul>
              </div>';
    }
}
?>

<?php
// Create database table if it doesn't exist
function create_tour_bookings_table() {
    global $wpdb;
    
    $table_name = $wpdb->prefix . 'tour_bookings';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        tour_type varchar(50) NOT NULL,
        full_name varchar(100) NOT NULL,
        phone_number varchar(20) NOT NULL,
        email_address varchar(100),
        care_home varchar(100) NOT NULL,
        preferred_date date,
        preferred_time time,
        message text,
        privacy_notice tinyint(1) DEFAULT 0,
        newsletter tinyint(1) DEFAULT 0,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP,
        status varchar(20) DEFAULT 'pending',
        PRIMARY KEY (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Run on theme activation and ensure table exists
add_action('after_switch_theme', 'create_tour_bookings_table');

// Also run on page load to ensure table exists
add_action('init', 'create_tour_bookings_table');
?>

<?php get_footer(); ?>
