#!/usr/bin/php
<?php
require "../lime.php";
$t = new lime_test(null, new lime_output_color());
/* レコードとカラムで構成される２次元配列から、特定のカラムだけを抽出するアルゴリズム */

$people = array(
                array(
                      'name' => 'doraemon',
                      'age' => 99,
                      ),
                array(
                      'name' => 'nobita',
                      'age' => 12,
                      ),
                array(
                      'name' => 'shizuka',
                      'age' => 12,
                      ),
                );

$names = getColFromRows($people,'name');
$t->is_deeply($names , array('doraemon', 'nobita', 'shizuka'), 'get_col');

function getColFromRows($rows, $colname)
{
    return  array_map(create_function('$row', 'return $row['.$colname.'];'), $rows);
}

