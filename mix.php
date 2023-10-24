<?php

session_start();

require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['mixPacks'])) {
    $_SESSION['mixPacks'] = generateMixPacks();
}

require_once __DIR__ . '/header.php';
?>
<main>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <form class="item-card-container">
        <?php foreach ($_SESSION['mixPacks'] as $packName => $pack) : ?>
            <div class="item-card">
                <div class="item-title-container">
                    <h3><?= ucfirst($packName); ?></h3>
                </div>
                <div class="item-content">
                    <div class="item-icon mix-icon-container">
                        <?php
                        $keys = array_keys($pack);
                        $iconLength = count($pack) <= 4 ? count($pack) : 4;
                        for ($i = 0; $i < $iconLength; $i++) : ?>
                            <div class="mix-icon"><?= $pack[$keys[$i]]['icon']; ?></div>
                        <?php endfor; ?>
                    </div>
                    <div class="item-info-container"></div>
                    <button name="pack" value="<?= $packName; ?>" type="submit" class="item-card-btn">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>