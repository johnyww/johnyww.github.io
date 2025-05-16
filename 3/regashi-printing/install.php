<?php
/**
 * Installation Script
 * Regashi Printing Website
 * 
 * This script helps set up the Regashi Printing website by:
 * 1. Checking server requirements
 * 2. Verifying directory permissions
 * 3. Creating the database
 * 4. Initializing the database schema
 * 5. Setting up the config file
 */

// Define the base directory
define('BASE_DIR', dirname(__FILE__));

// Start installation
$step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$error = '';
$success = '';

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($step) {
        case 1: // Requirements check - no action needed
            $step = 2;
            break;
            
        case 2: // Database setup
            // Get database credentials
            $db_server = $_POST['db_server'] ?? 'localhost';
            $db_name = $_POST['db_name'] ?? 'regashi_printing';
            $db_username = $_POST['db_username'] ?? 'root';
            $db_password = $_POST['db_password'] ?? '';
            
            // Try to connect to the database
            try {
                $pdo = new PDO("mysql:host=$db_server", $db_username, $db_password);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                
                // Create the database if it doesn't exist
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Select the database
                $pdo->exec("USE `$db_name`");
                
                // Import the schema
                $sql = file_get_contents(BASE_DIR . '/database/schema.sql');
                $pdo->exec($sql);
                
                // Update config file
                $config_content = file_get_contents(BASE_DIR . '/includes/config.php');
                $config_content = preg_replace('/define\(\'DB_SERVER\',\s*\'.*?\'\);/', "define('DB_SERVER', '$db_server');", $config_content);
                $config_content = preg_replace('/define\(\'DB_USERNAME\',\s*\'.*?\'\);/', "define('DB_USERNAME', '$db_username');", $config_content);
                $config_content = preg_replace('/define\(\'DB_PASSWORD\',\s*\'.*?\'\);/', "define('DB_PASSWORD', '$db_password');", $config_content);
                $config_content = preg_replace('/define\(\'DB_NAME\',\s*\'.*?\'\);/', "define('DB_NAME', '$db_name');", $config_content);
                
                // Save the updated config file
                file_put_contents(BASE_DIR . '/includes/config.php', $config_content);
                
                $success = "Database created and initialized successfully.";
                $step = 3;
            } catch (PDOException $e) {
                $error = "Database connection failed: " . $e->getMessage();
            }
            break;
            
        case 3: // Site settings
            // Get site settings
            $site_url = $_POST['site_url'] ?? 'http://localhost/regashi_printing';
            $admin_email = $_POST['admin_email'] ?? 'admin@regashi.com';
            
            // Update config file
            $config_content = file_get_contents(BASE_DIR . '/includes/config.php');
            $config_content = preg_replace('/define\(\'SITE_URL\',\s*\'.*?\'\);/', "define('SITE_URL', '$site_url');", $config_content);
            $config_content = preg_replace('/define\(\'ADMIN_EMAIL\',\s*\'.*?\'\);/', "define('ADMIN_EMAIL', '$admin_email');", $config_content);
            
            // Save the updated config file
            file_put_contents(BASE_DIR . '/includes/config.php', $config_content);
            
            $success = "Website configured successfully.";
            $step = 4;
            break;
    }
}

// Check requirements
$requirements = [
    'PHP Version' => [
        'required' => '7.4.0',
        'current' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, '7.4.0', '>=')
    ],
    'PDO Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('pdo')
    ],
    'PDO MySQL Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('pdo_mysql')
    ],
    'GD Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('gd')
    ],
    'FileInfo Extension' => [
        'required' => 'Enabled',
        'current' => extension_loaded('fileinfo') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('fileinfo')
    ]
];

// Check directory permissions
$directories = [
    '/assets/uploads/designs' => is_writable(BASE_DIR . '/assets/images/uploads/designs'),
    '/assets/uploads/receipts' => is_writable(BASE_DIR . '/assets/images/uploads/receipts'),
    '/logs' => is_writable(BASE_DIR . '/logs')
];

// Check if all requirements are met
$requirements_met = true;
foreach ($requirements as $requirement) {
    if (!$requirement['status']) {
        $requirements_met = false;
        break;
    }
}

// Check if all directories are writable
$directories_writable = true;
foreach ($directories as $writable) {
    if (!$writable) {
        $directories_writable = false;
        break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Regashi Printing - Installation</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', sans-serif;
        }
        
        .installation-wrapper {
            max-width: 800px;
            margin: 50px auto;
        }
        
        .steps {
            display: flex;
            margin-bottom: 30px;
        }
        
        .step {
            flex: 1;
            text-align: center;
            padding: 15px;
            background-color: #e9ecef;
            border-right: 1px solid #dee2e6;
            position: relative;
        }
        
        .step:last-child {
            border-right: none;
        }
        
        .step.active {
            background-color: #4263eb;
            color: white;
        }
        
        .step.completed {
            background-color: #1cc88a;
            color: white;
        }
        
        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            right: -10px;
            transform: translateY(-50%);
            width: 20px;
            height: 20px;
            background-color: inherit;
            border-right: 1px solid #dee2e6;
            border-top: 1px solid #dee2e6;
            transform: translateY(-50%) rotate(45deg);
            z-index: 1;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-weight: bold;
            color: #4263eb;
        }
    </style>
