/**
 * Task Management Handler - Manages task deletion functionality
 * 
 * Handles task deletion requests, UI updates, and empty state management
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @file Handles task deletion operations
 */
$(document).ready(function() {
    /**
     * Initialize task deletion handler
     * 
     * Manages:
     * - Click events on delete buttons
     * - Task deletion API requests
     * - Dynamic UI updates after deletion
     * - Empty state management
     */
    $(document).on('click', '.btn-delete', function() {
        // Extract task ID from data attribute
        const taskId = $(this).data('id');
        
        /**
         * Execute task deletion request
         * 
         * @typedef {Object} DeleteRequest
         * @property {string} task_id - ID of task to delete
         * 
         * @typedef {Object} DeleteResponse
         * @property {boolean} success - Deletion result
         * @property {string} message - Server response message
         */
        $.ajax({
            url: 'scripts/tasks/delete.php',
            method: 'DELETE',
            data: { task_id: taskId },
            success: function(response) {
                // TODO: Replace alert with toast notification
                alert('Task deleted successfully!');
                
                // Remove corresponding task row from DOM
                $(`tr[data-task-id="${taskId}"]`).remove();
                
                /**
                 * Handle empty table state
                 * 
                 * Checks if table is empty after deletion and displays
                 * placeholder message if no tasks remain
                 */
                if ($('#taskTable tbody tr').length === 0) {
                    $('#taskTable tbody').html(
                        '<tr><td colspan="5" class="text-center py-4 text-muted">No tasks found.</td></tr>'
                    );
                }
            },
            error: function(xhr) {
                // Fallback to page reload on error
                // TODO: Implement proper error handling with user feedback
                console.error('Task deletion failed:', xhr.responseText);
                location.reload();
            }
        });
    });
});