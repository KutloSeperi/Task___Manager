/**
 * Client-side login form handler
 * 
 * Handles form submission, performs client-side validation,
 * and communicates with server via AJAX. Manages user
 * feedback during authentication process.
 * 
 * @author Kutlo Sepesi
 * @since 1.0.0
 */
$(document).ready(function() {
    // Form submission handler
    $('#loginForm').submit(function(e) {
        e.preventDefault();
        
        // Get form values
        const username = $('#loginUsername').val().trim();
        const password = $('#loginPassword').val();
        
        // Validate required fields
        if (!username || !password) {
            showMessage('loginMessage', 'Username and password are required', 'error');
            return;
        }
        
        /* AJAX login request configuration
         * @type {object}
         * @property {string} url - Authentication endpoint
         * @property {string} method - HTTP verb
         * @property {string} contentType - Request format
         * @property {string} data - Credentials payload
         */
        $.ajax({
            url: 'scripts/auth/login.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                username: username,
                password: password
            }),
            
            // Success callback
            success: function(response) {
                if (response.success) {
                    window.location.href = 'dashboard.php';
                } else {
                    showMessage('loginMessage', response.message, 'error');
                }
            },
            
            // Error callback
            error: function() {
                showMessage('loginMessage', 'An error occurred during login', 'error');
            }
        });
    });
});

/**
 * Displays UI message to user
 * @param {string} elementId - Target container ID
 * @param {string} message - Content to display
 * @param {string} type - Message category (error/success/info)
 */
function showMessage(elementId, message, type) {
    const $element = $('#' + elementId);
    $element.text(message).removeClass().addClass(type);
}