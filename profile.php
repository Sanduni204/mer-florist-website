<?php 
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . APPURL . 'auth/1login.php');
    exit;
}

require_once __DIR__ . '/Config/config.php';
require_once __DIR__ . '/includes/header.php';

// Ensure users table has profile_image column
try {
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_image VARCHAR(255) NULL");
} catch (PDOException $e) {
    // Ignore if column already exists or insufficient permissions
}
// Ensure additional profile fields exist
try { $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS gender ENUM('male','female','other') NULL"); } catch (PDOException $e) {}
try { $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS date_of_birth DATE NULL"); } catch (PDOException $e) {}
try { $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS age INT NULL"); } catch (PDOException $e) {}
try { $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS mobile_no VARCHAR(20) NULL"); } catch (PDOException $e) {}

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email, profile_image, gender, date_of_birth, age, mobile_no FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: ' . APPURL . 'auth/1login.php');
    exit;
}

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_avatar'])) {
    $uploadError = null;
    $uploadSuccess = null;
    if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] === UPLOAD_ERR_NO_FILE) {
        $uploadError = 'Please choose an image file.';
    } else {
        $file = $_FILES['avatar'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadError = 'Upload failed. Please try again.';
        } else {
            $maxSize = 2 * 1024 * 1024; // 2MB
            if ($file['size'] > $maxSize) {
                $uploadError = 'Image too large. Max size is 2MB.';
            } else {
                // Validate mime type
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($file['tmp_name']);
                $allowed = [
                    'image/jpeg' => 'jpg',
                    'image/png' => 'png',
                    'image/gif' => 'gif',
                    'image/webp' => 'webp',
                ];
                if (!isset($allowed[$mime])) {
                    $uploadError = 'Invalid image type. Use JPG, PNG, GIF, or WEBP.';

                } else {
                    $ext = $allowed[$mime];
                    $uploadDir = __DIR__ . '/Images/avatars';
                    if (!is_dir($uploadDir)) {
                        @mkdir($uploadDir, 0775, true);
                    }
                    $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
                    $destPath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
                    $relativePath = 'Images/avatars/' . $filename;

                    // Remove old file if exists and inside avatars dir
                    if (!empty($user['profile_image'])) {
                        $oldPath = __DIR__ . '/' . $user['profile_image'];
                        if (strpos($user['profile_image'], 'Images/avatars/') === 0 && file_exists($oldPath)) {
                            @unlink($oldPath);
                        }
                    }

                    if (move_uploaded_file($file['tmp_name'], $destPath)) {
                        $stmt = $conn->prepare('UPDATE users SET profile_image = :img WHERE id = :id');
                        $stmt->execute([':img' => $relativePath, ':id' => $user_id]);
                        $user['profile_image'] = $relativePath; // update in-memory
                        $uploadSuccess = 'Profile picture updated.';
                    } else {
                        $uploadError = 'Failed to save the uploaded file.';
                    }
                }
            }
        }
    }
}

// Handle profile info update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_profile'])) {
    $saveError = null;
    $saveSuccess = null;
    $newUsername = trim($_POST['username'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $newGender = $_POST['gender'] ?? '';
    $newDob = $_POST['date_of_birth'] ?? '';
    $newMobile = trim($_POST['mobile_no'] ?? '');

    if ($newUsername === '' || $newEmail === '') {
        $saveError = 'Username and Email are required.';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $saveError = 'Please enter a valid email address.';
    } else {
        $allowedGenders = ['male','female','other',''];
        if (!in_array($newGender, $allowedGenders, true)) {
            $saveError = 'Invalid gender selection.';
        }
        if ($newDob !== '') {
            $d = DateTime::createFromFormat('Y-m-d', $newDob);
            if (!($d && $d->format('Y-m-d') === $newDob)) {
                $saveError = 'Invalid date of birth.';
            }
        }
        // Validate mobile number (basic)
        if ($newMobile !== '') {
            $digits = preg_replace('/\D+/', '', $newMobile);
            if (strlen($digits) < 7 || strlen($digits) > 15) {
                $saveError = 'Please enter a valid mobile number.';
            }
        }
    }

    if ($saveError === null) {
        try {
            $stmt = $conn->prepare("UPDATE users SET username = :username, email = :email, gender = :gender, date_of_birth = :dob, mobile_no = :mobile WHERE id = :id");
            $stmt->execute([
                ':username' => $newUsername,
                ':email' => $newEmail,
                ':gender' => ($newGender === '' ? null : $newGender),
                ':dob' => ($newDob === '' ? null : $newDob),
                ':mobile' => ($newMobile === '' ? null : $newMobile),
                ':id' => $user_id
            ]);
            $user['username'] = $newUsername;
            $user['email'] = $newEmail;
            $user['gender'] = ($newGender === '' ? null : $newGender);
            $user['date_of_birth'] = ($newDob === '' ? null : $newDob);
            $user['mobile_no'] = ($newMobile === '' ? null : $newMobile);
            $saveSuccess = 'Profile updated successfully.';
        } catch (PDOException $e) {
            $saveError = 'Failed to update profile.';
        }
    }
}

?>

<style>
.profile-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
    background: #ed7787;
    border-radius: 15px;
    box-shadow: 0 8px 24px rgba(237, 119, 135, 0.3);
}

