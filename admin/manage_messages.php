<?php require __DIR__ . '/admin_header.php'; ?>

<?php
// Handle reply submission
if (isset($_POST['send_reply'])) {
    $message_id = (int)$_POST['message_id'];
    $reply_text = trim($_POST['reply_text']);
    $customer_email = $_POST['customer_email'];
    $customer_name = $_POST['customer_name'];
    
    if (!empty($reply_text) && !empty($customer_email)) {
        try {
            // First, ensure replies table exists
            $conn->exec("CREATE TABLE IF NOT EXISTS message_replies (
                id INT AUTO_INCREMENT PRIMARY KEY,
                message_id INT,
                reply_text TEXT NOT NULL,
                admin_name VARCHAR(100),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
            )");
            
            // Insert the reply
            $stmt = $conn->prepare("INSERT INTO message_replies (message_id, reply_text, admin_name) VALUES (:message_id, :reply_text, :admin_name)");
            $admin_name = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
            $stmt->execute([
                ':message_id' => $message_id,
                ':reply_text' => $reply_text,
                ':admin_name' => $admin_name
            ]);
            
            // Mark original message as read
            $stmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE id = :id");
            $stmt->execute([':id' => $message_id]);
            
            $success_message = "Reply sent successfully! The customer will see your response when they log into their account.";
        } catch (PDOException $e) {
            $error_message = "Failed to send reply: " . $e->getMessage();
        }
    } else {
        $error_message = "Please enter a reply message.";
    }
}

// Handle message deletion
if (isset($_POST['delete_message'])) {
    $message_id = (int)$_POST['message_id'];
    try {
        $stmt = $conn->prepare("DELETE FROM messages WHERE id = :id");
        $stmt->execute([':id' => $message_id]);
        $success_message = "Message deleted successfully!";
    } catch (PDOException $e) {
        $error_message = "Failed to delete message.";
    }
}

// Handle mark as read/unread
if (isset($_POST['toggle_read'])) {
    $message_id = (int)$_POST['message_id'];
    $is_read = (int)$_POST['is_read'];
    try {
        // First, add is_read column if it doesn't exist
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS is_read BOOLEAN DEFAULT FALSE");
        
        $stmt = $conn->prepare("UPDATE messages SET is_read = :is_read WHERE id = :id");
        $stmt->execute([':is_read' => $is_read, ':id' => $message_id]);
        $success_message = $is_read ? "Message marked as read!" : "Message marked as unread!";
    } catch (PDOException $e) {
        $error_message = "Failed to update message status.";
    }
}

// Fetch all messages with reply counts
try {
    // Ensure is_read column exists
    try {
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS is_read BOOLEAN DEFAULT FALSE");
        $conn->exec("ALTER TABLE messages ADD COLUMN IF NOT EXISTS username VARCHAR(255) NULL");
    } catch (PDOException $e) {
        // Columns might already exist
    }
    
    // Ensure replies table exists
    try {
        $conn->exec("CREATE TABLE IF NOT EXISTS message_replies (
            id INT AUTO_INCREMENT PRIMARY KEY,
            message_id INT,
            reply_text TEXT NOT NULL,
            admin_name VARCHAR(100),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (message_id) REFERENCES messages(id) ON DELETE CASCADE
        )");
    } catch (PDOException $e) {
        // Table might already exist
    }
    
    $stmt = $conn->query("
        SELECT m.id, m.name, m.email, m.message, m.created_at, m.username,
               COALESCE(m.is_read, 0) as is_read,
               COUNT(r.id) as reply_count
        FROM messages m 
        LEFT JOIN message_replies r ON m.id = r.message_id 
        GROUP BY m.id 
        ORDER BY m.created_at DESC
    ");
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $messages = [];
    $error_message = "Failed to fetch messages.";
}
?>

<style>
.message-item {
    border: none;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 15px;
    background: white;
    transition: all 0.3s ease;
}

.message-item:hover {
    box-shadow: none;
}

.message-item.unread {
    border-left: 4px solid #ed7787;
    background: #fefefe;
}

.message-item.read {
    border-left: 4px solid #ccc;
    background: #f9f9f9;
}

.message-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    flex-wrap: wrap;
    gap: 10px;
}

.message-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.sender-name {
    font-weight: 600;
    color: #2c3e50;
    font-size: 1.1rem;
}

.sender-email {
    color: #666;
    font-size: 0.9rem;
}

.message-date {
    color: #888;
    font-size: 0.85rem;
}

.message-content {
    margin: 15px 0;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    line-height: 1.5;
    border-left: 3px solid #ed7787;
}

.message-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 6px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.btn-read {
    background: #28a745;
    color: white;
}

