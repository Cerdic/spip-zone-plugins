<?php
/*
 * Plugin Slogan
 * (c) 2009 C.Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function configuration_accueil_dist(){
	return recuperer_fond("prive/configurer/identite",array());
}
?>
