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

function generateMixPacks(array $inventory): array
{

    $packNames = [['name' => 'delightful treats', 'category' => 'baked goods'], ['name' => 'sweet sensations', 'category' => 'sweets'], ['name' => 'divine desserts', 'category' => 'desserts'], ['name' => 'refreshing quenchers', 'category' => 'beverages'], ['name' => 'eclectic fusion', 'category' => 'random'], ['name' => 'super deluxe Delights', 'category' => 'all']];
    $packs = [];

    foreach ($packNames as $pack) {
        $categoryItems = ['price' => 0, 'items' => []];
        switch ($pack['category']) {
            case 'random':
                $randomSnacks = $inventory;
                $categoryItems['items'] = array_slice($randomSnacks, 0, 10, true);
                break;
            case 'all':
                $categoryItems['items'] = $inventory;
                break;
            default:
                $categoryItems['items'] = array_filter($inventory, function ($item) use ($pack) {
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

    return $packs;
}

function sanitizeInput(string $input): string
{
    return strtolower(trim(htmlspecialchars($input)));
}
