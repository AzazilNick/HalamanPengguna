<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$conn = new mysqli('localhost', 'root', '', 'niflix');

// Get current user data
$user_id = $_SESSION['user']['id'];
$user_query = $conn->prepare("SELECT * FROM user WHERE id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();
$current_user = $user_result->fetch_assoc();

// Initialize variables
$message = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate username
    if (empty($username)) {
        $error = "Username cannot be empty";
    }
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    }
    
    // Check if password is being changed
    $password_changed = false;
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = "Please enter your current password to change it";
        } elseif (!password_verify($current_password, $current_user['password'])) {
            $error = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match";
        } else {
            $password_changed = true;
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }
    
    // Handle profile photo upload
    $photo_path = $current_user['foto_pengguna'];
    if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/profile_photos/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = pathinfo($_FILES['profile_photo']['name'], PATHINFO_EXTENSION);
        $file_name = 'user_' . $user_id . '_' . time() . '.' . $file_ext;
        $target_file = $upload_dir . $file_name;
        
        // Check if image file is a actual image
        $check = getimagesize($_FILES['profile_photo']['tmp_name']);
        if ($check !== false) {
            // Move uploaded file
            if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $target_file)) {
                // Delete old photo if it's not the default
                if ($photo_path !== 'default.png' && file_exists($upload_dir . $photo_path)) {
                    unlink($upload_dir . $photo_path);
                }
                $photo_path = $file_name;
            } else {
                $error = "Sorry, there was an error uploading your file.";
            }
        } else {
            $error = "File is not an image.";
        }
    }
    
    // If no errors, update the database
    if (empty($error)) {
        if ($password_changed) {
            $update_query = $conn->prepare("UPDATE user SET username = ?, email = ?, nama_lengkap = ?, password = ?, foto_pengguna = ? WHERE id = ?");
            $update_query->bind_param("sssssi", $username, $email, $fullname, $hashed_password, $photo_path, $user_id);
        } else {
            $update_query = $conn->prepare("UPDATE user SET username = ?, email = ?, nama_lengkap = ?, foto_pengguna = ? WHERE id = ?");
            $update_query->bind_param("ssssi", $username, $email, $fullname, $photo_path, $user_id);
        }
        
        if ($update_query->execute()) {
            $message = "Profile updated successfully!";
            
            // Update session data
            $_SESSION['user']['username'] = $username;
            
            // Refresh user data
            $user_query->execute();
            $user_result = $user_query->get_result();
            $current_user = $user_result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    }
}

include_once('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Niflix</title>
    <link rel="stylesheet" href="Style.css">
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <h1>My Profile</h1>
        </div>
        
        <?php if ($message): ?>
            <div class="notification success"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="notification error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form class="profile-content" method="POST" enctype="multipart/form-data">
            <div class="profile-photo-section">
                <img src="uploads/profile_photos/<?= htmlspecialchars($current_user['foto_pengguna'] ?? 'default.png') ?>" 
                     alt="Profile Photo" class="profile-photo" 
                     onerror="this.src='uploads/profile_photos/default.png'">
                
                <div class="photo-upload">
                    <label for="profile_photo" style="color: #ffcc00; display: block; margin-bottom: 10px;">
                        Change Profile Photo
                    </label>
                    <input type="file" id="profile_photo" name="profile_photo" accept="image/*">
                </div>
            </div>
            
            <div class="profile-info-section">
                <div class="profile-info">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" 
                           value="<?= htmlspecialchars($current_user['username']) ?>" required>
                    
                    <label for="fullname">Full Name</label>
                    <input type="text" id="fullname" name="fullname" 
                           value="<?= htmlspecialchars($current_user['nama_lengkap']) ?>">
                    
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($current_user['email']) ?>" required>
                </div>
                
                <div class="password-section">
                    <h3 style="color: #ffcc00; margin-bottom: 15px;">Change Password</h3>
                    
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password">
                    
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password">
                    
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password">
                </div>
                
                <button type="submit" class="btn-update">Update Profile</button>
            </div>
        </form>
    </div>
</body>
</html>

<?php
include_once('footer.php');
?>
