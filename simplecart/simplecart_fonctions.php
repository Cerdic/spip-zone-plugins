<?php
include_spip('inc/utils');

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_SIMPLECART_QUANTITY_dist($p) {
     $p->code = "'<span class=\"simpleCart_quantity\"></span>'";
    return $p;
}

function balise_SIMPLECART_TOTAL_dist($p) {
     $p->code = "'<span class=\"simpleCart_total\"></span>'";
    return $p;
}


function balise_SIMPLECART_CHECKOUT_dist($p) {
     $p->code = "'<a href=\"javascript:;\" class=\"simpleCart_checkout\">". _T('simplecart:checkout')."</a>'";
    return $p;
}
function balise_SIMPLECART_EMPTY_dist($p) {
     $p->code = "'<a href=\"javascript:;\" class=\"simpleCart_empty\">". _T('simplecart:empty')."</a>'";
    return $p;
}

function balise_SIMPLECART_ITEMS_dist($p) {
     $p->code = "'<div class=\"simpleCart_items\"></div>'";
    return $p;
}

function balise_SIMPLECART_TAX_RATE_dist($p) {
     $p->code = "'<div class=\"simpleCart_taxRate\"></div>'";
    return $p;
}

function balise_SIMPLECART_TAX_COST_dist($p) {
     $p->code = "'<div class=\"simpleCart_taxCost\"></div>'";
    return $p;
}



function balise_SIMPLECART_ADD_dist($p) {
    //just to test
    $p->code = "'<a href=\'javascript:;\' onclick=\"simpleCart.add( \'name=Awesome t-shirt\' , \'price=35.95\' , \'quantity=1\' );\">Add To Cart</a>'";
   return $p;
}




?>

