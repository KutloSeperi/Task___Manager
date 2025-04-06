/**
 * Client-side registration form handler
 * 
 * Manages form submission, performs client-side validation,
 * and communicates with server via AJAX. Provides user feedback
 * during registration process and handles success/error cases.
 * 
 * @author Kutlo Sepesi
 * 
 */
$(document).ready(function() {
    // Form submission handler
    $('#registerForm').submit(function(e) {
        e.preventDefault();
        
        // Get form values
        const username = $('#regUsername').val().trim();
        const email = $('#regEmail').val().trim();
        const password = $('#regPassword').val();
        const confirmPassword = $('#regConfirmPassword').val();
        
        /* Client-side validation checks
         * @validate Required fields
         * @validate Password match
         * @validate Password length
         */
        if (!username || !email || !password || !confirmPassword) {
            showMessage('registerMessage', 'All fields are required', 'error');
            return;
        }
        
        if (password !== confirmPassword) {
            showMessage('registerMessage', 'Passwords do not match', 'error');
            return;
        }
        
        if (password.length < 6) {
            showMessage('registerMessage', 'Password must be at least 6 characters', 'error');
            return;
        }
        
        /* AJAX registration request configuration
         * @type {object}
         * @property {string} url - Registration endpoint
         * @property {string} method - HTTP verb
         * @property {string} contentType - Request format
         * @property {string} data - Registration payload
         */
        $.ajax({
            url: 'scripts/auth/register.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                email: email,
                password: password,
                confirmPassword: confirmPassword
            }),
            
            // Success callback
            success: function(response) {
                if (response.success) {
                    showMessage('registerMessage', response.message, 'success');
                    $('#registerForm')[0].reset();
                    // Switch to login tab after successful registration
                    $('.tab-btn').removeClass('active');
                    $('.tab-content').removeClass('active');
                    $('[data-tab="login"]').addClass('active');
                    $('#login').addClass('active');
                } else {
                    showMessage('registerMessage', response.message, 'error');
                }
            },
            
            // Error callback
            error: function() {
                showMessage('registerMessage', 'An error occurred during registration', 'error');
            }
        });
    });
});

/**
 * Displays UI message with timed fade-out
 * @param {string} elementId - Target container ID
 * @param {string} message - Content to display
 * @param {string} type - Message type (error/success)
 */
function showMessage(elementId, message, type) {
    const element = $('#' + elementId);
    element.text(message);
    element.removeClass('success error').addClass(type);
    element.fadeIn().delay(3000).fadeOut();
}