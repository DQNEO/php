<?php

require_once 'OrderItem.class.php';
class Order {
    private $items;

    public function __construct()
    {
        $this->item = array();
    }

    public function addItem(OrderItem $order_item)
    {
        if ($order_item->getItem() === null) {
            throw new RuntimeException('invalid argument');
        }


        $this->items[$order_item->getItem()->getId()] = $order_item;
    }

    public function getItems()
    {
        return $this->items;
    }
}