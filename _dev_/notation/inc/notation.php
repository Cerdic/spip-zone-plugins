<?php
/**
* Plugin Notation 
* par JEM (jean-marc.viglino@ign.fr) / b_b / Matthieu Marcillaud
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
**/
if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Fonction pour commencer l'affichage de la page dans exec/
 * a virer en passant la conf depuis l'onglet CFG ?
 */
function notation_commencer_page(){

	$commencer_page = charger_fonction("commencer_page","inc");
	echo $commencer_page(_T('notation:notation'), "naviguer", "notation");

	include_spip('inc/autoriser');
	if (!autoriser('configurer')){
		// Pas d'acces
		echo debut_gauche("",true);
		echo debut_droite("",true);
		echo gros_titre("Plugin "._T('notation:notation'), "", false);
		return true;
	}

	// Informations
	echo debut_gauche("",true);	
	echo debut_droite("",true);

	// Afficher les onglets
	echo gros_titre("Plugin "._T('notation:notation'), "", false);

	return true;
}

/**
 * Retourne la configuration de la ponderation (defaut : 30)
 * @return int : valeur de ponderation
 */
function notation_get_ponderation(){
	static $ponderation="";
	if (!$ponderation) {
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
 * @return int : nombre d'etoiles a afficher
 */ 
function notation_get_nb_notes(){
	static $nb = "";
	if (!$nb) {
		$nb = intval(lire_config('notation/nombre', 5));
		if ($nb < 1) $nb = 5;
		if ($nb > 10) $nb = 10;
	}
	return $nb;
}

/**
 *  Calcule de la note ponderee
 * 
 * @param float $note : note moyenne obtenue
 * @param int $nb : nombre de votes 
 * @return int : note ponderee en fonction de la configuration du plugin
 */
function notation_ponderee($note, $nb){
   $note_ponderee = round($note*(1-exp(-5*$nb/notation_get_ponderation()))*100)/100;
   return $note_ponderee;
}


?>
