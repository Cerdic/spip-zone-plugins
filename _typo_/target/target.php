<?php

/*
 * Target
 *
 * Ouvre tous les liens de class spip_out dans une nouvelle fenêtre
 *
 * Auteur : francois.vachon@iago.ca
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */
	function target_affichage_final($texte) {
		$texte = str_replace('spip_out', 'spip_out" target="_blank"', $texte);
		return $texte;
	}
	
?>