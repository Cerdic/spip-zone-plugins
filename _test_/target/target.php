<?php

/*
 * Target
 *
 *	Ouvre tous les liens de class spip_out dans une nouvelle fen�tre.
 *	Ajoute la class spip_out aux liens de type directory des sites syndiqu�s et les ouvrent dans une nouvelle fen�tre.
 *	Ouvre tous les liens de class spip_glossaire (vers wikipedia)  aussi dans une nouvelle fen�tre.
 * Auteur : francois.vachon@iago.ca
 * � 2006 - Distribue sous licence GNU/GPL
 * Derni�re mise � jour: 18 juillet 2006
 */
	function target_affichage_final($texte) {
		$texte = str_replace('spip_out', 'spip_out" target="_blank"', $texte);
		$texte = str_replace('rel="directory"', 'class="spip_out" target="_blank"', $texte);
		$texte = str_replace('spip_glossaire', 'spip_glossaire" target="_blank"', $texte);
		return $texte;
	}
	
?>