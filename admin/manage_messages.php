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
    background: #f5f5f5; /* ash white */
    transition: all 0.2s ease;
}

.message-item:hover {
    box-shadow: none;
}

.message-item.unread {
    border-left: 4px solid #000; /* black */
    background: #f5f5f5; /* ash white */
}

.message-item.read {
    border-left: 4px solid #000; /* black */
    background: #f5f5f5; /* ash white */
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
    color: #000; /* black */
    font-size: 1.1rem;
}

.sender-email {
    color: #000; /* black */
    font-size: 0.9rem;
}

.message-date {
    color: #000; /* black */
    font-size: 0.85rem;
}

.message-content {
    margin: 15px 0;
    padding: 10px;
    background: #f5f5f5; /* ash white */
    border-radius: 6px;
    line-height: 1.5;
    border-left: 3px solid #000; /* black */
}

.message-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 6px 12px;
    border: 1px solid #000; /* black border */
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.85rem;
    transition: all 0.15s ease;
    background: transparent;
    color: #000; /* black */
}

.btn-read {
    background: #000; /* black */
    color: #f5f5f5; /* ash white text */
}

.btn-unread {
    background: transparent;
    color: #000; /* black */
}

.btn-delete {
    background: #000; /* black */
    color: #f5f5f5; /* ash white */
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
    background: #f5f5f5; /* ash white */
    color: #000; /* black */
    padding: 15px;
    border-radius: 8px;
    text-align: center;
    min-width: 120px;
    border: 1px solid #000;
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
    color: #000; /* black */
}

.reply-section {
    margin-top: 15px;
    padding: 15px;
    background: #f5f5f5; /* ash white */
    border-radius: 8px;
    border: none;
    display: none;
}

.reply-section.active {
    display: block;
    animation: slideDown 0.2s ease;
}

@keyframes slideDown {
    from { opacity: 0; transform: translateY(-6px); }
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
    border: 1px solid #000;
    border-radius: 6px;
    resize: vertical;
    font-family: inherit;
    font-size: 0.9rem;
    background: #f5f5f5; /* ash white */
    color: #000;
}

.reply-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
}

.btn-reply {
    background: #000; /* black */
    color: #f5f5f5; /* ash white */
}

.btn-cancel {
    background: transparent;
    color: #000; /* black */
}

.btn-send {
    background: #000; /* black */
    color: #f5f5f5; /* ash white */
}

.previous-replies {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #000;
}

.reply-item {
    background: #f5f5f5; /* ash white */
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 6px;
    border-left: 3px solid #000; /* black */
}

.reply-header {
    font-size: 0.85rem;
    color: #000; /* black */
    margin-bottom: 5px;
}

