#!/usr/bin/php
<?php
require_once './lime/lime.php';
$t = new lime_test(null, new lime_output_color());

require_once 'Table.class.php';

$t->diag('Test list of array');

$arys = array(
    array(
        'id' => 1,
        'name' => 'Hokkaido',
        ),
    array(
        'id' => 2,
        'name' => 'Aomori',
        ),
    array(
        'id' => 3,
        'name' => 'Iwate',
        ),
    );


$names = Table::getCol($arys, 'name');
$t->is_deeply($names , array('Hokkaido', 'Aomori', 'Iwate'), 'names');

$ids = Table::getCol($arys, 'id');
$t->is_deeply($ids , array(1, 2, 3), 'ids');

$false = Table::getCol($arys,'no_exist_key');
$t->is_deeply($false , false , 'bad colname');

$t->diag('Test list of obj');

$objs = array(
    (object)array(
        'id' => 12,
        'name' => 'Chiba',
        ),
    (object)array(
        'id' => 13,
        'name' => 'Tokyo',
        ),
    (object)array(
        'id' => 14,
        'name' => 'Kanagawa',
        ),
    );


$names = Table::getCol($objs, 'name');
$t->is_deeply($names , array('Chiba', 'Tokyo', 'Kanagawa'), 'names');

$ids = Table::getCol($objs, 'id');
$t->is_deeply($ids , array(12, 13, 14), 'ids');

$false = Table::getCol($objs,'no_exist_key');
$t->is_deeply($false , false , 'bad colname');

$t->is(Table::max($objs,'id'), 14, 'max');

