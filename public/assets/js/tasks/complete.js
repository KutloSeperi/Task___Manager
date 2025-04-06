/**
 * Task Completion Handler - Manages task status updates
 * 
 * Handles marking tasks as complete through API calls and UI updates.
 * Uses page refresh to ensure consistency between frontend and backend.
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @file complete.js
 */
$(document).on('click', '.btn-complete', function() {
    /**
     * Extract task ID from button data attribute
     * @type {string|number}
     */
    const taskId = $(this).data('id');
    console.log('Attempting to complete task ID:', taskId);

    /**
     * AJAX Request Configuration
     * @type {Object}
     * @property {string} url - API endpoint
     * @property {string} type - HTTP method
     * @property {Object} headers - Request headers
     * @property {string} headers.Content-Type - JSON content type
     * @property {string} headers.X-Requested-With - AJAX identifier
     * @property {string} data - JSON payload
     * @property {string} dataType - Expected response format
     * @property {boolean} processData - Data processing flag
     */
    const requestConfig = {
        url: 'scripts/tasks/complete.php',
        type: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        data: JSON.stringify({ task_id: taskId }),
        dataType: 'json',
        processData: false
    };

    /**
     * Disable button to prevent duplicate submissions
     */
    $(this).prop('disabled', true);

    /**
     * Execute AJAX request with promise handlers
     */
    $.ajax(requestConfig)
        .done(handleCompletionSuccess)
        .fail(handleCompletionError)
        .always(function() {
            console.debug('Completion processed for task:', taskId);
        });

    /**
     * Handles successful completion response
     * @callback handleCompletionSuccess
     * @param {Object} response - Server response object
     * @property {boolean} success - Operation status
     * @property {string} [message] - Optional status message
     */
    function handleCompletionSuccess(response) {
        if (!response || typeof response.success === 'undefined') {
            console.error('Invalid response format:', response);
            alert('Unexpected server response');
            return;
        }

        if (response.success) {
            // Force page refresh to ensure UI consistency
            location.reload();
        } else {
            alert(response.message || 'Task completion failed');
            console.warn('Server reported failure:', response.message);
        }
    }

    /**
     * Handles AJAX request failure
     * @callback handleCompletionError
     * @param {Object} xhr - jQuery XHR object
     * @param {string} status - Error status text
     * @param {string} error - Error message
     */
    function handleCompletionError(xhr, status, error) {
        console.error('Completion error:', {
            status: xhr.status,
            statusText: xhr.statusText,
            response: xhr.responseText,
            error: error
        });

        /**
         * Re-enable button to allow retry
         */
        $(`.btn-complete[data-id="${taskId}"]`).prop('disabled', false);
        
        alert('Failed to complete task. Please try again or refresh the page.');
    }
});