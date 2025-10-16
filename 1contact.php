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
                <!-- All social icons on a single line (Instagram, Facebook, Twitter, YouTube, WhatsApp) -->
                <p class="social-icons"><a href="<?php echo htmlspecialchars($c['instagram'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-instagram"></i></a>&nbsp;&nbsp;
                    <a href="<?php echo htmlspecialchars($c['facebook'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-facebook"></i></a>&nbsp;&nbsp;
                    <a href="<?php echo htmlspecialchars($c['twitter'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-twitter"></i></a>&nbsp;&nbsp;
                    <a href="<?php echo htmlspecialchars($c['youtube'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-youtube"></i></a>&nbsp;&nbsp;
                    <a href="<?php echo htmlspecialchars($c['whatsapp'] ?? '#'); ?>" target="_blank"><i class="fa-brands fa-whatsapp"></i></a>
                </p><br>
                <div class="contact-message-block">
                    <i class="fa-solid fa-message"></i>
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
                    <?php
                    // Prepare chat messages for logged-in users
                    $chat_messages = [];
                    $is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['username']);
                    if ($is_logged_in) {
                        try {
                            $user_id = $_SESSION['user_id'];
                            $username = $_SESSION['username'];
                            $stmt = $conn->prepare("SELECT id, message, created_at FROM messages WHERE (user_id = :user_id OR username = :username) ORDER BY created_at DESC LIMIT 10");
                            $stmt->execute([':user_id' => $user_id, ':username' => $username]);
                            $msgs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($msgs as $m) {
                                // fetch replies for each message
                                $rstmt = $conn->prepare("SELECT reply_text, admin_name, created_at FROM message_replies WHERE message_id = :message_id ORDER BY created_at ASC");
                                $rstmt->execute([':message_id' => $m['id']]);
                                $replies = $rstmt->fetchAll(PDO::FETCH_ASSOC);
                                $chat_messages[] = [
                                    'id' => $m['id'],
                                    'message' => $m['message'],
                                    'created_at' => $m['created_at'],
                                    'replies' => $replies
                                ];
                            }
                        } catch (PDOException $e) {
                            // ignore fetch errors; show empty chat
                            $chat_messages = [];
                        }
                    }
                    ?>

                    <form action="" method="Post" class="contact-form-column" style="display:flex;flex-direction:column;gap:5px;width:100%;">
                   <input type="text" name="name" size="30" placeholder="Name" value="<?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : ''; ?>" required>
                   <input type="email" name="email" size="30" placeholder="Email" value="<?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?>" required>
                   <textarea name="message" class="contact-textarea" rows="5" placeholder="Message.." style="width:100%;box-sizing:border-box;padding:8px;border:1px solid #ddd;border-radius:6px;resize:vertical;min-height:120px;"></textarea>

                   <div class="contact-actions-wrapper" style="position:relative;width:100%;">
                       <div class="contact-actions" style="display:flex;justify-content:flex-end;align-items:center;gap:15px;width:100%;margin-top:5px;">
                           <button type="button" id="chat-toggle" class="chat-btn">Chat</button>
                           <button type="submit" name="send" class="chat-btn send-btn">Send</button>
                       </div>

                       <!-- Chat dropdown (absolute, appears below buttons) -->
                       <div id="chat-dropdown" style="display:none;position:absolute;right:0;top:calc(100% + 10px);z-index:99999;max-width:420px;width: min(420px, 90vw);background:#fff;border:1px solid rgba(0,0,0,0.08);box-shadow:0 8px 24px rgba(0,0,0,0.12);border-radius:10px;overflow:hidden;font-family:inherit;">
                       <div style="padding:12px 14px;border-bottom:1px solid #eee;background:#fafafa;font-weight:700;">Chat history</div>
                       <div id="chat-list" style="max-height:360px;overflow:auto;padding:12px;">
                           <?php if (!$is_logged_in): ?>
                               <div style="padding:12px;color:#333">You must <a href="auth/1login.php">log in</a> to view your chat history.</div>
                           <?php else: ?>
                               <?php if (empty($chat_messages)): ?>
                                   <div style="padding:12px;color:#666">No recent messages.</div>
                               <?php else: ?>
                                   <?php foreach ($chat_messages as $cm): ?>
                                       <div style="margin-bottom:12px;padding:10px;border-radius:8px;background:#fff;border:1px solid #f0f0f0;">
                                           <div style="font-size:0.85rem;color:#666;margin-bottom:6px;">You • <?php echo htmlspecialchars(date('M j, Y \a\t g:i A', strtotime($cm['created_at']))); ?></div>
                                           <div style="color:#333;margin-bottom:8px;white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($cm['message'])); ?></div>
                                           <?php if (!empty($cm['replies'])): ?>
                                               <div style="margin-top:6px;border-top:1px dashed #eee;padding-top:8px;">
                                                   <?php foreach ($cm['replies'] as $rep): ?>
                                                       <div style="margin-bottom:8px;padding:8px;border-radius:6px;background:#f8f9fa;border:1px solid #e6f4ea;">
                                                           <div style="font-size:0.82rem;color:#2c7a3f;margin-bottom:4px;"><?php echo htmlspecialchars($rep['admin_name'] ?: 'Admin'); ?> • <?php echo htmlspecialchars(date('M j, Y \a\t g:i A', strtotime($rep['created_at']))); ?></div>
                                                           <div style="color:#234;white-space:pre-wrap;"><?php echo nl2br(htmlspecialchars($rep['reply_text'])); ?></div>
                                                       </div>
                                                   <?php endforeach; ?>
                                               </div>
                                           <?php endif; ?>
                                       </div>
                                   <?php endforeach; ?>
                               <?php endif; ?>
                           <?php endif; ?>
                       </div>
                       <div style="padding:10px;border-top:1px solid #eee;background:#fafafa;text-align:center;font-size:0.9rem;color:#666;">Showing up to 10 most recent messages</div>
                       </div>
                   </div>
                    </form>
                    <script>
                            function alignMessageIcon(){
                                // goal: move the whole form so its left edge is directly under the message icon
                                // prefer to target the instagram icon specifically, fallback to the first visible anchor
                                let instaAnchor = document.querySelector('.social-icons a[href][target] .fa-instagram');
                                if (instaAnchor) {
                                    // if we found the <i> icon, use its parent anchor
                                    instaAnchor = instaAnchor.closest('a');
                                } else {
                                    // if instagram icon isn't present, pick the first social anchor in the first row
                                    const anchors = Array.from(document.querySelectorAll('.social-icons a'));
                                    if (anchors.length === 0) instaAnchor = null;
                                    else {
                                        // pick the anchor whose icon sits on the top-most row (smallest top)
                                        let best = anchors[0];
                                        try {
                                            let minTop = anchors[0].getBoundingClientRect().top;
                                            anchors.forEach(a => {
                                                const t = a.getBoundingClientRect().top;
                                                if (t < minTop) { minTop = t; best = a; }
                                            });
                                        } catch (e) { best = anchors[0]; }
                                        instaAnchor = best;
                                    }
                                }
                                const msg = document.querySelector('.contact-message-block i.fa-message');
                                const msgBlock = document.querySelector('.col2 .contact-message-block');
                                const form = document.querySelector('.contact-form-column');
                                if (!instaAnchor || !msg || !msgBlock || !form) return;

                                const instaRect = instaAnchor.getBoundingClientRect();
                                const msgRect = msg.getBoundingClientRect();
                                const msgBlockRect = msgBlock.getBoundingClientRect();

                                // compute the instagram icon left relative to the contact-message-block
                                const leftRel = Math.max(0, Math.round(instaRect.left - msgBlockRect.left));

                                // position the message icon absolutely inside the block so it visually sits under instagram
                                msg.style.position = 'absolute';
                                msg.style.left = leftRel + 'px';
                                msg.style.top = '4px';
                                msg.style.margin = '0';

                                // reserve vertical space for the icon
                                msgBlock.style.paddingTop = Math.max(28, msg.offsetHeight + 8) + 'px';

                                // Allow the form to shift horizontally by setting padding-left on the form column
                                try {
                                    // determine icon offset inside the block after it's positioned
                                    const iconOffsetInBlock = msg.offsetLeft || Math.max(0, Math.round(msg.getBoundingClientRect().left - msgBlock.getBoundingClientRect().left));

                                    // small nudge for medium widths where spacing differs
                                    const vw = Math.max(document.documentElement.clientWidth || 0, window.innerWidth || 0);
                                    let nudge = 0;
                                    if (vw >= 480 && vw <= 1200) nudge = 2; // small mid-width adjustment (reduced)

                                    // apply padding-left to the form so its left edge starts at the icon's left
                                    form.style.setProperty('padding-left', (iconOffsetInBlock + nudge) + 'px', 'important');
                                    form.style.setProperty('margin-left', '0px', 'important');
                                    form.style.boxSizing = 'border-box';

                                    // ensure inputs are full width within the adjusted padding
                                    const inputs = form.querySelectorAll('input, textarea');
                                    inputs.forEach(i => {
                                        i.style.boxSizing = 'border-box';
                                        i.style.marginLeft = '0';
                                        i.style.setProperty('width', '100%', 'important');
                                        i.style.transform = ''; // clear any previous transforms
                                    });
                                } catch (e) {
                                    // fallback: move the form container by simple margin
                                    form.style.marginLeft = leftRel + 'px';
                                }

                                if (window && window.console) {
                                    console.log('alignMessageIcon: vw=', vw, 'leftRel=', leftRel, 'iconOffsetInBlock=', iconOffsetInBlock, 'nudge=', nudge, 'instaLeft=', instaRect.left, 'msgBlockLeft=', msgBlockRect.left);
                                }
                            }

                            // run on load, DOM ready and resize (debounced). Also run after short delay to catch icon font load.
                            let t;
                            function runAlignWithRafRetries(){
                                let attempts = 0;
                                function tryOnce(){
                                    alignMessageIcon();
                                    attempts++;
                                    if (attempts < 12) { // retry for up to ~200-300ms worth of frames
                                        requestAnimationFrame(tryOnce);
                                    }
                                }
                                tryOnce();
                            }

                            window.addEventListener('resize', function(){ clearTimeout(t); t = setTimeout(alignMessageIcon, 120); });
                            window.addEventListener('load', runAlignWithRafRetries);
                            document.addEventListener('DOMContentLoaded', runAlignWithRafRetries);
                            // multiple delayed runs as a backup to catch late icon or font loads
                            setTimeout(runAlignWithRafRetries, 250);
                            setTimeout(runAlignWithRafRetries, 650);
                            setTimeout(runAlignWithRafRetries, 1200);
                    </script>
                </div>
            </div>
            <div class="col3">
                <iframe  src="<?php echo htmlspecialchars($c['map_embed_url'] ?? ''); ?>" >
                </iframe><a href="https://embedgooglemap.net/124/"></a>
            </div>
            </div>


<?php require "includes/footer.php"; ?>
