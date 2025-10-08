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
                <img src=".\Images\shop.png">
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
                        <div style="color: red; margin: 10px 0; padding: 10px; background: #fdecea; border-radius: 5px;">
                            <?php echo htmlspecialchars($error_message); ?>
                        </div>
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

<!-- Messages and Replies Section for Logged-in Users -->
<?php if (isset($_SESSION['user_id']) && isset($_SESSION['username'])): ?>
    <?php
    // Get user's messages and replies
    try {
        $user_id = $_SESSION['user_id'];
        $username = $_SESSION['username'];
        
        // Get messages for this user
        $stmt = $conn->prepare("
            SELECT m.id, m.message, m.created_at, m.is_read,
                   COUNT(r.id) as reply_count
            FROM messages m 
            LEFT JOIN message_replies r ON m.id = r.message_id 
            WHERE (m.user_id = :user_id OR m.username = :username)
            GROUP BY m.id 
            ORDER BY m.created_at DESC
            LIMIT 5
        ");
        $stmt->execute([':user_id' => $user_id, ':username' => $username]);
        $user_messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $user_messages = [];
    }
    ?>
    
    <?php if (!empty($user_messages)): ?>
        <div id="user-messages-section" style="margin-top: 40px; padding: 30px; background: #f8f9fa; border-radius: 12px;">
            <style>
                .messages-header {
                    text-align: center;
                    margin-bottom: 30px;
                }
                
                .messages-header h3 {
                    color: #ed7787;
                    font-size: 1.8rem;
                    margin-bottom: 10px;
                }
                
                .message-card {
                    background: white;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    transition: all 0.3s ease;
                }
                
                .message-card:hover {
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                }
                
                .message-card.has-reply {
                    border-left: 4px solid #28a745;
                }
                
                .message-card.no-reply {
                    border-left: 4px solid #ffc107;
                }
                
                .message-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 15px;
                    flex-wrap: wrap;
                    gap: 10px;
                }
                
                .message-date {
                    color: #666;
                    font-size: 0.9rem;
                }
                
                .message-status {
                    padding: 4px 10px;
                    border-radius: 15px;
                    font-size: 0.8rem;
                    font-weight: 600;
                }
                
                .status-replied {
                    background: #d4edda;
                    color: #155724;
                }
                
                .status-pending {
                    background: #fff3cd;
                    color: #856404;
                }
                
                .message-content {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                    margin-bottom: 15px;
                    line-height: 1.5;
                    border-left: 3px solid #ed7787;
                }
                
                .replies-section {
                    border-top: 1px solid #eee;
                    padding-top: 15px;
                }
                
                .reply-item {
                    background: #e8f5e8;
                    padding: 15px;
                    border-radius: 6px;
                    border-left: 3px solid #28a745;
                    margin-bottom: 10px;
                }
                
                .reply-header {
                    font-size: 0.85rem;
                    color: #666;
                    margin-bottom: 8px;
                    font-weight: 600;
                }
                
                .reply-content {
                    line-height: 1.4;
                    color: #333;
                }
                
                .view-all-link {
                    text-align: center;
                    margin-top: 20px;
                }
                
                .view-all-link a {
                    background: #ed7787;
                    color: white;
                    padding: 10px 20px;
                    border-radius: 6px;
                    text-decoration: none;
                    font-weight: 600;
                    transition: all 0.3s ease;
                }
                
                .view-all-link a:hover {
                    background: #e05a6b;
                    transform: translateY(-1px);
                }
                
                @media (max-width: 768px) {
                    .message-header {
                        flex-direction: column;
                        align-items: flex-start;
                    }
                    
                    #user-messages-section {
                        padding: 20px 15px;
                    }
                }
            </style>
            
            <div class="messages-header">
                <h3><i class="fas fa-comments"></i> Your Messages & Replies</h3>
                <p style="color: #666; margin: 0;">Recent conversations with our team</p>
            </div>
            
            <?php foreach ($user_messages as $message): ?>
                <div class="message-card <?php echo $message['reply_count'] > 0 ? 'has-reply' : 'no-reply'; ?>">
                    <div class="message-header">
                        <div class="message-date">
                            <i class="fas fa-calendar"></i> Sent on <?php echo date('M j, Y \a\t g:i A', strtotime($message['created_at'])); ?>
                        </div>
                        <div class="message-status <?php echo $message['reply_count'] > 0 ? 'status-replied' : 'status-pending'; ?>">
                            <?php if ($message['reply_count'] > 0): ?>
                                <i class="fas fa-check-circle"></i> Replied (<?php echo $message['reply_count']; ?> response<?php echo $message['reply_count'] == 1 ? '' : 's'; ?>)
                            <?php else: ?>
                                <i class="fas fa-clock"></i> Pending Reply
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="message-content">
                        <strong><i class="fas fa-user"></i> Your Message:</strong><br>
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                    
                    <?php if ($message['reply_count'] > 0): ?>
                        <div class="replies-section">
                            <h4 style="margin: 0 0 15px 0; color: #28a745;">
                                <i class="fas fa-reply"></i> Our Response:
                            </h4>
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT reply_text, admin_name, created_at FROM message_replies WHERE message_id = :message_id ORDER BY created_at ASC");
                                $stmt->execute([':message_id' => $message['id']]);
                                $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                
                                foreach ($replies as $reply): ?>
                                    <div class="reply-item">
                                        <div class="reply-header">
                                            <i class="fas fa-user-tie"></i> 
                                            <?php echo htmlspecialchars($reply['admin_name']); ?> replied on 
                                            <?php echo date('M j, Y \a\t g:i A', strtotime($reply['created_at'])); ?>
                                        </div>
                                        <div class="reply-content">
                                            <?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?>
                                        </div>
                                    </div>
                                <?php endforeach;
                            } catch (PDOException $e) {
                                echo '<p style="color: #dc3545;">Error loading replies.</p>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <div class="view-all-link">
                <a href="<?php echo APPURL; ?>profile.php">
                    <i class="fas fa-eye"></i> View All Messages
                </a>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>

            <?php require "includes/footer.php"; ?>
            
        