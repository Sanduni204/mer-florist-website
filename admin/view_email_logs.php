<?php require __DIR__ . '/admin_header.php'; ?>

<style>
.log-viewer {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.log-content {
    background: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 15px;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
    line-height: 1.4;
    max-height: 500px;
    overflow-y: auto;
    white-space: pre-wrap;
}

.log-actions {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 0.9rem;
}

.btn-primary {
    background: #ed7787;
    color: white;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn:hover {
    opacity: 0.9;
    transform: translateY(-1px);
}

.log-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}

.stat-item {
    background: white;
    padding: 10px 15px;
    border-radius: 6px;
    border: 1px solid #dee2e6;
    text-align: center;
}

.stat-number {
    font-size: 1.2rem;
    font-weight: 600;
    color: #ed7787;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
}

.no-logs {
    text-align: center;
    padding: 40px;
    color: #666;
}
</style>

<?php
$log_file = __DIR__ . '/../logs/email_replies.log';
$action = $_GET['action'] ?? '';

// Handle clear logs action
if ($action === 'clear' && $_POST['confirm'] ?? false) {
    if (file_exists($log_file)) {
        file_put_contents($log_file, '');
        $success_message = "Email logs cleared successfully!";
    }
}

// Read log content
$log_content = '';
$log_stats = [
    'total_entries' => 0,
    'file_size' => 0,
    'last_modified' => null
];

if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    $log_stats['file_size'] = filesize($log_file);
    $log_stats['last_modified'] = filemtime($log_file);
    $log_stats['total_entries'] = substr_count($log_content, '=== EMAIL LOG -');
}
?>

 

<a href="manage_messages.php" class="btn btn-secondary">
    <i class="fas fa-arrow-left"></i> Back to Messages
</a>

<?php if (isset($success_message)): ?>
    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 6px; margin: 15px 0;">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<div class="log-stats">
    <div class="stat-item">
        <div class="stat-number"><?php echo $log_stats['total_entries']; ?></div>
        <div class="stat-label">Total Emails</div>
    </div>
    <div class="stat-item">
        <div class="stat-number"><?php echo number_format($log_stats['file_size'] / 1024, 1); ?>KB</div>
        <div class="stat-label">File Size</div>
    </div>
    <div class="stat-item">
        <div class="stat-number">
            <?php echo $log_stats['last_modified'] ? date('M j', $log_stats['last_modified']) : 'Never'; ?>
        </div>
        <div class="stat-label">Last Updated</div>
    </div>
</div>

<div class="log-viewer">
    <div class="log-actions">
        <button onclick="refreshLogs()" class="btn btn-primary">
            <i class="fas fa-sync-alt"></i> Refresh
        </button>
        
        <?php if (!empty($log_content)): ?>
            <a href="?action=download" class="btn btn-secondary">
                <i class="fas fa-download"></i> Download Log
            </a>
            
            <button onclick="showClearConfirm()" class="btn btn-danger">
                <i class="fas fa-trash"></i> Clear Logs
            </button>
        <?php endif; ?>
    </div>
    
    <?php if (empty($log_content)): ?>
        <div class="no-logs">
            <i class="fas fa-file-alt" style="font-size: 3rem; color: #ccc; margin-bottom: 15px;"></i>
            <h3>No email logs yet</h3>
            <p>Email logs will appear here when admins send replies to customer messages.</p>
            <p><strong>Current status:</strong> Email functionality is in development mode.</p>
        </div>
    <?php else: ?>
        <div class="log-content" id="logContent">
            <?php echo htmlspecialchars($log_content); ?>
        </div>
    <?php endif; ?>
</div>

<!-- Clear Confirmation Modal -->
<div id="clearModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; max-width: 400px; width: 90%;">
        <h3>Clear Email Logs</h3>
        <p>Are you sure you want to clear all email logs? This action cannot be undone.</p>
        <form method="POST" action="?action=clear">
            <input type="hidden" name="confirm" value="1">
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="button" onclick="hideClearConfirm()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-danger">Clear Logs</button>
            </div>
        </form>
    </div>
</div>

<script>
function refreshLogs() {
    location.reload();
}

function showClearConfirm() {
    document.getElementById('clearModal').style.display = 'block';
}

function hideClearConfirm() {
    document.getElementById('clearModal').style.display = 'none';
}

// Auto-scroll to bottom of logs
const logContent = document.getElementById('logContent');
if (logContent) {
    logContent.scrollTop = logContent.scrollHeight;
}

// Auto-refresh every 30 seconds
setInterval(refreshLogs, 30000);
</script>

<?php
// Handle download action
if ($action === 'download' && file_exists($log_file)) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="email_replies_' . date('Y-m-d_H-i-s') . '.log"');
    header('Content-Length: ' . filesize($log_file));
    readfile($log_file);
    exit;
}
?>

<?php require __DIR__ . '/admin_footer.php'; ?>