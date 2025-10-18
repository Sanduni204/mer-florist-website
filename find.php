<?php require "includes/header.php"; ?>
<?php require "Config/config.php"; ?>

<div class="finder-wrapper">
<div class="search-section">
    <div class="search-container">
        <h2 class="search-title">Find Your Perfect Bouquet</h2>
        <p class="search-subtitle">Discover beautiful flowers for every occasion</p>
        
        <form class="search-form" action="search.php" method="POST">
            <div class="dropdowns-row">
                <!-- Styled dropdown for bouquet type (posts via hidden input 'types') -->
                <div class="search-input-group">
                    <div class="select-wrapper">
                        <div class="custom-select" data-name="types">
                            <button type="button" class="dropdown-btn" aria-haspopup="listbox" aria-expanded="false">
                                <span class="selected-label">Pick a bouquet</span>
                                <span class="dropdown-arrow">▼</span>
                            </button>
                            <div class="dropdown-content" role="listbox">
                                <div class="dropdown-item" data-value="">Pick a bouquet</div>
                                <div class="dropdown-item" data-value="Rose Bouquets">Rose Bouquets</div>
                                <div class="dropdown-item" data-value="Lily Bouquets">Lily Bouquets</div>
                                <div class="dropdown-item" data-value="Daisy Bouquets">Daisy Bouquets</div>
                                <div class="dropdown-item" data-value="Tulip Bouquets">Tulip Bouquets</div>
                                <div class="dropdown-item" data-value="Sunflower Bouquets">Sunflower Bouquets</div>
                                <div class="dropdown-item" data-value="Hydrangea Bouquets">Hydrangea Bouquets</div>
                            </div>
                        </div>
                        <!-- Hidden input that will be submitted with the form -->
                        <input type="hidden" name="types" value="">
                    </div>
                </div>

                <!-- Styled dropdown for color theme (posts via hidden input 'color_theme') -->
                <div class="search-input-group">
                    <div class="select-wrapper">
                        <div class="custom-select" data-name="color_theme">
                            <button type="button" class="dropdown-btn" aria-haspopup="listbox" aria-expanded="false">
                                <span class="selected-label">Pick a color</span>
                                <span class="dropdown-arrow">▼</span>
                            </button>
                            <div class="dropdown-content" role="listbox">
                                <div class="dropdown-item" data-value="">Pick a color</div>
                                <div class="dropdown-item" data-value="Red">Red</div>
                                <div class="dropdown-item" data-value="White">White</div>
                                <div class="dropdown-item" data-value="Yellow">Yellow</div>
                                <div class="dropdown-item" data-value="Blue">Blue</div>
                                <div class="dropdown-item" data-value="Orange">Orange</div>
                                <div class="dropdown-item" data-value="Pink">Pink</div>
                                <div class="dropdown-item" data-value="Purple">Purple</div>
                                <div class="dropdown-item" data-value="Mix">Mix</div>
                            </div>
                        </div>
                        <!-- Hidden input that will be submitted with the form -->
                        <input type="hidden" name="color_theme" value="">
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