.profile-header {
    background: white;
    color: black;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    text-align: left;
    border: 3px solid #ed7787;
}

.avatar-wrap { display:flex; align-items:center; gap:20px; flex-wrap:wrap; }
.avatar-circle { width:96px; height:96px; border-radius:50%; overflow:hidden; border:3px solid #ed7787; background:#f5f5f5; display:flex; align-items:center; justify-content:center; }
.avatar-circle img { width:100%; height:100%; object-fit:cover; display:block; }
.avatar-form { display:flex; align-items:center; gap:10px; flex-wrap:wrap; }
.avatar-msg { margin-top:8px; font-size:0.9rem; }

.profile-nav {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
    flex-wrap: wrap;
    padding: 20px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    width: 250px;
    min-height: 300px;
}

.profile-content-wrapper {
    display: flex;
    gap: 30px;
    align-items: flex-start;
}

.profile-sidebar { flex-shrink: 0; width: 250px; }
.profile-main { flex: 1; min-width: 0; }

.nav-tab {
    padding: 12px 20px;
    background: white;
    border: 2px solid #ed7787;
    border-radius: 8px;
    color: black;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}
.nav-tab:hover, .nav-tab.active { background: #ed7787; color: white; }

.content-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 2px solid #ed7787;
    color: black;
}

@media (max-width: 768px) {
    .profile-container { padding: 15px; }
    .profile-content-wrapper { flex-direction: column; gap: 20px; }
    .profile-sidebar { width: 100%; }
    .profile-nav { width: 100%; flex-direction: row; min-height: auto; flex-wrap: wrap; justify-content: center; }
}
</style>

<div class="profile-container">
    <div class="profile-header">
        <div class="avatar-wrap">
            <div class="avatar-circle">
                <?php if (!empty($user['profile_image'])): ?>
                    <img src="<?php echo APPURL . $user['profile_image']; ?>" alt="Profile picture">
                <?php else: ?>
                    <span style="color:#bbb; font-weight:700;">No Image</span>
                <?php endif; ?>
            </div>
            <div>
                <div style="margin:0 0 8px 0;"><strong>Username:</strong> <?php echo htmlspecialchars($user['username']); ?></div>
                <div><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></div>
            </div>
        </div>
    </div>

    <div class="profile-content-wrapper">
        <div class="profile-sidebar">
            <div class="profile-nav">
                <a class="nav-tab active">Account Info</a>
                <a href="<?php echo APPURL; ?>1contact.php" class="nav-tab">Send New Message</a>
                <a href="<?php echo APPURL; ?>auth/logout.php" class="nav-tab" style="background: #dc3545; border-color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="profile-main">
            <div class="content-section">
                <h2>Account Information</h2>
                <?php if (isset($saveError) && $saveError): ?>
                    <div style="margin:10px 0; padding:10px; border:1px solid #dc3545; color:#dc3545; border-radius:6px; background:#fff5f5;">
                        <?php echo htmlspecialchars($saveError); ?>
                    </div>
                <?php elseif (isset($saveSuccess) && $saveSuccess): ?>
                    <div style="margin:10px 0; padding:10px; border:1px solid #28a745; color:#155724; border-radius:6px; background:#e8f5e8;">
                        <?php echo htmlspecialchars($saveSuccess); ?>
                    </div>
                <?php endif; ?>
                <form method="post" style="display:grid; gap:15px; max-width: 450px;">
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;" for="username">Username</label>
                        <input id="username" name="username" type="text" value="<?php echo htmlspecialchars($user['username']); ?>" required style="width:100%; padding:10px; background:#f8f9fa; border-radius:6px; border:1px solid #ddd;">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;" for="email">Email</label>
                        <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width:100%; padding:10px; background:#f8f9fa; border-radius:6px; border:1px solid #ddd;">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;">Profile Picture</label>
                        <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                            <div class="avatar-circle" style="width:64px; height:64px;">
                                <?php if (!empty($user['profile_image'])): ?>
                                    <img id="avatarPreview" src="<?php echo APPURL . $user['profile_image']; ?>" alt="Profile picture">
                                <?php else: ?>
                                    <img id="avatarPreview" src="" alt="Profile picture" style="display:none;">
                                    <span id="avatarPlaceholder" style="color:#bbb; font-weight:700;">No Image</span>
                                <?php endif; ?>
                            </div>
                            <form class="avatar-form" method="post" enctype="multipart/form-data" style="display:flex; align-items:center; gap:10px;">
                                <input type="file" name="avatar" id="avatarInput" accept="image/*">
                                <button type="submit" name="upload_avatar" style="background:#ed7787;color:#fff;border:2px solid #ed7787;padding:8px 14px;border-radius:6px;cursor:pointer;">Upload</button>
                            </form>
                        </div>
                        <?php if (isset($uploadError) && $uploadError): ?>
                            <div class="avatar-msg" style="color:#dc3545;">&bull; <?php echo htmlspecialchars($uploadError); ?></div>
                        <?php elseif (isset($uploadSuccess) && $uploadSuccess): ?>
                            <div class="avatar-msg" style="color:#28a745;">&bull; <?php echo htmlspecialchars($uploadSuccess); ?></div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;" for="gender">Gender</label>
                        <select id="gender" name="gender" style="width:100%; padding:10px; background:#f8f9fa; border-radius:6px; border:1px solid #ddd;">
                            <option value="" <?php echo empty($user['gender']) ? 'selected' : ''; ?>>Select</option>
                            <option value="male" <?php echo (isset($user['gender']) && $user['gender']==='male') ? 'selected' : ''; ?>>Male</option>
                            <option value="female" <?php echo (isset($user['gender']) && $user['gender']==='female') ? 'selected' : ''; ?>>Female</option>
                            <option value="other" <?php echo (isset($user['gender']) && $user['gender']==='other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;" for="date_of_birth">Date of Birth</label>
                        <input id="date_of_birth" name="date_of_birth" type="date" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>" style="width:100%; padding:10px; background:#f8f9fa; border-radius:6px; border:1px solid #ddd;">
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;" for="mobile_no">Mobile No</label>
                        <input id="mobile_no" name="mobile_no" type="tel" value="<?php echo htmlspecialchars($user['mobile_no'] ?? ''); ?>" placeholder="e.g. +94771234567" style="width:100%; padding:10px; background:#f8f9fa; border-radius:6px; border:1px solid #ddd;">
                    </div>
                    <div>
                        <button type="submit" name="save_profile" style="background:#ed7787;color:#fff;border:2px solid #ed7787;padding:10px 16px;border-radius:6px;cursor:pointer;font-weight:600;">Save Changes</button>
                    </div>
                </form>
                <script>
                (function(){
                    const input = document.getElementById('avatarInput');
                    if (input) {
                        input.addEventListener('change', function(e){
                            const file = e.target.files && e.target.files[0];
                            const preview = document.getElementById('avatarPreview');
                            const placeholder = document.getElementById('avatarPlaceholder');
                            if (!file || !preview) return;
                            const url = URL.createObjectURL(file);
                            preview.src = url;
                            preview.style.display = 'block';
                            if (placeholder) placeholder.style.display = 'none';
                        });
                    }
                })();
                </script>
                <script>
                // Preview selected avatar in the account section
                (function(){
                    const input = document.getElementById('avatarInput');
                    if (input) {
                        input.addEventListener('change', function(e){
                            const file = e.target.files && e.target.files[0];
                            const preview = document.getElementById('avatarPreview');
                            const placeholder = document.getElementById('avatarPlaceholder');
                            if (!file || !preview) return;
                            const url = URL.createObjectURL(file);
                            preview.src = url;
                            preview.style.display = 'block';
                            if (placeholder) placeholder.style.display = 'none';
                        });
                    }
                })();
                </script>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>