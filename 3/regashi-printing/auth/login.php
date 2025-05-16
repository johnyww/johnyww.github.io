<?php
/**
 * Login Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "Login";

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

// Get redirect URL if provided
$redirect = isset($_GET['redirect']) ? $_GET['redirect'] : '';

// Process login form
$errors = [];
$login_failed = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    // Validate form data
    if (empty($username)) {
        $errors['username'] = "Username is required";
    }
    
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }
    
    // If no errors, attempt login
    if (empty($errors)) {
        try {
            // Check if username exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $username, PDO::PARAM_STR); // Allow login with email as well
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $user = $stmt->fetch();
                
                // Verify password
                if (verifyPassword($password, $user['password'])) {
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    // If remember me is checked, set a cookie
                    if ($remember) {
                        $token = generateRandomString(32);
                        
                        // Store token in database
                        $stmt = $pdo->prepare("
                            UPDATE users 
                            SET remember_token = :token 
                            WHERE user_id = :user_id
                        ");
                        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
                        $stmt->bindParam(':user_id', $user['user_id'], PDO::PARAM_INT);
                        $stmt->execute();
                        
                        // Set cookie for 30 days
                        setcookie('remember_token', $token, time() + (86400 * 30), '/');
                    }
                    
                    // Redirect based on role or redirect parameter
                    if ($redirect == 'admin' && $user['role'] == 'admin') {
                        header("Location: " . SITE_URL . "/admin/index.php");
                    } elseif ($user['role'] == 'admin') {
                        header("Location: " . SITE_URL . "/admin/index.php");
                    } else {
                        header("Location: " . SITE_URL . "/customer/dashboard.php");
                    }
                    exit;
                } else {
                    $login_failed = true;
                }
            } else {
                $login_failed = true;
            }
        } catch(PDOException $e) {
            $errors['general'] = "Login failed: " . $e->getMessage();
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
                        <h2 class="fw-bold">Login to Your Account</h2>
                        <p class="text-muted">Welcome back to Regashi Printing</p>
                    </div>
                    
                    <?php if ($login_failed): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle me-2"></i> Invalid username or password
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($errors['general'])): ?>
                        <div class="alert alert-danger">
                            <?php echo $errors['general']; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . ($redirect ? "?redirect=$redirect" : "")); ?>" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username or Email</label>
                            <input type="text" class="form-control <?php echo isset($errors['username']) ? 'is-invalid' : ''; ?>" id="username" name="username" value="<?php echo $username ?? ''; ?>" required>
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['username']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label">Password</label>
                                <a href="forgot-password.php" class="small text-primary">Forgot Password?</a>
                            </div>
                            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
                            <?php if (isset($errors['password'])): ?>
                                <div class="invalid-feedback">
                                    <?php echo $errors['password']; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-4 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        
                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">Login</button>
                        </div>
                        
                        <div class="text-center">
                            <p class="mb-0">Don't have an account? <a href="register.php" class="text-primary">Register</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Include footer
include_once '../includes/footer.php';
?>
