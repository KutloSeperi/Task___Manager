/**
 * Tab and Session Management Module
 * 
 * Handles UI 
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * 
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
    * 
    *  DOM Elements
    */
        
    const $filterButtons = $('.filter-btn');
    const $searchInput = $('.input-group input');
    const $searchButton = $('.input-group button');
    const $taskTable = $('#taskTable tbody');
    
    // Initialize functionality
    initTaskFilters();
    initTaskSearch();
    
    /**
     * Initialize task status filters
     */
    function initTaskFilters() {
        $filterButtons.on('click', function() {
            // Update UI state
            $filterButtons.removeClass('active');
            $(this).addClass('active');
            
            // Apply current filters
            applyFilters();
        });
    }
    
    /**
     * Initialize task search functionality
     */
    function initTaskSearch() {
        // Search button click handler
        $searchButton.on('click', applyFilters);
        
        // Search on Enter key
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) applyFilters();
        });
        
        // Optional: Real-time search
        $searchInput.on('input', function() {
            if ($(this).val().trim() === '') applyFilters();
        });
    }
    
    /**
     * Apply current filters and search term
     */
    function applyFilters() {
        const activeFilter = $('.filter-btn.active').data('filter');
        const searchTerm = $searchInput.val().toLowerCase().trim();
        
        let hasVisibleTasks = false;
        
        $taskTable.find('tr').each(function() {
            const $row = $(this);
            
            // Skip loading placeholders and message rows
            if ($row.hasClass('task-placeholder') || $row.hasClass('no-tasks-row')) {
                return;
            }
            
            // Get task status from the badge text
            const status = $row.find('td:nth-child(4) .badge').text().toLowerCase();
            const rowText = $row.text().toLowerCase();
            
            // Check against filters
            const matchesFilter = activeFilter === 'all' || status === activeFilter;
            const matchesSearch = searchTerm === '' || rowText.includes(searchTerm);
            
            // Show/hide row
            $row.toggle(matchesFilter && matchesSearch);
            
            // Track if we have any visible rows
            if (matchesFilter && matchesSearch) hasVisibleTasks = true;
        });
        
        // Update no tasks message
        updateNoTasksMessage(activeFilter, searchTerm, hasVisibleTasks);
    }
    
    /**
     * Update the "no tasks" message based on current filters
     */
    function updateNoTasksMessage(activeFilter, searchTerm, hasVisibleTasks) {
        // Remove any existing message
        $('.no-tasks-row').remove();
        
        // Add message if no tasks visible
        if (!hasVisibleTasks) {
            let message = 'No tasks found';
            
            if (activeFilter !== 'all') {
                message = `No ${activeFilter} tasks`;
            }
            
            if (searchTerm) {
                message += ` matching "${searchTerm}"`;
            }
            
            $taskTable.append(`
                <tr class="no-tasks-row">
                    <td colspan="5" class="text-center py-4 text-muted">
                        ${message}
                    </td>
                </tr>
            `);
        }
    }
    
    // Apply filters when new tasks are loaded
    $(document).on('tasksLoaded', function() {
        applyFilters();
    });
    
    // Initialize filters on page load
    applyFilters();

    //
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