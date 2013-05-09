<?php
/**
 * Plugin Notation
 * par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
 *
 * Copyright (c) 2008
 * Logiciel libre distribue sous licence GNU/GPL.
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Retourne la configuration de la ponderation (defaut : 30)
 * 
 * @return int $ponderation
 * 		Valeur de ponderation
 */
function notation_get_ponderation(){
	static $ponderation="";
	if (!$ponderation) {
		include_spip('inc/config'); // lire_config
		$ponderation = lire_config('notation/ponderation',30);
		$ponderation = intval($ponderation);
		if ($ponderation < 1) $ponderation = 1;
	}
	return $ponderation;
}


/**
 * Nombre d'etoile a afficher en fonction de la configuration
 * du plugin. Varie de 1 a 10. Defaut 5.
 * 
 * @return int $nb 
 * 		Nombre d'etoiles a afficher
 */ 
function notation_get_nb_notes(){
	static $nb = "";
	if (!$nb) {
		include_spip('inc/config'); // lire_config
		$nb = intval(lire_config('notation/nombre', 5));
		if ($nb < 1) $nb = 5;
		if ($nb > 10) $nb = 10;
	}
	return $nb;
}

/**
 * Calcule de la note ponderee
 * utilise uniquement pour l'affichage dans la page de configuration
 * (vrai calcul en SQL dans action/editer_notation)
 * 
 * @param float $note
 * 		Note moyenne obtenue
 * @param int $nb 
 * 		Nombre de votes 
 * @return int $note_ponderee 
 * 		Note ponderee en fonction de la configuration du plugin
 */
function notation_ponderee($note, $nb){
   $note_ponderee = round($note*(1-exp(-5*$nb/notation_get_ponderation())),2);
   return $note_ponderee;
}


?>