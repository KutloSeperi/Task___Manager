/**
 * Task Creation Handler - Manages new task submission
 * 
 * Handles form validation, AJAX submission, and UI updates for task creation.
 * Includes duplicate submission prevention and connection error recovery.
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @file create.js
 */
$(document).ready(function() {
    /**
     * Flag to prevent duplicate form submissions
     * @type {boolean}
     */
    let isSubmitting = false;

    /**
     * Form submission handler for task creation
     * @listens #createTaskForm#submit
     */
    $('#createTaskForm').on('submit', function(e) {
        e.preventDefault();

        // Prevent duplicate submissions
        if (isSubmitting) return;
        isSubmitting = true;

        /**
         * Disable submit button during processing
         * @type {jQuery}
         */
        const $submitBtn = $(this).find('[type="submit"]');
        $submitBtn.prop('disabled', true).text('Creating...');

        /**
         * Collected form data
         * @type {Object}
         * @property {string} title - Task title
         * @property {string} description - Task description
         * @property {string|null} due_date - Optional due date
         */
        const formData = {
            title: $('#title').val().trim(),
            description: $('#description').val().trim(),
            due_date: $('#due_date').val() || null
        };

        // Client-side validation
        if (!formData.title) {
            showAlert('error', 'Title is required!');
            $submitBtn.prop('disabled', false).text('Create Task');
            isSubmitting = false;
            return;
        }

        console.log('Creating task:', formData);

        /**
         * AJAX Request Configuration
         * @type {Object}
         */
        $.ajax({
            type: 'POST',
            url: 'scripts/tasks/create.php',
            data: JSON.stringify(formData),
            contentType: 'application/json',
            dataType: 'json',
            
            /**
             * Handles successful task creation
             * @callback successCallback
             * @param {Object} response - Server response
             */
            success: function(response) {
                console.log('Server response:', response);
                
                if (!response || typeof response.success === 'undefined') {
                    console.error('Invalid response format:', response);
                    showAlert('error', 'Unexpected server response');
                    return;
                }

                if (response.success) {
                    location.reload();
                    
                    if (response.task) {
                        prependTaskToUI(response.task);
                    }
                    
                    $('#createTaskForm')[0].reset();
                    showAlert('success', 'Task created successfully!');
                } else {
                    showAlert('error', response.error || 'Failed to create task');
                }
            },
            
            /**
             * Handles AJAX errors
             * @callback errorCallback
             */
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                showAlert('error', 'Connection error. Task may have been created.');
                setTimeout(verifyTaskCreation, 2000, formData.title);
            },
            
            /**
             * Always executes after request completes
             */
            complete: function() {
                isSubmitting = false;
                $submitBtn.prop('disabled', false).text('Create Task');
            }
        });
    });

    /**
     * Adds new task to UI without page reload
     * @param {Object} task - Task data object
     * @property {number} id - Task ID
     * @property {string} title - Task title
     * @property {string} description - Task description
     * @property {string} [due_date] - Optional due date
     */
    function prependTaskToUI(task) {
        const taskHtml = `
            <tr data-task-id="${task.id}">
                <td>${escapeHtml(task.title)}</td>
                <td>${escapeHtml(task.description)}</td>
                <td>${task.due_date || 'No deadline'}</td>
                <td><span class="status-badge bg-warning">Pending</span></td>
                <td>
                    <button class="btn-complete" data-id="${task.id}">Complete</button>
                </td>
            </tr>
        `;
        $('#tasksTable tbody').prepend(taskHtml);
    }

    /**
     * Displays user notification messages
     * @param {string} type - Message type ('success'|'error'|'warning')
     * @param {string} message - Notification content
     */
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const $alertBox = $('#notification-area');
        
        if ($alertBox.length) {
            $alertBox.html(`
                <div class="alert ${alertClass} alert-dismissible fade show">
                    ${message}
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                </div>
            `);
        } else {
            alert(message);
        }
    }

    /**
     * Verifies task creation after connection issues
     * @param {string} title - Task title to verify
     */
    function verifyTaskCreation(title) {
        $.get('scripts/tasks/verify.php', { title: title }, function(response) {
            if (response.exists) {
                showAlert('warning', 
                    'Task was created despite connection error. Refreshing...',
                    function() { location.reload(); }
                );
            }
        });
    }

    /**
     * Basic HTML escaping for XSS protection
     * @param {string} unsafe - Untrusted input string
     * @return {string} Sanitized HTML string
     */
    function escapeHtml(unsafe) {
        return unsafe?.toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;") || '';
    }
});