<?php

session_start();

require_once __DIR__ . '/functions.php';

initialSessionSet();

if (!isset($_SESSION['mixPacks'])) {
    $packNames = [['name' => 'delightful treats', 'category' => 'baked goods'], ['name' => 'sweet sensations', 'category' => 'sweets'], ['name' => 'divine desserts', 'category' => 'desserts'], ['name' => 'refreshing quenchers', 'category' => 'beverages'], ['name' => 'eclectic fusion', 'category' => 'random'], ['name' => 'super deluxe Delights', 'category' => 'all']];
    $packs = [];

    foreach ($packNames as $pack) {
        $categoryItems = ['price' => 0, 'items' => []];
        switch ($pack['category']) {
            case 'random':
                $randomSnacks = $_SESSION['inventory'];
                $categoryItems['items'] = array_slice($randomSnacks, 0, 10, true);
                break;
            case 'all':
                $categoryItems['items'] = $_SESSION['inventory'];
                break;
            default:
                $categoryItems['items'] = array_filter($_SESSION['inventory'], function ($item) use ($pack) {
                    return $item['category'] === $pack['category'];
                });
                if (count($categoryItems['items']) > 5) {
                    $categoryItems['items'] = array_slice($categoryItems['items'], 0, 5, true);
                }
        }
        foreach ($categoryItems['items'] as $item) {
            $categoryItems['price'] += $item['price'];
        }
        $packs[$pack['name']] = $categoryItems;
    }
    $_SESSION['mixPacks'] = $pack;

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
        'wordFilter' => ['filterType' => '', 'items' => []]
    ];
}

if (isset($_GET['pack'])) {
    addPackToCart($_GET['pack']);
}

if (isset($_GET['filter'])) {
    $_SESSION['filter']['min'] = $_GET['min'];
    $_SESSION['filter']['max'] = $_GET['max'];

    if (!empty($_GET['wordFilterInput'])) {
        $word = strtolower(trim(htmlspecialchars($_GET['wordFilterInput'])));
        if (!in_array($word, $_SESSION['filter']['wordFilter']['items']) && array_key_exists($word, $inventory)) {
            $_SESSION['filter']['wordFilter']['items'][] = $word;
        }
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['removeWord'])) {
    $_SESSION['filter']['wordFilter']['items'] = array_filter($_SESSION['filter']['wordFilter']['items'], function ($item) {
        return $item !== $_GET['removeWord'];
    });

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['wordFilterType'])) {
    $_SESSION['filter']['wordFilter']['filterType'] = $_GET['wordFilterType'];

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['clearFilterType'])) {
    $_SESSION['filter']['wordFilter']['filterType'] = '';

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

if (isset($_GET['stock'])) {
    orderNewStock();
}

require_once __DIR__ . '/header.php';
?>
<main>
    <?php require_once __DIR__ . '/navigation.php'; ?>
    <form class="filter-form flex">
        <div class="flex-col">
            <div class="flex">
                <div class="flex">
                    <label for="min">Min</label>
                    <input class="item-name stock-btn" type="number" min="0" max="<?= $_SESSION['highestPrice']; ?>" name="min" value="<?= $_SESSION['filter']['min']; ?>">
                </div>
                <div class="flex">
                    <label for="max">Max</label>
                    <input class="item-name stock-btn" type="number" min="0" max="<?= $_SESSION['highestPrice']; ?>" name="max" value="<?= $_SESSION['filter']['max']; ?>">
                </div>
            </div>
            <button class="item-card-btn blue-container stock-btn center" name="filter" type="submit">Filter</button>
        </div>
        <div class="include-exclude flex-col">
            <div class="flex">
                <input autocomplete="off" class="item-name stock-btn" type="text" name="wordFilterInput" placeholder="Add item">
                <button type="submit" class="item-name stock-btn <?= $_SESSION['filter']['wordFilter']['filterType'] === 'include' ? 'blue-container' : ''; ?>" name="wordFilterType" value="include">Include</button>
                <button type="submit" class="item-name stock-btn <?= $_SESSION['filter']['wordFilter']['filterType'] === 'exclude' ? 'blue-container' : ''; ?>" name="wordFilterType" value="exclude">Exclude</button>
                <button type="submit" name="clearFilterType" class="blue-container item-card-btn w-unset stock-btn"><i class="fa-solid fa-x"></i></button>
            </div>
            <div class="filter-words flex">
                <?php foreach ($_SESSION['filter']['wordFilter']['items'] as $item) : ?>
                    <button type="submit" name="removeWord" value="<?= $item; ?>" class="item-name stock-btn red-hover"><?= ucfirst($item); ?></button>
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
                    <button name="pack" value="<?= $packName; ?>" type="submit" class="item-card-btn blue-container">Add to cart</button>
                </div>
            </div>
        <?php endforeach; ?>
    </form>
</main>
<?php require_once __DIR__ . '/footer.php'; ?>