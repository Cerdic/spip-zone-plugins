<?php
/**
 * GetID3
 * Gestion des métadonnées de fichiers sonores et vidéos directement dans SPIP
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info), BoOz
 * 2008-2013 - Distribué sous licence GNU/GPL
 *
 * @package SPIP\GetID3\Crons
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Fonction appelée par le génie de SPIP à intervalle régulier
 * Par défaut tous les jours
 *
 * -* vérifie que la configuration n'est pas cassée (en activant la notification)
 *
 * @return
 * @param object $time
 */
function genie_getid3_taches_generales($time){
	$verifier_binaires = charger_fonction('getid3_verifier_binaires','inc');
	$verifier_binaires(true);

	return 1;
}
?>