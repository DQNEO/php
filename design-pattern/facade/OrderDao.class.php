<?php

require_once 'Order.class.php';

class OrderDao {
    public static function createOrder(Order $order)
    {
        echo "注文完了しました。\n";
        echo "商品番号\t商品名\t単価\t数量\t金額\n";

        foreach ($order->getItems() as $order_item) {
            echo $order_item->getItem()->getId() . "\t";
            echo $order_item->getItem()->getName() . "\t";
            echo $order_item->getItem()->getPrice() . "\t";
            echo $order_item->getAmount() . "\t";
            echo ($order_item->getItem()->getPrice() * $order_item->getAmount());
            echo "\n";
        }

    }
}
