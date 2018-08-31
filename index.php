<?php
include 'source/MainController.php';
 
function match($t1, $t2){
    $main = new Main();
    return $main->calculate($t1, $t2);
}

?>
