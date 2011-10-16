#!/usr/bin/php
<?php
require_once 'OrderManager.class.php';

$order = new Order();
$item_dao = ItemDao::getInstance();

$order->addItem(new OrderItem($item_dao->findById(1),2));
$order->addItem(new OrderItem($item_dao->findById(2),1));
$order->addItem(new OrderItem($item_dao->findById(3),3));

OrderManager::order($order);

