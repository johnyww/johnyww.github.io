<?php
/**
 * Forgot Password Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Forgot Password";

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is already logged in
if (isLoggedIn()) {
    // Redirect to appropriate dashboard
    if (isAdmin()) {
        header("Location: " . SITE_URL . "/admin/index.php");
    } else {
        header("Location: " . SITE_URL . "/customer/dashboard.php");
    }
    exit;
}

// Process form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get email from form
    $email = sanitize($_POST['email'] ?? '');
    
    // Validate email
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Check if email exists in database
        try {
            $stmt = $pdo->prepare("SELECT user_id, username, first_name FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                // Generate temporary password
                $tempPassword = generateRandomString(8);
                $hashedPassword = generatePasswordHash($tempPassword);
                
                // Update user password
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                
                // In a production environment, send an email with the temporary password
                // For demonstration purposes, we'll just show it on the page
                
                $success = true;
                $firstName = $user['first_name'];
                $username = $user['username'];
            } else {
                $error = "No account found with that email address";
            }
        } catch(PDOException $e) {
            $error = "An error occurred. Please try again.";
            error_log("Forgot password error: " . $e->getMessage());
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Forgot Password</h2>
                        <p class="text-muted">Enter your email address to reset your password</p>
                    </div>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <h5 class="alert-heading">Password Reset Successful!</h5>
                            <p>Hello <?php echo $firstName; ?>,</p>
                            <p>A temporary password has been generated for your account.</p>
                            <div class="bg-light p-3 rounded mb-3">
                                <div class="row">
                                    <div class="col-4 text-end fw-bold">Username:</div>
                                    <div class="col-8"><?php echo $username; ?></div>
                                </div>
                                <div class="row">
                                    <div class="col-4 text-end fw-bold">New Password:</div>
                                    <div class="col-8"><?php echo $tempPassword; ?></div>
                                </div>
                            </div>
                            <p class="mb-0">Please <a href="<?php echo SITE_URL; ?>/auth/login.php" class="alert-link">login</a> with this temporary password and change it in your profile settings.</p>
                            <hr>
                            <p class="small mb-0"><strong>Note:</strong> In a real-world scenario, this password would be sent to your email and not displayed on the website.</p>
                        </div>
                    <?php else: ?>
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-lg">Reset Password</button>
                            </div>
                            
                            <div class="text-center">
                                <p class="mb-0">Remember your password? <a href="<?php echo SITE_URL; ?>/auth/login.php" class="text-primary">Login</a></p>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>