<?php
/*
 * Plugin FBLogin / gestion du login FB
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 * Charger la librairie Facebook en fonction de la version de php
 *
 */

if (version_compare(phpversion(),'5','<'))
	include_spip('inc/php4client/facebook');
else 
	include_spip('inc/client/facebook');

?>