<?php
/*
 * Extension Mapstraction pour GMap
 *
 * Auteur : Fabrice ALBERT
 * (c) 2011 - licence GNU/GPL
 *
 */
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajout de l'implémentation Mapstraction
function gmapmxn_declare_implementation($apis)
{
	$apis['mxn'] = array( 'name' => _T('gmapmxn:api_mapstraction'), 'explic' => _T('gmapmxn:api_mapstraction_desc'));
	return $apis;
}

?>