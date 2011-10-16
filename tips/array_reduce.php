#!/usr/bin/php
<?php
/* array_reduceの使い方 */
function _sum($a, $b)
{
    return $a + $b;

}


$x = array(1, 2, 3, 4, 5);

/* 配列の要素を合計する */
echo array_reduce($x, "_sum"). "\n";

/* 同じことを無名関数で行う */
echo array_reduce($x, create_function('$a,$b', 'return $a + $b;')). "\n";

?>

