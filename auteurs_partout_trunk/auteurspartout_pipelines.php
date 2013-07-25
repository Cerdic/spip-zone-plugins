<?php
/**
 * Plugin auteurs partout
 * (c) 2012 cy_altern
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function auteurspartout_post_insertion($flux) {
	$Tinfos = lister_tables_objets_sql();	// tous les objets OK pour un auteur (SPIP + plugins)
	$Tcfg = explode(',',$GLOBALS['meta']['auteurs_objets']);	// recup config du plugin
	
	if ($table = $flux['args']['table']
		AND in_array($table, $Tcfg)
		AND array_key_exists($table, $Tinfos)
		AND ($Tinfos[$table]['editable'] == 'oui')
		AND ($id = intval($flux['args']['id_objet']))
		AND $type = $Tinfos[$table]['type']
		) {
        sql_insertq("spip_auteurs_liens", array(
            'id_auteur' => $GLOBALS['auteur_session']['id_auteur'],
            'id_objet' => $id,
            'objet' => $type));
    }

    return $flux;
}

function auteurspartout_affiche_milieu($flux){
	// ajouter la box de config dans la page de config des contenus de SPIP 
	if ($flux["args"]["exec"] == "configurer_contenu") {
		$flux["data"] .=  recuperer_fond('prive/squelettes/inclure/configurer',array('configurer'=>'configurer_auteurspartout'));
	}
	
	// si on est sur une page ou il faut inserer les auteurs...
	$Tcfg = explode(',',$GLOBALS['meta']['auteurs_objets']);	// recup config du plugin
	if ($en_cours = trouver_objet_exec($flux['args']['exec'])
		AND in_array($en_cours['table_objet_sql'], $Tcfg)
		AND $en_cours['edition']!==true // page visu
		AND $type = $en_cours['type']
		AND $id_table_objet = $en_cours['id_table_objet']
		AND ($id = intval($flux['args'][$id_table_objet]))
		) {
		$texte = recuperer_fond(
				'prive/objets/editer/liens',
				array(
					'table_source'=>'auteurs',
					'objet'=>$type,
					'id_objet'=>$id,
				)
		);

		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}

?>
