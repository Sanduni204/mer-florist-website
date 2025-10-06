<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>

<div class="finder-wrapper">
<div class="search-section">
    <div class="search-container">
        <h2 class="search-title">Find Your Perfect Bouquet</h2>
        <p class="search-subtitle">Discover beautiful flowers for every occasion</p>
        
        <form class="search-form" action="search.php" method="POST">
            <div class="dropdowns-row">
                <div class="search-input-group">
                    <div class="select-wrapper">
                        <select name="types" class="category-select">
                            <option value="">Pick a bouquet</option>
                            <option value="Rose Bouquets">Rose Bouquets</option>
                            <option value="Lily Bouquets">Lily Bouquets</option>
                            <option value="Daisy Bouquets">Daisy Bouquets</option>
                            <option value="Tulip Bouquets">Tulip Bouquets</option>
                            <option value="Sunflower Bouquets">Sunflower Bouquets</option>
                            <option value="Hydrangea Bouquets">Hydrangea Bouquets</option>
                        </select>
                        <span class="select-arrow">▼</span>
                    </div>
                </div>

                <div class="search-input-group">
                    <div class="select-wrapper">
                        <select name="color_theme" class="category-select">
                            <option value="">Pick a color</option>
                            <option value="Red">Red</option>
                            <option value="White">White</option>
                            <option value="Yellow">Yellow</option>
                            <option value="Blue">Blue</option>
                            <option value="Orange">Orange</option>
                            <option value="Pink">Pink</option>
                            <option value="Purple">Purple</option>
                            <option value="Mix">Mix</option>
                        </select>
                        <span class="select-arrow">▼</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="search-btn" name="submit">
                <i class="fas fa-search"></i>
                Search
            </button>
        </form>
    </div>
    </div>
</div>

<?php require "includes/footer.php"; ?>
