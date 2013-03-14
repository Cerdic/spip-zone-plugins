<?php
/**
 * Plugin Simple Calendrier v2 pour SPIP 3.0
 * Licence GNU/GPL
 * 2010-2012
 *
 * cf. paquet.xml pour plus d'infos.
 */

// --------------------------------------
//  COMPATIBILITE PLUGIN ACCES RESTREINT
// --------------------------------------

/**
 * Critere {tout_voir} permet de deverouiller l'acces restreint sur une boucle
 *
 * @param unknown_type $idb
 * @param unknown_type $boucles
 * @param unknown_type $crit
 */
if (!function_exists('critere_tout_voir_dist')){
function critere_tout_voir_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$boucle->modificateur['tout_voir'] = true;
}
}
function simplecal_pre_boucle(&$boucle){
	if (!isset($boucle->modificateur['tout_voir'])){
		$securise = false;
		
		// ------------------------------------------
		// si le plugin Acces restreint est actif 
		// (= Test dispo de la fonction 'accesrestreint_rubriques_accessibles_where')
		// ------------------------------------------
		if (defined('_DIR_PLUGIN_ACCESRESTREINT')){
			switch ($boucle->type_requete){
				case 'evenements':
					$t = $boucle->id_table . '.id_rubrique';
					$boucle->select = array_merge($boucle->select, array($t)); // pour postgres
					
					// ajoute un "AND ((evenements.id_rubrique NOT IN (id1, id2, id3, etc.)))"
					$boucle->where[] = accesrestreint_rubriques_accessibles_where($t);
					$securise = true;
					break;
			}
		}
		if ($securise){
			$boucle->hash .= "if (!defined('_DIR_PLUGIN_ACCESRESTREINT')){
			\$link_empty = generer_url_ecrire('admin_vider'); \$link_plugin = generer_url_ecrire('admin_plugin');
			\$message_fr = 'La restriction d\'acc&egrave;s a ete desactiv&eacute;e. <a href=\"'.\$link_plugin.'\">Corriger le probl&egrave;me</a> ou <a href=\"'.\$link_empty.'\">vider le cache</a> pour supprimer les restrictions.';
			\$message_en = 'Acces Restriction is now unusable. <a href=\"'.\$link_plugin.'\">Correct this trouble</a> or <a href=\"'.generer_url_ecrire('admin_vider').'\">empty the cache</a> to finish restriction removal.';
			die(\$message_fr.'<br />'.\$message_en);
			}";
		}
	}
	return $boucle;
}



?>