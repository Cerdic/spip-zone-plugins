<?php
/**
 * Plugin Catalogue pour Spip 2.0
 *   (c) 2010 - Bernard Blazin
 */
function boutique_objets_extensibles($objets){
		return array_merge($objets, array('produits' => _T('produit:produits')));
}



?>