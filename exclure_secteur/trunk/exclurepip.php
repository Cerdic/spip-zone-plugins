<?php
include_spip('inc/exclure_utils');

/** 
 * Filtrer les boucles pour ne pas afficher le ou les secteurs configurés
 */
function exclure_sect_pre_boucle(&$boucle){

	if (
		!empty($boucle->modificateur['tout_voir']) 
		or (!empty($boucle->modificateur['tout']) and lire_config('secteur/tout') == 'oui') 
		or test_espace_prive() == 1 
		or (!empty($boucle->nom) and $boucle->nom == 'calculer_langues_utilisees')
	) {
		return $boucle;
	}

	$type = $boucle->id_table;
	$crit = $boucle->criteres;
	$exclut = exclure_sect_choisir($crit, $type);

	if (in_array($type, array('articles', 'rubriques', 'syndic'))) {
		if ($exclut !='z'){
			$boucle->where[] = "sql_in('$type.id_secteur', '$exclut', 'NOT')";
		}
	}

	if ($type == 'breves'){
		if ($exclut !='z'){
			$boucle->where[] = "sql_in('$type.id_rubrique', '$exclut', 'NOT')";
		}
	}

	if ($type == 'forum'){
		$select_article = "sql_get_select('id_article', 'spip_articles', sql_in('id_secteur', '$exclut'))";
		if ($exclut !='z'){
			$where = array(
				sql_quote('NOT'),
				array(
					sql_quote('AND'),
					"sql_in('forum.objet', sql_quote('article'))",
					"sql_in('id_objet', $select_article)"
				)
			);
		}
	}

	return $boucle;
}
