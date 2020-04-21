<?php

class Item implements JsonSerializable
{
    private $id;
    private $name;
    private $price;
    private $description;
    private $tax;

    public function __construct(int $id, string $name, int $price, string $description)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->tax = 'none';
    }

    public static function getAllItems(): array
    {
        return [
            new self(101, 'First', 150, 'Some cool description'),
            new self(202, 'Second', 300, 'Some cool description 2'),
            new self(303, 'Third', 500, 'Some cool description 3'),
        ];
    }

    public static function getItemById($id): Item
    {
        foreach (self::getAllItems() as $item) {
            if ($item->id === (int) $id) {
                return $item;
            }
        }

        throw new Exception('Item not founded!');
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price,
            'description' => $this->description,
        ];
    }

    public static function toBuy(array $bag)
    {
        $itemsToBuy = [];

        foreach ($bag as $itemId) {
            if (array_key_exists($itemId, $itemsToBuy)) {
                $itemData = $itemsToBuy[$itemId];
                $itemData['Quantity']++;
                $itemData['Amount'] = $itemData['Price'] * $itemData['Quantity'];
                $itemsToBuy[$itemId] = $itemData;
            } else {
                $item = self::getItemById($itemId);

                $itemsToBuy[$item->id] = [
                    'Name' => $item->name,
                    'Quantity' => 1,
                    'Amount' => $item->price * 100,
                    'Price' => $item->price * 100,
                    'Tax' => $item->tax,
                ];
            }
        }

        self::generateRequest(array_values($itemsToBuy));
    }

    private static function generateRequest($items)
    {
        $fields = [
            'TerminalKey'   => 'TestB',
            'Amount'        => array_reduce($items, function ($total, $item) {
                return $total + $item['Amount'];
            }),
            'OrderId'       => random_int(1, PHP_INT_MAX),
            'Description'   => 'Test buy',
            'Receipt'         => [
                'Email' => 'test@test.test',
                'Taxation' => 'osm',
                'Items' => $items
            ],
        ];

        $ch = curl_init('https://securepay.tinkoff.ru/v2/Init');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($ch);
        curl_close($ch);
        print_r ($result);
    }
}