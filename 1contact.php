<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>
<?php
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
                    <form action="" method="Post">
                   <input type="text" name="name" size="30" placeholder="Name"  required><br>
                   <input type="email" name="email" size="30"placeholder="Email"  required><br>
                   <textarea name="message" cols="30" rows="5"placeholder="Message.."></textarea><br>
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
            
        