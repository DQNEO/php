#!/usr/bin/php
<?php
/* array_reduce�λȤ��� */
function _sum($a, $b)
{
    return $a + $b;

}


$x = array(1, 2, 3, 4, 5);

/* ��������Ǥ��פ��� */
echo array_reduce($x, "_sum"). "\n";

/* Ʊ�����Ȥ�̵̾�ؿ��ǹԤ� */
echo array_reduce($x, create_function('$a,$b', 'return $a + $b;')). "\n";

?>

