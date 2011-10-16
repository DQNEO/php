#!/usr/bin/php
<?php
/**
 * unpackの使い方
 *
 * 日本語文字列をバイナリダンプする
 * 
 */
$string = "あい";
$bin = toBin($string);
echo $string ."\n";
echo $bin . "\n";

function toBin($str)
{
    $ary  = unpack('H*', $str);
    return $ary[1];
}