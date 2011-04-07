<?php
/**
 * SPIPmotion
 * Gestion de l'encodage et des métadonnées de vidéos et son directement dans spip
 *
 * Auteurs :
 * Quentin Drouet (kent1)
 *
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Par défaut tous les jours
 *
 * Réalise plusieurs actions :
 * -* vérifie que la configuration n'est pas cassée (en activant la notification)
 * -* vérifie s'il y a des encodages en erreur et notifie les admins dans ce cas
 * -* recharge les informations relatives à ffmpeg et les mets en mémoire
 *
 * @return
 * @param object $time
 */
function genie_spipmotion_taches_generales($time){
	$verifier_binaires = charger_fonction('spipmotion_verifier_binaires','inc');
	$verifier_binaires('',true);
	
	$verifier_erreurs_encodages = charger_fonction('spipmotion_erreurs_encodages','inc');
	$verifier_erreurs_encodages();

	$ffmpeg_infos = charger_fonction('ffmpeg_infos','inc');
	$ffmpeg_infos(true);
	
	return 1;
}
?>