<?php
/**
 * Customer Profile Page
 * Regashi Printing Website
 */

// Set page title
$page_title = "My Profile";

// Include config and functions
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isLoggedIn()) {
    // Redirect to login page
    header("Location: " . SITE_URL . "/auth/login.php");
    exit;
}

// Check if user is admin
if (isAdmin()) {
    // Redirect to admin dashboard
    header("Location: " . SITE_URL . "/admin/index.php");
    exit;
}

// Get user information
$user = getUserById($pdo, $_SESSION['user_id']);

if (!$user) {
    // If user not found, log out and redirect to login page
    session_destroy();
    header("Location: " . SITE_URL . "/auth/login.php");
    exit;
}

// Process form submission
$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update profile
    if (isset($_POST['update_profile'])) {
        $firstName = sanitize($_POST['first_name'] ?? '');
        $lastName = sanitize($_POST['last_name'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $address = sanitize($_POST['address'] ?? '');
        $city = sanitize($_POST['city'] ?? '');
        $postalCode = sanitize($_POST['postal_code'] ?? '');
        $country = sanitize($_POST['country'] ?? '');
        
        // Validate inputs
        if (empty($firstName)) {
            $error = "First name is required";
        } elseif (empty($lastName)) {
            $error = "Last name is required";
        } elseif (empty($email)) {
            $error = "Email is required";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Invalid email format";
        } else {
            // Check if email is already in use by another user
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = :email AND user_id != :user_id");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error = "Email is already in use by another account";
            } else {
                // Update user information
                try {
                    $stmt = $pdo->prepare("
                        UPDATE users 
                        SET first_name = :first_name, 
                            last_name = :last_name, 
                            email = :email, 
                            phone = :phone, 
                            address = :address, 
                            city = :city, 
                            postal_code = :postal_code, 
                            country = :country 
                        WHERE user_id = :user_id
                    ");
                    
                    $stmt->bindParam(':first_name', $firstName, PDO::PARAM_STR);
                    $stmt->bindParam(':last_name', $lastName, PDO::PARAM_STR);
                    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
                    $stmt->bindParam(':address', $address, PDO::PARAM_STR);
                    $stmt->bindParam(':city', $city, PDO::PARAM_STR);
                    $stmt->bindParam(':postal_code', $postalCode, PDO::PARAM_STR);
                    $stmt->bindParam(':country', $country, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    
                    $stmt->execute();
                    
                    // Update user variable with new data
                    $user = getUserById($pdo, $_SESSION['user_id']);
                    
                    $success = "Your profile has been updated successfully.";
                } catch(PDOException $e) {
                    $error = "An error occurred while updating your profile. Please try again.";
                    error_log("Profile update error: " . $e->getMessage());
                }
            }
        }
    }
    
    // Change password
    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Validate inputs
        if (empty($currentPassword)) {
            $error = "Please enter your current password";
        } elseif (empty($newPassword)) {
            $error = "Please enter a new password";
        } elseif (strlen($newPassword) < 6) {
            $error = "New password must be at least 6 characters long";
        } elseif ($newPassword !== $confirmPassword) {
            $error = "New password and confirm password do not match";
        } else {
            // Verify current password
            if (verifyPassword($currentPassword, $user['password'])) {
                // Update password
                try {
                    $hashedPassword = generatePasswordHash($newPassword);
                    
                    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
                    $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                    $stmt->execute();
                    
                    $success = "Your password has been changed successfully.";
                } catch(PDOException $e) {
                    $error = "An error occurred while changing your password. Please try again.";
                    error_log("Password change error: " . $e->getMessage());
                }
            } else {
                $error = "Current password is incorrect";
            }
        }
    }
}

// Include header
include_once '../includes/header.php';
?>

<!-- Profile Page Header -->
<section class="page-header bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-0">My Profile</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo SITE_URL; ?>/customer/dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Profile Content -->
<section class="profile-section py-5">
    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <!-- Profile Sidebar -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body text-center p-4">
                        <div class="avatar-circle mx-auto mb-3">
                            <span class="avatar-initials"><?php echo substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1); ?></span>
                        </div>
                        <h5 class="fw-bold mb-1"><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></h5>
                        <p class="text-muted mb-3"><?php echo $user['email']; ?></p>
                        <div class="d-grid gap-2">
                            <a href="<?php echo SITE_URL; ?>/customer/dashboard.php" class="btn btn-outline-primary">
                                <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                            </a>
                            <a href="<?php echo SITE_URL; ?>/customer/orders.php" class="btn btn-outline-primary">
                                <i class="fas fa-shopping-bag me-2"></i> My Orders
                            </a>
                            <a href="<?php echo SITE_URL; ?>/customer/saved-designs.php" class="btn btn-outline-primary">
                                <i class="fas fa-palette me-2"></i> Saved Designs
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Account Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 text-muted">Username:</div>
                            <div class="col-7 fw-medium"><?php echo $user['username']; ?></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 text-muted">Member Since:</div>
                            <div class="col-7 fw-medium"><?php echo formatDate($user['created_at'], 'd M Y'); ?></div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-5 text-muted">Account Type:</div>
                            <div class="col-7 fw-medium"><?php echo ucfirst($user['role']); ?></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-8">
                <!-- Personal Information Form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $user['first_name']; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $user['last_name']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $user['phone']; ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo $user['address']; ?>">
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="city" class="form-label">City</label>
                                    <input type="text" class="form-control" id="city" name="city" value="<?php echo $user['city']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code" value="<?php echo $user['postal_code']; ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="country" class="form-label">Country</label>
                                    <input type="text" class="form-control" id="country" name="country" value="<?php echo $user['country']; ?>">
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Change Password Form -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 fw-bold">Change Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="confirm_password" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #4263eb;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 32px;
        font-weight: 600;
    }
    
    .avatar-initials {
        text-transform: uppercase;
    }
</style>

<?php
// Include footer
include_once '../includes/footer.php';
?>