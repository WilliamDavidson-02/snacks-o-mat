<?php

session_start();

require_once __DIR__ . '/functions.php';

if (!isset($_SESSION['inventory'])) {
    $_SESSION['inventory'] = generatePriceAndInventory();
}


if (!isset($_SESSION['mixPacks'])) {
    $_SESSION['mixPacks'] = generateMixPacks($_SESSION['inventory']);

    $highestPrice = 0;
    foreach ($_SESSION['mixPacks'] as $pack) {
        if ($pack['price'] > $highestPrice) {
            $highestPrice = $pack['price'];
        }
    }

    $_SESSION['highestPrice'] = $highestPrice;

    $_SESSION['filter'] = [
        'min' => 0,
        'max' => $highestPrice,
        'wordFilter' => ['filterType' => '', 'items' => []] // filter type, include or exclude.
    ];
}

if (isset($_GET['pack'])) {
    $pack = $_SESSION['mixPacks'][$_GET['pack']];
    if (($_SESSION['wallet'] - $pack['price']) >= 0) {
        $isInStock = true;
        foreach ($pack['items'] as $key => $item) {
            if ($_SESSION['inventory'][$key]['stock'] - 1 < 0) {
                $isInStock = false;
            }
        }
        if ($isInStock) {
            $_SESSION['cart'][] = $_GET['pack'];
            foreach ($_SESSION['inventory'] as $itemName => $item) {
                if (array_key_exists($itemName, $pack['items'])) {
                    $_SESSION['inventory'][$itemName]['stock'] -= 1;
                }
            }
            $_SESSION['wallet'] -= $pack['price'];
        }
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['filter']) || isset($_GET['wordFilterType']) || isset($_GET['clearFilterType']) || isset($_GET['removeWord'])) {
    $_SESSION['filter']['min'] = $_GET['min'];
    $_SESSION['filter']['max'] = $_GET['max'];

    if (!empty($_GET['wordFilterInput'])) {
        $word = sanitizeInput($_GET['wordFilterInput']);
        if (!in_array($word, $_SESSION['filter']['wordFilter']['items']) && array_key_exists($word, $inventory)) {
            $_SESSION['filter']['wordFilter']['items'][] = $word;
        }
    }

    if (isset($_GET['removeWord'])) {
        $_SESSION['filter']['wordFilter']['items'] = array_filter($_SESSION['filter']['wordFilter']['items'], function ($item) {
            return $item !== $_GET['removeWord'];
        });
    }

    if (isset($_GET['wordFilterType'])) {
        $_SESSION['filter']['wordFilter']['filterType'] = $_GET['wordFilterType'];
    } else if (isset($_GET['clearFilterType'])) {
        $_SESSION['filter']['wordFilter']['filterType'] = '';
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

$filteredPacks = array_filter($_SESSION['mixPacks'], function ($pack) {
    return $pack['price'] >= $_SESSION['filter']['min'] && $pack['price'] <= $_SESSION['filter']['max'];
});

if (!empty($_SESSION['filter']['wordFilter']['filterType'])) {
    $filteredPacks = array_filter($filteredPacks, function ($pack) {
        $items = array_keys($pack['items']);
        $filterItems = $_SESSION['filter']['wordFilter']['items'];

        if ($_SESSION['filter']['wordFilter']['filterType'] === 'include') {
            return !empty(array_intersect($items, $filterItems)); // Checks if any of the keys of the pack items is in filterItems.
        } else if ($_SESSION['filter']['wordFilter']['filterType'] === 'exclude') {
            return empty(array_intersect($items, $filterItems)); // Checks if any of the keys of the pack items is in filterItems.
        }

        return false;
    });
}

require_once __DIR__ . '/header.php';
?>
<main>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <form class="filter-form flex">
        <div class="flex-col">
            <div class="flex">
                <div>
                    <label for="min">Min</label>
                    <input class="item-name" type="number" min="0" max="<?= $_SESSION['highestPrice']; ?>" name="min" value="<?= $_SESSION['filter']['min']; ?>">
                </div>
                <div>
                    <label for="max">Max</label>
                    <input class="item-name" type="number" min="0" max="<?= $_SESSION['highestPrice']; ?>" name="max" value="<?= $_SESSION['filter']['max']; ?>">
                </div>
            </div>
            <button class="item-card-btn green-container" name="filter" type="submit">Filter</button>
        </div>
        <div class="include-exclude flex-col">
            <div class="flex">
                <input autocomplete="off" class="item-name" type="text" name="wordFilterInput" placeholder="Add item">
                <button type="submit" class="item-name <?= $_SESSION['filter']['wordFilter']['filterType'] === 'include' ? 'green-container' : ''; ?>" name="wordFilterType" value="include">Include</button>
                <button type="submit" class="item-name <?= $_SESSION['filter']['wordFilter']['filterType'] === 'exclude' ? 'green-container' : ''; ?>" name="wordFilterType" value="exclude">Exclude</button>
                <button type="submit" name="clearFilterType" class="green-container item-card-btn w-unset"><i class="fa-solid fa-x"></i></button>
            </div>
            <div class="filter-words flex">
                <?php foreach ($_SESSION['filter']['wordFilter']['items'] as $item) : ?>
                    <button type="submit" name="removeWord" value="<?= $item; ?>" class="item-name"><?= ucfirst($item); ?></button>
                <?php endforeach; ?>
            </div>
        </div>
    </form>
    <form class="item-card-container">
        <?php foreach ($filteredPacks as $packName => $pack) : ?>
            <div class="item-card">
                <div class="item-title-container">
                    <h3><?= ucfirst($packName); ?></h3>
                </div>
                <div class="item-content">
                    <div class="items-words-container">
                        <?php foreach ($pack['items'] as $itemName => $item) : ?>
                            <div class="item-name"><?= ucfirst($itemName); ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="price">$<?= $pack['price']; ?></div>
                    <button name="pack" value="<?= $packName; ?>" type="submit" class="item-card-btn green-container">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>