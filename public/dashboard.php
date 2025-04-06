<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Core\Session;

Session::start();
$username = Session::get('username');

if (!$username) {
    header('Location: login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/assets/css/style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="dashboard-layout">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-tasks me-2"></i>Task Manager
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        <span class="d-none d-sm-inline"><?php echo htmlspecialchars($username); ?></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        
                        <li><button id="logoutBtn" class="dropdown-item"><i class="fas fa-sign-out-alt me-2"></i>Logout</button></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card task-manager-card shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0"><i class="fas fa-tasks me-2"></i>Your Tasks</h4>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="collapse" href="#taskFormCollapse">
                                    <i class="fas fa-plus me-1"></i> New Task
                                </button>
                            </div>

                            <!-- Task Form (Collapsible) -->
                            <div class="collapse mb-4" id="taskFormCollapse">
                                <form id="createTaskForm" class="row g-3">
                                    <div class="col-md-6">
                                        <label for="title" class="form-label">Title</label>
                                        <input type="text" id="title" class="form-control" placeholder="Buy groceries" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="due_date" class="form-label">Due Date</label>
                                        <input type="date" id="due_date" class="form-control">
                                    </div>
                                    <div class="col-12">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" class="form-control" rows="2" placeholder="Milk, eggs, bread..."></textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary px-4">
                                            <i class="fas fa-save me-1"></i> Save Task
                                        </button>
                                    </div>
                                </form>
                                <hr class="my-3">
                            </div>

                            <!-- Task Filters -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary active filter-btn" data-filter="all">All</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="pending">Pending</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary filter-btn" data-filter="completed">Completed</button>
                                </div>
                                <div class="input-group" style="width: 250px;">
                                    <input type="text" class="form-control form-control-sm" placeholder="Search tasks...">
                                    <button class="btn btn-sm btn-outline-secondary" type="button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            

                            <!-- Task Table -->
                            <div class="table-responsive">
                                <table class="table table-hover align-middle" id="taskTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="25%">Title</th>
                                            <th width="35%">Description</th>
                                            <th width="15%">Due Date</th>
                                            <th width="15%">Status</th>
                                            <th width="10%" class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Tasks will be loaded here -->
                                        <tr class="task-placeholder">
                                            <td colspan="5" class="text-center py-4">
                                                <div class="spinner-border text-primary" role="status">
                                                    <span class="visually-hidden">Loading...</span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Edit Task Modal -->
    <div class="modal fade" id="editTaskModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Task</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editTaskForm">
                        <input type="hidden" id="edit_task_id">
                        <div class="mb-3">
                            <label for="edit_title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit_title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control" id="edit_due_date">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveTaskChanges" class="btn btn-primary">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div id="successToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-success text-white">
                <strong class="me-auto">Success</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header bg-danger text-white">
                <strong class="me-auto">Error</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body"></div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer bg-light py-3 ">
        <div class="container text-center">
            <small class="text-muted">&copy; <?php echo date('Y'); ?> Task Manager. All rights reserved.</small>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="assets/js/app.js"></script>
    <script src="assets/js/tasks/create.js"></script>
    <script src="assets/js/tasks/fetch.js"></script>
    <script src="assets/js/tasks/complete.js"></script>
    <script src="assets/js/tasks/delete.js"></script>
    <script src="assets/js/tasks/edit.js"></script>

    <script>
        // Initialize Bootstrap components
        const editTaskModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
        const successToast = new bootstrap.Toast(document.getElementById('successToast'));
        const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
        
        // Logout Button Action
        $('#logoutBtn').click(function() {
            window.location.href = 'scripts/auth/logout.php';
        });
    </script>
</body>
</html>