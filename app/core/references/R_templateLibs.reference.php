<?php
    $serve = dirname(_APP_,3);
    $path = $serve . "/smarty/Smarty.class.php";
    //require('/usr/local/lib/php/Smarty/Smarty.class.php');
    require $path;
    $displayer = new Smarty;
    $displayer->setCompileDir(_APP_ . "/compiles/"); 
    $displayer->setCacheDir(_APP_ . "/cache/"); 
    $displayer->left_delimiter = '<{/'; 
    $displayer->right_delimiter = '\}>';
?>