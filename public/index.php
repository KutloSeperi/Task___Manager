<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager - Login/Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="bg-light min-vh-100 d-flex align-items-center">
    <div class="container-lg"> <!-- Changed to container-lg for better width control -->
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6"> <!-- Adjusted column sizes -->
                <div class="card shadow">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-justified">
                            <li class="nav-item">
                                <a class="nav-link active py-3" data-bs-toggle="tab" href="#login">Login</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3" data-bs-toggle="tab" href="#register">Register</a>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="card-body p-4">
                        <div class="tab-content">
                            <!-- Login Tab -->
                            <div class="tab-pane fade show active" id="login">
                                <form id="loginForm" class="gap-3">
                                    <div class="mb-3">
                                        <label for="loginUsername" class="form-label fw-bold">Username</label>
                                        <input type="text" class="form-control form-control-lg" id="loginUsername" name="username" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="loginPassword" class="form-label fw-bold">Password</label>
                                        <input type="password" class="form-control form-control-lg" id="loginPassword" name="password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Login</button>
                                </form>
                                <div id="loginMessage" class="mt-3"></div>
                            </div>
                            
                            <!-- Register Tab -->
                            <div class="tab-pane fade" id="register">
                                <form id="registerForm" class="gap-3">
                                    <div class="mb-3">
                                        <label for="regUsername" class="form-label fw-bold">Username</label>
                                        <input type="text" class="form-control form-control-lg" id="regUsername" name="username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="regEmail" class="form-label fw-bold">Email</label>
                                        <input type="email" class="form-control form-control-lg" id="regEmail" name="email" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="regPassword" class="form-label fw-bold">Password</label>
                                        <input type="password" class="form-control form-control-lg" id="regPassword" name="password" required>
                                    </div>
                                    <div class="mb-4">
                                        <label for="regConfirmPassword" class="form-label fw-bold">Confirm Password</label>
                                        <input type="password" class="form-control form-control-lg" id="regConfirmPassword" name="confirmPassword" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg w-100">Register</button>
                                </form>
                                <div id="registerMessage" class="mt-3"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/auth/login.js"></script>
    <script src="assets/js/auth/register.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>