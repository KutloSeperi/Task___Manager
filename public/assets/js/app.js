/**
 * Tab and Session Management Module
 * 
 * Handles UI tab navigation and user logout functionality.
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @since 1.0.0
 */
$(document).ready(function() {
    /**
     * Tab switching functionality
     * @listens .tab-btn#click
     */
    $('.tab-btn').click(function() {
        /**
         * Target tab ID from data attribute
         * @type {string}
         */
        const tabId = $(this).data('tab');
        
        // Update active states
        $('.tab-btn').removeClass('active');
        $('.tab-content').removeClass('active');
        
        // Set new active tab
        $(this).addClass('active');
        $('#' + tabId).addClass('active');
    });
    
    /**
     * Logout button handler
     * @listens #logoutBtn#click
     */
    $('#logoutBtn').click(function(e) {
        e.preventDefault();
        
        /**
         * Button element in loading state
         * @type {jQuery}
         */
        const $btn = $(this);
        $btn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Logging out...
        `);

        /**
         * AJAX logout request configuration
         * @type {Object}
         */
        $.ajax({
            url: 'scripts/auth/logout.php',
            method: 'POST',
            dataType: 'json',
            /**
             * Handles successful logout response
             * @param {Object} response - Server response
             */
            success: function(response) {
                window.location.href = 'index.php';
            },
            /**
             * Handles logout failure
             * @param {Object} xhr - jQuery XHR object
             */
            error: function(xhr) {
                console.error('Logout error:', xhr.responseText);
                window.location.href = 'index.php';
            }
        });
    });
});