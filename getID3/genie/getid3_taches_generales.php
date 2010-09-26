<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores directement dans SPIP
 *
 * Auteurs :
 * Quentin Drouet (kent1), BoOz
 * 2008-2010 - Distribué sous licence GNU/GPL
 *
 */

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Par défaut tous les jours
 *
 * -* vérifie que la configuration n'est pas cassée (en activant la notification)
 *
 * @return
 * @param object $time
 */
function genie_spipmotion_taches_generales($time){
	$verifier_binaires = charger_fonction('getid3_verifier_binaires','inc');
	$verifier_binaires(true);

	return 1;
}
?>