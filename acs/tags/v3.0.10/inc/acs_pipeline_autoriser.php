<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2015
# Copyleft: licence GPL - Cf. LICENCES.txt

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Pipeline SPIP autoriser
 */
function acs_autoriser(){}

/**
 * Fonction appelée par le pipeline SPIP "autoriser"
 */
function autoriser_acs_dist($faire, $type, $id, $qui, $opt) {
	// Si l'utilisateur n'est pas identifie, pas la peine d'aller plus loin
	if (!isset($qui['statut'])) return false;

	$admin = ($qui['statut'] === '0minirezo');
	$webmestre = ($qui['webmestre'] === 'oui');
	$ok = ($admin && $webmestre);
	acs_log('autoriser_acs_dist($faire="'.$faire.'", $type="'.$type.'") id_auteur='.$qui['id_auteur'].' ('.$qui['nom'].') webmestre='.$webmestre.' admin='.$admin.' : '.($ok ? 'ok' : 'niet'));
	if ($ok)
		return true;
	return false;
}
?>