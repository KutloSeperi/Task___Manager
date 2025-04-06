/**
 * TaskDataTable Module - Encapsulates DataTable configuration and operations
 * 
 * Manages initialization and server-side data loading for the tasks table.
 * Provides table reload capability without exposing internal implementation.
 * 
 * @author Kutlo Sepesi
 * @requires jQuery
 * @requires DataTables
 */
const TaskDataTable = (() => {
    /**
     * Internal DataTable instance reference
     * @type {DataTable}
     * @private
     */
    let dataTable;

    /**
     * Initializes DataTable with AJAX loading and column configuration
     * Configures server-side data source and custom action button rendering
     */
    const init = () => {
        dataTable = $('#taskTable').DataTable({
            /**
             * Server-side data loading configuration
             * @property {string} url - Endpoint for task data
             * @property {string} dataSrc - Response data property containing records
             */
            ajax: {
                url: 'scripts/tasks/fetch.php',
                dataSrc: 'data'
            },
            /**
             * Column definitions including custom renderers
             * @property {Object[]} columns - Column configuration objects
             */
            columns: [
                { data: 'title' },
                { data: 'description' },
                { 
                    data: 'due_date',
                    render: function(data) {
                        return data || 'No due date';
                    }
                },
                { 
                    data: 'status',
                    render: function(data) {
                        const statusClass = {
                            'Pending': 'warning',
                            'In Progress': 'info',
                            'Completed': 'success'
                        }[data] || 'secondary';
                        return `<span class="badge bg-${statusClass}">${data}</span>`;
                    }
                },
                { 
                    data: null,
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary editBtn" data-id="${data.id}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                ${data.status !== 'Completed' ? 
                                    `<button class="btn btn-sm btn-outline-success completeBtn" data-id="${data.id}">
                                        <i class="fas fa-check"></i>
                                    </button>` : ''}
                                <button class="btn btn-sm btn-outline-danger deleteBtn" data-id="${data.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    }
                }
            ]
        });
    };

    /**
     * Reloads table data from server
     * Maintains current pagination and sorting state
     */
    const reload = () => dataTable.ajax.reload();

    return { 
        init, 
        reload 
    };
})();