</head>
<body>
    <div class="installation-wrapper">
        <div class="logo">
            <h1>Regashi Printing</h1>
            <p class="text-muted">Installation Wizard</p>
        </div>
        
        <div class="steps">
            <div class="step <?php echo ($step == 1) ? 'active' : (($step > 1) ? 'completed' : ''); ?>">
                <div class="step-number">1</div>
                <div class="step-title">Requirements</div>
            </div>
            <div class="step <?php echo ($step == 2) ? 'active' : (($step > 2) ? 'completed' : ''); ?>">
                <div class="step-number">2</div>
                <div class="step-title">Database</div>
            </div>
            <div class="step <?php echo ($step == 3) ? 'active' : (($step > 3) ? 'completed' : ''); ?>">
                <div class="step-number">3</div>
                <div class="step-title">Configuration</div>
            </div>
            <div class="step <?php echo ($step == 4) ? 'active' : (($step > 4) ? 'completed' : ''); ?>">
                <div class="step-number">4</div>
                <div class="step-title">Finish</div>
            </div>
        </div>
        
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($step == 1): // Requirements check ?>
                    <h3 class="mb-4">System Requirements</h3>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Requirement</th>
                                <th>Required</th>
                                <th>Current</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requirements as $name => $requirement): ?>
                                <tr>
                                    <td><?php echo $name; ?></td>
                                    <td><?php echo $requirement['required']; ?></td>
                                    <td><?php echo $requirement['current']; ?></td>
                                    <td>
                                        <?php if ($requirement['status']): ?>
                                            <span class="badge bg-success">Pass</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Fail</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <h4 class="mt-4 mb-3">Directory Permissions</h4>
                    <p class="text-muted mb-3">The following directories need to be writable:</p>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Directory</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($directories as $dir => $writable): ?>
                                <tr>
                                    <td><?php echo $dir; ?></td>
                                    <td>
                                        <?php if ($writable): ?>
                                            <span class="badge bg-success">Writable</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Not Writable</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <form method="post" action="install.php?step=2" class="mt-4">
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" <?php echo (!$requirements_met || !$directories_writable) ? 'disabled' : ''; ?>>
                                <?php echo ($requirements_met && $directories_writable) ? 'Continue' : 'Please Fix Issues Before Continuing'; ?>
                            </button>
                        </div>
                    </form>
                
                <?php elseif ($step == 2): // Database setup ?>
                    <h3 class="mb-4">Database Configuration</h3>
                    <p class="text-muted mb-4">Please enter your database connection details:</p>
                    
                    <form method="post" action="install.php?step=2">
                        <div class="mb-3">
                            <label for="db_server" class="form-label">Database Server</label>
                            <input type="text" class="form-control" id="db_server" name="db_server" value="localhost" required>
                            <div class="form-text">Usually this is "localhost"</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_name" class="form-label">Database Name</label>
                            <input type="text" class="form-control" id="db_name" name="db_name" value="regashi_printing" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_username" class="form-label">Database Username</label>
                            <input type="text" class="form-control" id="db_username" name="db_username" value="root" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="db_password" class="form-label">Database Password</label>
                            <input type="password" class="form-control" id="db_password" name="db_password">
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Continue</button>
                        </div>
                    </form>
                
                <?php elseif ($step == 3): // Site configuration ?>
                    <h3 class="mb-4">Website Configuration</h3>
                    <p class="text-muted mb-4">Configure your website settings:</p>
                    
                    <form method="post" action="install.php?step=3">
                        <div class="mb-3">
                            <label for="site_url" class="form-label">Site URL</label>
                            <?php
                            // Try to determine the site URL automatically
                            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
                            $host = $_SERVER['HTTP_HOST'];
                            $path = dirname($_SERVER['REQUEST_URI']);
                            $suggested_url = $protocol . $host . $path;
                            ?>
                            <input type="text" class="form-control" id="site_url" name="site_url" value="<?php echo $suggested_url; ?>" required>
                            <div class="form-text">The full URL of your website (e.g., http://localhost/regashi_printing)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="admin_email" class="form-label">Admin Email</label>
                            <input type="email" class="form-control" id="admin_email" name="admin_email" value="admin@regashi.com" required>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Continue</button>
                        </div>
                    </form>
                
                <?php elseif ($step == 4): // Finish ?>
                    <div class="text-center mb-4">
                        <div class="mb-4">
                            <i class="fas fa-check-circle text-success fa-5x"></i>
                        </div>
                        <h3 class="mb-3">Installation Complete!</h3>
                        <p class="mb-4">Regashi Printing has been successfully installed on your server.</p>
                        
                        <div class="alert alert-info mb-4">
                            <strong>Admin Login Details:</strong><br>
                            Username: admin<br>
                            Password: admin123
                        </div>
                        
                        <div class="alert alert-warning mb-4">
                            <strong>Important:</strong> For security reasons, please change the admin password after your first login.
                        </div>
                        
                        <div class="d-grid">
                            <a href="index.php" class="btn btn-primary btn-lg">Go to Website</a>
                        </div>
                        <div class="mt-3">
                            <a href="admin/index.php" class="btn btn-outline-primary">Go to Admin Panel</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>