/**
 * Task Editing Handler - Manages task editing workflow
 * 
 * Handles:
 * - Populating edit modal with task data
 * - Processing date formatting
 * - Saving updated task information
 * 
 * @author Kutlo Sepesi
 * @requires jQuery, Bootstrap Modal
 * @file Handles task editing operations
 */
$(document).ready(function() {
    /**
     * Initialize task edit button handler
     * 
     * Manages:
     * - Click events on edit buttons
     * - Modal population with current task data
     * - Date format conversion for datepicker
     */
    $(document).on('click', '.btn-edit', function() {
        // Get task ID and reference to table row
        const taskId = $(this).data('id');
        const $row = $(this).closest('tr');
        
        /**
         * Populate edit modal with current values
         * 
         * Extracts values from table cells and formats for form:
         * - Title (column 0)
         * - Description (column 1)
         * - Due date (column 2, with "No due date" handling)
         */
        $('#edit_task_id').val(taskId);
        $('#edit_title').val($row.find('td:eq(0)').text());
        $('#edit_description').val($row.find('td:eq(1)').text());
        
        /* Date parsing and formatting */
        const dueText = $row.find('td:eq(2)').text();
        const dueDate = dueText.includes('No due') ? '' : 
            new Date(dueText).toISOString().split('T')[0];
        $('#edit_due_date').val(dueDate);
        
        // Display modal
        $('#editTaskModal').modal('show');
    });

    /**
     * Save task changes handler
     * 
     * Manages:
     * - Form data collection
     * - AJAX submission to server
     * - Response handling
     */
    $('#saveTaskChanges').click(function() {
        /**
         * @typedef {Object} EditTaskRequest
         * @property {string} task_id - ID of task being edited
         * @property {string} title - Updated task title
         * @property {string} description - Updated task description
         * @property {string|null} due_date - Updated due date (ISO format or null)
         */
        const formData = {
            task_id: $('#edit_task_id').val(),
            title: $('#edit_title').val(),
            description: $('#edit_description').val(),
            due_date: $('#edit_due_date').val() || null
        };
        
        /**
         * Execute task update request
         * 
         * @typedef {Object} EditTaskResponse
         * @property {boolean} success - Update result
         * @property {string} message - Server response message
         */
        $.ajax({
            url: 'scripts/tasks/edit.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(formData),
            success: function(response) {
                if (response.success) {
                    
                    location.reload();
                } else {
                    
                    console.error('Task update failed:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Task update request failed:', status, error);
                
            }
        });
    });
});