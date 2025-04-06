/**
 * Task Management Controller - Handles task loading and operations
 * 
 * Manages the complete task lifecycle including:
 * - Initial task loading with loading state
 * - Task rendering with proper status indicators
 * - Delete/Complete/Edit operations
 * - User feedback and error handling
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @file fetch.js
 */
$(document).ready(function() {
    /**
     * Cached table elements
     * @type {jQuery}
     */
    const $taskTable = $('#taskTable');
    const $taskBody = $taskTable.find('tbody');
    
    // Load tasks immediately on page ready
    loadTasks();

    /**
     * Loads tasks from server via AJAX
     * Shows loading state during request
     * Handles empty state and error cases
     */
    function loadTasks() {
        $.ajax({
            url: 'scripts/tasks/fetch.php',
            method: 'GET',
            dataType: 'json',
            /**
             * Shows loading spinner before request is sent
             */
            beforeSend: function() {
                $taskBody.html(`
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </td>
                    </tr>
                `);
            },
            /**
             * Handles successful task loading
             * @param {Object} response - Server response
             * @property {Array} data - Array of task objects
             */
            success: function(response) {
                if (response.data?.length > 0) {
                    renderTasks(response.data);
                } else {
                    showNoTasks();
                }
            },
            /**
             * Handles AJAX errors during task loading
             * @param {Object} xhr - jQuery XHR object
             */
            error: function(xhr) {
                showError('Failed to load tasks. Please try again.');
                console.error('Error:', xhr.responseText);
            }
        });
    }

    /**
     * Renders tasks to the table
     * @param {Array} tasks - Array of task objects
     */
    function renderTasks(tasks) {
        $taskBody.empty();
        
        tasks.forEach(task => {
            const $row = $(`
                <tr data-task-id="${task.id}">
                    <td>${escapeHtml(task.title)}</td>
                    <td>${escapeHtml(task.description)}</td>
                    <td>${task.due_date || 'No due date'}</td>
                    <td>${getStatusBadge(task.status)}</td>
                    <td>
                        <div class="d-flex">
                            <button class="btn btn-sm btn-primary btn-edit me-1" data-id="${task.id}">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${task.status !== 'Completed' ? 
                                `<button class="btn btn-sm btn-success btn-complete me-1" data-id="${task.id}">
                                    <i class="fas fa-check"></i>
                                </button>` : ''}
                            <button class="btn btn-sm btn-danger btn-delete" data-id="${task.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
            $taskBody.append($row);
        });
    }

    /**
     * Delete task handler (event delegation)
     * @listens .btn-delete#click
     */
    $(document).on('click', '.btn-delete', function() {
        const taskId = $(this).data('id');
        const $row = $(this).closest('tr');
        
        if (!confirm('Are you sure you want to delete this task?')) return;
        
        console.log('Attempting to delete task ID:', taskId);
        
        $.ajax({
            url: 'scripts/tasks/delete.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ task_id: taskId }),
            dataType: 'json',
            success: function(response) {
                console.log('Delete response:', response);
                if (response.success) {
                    $row.fadeOut(300, function() { $(this).remove(); });
                    showSuccess('Task deleted successfully!');
                } else {
                    showError(response.message || 'Failed to delete task');
                }
            },
            error: function(xhr) {
                console.error('Delete error:', xhr.responseText);
                showError('Server error - check console');
            }
        });
    });

    /**
     * Complete task handler (event delegation)
     * @listens .btn-complete#click
     */
    $(document).on('click', '.btn-complete', function() {
        const taskId = $(this).data('id');
        $.ajax({
            url: 'scripts/tasks/complete.php',
            method: 'POST',
            data: { task_id: taskId },
            success: function(response) {
                if (response.success) {
                    loadTasks(); // Refresh the list
                    showSuccess('Task marked as complete');
                } else {
                    showError(response.message || 'Failed to complete task');
                }
            },
            error: function(xhr) {
                showError('Server error - check console');
                console.error('Complete error:', xhr.responseText);
            }
        });
    });

    /**
     * Edit task handler placeholder (event delegation)
     * @listens .btn-edit#click
     */
    $(document).on('click', '.btn-edit', function() {
        const taskId = $(this).data('id');
        console.log('Edit task:', taskId);
        // Edit implementation will go here
    });

    /**
     * Shows empty state when no tasks exist
     */
    function showNoTasks() {
        $taskBody.html(`
            <tr>
                <td colspan="5" class="text-center py-4 text-muted">
                    No tasks found. Create your first task!
                </td>
            </tr>
        `);
    }

    /**
     * Generates status badge HTML with appropriate styling
     * @param {string} status - Task status
     * @return {string} HTML badge element
     */
    function getStatusBadge(status) {
        const statusClass = {
            'Pending': 'bg-warning',
            'In Progress': 'bg-info',
            'Completed': 'bg-success'
        }[status] || 'bg-secondary';
        return `<span class="badge ${statusClass}">${status}</span>`;
    }

    /**
     * Shows success notification
     * @param {string} message - Success message
     */
    function showSuccess(message) {
        showToast(message, 'bg-success');
    }

    /**
     * Shows error notification
     * @param {string} message - Error message
     */
    function showError(message) {
        showToast(message, 'bg-danger');
    }

    /**
     * Displays toast notification
     * @param {string} message - Notification content
     * @param {string} bgClass - Background CSS class
     */
    function showToast(message, bgClass) {
        const toast = $('#operationToast');
        toast.find('.toast-body').text(message);
        toast.removeClass('bg-success bg-danger').addClass(bgClass);
        toast.toast('show');
    }

    /**
     * Escapes HTML special characters to prevent XSS
     * @param {string} text - Unsafe input text
     * @return {string} Sanitized HTML string
     */
    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});