<?php
/**
 * Plugin Simple Calendrier pour Spip 2.1.2
 * Licence GPL (c) 2010-2011 Julien Lanfrey
 *
 */

//
// <BOUCLE(EVENEMENTS)>
//
function boucle_EVENEMENTS_dist($id_boucle, &$boucles) {
    $boucle = &$boucles[$id_boucle];
    $id_table = $boucle->id_table;
	$mstatut = $id_table.'.statut';
    
    if (!isset($boucle->modificateur['criteres']['statut'])) {
        // On n'est pas en mode pré-visualisation
        if (!$GLOBALS['var_preview']) {
            // Le critère {tout} est absent de la boucle
            if (!isset($boucle->modificateur['tout'])) {
                // on rajoute la condition "statut = publie"
                array_unshift($boucle->where, array("'='", "'$mstatut'", "'\\'publie\\''"));
            }
        } 
        
        // On est en mode pré-visualisation
        else {
            array_unshift($boucle->where,array("'IN'", "'$mstatut'", "'(\\'publie\\',\\'prop\\')'"));
        }
    }
    return calculer_boucle($id_boucle, $boucles); 
}

// Notes :
// - Pré-visualisation = prévisu autorisée dans la config + url suffixée par "&var_mode=preview"
// - {tout} => permet de ne pas appliquer de filtre de statut


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


// -----------------------------------------
// Surcharge de ecrire/public/boucles.php : 
// Prise en compte de l'objet 'evenement'
// -----------------------------------------
//
// <BOUCLE(DOCUMENTS)>
//
// http://doc.spip.org/@boucle_DOCUMENTS_dist
function boucle_DOCUMENTS($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;

	// on ne veut pas des fichiers de taille nulle,
	// sauf s'ils sont distants (taille inconnue)
	array_unshift($boucle->where,array("'($id_table.taille > 0 OR $id_table.distant=\\'oui\\')'"));

	// Supprimer les vignettes
	if (!isset($boucle->modificateur['criteres']['mode'])
	AND !isset($boucle->modificateur['criteres']['tout'])) {
		array_unshift($boucle->where,array("'!='", "'$id_table.mode'", "'\\'vignette\\''"));
	}

	// Pour une boucle generique (DOCUMENTS) sans critere de lien, verifier
	// qu notre document est lie a un element publie
	// (le critere {tout} permet de les afficher tous quand meme)
	// S'il y a un critere de lien {id_article} par exemple, on zappe
	// ces complications (et tant pis si la boucle n'a pas prevu de
	// verification du statut de l'article)
	if ((!isset($boucle->modificateur['tout']) OR !$boucle->modificateur['tout'])
	AND (!isset($boucle->modificateur['criteres']['id_objet']) OR !$boucle->modificateur['criteres']['id_objet'])
	) {
		# Espace avant LEFT JOIN indispensable pour insertion de AS
		# a refaire plus proprement

		## la boucle par defaut ignore les documents de forum
		$boucle->from[$id_table] = "spip_documents LEFT JOIN spip_documents_liens AS l
			ON $id_table.id_document=l.id_document
			LEFT JOIN spip_articles AS aa
				ON (l.id_objet=aa.id_article AND l.objet=\'article\')
			LEFT JOIN spip_breves AS bb
				ON (l.id_objet=bb.id_breve AND l.objet=\'breve\')
			LEFT JOIN spip_rubriques AS rr
				ON (l.id_objet=rr.id_rubrique AND l.objet=\'rubrique\')
			LEFT JOIN spip_forum AS ff
				ON (l.id_objet=ff.id_forum AND l.objet=\'forum\')
			LEFT JOIN spip_evenements AS ee
				ON (l.id_objet=ee.id_evenement AND l.objet=\'evenement\')
		";
		$boucle->group[] = "$id_table.id_document";

		if ($GLOBALS['var_preview']) {
			array_unshift($boucle->where,"'(aa.statut IN (\'publie\',\'prop\') OR bb.statut  IN (\'publie\',\'prop\') OR rr.statut IN (\'publie\',\'prive\') OR ff.statut IN (\'publie\',\'prop\') OR ee.statut IN (\'publie\',\'prop\'))'");
		} else {
			$postdates = ($GLOBALS['meta']['post_dates'] == 'non')
				? ' AND aa.date<=\'.sql_quote(quete_date_postdates()).\''
				: '';
			array_unshift($boucle->where,"'((aa.statut = \'publie\'$postdates) OR bb.statut = \'publie\' OR rr.statut = \'publie\' OR ff.statut=\'publie\' OR ee.statut=\'publie\')'");
		}
	}


	return calculer_boucle($id_boucle, $boucles);
}


?>