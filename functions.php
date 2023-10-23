<?php

declare(strict_types=1);

function generatePriceAndInventory(array $inventory): array
{
    return array_map(function ($item) {
        $item['price'] = rand(1, 20);
        $item['stock'] = rand(10, 100);
        return $item;
    }, $inventory);
}
