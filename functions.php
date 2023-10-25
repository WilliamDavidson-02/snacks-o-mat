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
