<?php
include_spip('inc/presentation');
function boutique_rechercher_liste_des_champs($tables){
    //$tables['spip_enchere']['id_enchere'] = 3;
    $tables['produit']['nom'] = 3;
    $tables['produit']['texte'] = 3;
    $tables['produit']['descriptif'] = 3;
    ///unset($tables['rubrique']['titre']);

    return $tables;
} 
include_spip('inc/utils');

if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_BOUTIQUE_QUANTITE_dist($p) {
     $p->code = "'<span class=\"simpleCart_quantity\"></span>'";
    return $p;
}

function balise_BOUTIQUE_TOTAL_dist($p) {
     $p->code = "'<span class=\"simpleCart_total\"></span>'";
    return $p;
}

function balise_BOUTIQUE_METHODE_dist($p) {
    $p->code = "recuperer_fond('fonds/checkout_methods')";
    return $p;
}


function balise_BOUTIQUE_FINAL_TOTAL_dist($p) {
     $p->code = "'<span class=\"simpleCart_finalTotal\"></span>'";
    return $p;
}

function balise_BOUTIQUE_CHECKOUT_dist($p) {
     $p->code = "'<a href=\"javascript:;\" class=\"simpleCart_checkout\">". _T('boutique:checkout')."</a>'";
    return $p;
}
function balise_BOUTIQUE_EMPTY_dist($p) {
     $p->code = "'<a href=\"javascript:;\" class=\"simpleCart_empty\">". _T('boutique:empty')."</a>'";
    return $p;
}

function balise_BOUTIQUE_ITEMS_dist($p) {
     $p->code = "'<div class=\"simpleCart_items\"></div>'";
    return $p;
}

function balise_BOUTIQUE_TAX_RATE_dist($p) {
     $p->code = "'<span class=\"simpleCart_taxRate\"></span>'";
    return $p;
}

function balise_BOUTIQUE_TAX_COST_dist($p) {
     $p->code = "'<span class=\"simpleCart_taxCost\"></span>'";
    return $p;
}


function balise_BOUTIQUE_SHIPPING_COST_dist($p) {
     $p->code = "'<span class=\"simpleCart_shippingCost\"></span>'";
    return $p;
}


function balise_SIMPLECART_ADD_dist($p) {
    //just to test
    $p->code = "'<a href=\'javascript:;\' onclick=\"simpleCart.add( \'name=Awesome t-shirt\' , \'price=35.95\' , \'quantity=1\' );\">Add To Cart</a>'";
   return $p;
}
?>