.btn-unread {
    background: #6c757d;
    color: white;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn:hover {
    transform: translateY(-1px);
}

.stats {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.stat-card {
    background: linear-gradient(135deg, #fcd4e8 0%, #ed7787 100%);
    color: white;
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    min-width: 120px;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 600;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.no-messages {
    text-align: center;
    padding: 40px;
    color: #666;
}

.reply-section {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border: none;
    display: none;
}

.reply-section.active {
    display: block;
    animation: slideDown 0.3s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.reply-form {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.reply-textarea {
    width: 100%;
    min-height: 80px;
    padding: 10px;
    border: none;
    border-radius: 6px;
    resize: vertical;
    font-family: inherit;
    font-size: 0.9rem;
}

.reply-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-reply {
    background: #ed7787;
    color: white;
}

.btn-cancel {
    background: #6c757d;
    color: white;
}

.btn-send {
    background: #28a745;
    color: white;
}

.previous-replies {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #ddd;
}

.reply-item {
    background: #fff;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    border-left: 3px solid #28a745;
}

.reply-header {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 5px;
}

.reply-content {
    font-size: 0.9rem;
    line-height: 1.4;
}

@media (max-width: 768px) {
    .message-header {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats {
        justify-content: center;
    }
    
    .message-actions {
        justify-content: center;
    }
}
</style>

 

<?php if (isset($success_message)): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 6px; margin-bottom: 15px;">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>

<?php
// Calculate statistics
$total_messages = count($messages);
$unread_messages = array_filter($messages, function($msg) { return !$msg['is_read']; });
$read_messages = array_filter($messages, function($msg) { return $msg['is_read']; });
$unread_count = count($unread_messages);
$read_count = count($read_messages);
?>

<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?php echo $total_messages; ?></div>
        <div class="stat-label">Total Messages</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $unread_count; ?></div>
        <div class="stat-label">Unread</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?php echo $read_count; ?></div>
        <div class="stat-label">Read</div>
    </div>
</div>

<?php if (empty($messages)): ?>
    <div class="no-messages">
        <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
        <h3>No messages yet</h3>
        <p>Customer messages will appear here when they contact you through the contact form.</p>
    </div>
<?php else: ?>
    <?php foreach ($messages as $message): ?>
        <div class="message-item <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
            <div class="message-header">
                <div class="message-info">
                    <div class="sender-name">
                        <?php echo htmlspecialchars($message['name']); ?>
                        <?php if (!empty($message['username'])): ?>
                            <span style="background: #6c757d; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 8px;">
                                @<?php echo htmlspecialchars($message['username']); ?>
                            </span>
                        <?php endif; ?>
                        <?php if (!$message['is_read']): ?>
                            <span style="background: #ed7787; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 8px;">NEW</span>
                        <?php endif; ?>
                        <?php if ($message['reply_count'] > 0): ?>
                            <span style="background: #28a745; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.7rem; margin-left: 8px;">
                                <?php echo $message['reply_count']; ?> Repl<?php echo $message['reply_count'] == 1 ? 'y' : 'ies'; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="sender-email"><?php echo htmlspecialchars($message['email']); ?></div>
                    <div class="message-date"><?php echo date('M j, Y \a\t g:i A', strtotime($message['created_at'])); ?></div>
                </div>
                <div class="message-actions">
                    <button type="button" onclick="toggleReply(<?php echo $message['id']; ?>)" class="btn btn-reply">
                        <i class="fas fa-reply"></i> Reply
                    </button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <input type="hidden" name="is_read" value="<?php echo $message['is_read'] ? 0 : 1; ?>">
                        <button type="submit" name="toggle_read" class="btn <?php echo $message['is_read'] ? 'btn-unread' : 'btn-read'; ?>">
                            <?php echo $message['is_read'] ? 'Mark Unread' : 'Mark Read'; ?>
                        </button>
                    </form>
                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                        <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                        <button type="submit" name="delete_message" class="btn btn-delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
            <div class="message-content">
                <?php echo nl2br(htmlspecialchars($message['message'])); ?>
            </div>
            
            <!-- Reply Section -->
            <div id="reply-section-<?php echo $message['id']; ?>" class="reply-section">
                <!-- Previous Replies -->
                <?php
                try {
                    $stmt = $conn->prepare("SELECT reply_text, admin_name, created_at FROM message_replies WHERE message_id = :message_id ORDER BY created_at DESC");
                    $stmt->execute([':message_id' => $message['id']]);
                    $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    if (!empty($replies)): ?>
                        <div class="previous-replies">
                            <h4 style="margin: 0 0 10px 0; font-size: 0.9rem; color: #666;">Previous Replies:</h4>
                            <?php foreach ($replies as $reply): ?>
                                <div class="reply-item">
                                    <div class="reply-header">
                                        <strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong> - 
                                        <?php echo date('M j, Y \a\t g:i A', strtotime($reply['created_at'])); ?>
                                    </div>
                                    <div class="reply-content"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif;
                } catch (PDOException $e) {
                    // Ignore errors when fetching replies
                }
                ?>
                
                <!-- Reply Form -->
                <form method="POST" class="reply-form">
                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                    <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($message['email']); ?>">
                    <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($message['name']); ?>">
                    <input type="hidden" name="customer_username" value="<?php echo htmlspecialchars($message['username'] ?? ''); ?>">
                    
                    <label for="reply_text_<?php echo $message['id']; ?>">Your Reply:</label>
                    <textarea 
                        name="reply_text" 
                        id="reply_text_<?php echo $message['id']; ?>" 
                        class="reply-textarea" 
                        placeholder="Type your reply here..." 
                        required
                    ></textarea>
                    
                    <div class="reply-actions">
                        <button type="button" onclick="toggleReply(<?php echo $message['id']; ?>)" class="btn btn-cancel">
                            Cancel
                        </button>
                        <button type="submit" name="send_reply" class="btn btn-send">
                            <i class="fas fa-paper-plane"></i> Send Reply
                        </button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<script>
function toggleReply(messageId) {
    const replySection = document.getElementById('reply-section-' + messageId);
    const isActive = replySection.classList.contains('active');
    
    // Close all other reply sections
    document.querySelectorAll('.reply-section.active').forEach(section => {
        section.classList.remove('active');
    });
    
    // Toggle current section
    if (!isActive) {
        replySection.classList.add('active');
        // Focus on the textarea
        const textarea = document.getElementById('reply_text_' + messageId);
        if (textarea) {
            setTimeout(() => textarea.focus(), 100);
        }
    }
}

// Close reply sections when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.reply-section') && !event.target.closest('.btn-reply')) {
        document.querySelectorAll('.reply-section.active').forEach(section => {
            section.classList.remove('active');
        });
    }
});
</script>

<?php require __DIR__ . '/admin_footer.php'; ?>