.reply-content {
    font-size: 0.9rem;
    line-height: 1.4;
    color: #000; /* black */
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
    <div style="background: #f5f5f5; color: #000; padding: 10px; border-radius: 6px; margin-bottom: 15px; border:1px solid #000;">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if (isset($error_message)): ?>
    <div style="background: #f5f5f5; color: #000; padding: 10px; border-radius: 6px; margin-bottom: 15px; border:1px solid #000;">
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
// Group messages by sender email for a per-user chat view
$groupedUsers = [];
foreach ($messages as $msg) {
    $email = $msg['email'] ?? 'unknown';
    if (!isset($groupedUsers[$email])) {
        $groupedUsers[$email] = [
            'name' => $msg['name'] ?? $email,
            'email' => $email,
            'username' => $msg['username'] ?? '',
            'messages' => [],
        ];
    }
    $groupedUsers[$email]['messages'][] = $msg;
}

$users = array_values($groupedUsers);
foreach ($users as &$u) {
    usort($u['messages'], function($a, $b) { return strtotime($b['created_at']) - strtotime($a['created_at']); });
    $u['last_at'] = $u['messages'][0]['created_at'] ?? null;
    $u['unread_count'] = count(array_filter($u['messages'], function($m){ return !$m['is_read']; }));
}
unset($u);
usort($users, function($a, $b) { return strtotime($b['last_at']) - strtotime($a['last_at']); });
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
        <i class="fas fa-inbox" style="font-size: 3rem; color: #000; margin-bottom: 15px;"></i>
        <h3>No messages yet</h3>
        <p>Customer messages will appear here when they contact you through the contact form.</p>
    </div>
<?php else: ?>
    <style>
    /* User section styles */
    .user-section { border:1px solid #000; border-radius:8px; margin-bottom:12px; background:#f5f5f5; }
    .user-header { display:flex; justify-content:space-between; align-items:center; padding:10px 12px; cursor:pointer; }
    .user-header .meta { font-size:0.95rem; color:#000; }
    .user-header .counts { font-size:0.85rem; color:#000; }
    .user-chat { padding:12px; border-top:1px solid #000; display:none; }

    /* Override button styles inside chat: use ash background and black text */
    .user-chat .btn {
        background: #f5f5f5 !important;
        color: #000 !important;
        border: 1px solid #000 !important;
    }
    .user-chat .btn.btn-read,
    .user-chat .btn.btn-unread,
    .user-chat .btn.btn-delete,
    .user-chat .btn.btn-reply,
    .user-chat .btn.btn-send,
    .user-chat .btn.btn-cancel {
        background: #f5f5f5 !important;
        color: #000 !important;
    }
    .user-chat .btn:hover {
        transform: translateY(-1px);
        background: #e9e9e9 !important;
    }
    </style>

    <?php foreach ($users as $u_index => $user): ?>
        <div class="user-section">
            <div class="user-header" onclick="toggleUser(<?php echo $u_index; ?>)">
                <div class="meta">
                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                    <div style="font-size:0.85rem; color:#000;">&lt;<?php echo htmlspecialchars($user['email']); ?>&gt;
                        <?php if (!empty($user['username'])): ?>
                            <span style="background:#000;color:#f5f5f5;padding:2px 6px;border-radius:10px;font-size:0.7rem;margin-left:8px;">@<?php echo htmlspecialchars($user['username']); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="counts">
                    <span style="margin-right:8px;"><?php echo count($user['messages']); ?> msgs</span>
                    <span><?php echo $user['unread_count']; ?> new</span>
                </div>
            </div>
            <div class="user-chat" id="user-chat-<?php echo $u_index; ?>">
                <?php foreach ($user['messages'] as $message): ?>
                    <div class="message-item <?php echo $message['is_read'] ? 'read' : 'unread'; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <div class="sender-name"><?php echo htmlspecialchars($message['name']); ?></div>
                                <div class="sender-email"><?php echo htmlspecialchars($message['email']); ?></div>
                                <div class="message-date"><?php echo date('M j, Y \a\t g:i A', strtotime($message['created_at'])); ?></div>
                            </div>
                            <div class="message-actions">
                                <button type="button" onclick="toggleReply(<?php echo $message['id']; ?>)" class="btn btn-reply"><i class="fas fa-reply"></i> Reply</button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <input type="hidden" name="is_read" value="<?php echo $message['is_read'] ? 0 : 1; ?>">
                                    <button type="submit" name="toggle_read" class="btn <?php echo $message['is_read'] ? 'btn-unread' : 'btn-read'; ?>"><?php echo $message['is_read'] ? 'Mark Unread' : 'Mark Read'; ?></button>
                                </form>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this message?')">
                                    <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                    <button type="submit" name="delete_message" class="btn btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                </form>
                            </div>
                        </div>
                        <div class="message-content"><?php echo nl2br(htmlspecialchars($message['message'])); ?></div>

                        <div id="reply-section-<?php echo $message['id']; ?>" class="reply-section">
                            <?php
                            try {
                                $stmt = $conn->prepare("SELECT reply_text, admin_name, created_at FROM message_replies WHERE message_id = :message_id ORDER BY created_at DESC");
                                $stmt->execute([':message_id' => $message['id']]);
                                $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                if (!empty($replies)): ?>
                                    <div class="previous-replies">
                                        <h4 style="margin:0 0 10px 0; font-size:0.9rem; color:#000;">Previous Replies:</h4>
                                        <?php foreach ($replies as $reply): ?>
                                            <div class="reply-item">
                                                <div class="reply-header"><strong><?php echo htmlspecialchars($reply['admin_name']); ?></strong> - <?php echo date('M j, Y \a\t g:i A', strtotime($reply['created_at'])); ?></div>
                                                <div class="reply-content"><?php echo nl2br(htmlspecialchars($reply['reply_text'])); ?></div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif;
                            } catch (PDOException $e) {
                                // ignore
                            }
                            ?>

                            <form method="POST" class="reply-form">
                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                <input type="hidden" name="customer_email" value="<?php echo htmlspecialchars($message['email']); ?>">
                                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($message['name']); ?>">
                                <label for="reply_text_<?php echo $message['id']; ?>">Your Reply:</label>
                                <textarea name="reply_text" id="reply_text_<?php echo $message['id']; ?>" class="reply-textarea" placeholder="Type your reply here..." required></textarea>
                                <div class="reply-actions">
                                    <button type="button" onclick="toggleReply(<?php echo $message['id']; ?>)" class="btn btn-cancel">Cancel</button>
                                    <button type="submit" name="send_reply" class="btn btn-send"><i class="fas fa-paper-plane"></i> Send Reply</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
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

// Toggle a user's chat section
function toggleUser(index) {
    const el = document.getElementById('user-chat-' + index);
    if (!el) return;
    const isVisible = el.style.display === 'block';

    // close other user chats
    document.querySelectorAll('.user-chat').forEach(c => { c.style.display = 'none'; });

    if (!isVisible) {
        el.style.display = 'block';
        // scroll into view
        setTimeout(() => el.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
    }
}
</script>

<?php require __DIR__ . '/admin_footer.php'; ?>