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
    // Prefer id DESC if exists; fallback to name
    $sql = "SELECT id, fid, name, type, color_theme, price, image, description FROM shop WHERE $where ORDER BY id DESC";
    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        // Fallback without id ordering (in case schema differs)
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
    return '<a href="1payment.html"><div class="'. $cardClass .'">'
         . $imgTag
         . '<p>'. $name .'</p>'
         . '<p>RS.'. $price .'</p>'
         . ($desc !== '' ? '<p><b>'. $desc .'</b></p>' : '')
         . '</div></a>';
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