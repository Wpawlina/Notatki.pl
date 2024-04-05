<?php

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors','1');


function dump($data):void
{
    echo '<br/><div style="
       background: lightgray;
       padding: 0 10 px;
       border: 1px dashed gray;
       display: inline-block;
       min-height:25px;
       min-width:25px;
           "> <pre>';
    print_r($data);
    echo '</pre></div><br/>';
}


?>