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

function generateMixPacks(): array
{
    global $inventory;

    $packNames = [['name' => 'delightful treats', 'category' => 'baked goods'], ['name' => 'sweet sensations', 'category' => 'sweets'], ['name' => 'divine desserts', 'category' => 'desserts'], ['name' => 'refreshing quenchers', 'category' => 'beverages'], ['name' => 'eclectic fusion', 'category' => 'random'], ['name' => 'super deluxe delights', 'category' => 'all']];
    $packs = [];

    foreach ($packNames as $pack) {
        $categoryItems = [];
        switch ($pack['category']) {
            case 'random':
                $randomSnacks = $inventory;
                shuffle($randomSnacks);
                array_splice($randomSnacks, 10);
                $categoryItems = $randomSnacks;
                break;
            case 'all':
                $categoryItems = $inventory;
                break;
            default:
                $categoryItems = array_filter($inventory, function ($item) use ($pack) {
                    return $item['category'] === $pack['category'];
                });
                if (count($categoryItems) > 5) {
                    shuffle($categoryItems);
                    array_splice($categoryItems, 5);
                }
        }
        $packs[$pack['name']] = $categoryItems;
    }

    return $packs;
}
