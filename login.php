<?php
session_start();
include 'db.php';

// Agar admin pehle se logged in hai toh direct dashboard par bhej do
if (isset($_SESSION['admin'])) {
    header('Location: admin.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Fetch user from database
        $sql = "SELECT * FROM admin_users WHERE username = '$username'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();

            // Dual Check: Plain text password OR Hashed Password both supported
            if ($password === $user['password'] || password_verify($password, $user['password'])) {
                $_SESSION['admin'] = $user['username'];
                header('Location: admin.php');
                exit();
            } else {
                $error = 'Incorrect Password!';
            }
        } else {
            $error = 'Username not found! Check your database table.';
        }
    } else {
        $error = 'Please fill all fields!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - TechStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #092e20; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; border-radius: 15px; border: none; }
        .btn-green { background-color: #118b50; color: white; font-weight: 600; }
        .btn-green:hover { background-color: #0e7041; color: white; }
    </style>
</head>
<body>

<div class="container">
    <div class="card login-card shadow-lg mx-auto p-4 bg-white">
        <div class="text-center mb-4">
            <h3 class="fw-bold text-dark"><i class="fa-solid fa-user-shield text-success me-2"></i>Admin Panel</h3>
            <p class="text-muted small">Enter credentials to manage TechStore</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 fs-6" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="mb-3">
                <label class="form-label font-monospace fw-bold small text-secondary">USERNAME</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-user text-muted"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="admin" required autocomplete="off">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label font-monospace fw-bold small text-secondary">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="fa-solid fa-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>

            <button type="submit" class="btn btn-green w-100 py-2 shadow-sm">
                Login to Dashboard <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="index.php" class="text-decoration-none text-muted small"><i class="fa-solid fa-house me-1"></i> Back to Main Store</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>