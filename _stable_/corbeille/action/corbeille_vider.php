<?php
/**
 * Plugin Corbeille 2.0
 * La corbeille pour Spip 2.0
 * Collectif
 * Licence GPL
 */

/**
 * Parametre de configuration de la corbeille.
 * 
 * "nom de l'objet spip" => array ("statut" => nom du statut dans la base de données (bdd),
 * 									"titre" => nom du champ retourné dans le listing,
 * 									"table" => nom de la table spip dans la bdd,
 * 									"id" => clef primaire dans la table,
 * 									"temps" => aucune idée à quoi ça peut servir,
 * 									"page_voir" => parametres pour voir le détail d'un objet
 * 									"libelle" => texte long dans la partie droite de l'affichage,
 * 									"libelle_court" => texte court dans le menu gauche,
 * 									"tablelie"  => tableau des tables spip à vider en meme temps    )  
 * 
 * @param string $table
 * @return array
 */
function corbeille_table_infos($table){
	$corbeille_param = array (
	"articles"=>	 	array(	"statut" => "poubelle",
								"tableliee"=> array("spip_auteurs_articles","spip_documents_liens","spip_mots_articles","spip_signatures","spip_versions","spip_versions_fragments","spip_forum"),
								"temps" => "date",
								"libelle" => _T("corbeille:articles_tous"),
								"libelle_court" => _T('icone_articles')
								),
	"auteurs" =>		array(	"statut" => "5poubelle",
								"temps" => "maj",
								"libelle" => _T("corbeille:auteurs_tous"),
								"libelle_court" => _T('icone_auteurs')
								),					
	"breves"=>	 		array(	"statut" => "refuse", 
								"temps" => "date_heure",
								"libelle" => _T("corbeille:breves_toutes"),
								"libelle_court" => _T('icone_breves')
								),
	"forums_publics"=>	array(	"statut" => "off",
								"table"=>"forum",
								"temps" => "date_heure",
								"libelle" => _T("corbeille:messages_tous_pub"),
								"libelle_court" => _T('titre_forum')
								),
	"forums_prives"=>	array(	"statut" => "privoff",
								"table"=>"forum",
								"temps" => "date_heure",
								"libelle" => _T("corbeille:messages_tous_pri"),
								"libelle_court" => _T('icone_forum_administrateur')
								),
	"signatures"=> 		array(	"statut" => "poubelle", 
								"temps" => "date_time",
								"page_voir" => array("signatures",'id_document'),
								"libelle" => _T("corbeille:petitions_toutes"),
								"libelle_court" => strtolower(_T('lien_petitions')),
								),
	"sites" =>			array(	"statut" => "refuse",
								"tableliee"=> array("spip_syndic_articles","spip_mots_syndic"),
								"temps" => "maj",
								"page_voir" => array("sites",'id_syndic'),
								"libelle" => _T("corbeille:syndic_tous"),
								"libelle_court" => _T('titre_syndication')
								)	,
	);
	if (isset($corbeille_param[$table]))
		return $corbeille_param[$table];
	return false;
}

/**
 * supprime les elements listes d'un type donne
 *
 * @param nom $table
 * @param tableau $ids
 * @return neant
 */
function corbeille_vider($table, $ids=array()) {
	include_spip('base/abstract_sql');
	$corbeille_param = corbeille_table_infos($table);
	if (isset($corbeille_param['table']))
		$table = $corbeille_param['table'];
	
	$type = objet_type($table);
	$table_sql = table_objet_sql($type);
	$id_table = id_table_objet($type);
	$statut = $corbeille_param[$type_doc]["statut"];
	$titre = $corbeille_param[$type_doc]["titre"];
	$table_liee = $corbeille_param[$type_doc]["tableliee"];
	
	$statut = $corbeille_param['statut'];
	if (!$statut)
		return false;

	//determine les index des elements a supprimer
	if ($ids===-1) {
		//recupere les identifiants des objets  supprimer
		$ids = array_map('reset',sql_allfetsel($id_table,$table_sql,'statut='.sql_quote($statut)));
	}
	else {
		// verifions les ids qui existent vraiment
		$ids = array_map('reset',sql_allfetsel($id_table,$table_sql,sql_in($id_table,$ids).' AND statut='.sql_quote($statut)));
	}
	if (!count($ids))
		return false;
		

	//supprime les elements definis par la liste des index
	sql_delete($table_sql,sql_in($id_table,$ids));
	//suppresion des elements lies
	if ($table_liee=$corbeille_param['tableliee']) {
		$trouver_table = charger_fonction('trouver_table','base');
		foreach($table_liee as $unetable) {
			$desc = $trouver_table($unetable);
			if (isset($desc['fields'][$id_table]))
				sql_delete($unetable,sql_in($id_table,$ids));
			elseif(isset($desc['fields']['id_objet']) AND isset($desc['fields']['objet']))
				sql_delete($unetable,sql_in('id_objet',$ids)." AND objet=".sql_quote($type));		
		}
	}
	return $ids;
}

?>