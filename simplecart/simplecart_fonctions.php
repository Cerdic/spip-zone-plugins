<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_SIMPLECART_QUANTITY_dist($p) {
    
     $p->code = "<span class="simpleCart_quantity"></span>";
    return $p;
}

function balise_SIMPLECART_ADD_dist($p) {
    $p->code = "'<a href=\"javascript:;\" onclick=\"simpleCart.add( 'name=Awesome t-shirt' , 'price=35.95' , 'quantity=1' );\">Add To Cart</a>'";
   return $p
}


<?php

function balise_HELLO_WORLD($p) {
    $p->code = "'Hello World!'";
    return $p;
}

?>



?>


