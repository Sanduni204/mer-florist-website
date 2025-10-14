<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>
<?php
// Create messages table if it doesn't exist
try {
    $createTable = "CREATE TABLE IF NOT EXISTS messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        user_id INT NULL,
        username VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conn->exec($createTable);

    // Add user_id and username columns if they don't exist (for existing installations)
    try {
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS user_id INT NULL");
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS username VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // Columns might already exist
    }
} catch (PDOException $e) {
    // Table creation failed, but continue
}

// Handle form submission
if (isset($_POST['send'])) {
    if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['message'])) {
        try {
            // Get user info if user is logged in
            $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $username = isset($_SESSION['username']) ? $_SESSION['username'] : null;

            $stmt = $conn->prepare("INSERT INTO messages (name, email, message, user_id, username) VALUES (:name, :email, :message, :user_id, :username)");
            $stmt->execute([
                ':name' => $_POST['name'],
                ':email' => $_POST['email'],
                ':message' => $_POST['message'],
                ':user_id' => $user_id,
                ':username' => $username
            ]);
            echo "<script>
                // Create custom notification
                setTimeout(function() {
                    // Remove existing notifications
                    const existingNotification = document.querySelector('.custom-notification');
                    if (existingNotification) {
                        existingNotification.remove();
                    }

                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = 'custom-notification';
                    notification.innerHTML = 'Your message has been sent successfully!';

                    // Style the notification
                    notification.style.cssText = `
                        position: fixed;
                        top: 20px;
                        right: 20px;
                        background: rgba(255, 255, 255, 0.5);
                        color: #2c3e50;
                        padding: 15px 25px;
                        border-radius: 25px;
                        font-weight: 600;
                        z-index: 9999;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                        border: 2px solid #ed7787;
                        animation: slideIn 0.3s ease;
                        max-width: 300px;
                        text-align: center;
                        font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
                        backdrop-filter: blur(10px);
                    `;

                    // Add CSS for animation if not already present
                    if (!document.querySelector('#notification-styles')) {
                        const style = document.createElement('style');
                        style.id = 'notification-styles';
                        style.textContent = `
                            @keyframes slideIn {
                                from { transform: translateX(100%); opacity: 0; }
                                to { transform: translateX(0); opacity: 1; }
                            }
                            @keyframes slideOut {
                                from { transform: translateX(0); opacity: 1; }
                                to { transform: translateX(100%); opacity: 0; }
                            }
                        `;
                        document.head.appendChild(style);
                    }

                    // Add to body
                    document.body.appendChild(notification);

                    // Auto remove after 4 seconds
                    setTimeout(() => {
                        notification.style.animation = 'slideOut 0.3s ease';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.remove();
                            }
                        }, 300);
                    }, 4000);
                }, 100);
            </script>";
        } catch (PDOException $e) {
            $error_message = "Failed to send message. Please try again.";
        }
    } else {
        $error_message = "Please fill in all fields.";
    }
}

// Load contact info (single row id=1) with fallbacks
$c = [
    'address' => 'Barnes Pl, Colombo 07',
    'email' => 'mer_shopping@gmail.com',
    'phone' => '0112345678',
    'instagram' => '#',
    'facebook' => '#',
    'twitter' => '#',
    'youtube' => '#',
    'whatsapp' => '#',
    'map_embed_url' => 'https://maps.google.com/maps?q=barns%20place%20sri%20lanka&t=&z=13&ie=UTF8&iwloc=&output=embed',
];
try {
    $row = $conn->query('SELECT * FROM contact_info WHERE id = 1')->fetch(PDO::FETCH_ASSOC);
    if ($row) { $c = array_merge($c, $row); }
} catch (Throwable $e) { /* ignore if table not present */ }
?>


<h3 class="meet_us">Let's Talk</h3>
        <div id="info">
            <div class="col1">
                <img src="./Images/shop.png">
            </div>
            <div class="col2">
               
                <p><i class="fa-solid fa-location-dot"></i>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($c['address'] ?? ''); ?></p>
                <p><i class="fa-solid fa-envelope"></i>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($c['email'] ?? ''); ?></p>
                <p><i class="fa-solid fa-phone"></i>&nbsp;&nbsp;&nbsp;<?php echo htmlspecialchars($c['phone'] ?? ''); ?></p><br>
                <p><a href="<?php echo htmlspecialchars($c['instagram'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a>&nbsp;&nbsp;
                    <a href="<?php echo htmlspecialchars($c['facebook'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-facebook"></i></a>&nbsp;
                    <a href="<?php echo htmlspecialchars($c['twitter'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>&nbsp;
                    <a href="<?php echo htmlspecialchars($c['youtube'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-youtube"></i></a>&nbsp;
                    <a href="<?php echo htmlspecialchars($c['whatsapp'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                </p><br>
                <p><i class="fa-solid fa-message"></i>
                    <?php if (isset($error_message)): ?>
                        <script>
                            // Show error as the same styled notification used for success
                            setTimeout(function() {
                                // Remove existing notifications
                                const existingNotification = document.querySelector('.custom-notification');
                                if (existingNotification) {
                                    existingNotification.remove();
                                }

                                // Create notification element
                                const notification = document.createElement('div');
                                notification.className = 'custom-notification';
                                notification.innerHTML = <?php echo json_encode($error_message); ?>;

                                // Style the notification (same as success)
                                notification.style.cssText = `
                                    position: fixed;
                                    top: 20px;
                                    right: 20px;
                                    background: rgba(255, 255, 255, 0.5);
                                    color: #2c3e50;
                                    padding: 15px 25px;
                                    border-radius: 25px;
                                    font-weight: 600;
                                    z-index: 9999;
                                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                                    border: 2px solid #ed7787;
                                    animation: slideIn 0.3s ease;
                                    max-width: 300px;
                                    text-align: center;
                                    font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
                                    backdrop-filter: blur(10px);
                                `;

                                // Add CSS for animation if not already present
                                if (!document.querySelector('#notification-styles')) {
                                    const style = document.createElement('style');
                                    style.id = 'notification-styles';
                                    style.textContent = `
                                        @keyframes slideIn {
                                            from { transform: translateX(100%); opacity: 0; }
                                            to { transform: translateX(0); opacity: 1; }
                                        }
                                        @keyframes slideOut {
                                            from { transform: translateX(0); opacity: 1; }
                                            to { transform: translateX(100%); opacity: 0; }
                                        }
                                    `;
                                    document.head.appendChild(style);
                                }

                                // Add to body
                                document.body.appendChild(notification);

                                // Auto remove after 4 seconds
                                setTimeout(() => {
                                    notification.style.animation = 'slideOut 0.3s ease';
                                    setTimeout(() => {
                                        if (notification.parentNode) {
                                            notification.remove();
                                        }
                                    }, 300);
                                }, 4000);
                            }, 100);
                        </script>
                    <?php endif; ?>
                    <form action="" method="Post">
                   <input type="text" name="name" size="30" placeholder="Name" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required><br>
                   <input type="email" name="email" size="30" placeholder="Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required><br>
                   <textarea name="message" cols="30" rows="5" placeholder="Message.."></textarea><br>
                   <input type="submit" name="send" value="send">
                    </form>
                </p>
            </div>
            <div class="col3">
                <iframe  src="<?php echo htmlspecialchars($c['map_embed_url'] ?? ''); ?>" >
                </iframe><a href="https://embedgooglemap.net/124/"></a>
            </div>
            </div>


<?php require "includes/footer.php"; ?>
