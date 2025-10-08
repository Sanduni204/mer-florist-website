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

// Get user info
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = :id");
$stmt->execute([':id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header('Location: ' . APPURL . 'auth/1login.php');
    exit;
}

// Get user's messages and replies
try {
    // Ensure user_id column exists in messages table
    try {
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS user_id INT NULL");
    } catch (PDOException $e) {
        // Column might already exist
    }
    
    // Update existing messages to link them to this user if email matches
    $stmt = $conn->prepare("UPDATE messages SET user_id = :user_id WHERE email = :email AND user_id IS NULL");
    $stmt->execute([':user_id' => $user_id, ':email' => $user['email']]);
    
    // Get messages for this user (both by user_id and email for backward compatibility)
    $stmt = $conn->prepare("
        SELECT m.id, m.message, m.created_at, m.is_read,
               COUNT(r.id) as reply_count
        FROM messages m 
        LEFT JOIN message_replies r ON m.id = r.message_id 
        WHERE (m.user_id = :user_id OR m.email = :email)
        GROUP BY m.id 
        ORDER BY m.created_at DESC
    ");
    $stmt->execute([':user_id' => $user_id, ':email' => $user['email']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
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
    text-align: center;
    border: 3px solid #ed7787;
}

.profile-header h1 {
    margin: 0;
    font-size: 2rem;
    color: black;
}

.profile-header p {
    margin: 0;
    opacity: 0.9;
}

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

.profile-sidebar {
    flex-shrink: 0;
    width: 250px;
}

.profile-main {
    flex: 1;
    min-width: 0;
}

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

.nav-tab:hover, .nav-tab.active {
    background: #ed7787;
    color: white;
}

.content-section {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border: 2px solid #ed7787;
    color: black;
}

.message-item {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    background: white;
    color: black;
}

.message-item:hover {
    box-shadow: 0 4px 12px rgba(237, 119, 135, 0.2);
    border-color: #ed7787;
}

.message-item.has-reply {
    border-left: 4px solid #28a745;
    background: white;
}

.message-item.no-reply {
    border-left: 4px solid #ffc107;
    background: white;
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
    color: black;
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
    color: black;
}

.status-pending {
    background: #fff3cd;
    color: black;
}

.message-content {
    background: #fcd4e8;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 15px;
    line-height: 1.5;
    border-left: 3px solid #ed7787;
    color: black;
}

.replies-section {
    border-top: 1px solid #eee;
    padding-top: 15px;
    background: white;
    color: black;
}

.reply-item {
    background: #e8f5e8;
    padding: 15px;
    border-radius: 6px;
    border-left: 3px solid #28a745;
    margin-bottom: 10px;
    color: black;
}

.reply-header {
    font-size: 0.85rem;
    color: black;
    margin-bottom: 8px;
    font-weight: 600;
}

.reply-content {
    line-height: 1.4;
    color: black;
}

.no-messages {
    text-align: center;
    padding: 40px;
    color: black;
    background: white;
    border-radius: 8px;
    border: 2px solid #ed7787;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
    border: 2px solid #ed7787;
    box-shadow: 0 2px 8px rgba(237, 119, 135, 0.1);
    color: black;
}

.stat-number {
    font-size: 2rem;
    font-weight: 600;
    color: black;
    margin-bottom: 5px;
}

.stat-label {
    color: black;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .profile-container {
        padding: 15px;
    }
    
    .profile-content-wrapper {
        flex-direction: column;
        gap: 20px;
    }
    
    .profile-sidebar {
        width: 100%;
    }
    
    .profile-nav {
        width: 100%;
        flex-direction: row;
        min-height: auto;
        flex-wrap: wrap;
        justify-content: center;
    }
    
    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
    </div>

    <div class="profile-content-wrapper">
        <div class="profile-sidebar">
            <div class="profile-nav">
                <a href="#messages" class="nav-tab active" onclick="showSection('messages')">My Messages</a>
                <a href="#account" class="nav-tab" onclick="showSection('account')">Account Info</a>
                <a href="<?php echo APPURL; ?>1contact.php" class="nav-tab">Send New Message</a>
                <a href="<?php echo APPURL; ?>auth/logout.php" class="nav-tab" style="background: #dc3545; border-color: #dc3545;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>

        <div class="profile-main">
            <!-- Messages Section -->
            <div id="messages-section" class="content-section">
                <h2>Your Messages & Replies</h2>
        
        <?php
        $total_messages = count($messages);
        $replied_messages = array_filter($messages, function($msg) { return $msg['reply_count'] > 0; });
        $pending_messages = array_filter($messages, function($msg) { return $msg['reply_count'] == 0; });
        $replied_count = count($replied_messages);
        $pending_count = count($pending_messages);
        ?>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?php echo $total_messages; ?></div>
                <div class="stat-label">Total Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $replied_count; ?></div>
                <div class="stat-label">Replied</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $pending_count; ?></div>
                <div class="stat-label">Pending Reply</div>
            </div>
        </div>

        <?php if (empty($messages)): ?>
            <div class="no-messages">
                <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
                <h3>No messages yet</h3>
                <p>You haven't sent any messages yet. Click "Send New Message" to contact us!</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-item <?php echo $message['reply_count'] > 0 ? 'has-reply' : 'no-reply'; ?>">
                    <div class="message-header">
                        <div class="message-date">
                            Sent on <?php echo date('M j, Y \a\t g:i A', strtotime($message['created_at'])); ?>
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
                        <strong>Your Message:</strong><br>
                        <?php echo nl2br(htmlspecialchars($message['message'])); ?>
                    </div>
                    
                    <?php if ($message['reply_count'] > 0): ?>
                        <div class="replies-section">
                            <h4 style="margin: 0 0 15px 0; color: #28a745;">
                                <i class="fas fa-reply"></i> Admin Responses:
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
        <?php endif; ?>
            </div>

            <!-- Account Section -->
            <div id="account-section" class="content-section" style="display: none;">
                <h2>Account Information</h2>
                <div style="display: grid; gap: 15px; max-width: 400px;">
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;">Username:</label>
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 6px;">
                            <?php echo htmlspecialchars($user['username']); ?>
                        </div>
                    </div>
                    <div>
                        <label style="font-weight: 600; color: black; display: block; margin-bottom: 5px;">Email:</label>
                        <div style="padding: 10px; background: #f8f9fa; border-radius: 6px;">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    // Hide all sections
    document.getElementById('messages-section').style.display = 'none';
    document.getElementById('account-section').style.display = 'none';
    
    // Remove active class from all tabs
    document.querySelectorAll('.nav-tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Show selected section
    document.getElementById(section + '-section').style.display = 'block';
    
    // Add active class to clicked tab
    event.target.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>