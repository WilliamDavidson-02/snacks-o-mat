<?php

declare(strict_types=1);

require_once __DIR__ . '/inventory.php';

function generatePriceAndInventory(): array
{
    global $inventory;

    return array_map(function ($item) {
        $item['price'] = rand(1, 20);
        $item['stock'] = rand(10, 100);
        return $item;
    }, $inventory);
}

function initialSessionSet()
{
    if (!isset($_SESSION['inventory'])) {
        $_SESSION['inventory'] = generatePriceAndInventory();
    }

    if (!isset($_SESSION['wallet'])) {
        $_SESSION['wallet'] = 9999;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}


function orderNewStock()
{
    $_SESSION['inventory'] = array_map(function ($item) {
        $item['stock'] += rand(10, 100);
        return $item;
    }, $_SESSION['inventory']);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function addItemToCart(string $item)
{
    $snack = $_SESSION['inventory'][$item];
    if (($_SESSION['wallet'] - $snack['price']) >= 0 && $snack['stock'] > 0) {
        $_SESSION['cart'][] = $item;
        $_SESSION['inventory'][$item]['stock'] -= 1;
        $_SESSION['wallet'] -= $snack['price'];
    }
    // preventing same action to run again if page is reloaded.
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

function addPackToCart(string $packName)
{
    $pack = $_SESSION['mixPacks'][$packName];
    if (($_SESSION['wallet'] - $pack['price']) >= 0) {
        $isInStock = true;
        foreach ($pack['items'] as $key => $item) {
            if ($_SESSION['inventory'][$key]['stock'] - 1 < 0) {
                $isInStock = false;
            }
        }
        if ($isInStock) {
            $_SESSION['cart'][] = $packName;
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
