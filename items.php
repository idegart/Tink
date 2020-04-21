<?php

require_once 'Item.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['bag'])) {
    Item::toBuy($input['bag']);
} else {
    $items = Item::getAllItems();

    echo json_encode(array_merge(
        ['success' => true,],
        compact('items')
    ));
}