<?php
include_spip('inc/utils');

if (!defined("_ECRIRE_INC_VERSION")) return;

function iso_devise($devise){
	  switch($devise){
            case CHF:
                return "CHF&nbsp;";
            case CZK:
                return "CZK&nbsp;";
            case DKK:
                return "DKK&nbsp;";
            case HUF:
                return "HUF&nbsp;";
            case NOK:
                return "NOK&nbsp;";
            case PLN:
                return "PLN&nbsp;";
            case SEK:
                return "SEK&nbsp;";
            case JPY:
                return "&yen;";
            case EUR:
                return "&euro;";
            case GBP:
                return "&pound;";
            case USD:
            	 return "$";
            case SGD:
                return "&#36;";
            default:
                return "";
          }
}

function balise_SIMPLECART_QUANTITY_dist($p) {
     $p->code = "'<span class=\"simpleCart_quantity\"></span>'";
    return $p;
}

function balise_SIMPLECART_TOTAL_dist($p) {
     $p->code = "'<span class=\"simpleCart_total\"></span>'";
    return $p;
}

function balise_SIMPLECART_METHODS_dist($p) {
    $p->code = "recuperer_fond('fonds/checkout_methods')";
    return $p;
}


function balise_SIMPLECART_FINAL_TOTAL_dist($p) {
     $p->code = "'<span class=\"simpleCart_finalTotal\"></span>'";
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
     $p->code = "'<span class=\"simpleCart_taxRate\"></span>'";
    return $p;
}

function balise_SIMPLECART_TAX_COST_dist($p) {
     $p->code = "'<span class=\"simpleCart_taxCost\"></span>'";
    return $p;
}


function balise_SIMPLECART_SHIPPING_COST_dist($p) {
     $p->code = "'<span class=\"simpleCart_shippingCost\"></span>'";
    return $p;
}


function balise_SIMPLECART_ADD_dist($p) {
    //just to test
    $p->code = "'<a href=\'javascript:;\' onclick=\"simpleCart.add( \'name=Awesome t-shirt\' , \'price=35.95\' , \'quantity=1\' );\">Add To Cart</a>'";
   return $p;
}




?>
