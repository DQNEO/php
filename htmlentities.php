#!/usr/bin/php
<?php
$in = file_get_contents("php://stdin");
echo  htmlentities($in);
