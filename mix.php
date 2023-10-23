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
    This is mix page.
</main>
<?php require_once __DIR__ . '/footer.php'; ?>