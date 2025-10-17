<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>
<?php
// Dynamic shop: render sections by bouquet type using DB data from `shop`
// Helper: fetch items by type keywords (case-insensitive)
function fetch_items_by_types(PDO $conn, array $keywords): array {
    if (empty($keywords)) return [];
    $clauses = [];
    $params = [];
    foreach ($keywords as $i => $kw) {
        $clauses[] = "type LIKE :t$i";
        $params[":t$i"] = "%$kw%"; // match both 'Rose' and 'Rose Bouquets'
    }
    $where = implode(' OR ', $clauses);
    // Use fid for ordering and selection
    $sql = "SELECT fid, name, type, color_theme, price, image, description FROM shop WHERE $where ORDER BY fid DESC";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        // Fallback without ordering
        try {
            $stmt = $conn->prepare("SELECT fid, name, type, color_theme, price, image, description FROM shop WHERE $where ORDER BY name ASC");
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (Throwable $e2) { return []; }
    }
}

function render_item_card(array $it, string $cardClass): string {
    $name = htmlspecialchars($it['name'] ?? '');
    $price = isset($it['price']) ? number_format((float)$it['price'], 2) : '0.00';
    $desc = htmlspecialchars(trim((string)($it['description'] ?? '')));
    $img = htmlspecialchars($it['image'] ?? '');
    $imgTag = $img !== ''
        ? '<img class="img" src="'.APPURL.'Images/'.$img.'" alt="'. $name .'" />'
        : '<div class="img" style="display:flex;align-items:center;justify-content:center;background:#f6f6f6;color:#999;">No Image</div>';
    $fid = isset($it['fid']) ? (int)$it['fid'] : 0;
    return '<div class="'. $cardClass .'">'.
         $imgTag.
         '<p>'. $name .'</p>'.
         '<p>RS.'. $price .'</p>'.
         ($desc !== '' ? '<p><b>'. $desc .'</b></p>' : '').
         '<div class="item-buttons">'.
         '<button onclick="addToCart('.$fid.')" class="add-to-cart-btn" data-id="'.$fid.'">Add to Cart</button>'.
         '<a href="1payment.php?id='.$fid.'" class="pay-now-btn" onclick="return payNowClicked(this)">Pay Now</a>'.
         '</div>'.
         '</div>';
}
?>


<div class="section">
    <p id="sec_text">
        
        Let the delicate fragrance of blooming blossoms fill the hearts<br> and souls of your
         beloved ones,<br> wrapping them in the tender embrace<br> of nature's timeless beauty,
          and nurturing a sense of <br>serenity and joy that lasts for eternity.</p>
</div>
    

    <div class="gallery">
       
        <div class="groupA">
        <a href="#Rose"><div  id="group1">
        <p>Rose Bouquets</p>
        </div></a>

        <a href="#Lily"><div  id="group2">
        <p>Lily Bouquets</p>
        </div></a>

        <a href="#Sunflower"><div  id="group5">
        <p>Sunflower Bouquets</p>
        </div></a>
        </div>

        <div class="groupB">
        <a href="#Daisy"><div id="group3">
        <p>Daisy Bouquets</p>
        </div></a>

        <a href="#Tulip"><div id="group4">
        <p>Tulip Bouquets</p>
        </div></a>
        
        <a href="#Hydrangea"><div  id="group6">
        <p>Hydrangeas Bouquets</p>
        </div></a>
        </div>
    
    </div>
    
    <div id="Rose">
        <?php $roseItems = fetch_items_by_types($conn, ['Rose']); ?>
        <?php if (!empty($roseItems)): ?>
            <?php foreach ($roseItems as $it) { echo render_item_card($it, 'R'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Rose bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

    <div id="Lily">
        <?php $lilyItems = fetch_items_by_types($conn, ['Lily']); ?>
        <?php if (!empty($lilyItems)): ?>
            <?php foreach ($lilyItems as $it) { echo render_item_card($it, 'L'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Lily bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

    <div id="Daisy">
        <?php $daisyItems = fetch_items_by_types($conn, ['Daisy']); ?>
        <?php if (!empty($daisyItems)): ?>
            <?php foreach ($daisyItems as $it) { echo render_item_card($it, 'D'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Daisy bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

    <div id="Tulip">
        <?php $tulipItems = fetch_items_by_types($conn, ['Tulip']); ?>
        <?php if (!empty($tulipItems)): ?>
            <?php foreach ($tulipItems as $it) { echo render_item_card($it, 'T'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Tulip bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

    <div id="Sunflower">
        <?php $sunItems = fetch_items_by_types($conn, ['Sunflower', 'Sun']); ?>
        <?php if (!empty($sunItems)): ?>
            <?php foreach ($sunItems as $it) { echo render_item_card($it, 'S'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Sunflower bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

    <div id="Hydrangea">
        <?php $hydraItems = fetch_items_by_types($conn, ['Hydrangea', 'Dahlia', 'Dahliah']); ?>
        <?php if (!empty($hydraItems)): ?>
            <?php foreach ($hydraItems as $it) { echo render_item_card($it, 'Dh'); } ?>
        <?php else: ?>
            <p style="padding:10px;color:#666;">No Hydrangea/Dahlia bouquets yet. Please check back later.</p>
        <?php endif; ?>
    </div>

  
   <?php require "includes/footer.php"; ?>
   <style>
/* Add to Cart and Pay Now button feedback styles */
.add-to-cart-btn.clicked, .pay-now-btn.clicked {
    background: linear-gradient(135deg, #4CAF50 0%, #45a049 100%);
    color: white !important;
    border: none;
    box-shadow: 0 2px 8px rgba(76,175,80,0.2);
    position: relative;
}

/* Remove spinner styles */
.cart-spinner { display: none; }
</style>