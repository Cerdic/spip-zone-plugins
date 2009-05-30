<?php


/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Plugin Mots-Partout                                                    *
 *                                                                         *
 *  Copyright (c) 2006-2008                                                *
 *  Pierre ANDREWS, Yoann Nogues, Emmanuel Saint-James                     *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
 *    This program is free software; you can redistribute it and/or modify *
 *    it under the terms of the GNU General Public License as published by * 
 *    the Free Software Foundation.                                        *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

  // 2 petits utilitaires
//force a un tableau de int
if(!function_exists('secureIntArray')){
	function secureIntArray($array) {
	  $to_return = Array();
	  if(is_array($array)) {
		foreach($array as $id) {
		  $to_return[] = intval($id);
		}
	  } 
	  return $to_return;
	}
}

// transfert la variable POST d'un tableau (19 => 'avec', 20=>'voir') en 4 tableaux avec=(19) voir=(20)
function splitArrayIds($array) {
  $voir = Array();
  $cacher = Array();
  $ajouter = Array();
  $enlever = Array();
  if(is_array($array)) {
    foreach($array as $id_mot => $action) {
      $id_mot = intval($id_mot);
      if($id_mot > 0) {
        switch(addslashes($action)) {
		  case 'avec': 
			$ajouter[] = $id_mot;
		  case 'voir':
			$voir[] = $id_mot;
			break;
		  case 'sans':
			$enlever[] = $id_mot;
			break;
		  case 'cacher':
			$cacher[] = $id_mot;
            break; 

        }
      }
    }
  }
  return array($voir, $cacher, $ajouter, $enlever);
}


global $tables_principales;
$tables_principales['spip_mots_documents']['field'] = array(
        "id_mot"    => "BIGINT (21) DEFAULT '0' NOT NULL",
        "id_document"    => "BIGINT (21) DEFAULT '0' NOT NULL");

$tables_principales['spip_mots_documents']['key'] = array(
        "KEY id_mot"    => "id_mot",
        "KEY id_document"    => "id_document");

global $tables_relations;
$tables_relations['mots']['id_document'] = 'mots_documents';
$tables_relations['documents']['id_mot'] = 'mots_documents';

global $choses_possibles,$statuts_possibles;
$statuts_possibles[]='publie';
$statuts_possibles[]='propose';
$statuts_possibles[]='technique';
$statuts_possibles[]='poubelle';

$choses_possibles['articles'] = array(
									  'titre_chose' => 'public:articles',
									  'id_chose' => 'id_article',
									  'table_principale' => 'spip_articles',
									  'table_auth' => 'spip_auteurs_articles',
									  'tables_limite' => array(
															   'articles' => array(
																				   'table' => 'spip_articles',
																				   'nom_id' => 'id_article'),
															   'rubriques' => array(
																					'table' => 'spip_articles',
																					'nom_id' =>  'id_rubrique'),
															   'documents' => array(
																					'table' => 'spip_documents_articles',
																					'nom_id' =>  'id_document'),
															   'auteurs' => array(
																				  'table' => 'spip_auteurs_articles',
																				  'nom_id' => 'id_auteur')
															   )
									  );
									  
$choses_possibles['breves'] = array(
									  'titre_chose' => 'breves',
									  'id_chose' => 'id_breve',
									  'table_principale' => 'spip_breves',
									  'url_base' => 'breves_voir',
									  'tables_limite' => array(
															   'breves' => array(
																				   'table' => 'spip_breves',
																				   'nom_id' => 'id_breve'),
															   'rubriques' => array(
																					'table' => 'spip_breves',
																					'nom_id' =>  'id_rubrique')
															   )
									  );


$choses_possibles['rubriques'] = array(
									  'titre_chose' => 'rubriques',
									  'id_chose' => 'id_rubrique',
									  'table_principale' => 'spip_rubriques',
									  'url_base' => 'naviguer',
									  'table_auth' => 'spip_auteurs_rubriques',
									  'tables_limite' => array(
															   'rubriques' => array(
																					'table' => 'spip_rubriques',
																					'nom_id' =>  'id_rubrique'),
																'secteurs' => array(
																					 'table' => 'spip_rubriques',
																					 'nom_id' =>  'id_secteur'),
																'parents' => array(
																					 'table' => 'spip_rubriques',
																					 'nom_id' =>  'id_parent'),
															   'documents' => array(
																					'table' => 'spip_documents_rubriques',
																					'nom_id' =>  'id_document'),
															   'auteurs' => array(
																				  'table' => 'spip_auteurs_articles',
																				  'nom_id' => 'id_auteur')
															   )
									  );

$choses_possibles['syndic'] = array(
									  'titre_chose' => 'syndic',
									  'id_chose' => 'id_syndic',
//exception : objet (voir inc/mot.php)
									  'objet' => 'syndic',
									  'table_principale' => 'spip_syndic',
									  'tables_limite' => array(
															   'rubrique' => array(
																				  'table' => 'spip_syndic',
																				  'nom_id' => 'id_rubriques'),
															   'secteur' => array(
																				  'table' => 'spip_syndic',
																				  'nom_id' => 'id_secteur')
															   )
									  );
$choses_possibles['syndic_articles'] = array(
									  'titre_chose' => 'Items RSS',
									  'id_chose' => 'id_syndic_article',
									  'table_principale' => 'spip_syndic_articles',
									  'tables_limite' => array(
															   'sites' => array(
																				   'table' => 'spip_syndic',
																				   'nom_id' => 'id_syndic'),
															   )
									  );


/*$choses_possibles['forum'] = array(
									  'titre_chose' => 'forum',
									  'id_chose' => 'id_forum',
									  'table_principale' => 'spip_forum',
									  'tables_limite' => array(
															   'forum' => array(
																				   'table' => 'spip_forum',
																				   'nom_id' => 'id_forum'),
															   'parent' => array(
																				   'table' => 'spip_forum',
																				   'nom_id' => 'id_parent'),
															   'thread' => array(
																				   'table' => 'spip_forum',
																				   'nom_id' => 'id_thread'),
															   'articles' => array(
																				   'table' => 'spip_articles',
																				   'nom_id' => 'id_article'),
															   'breves' => array(
																				   'table' => 'spip_breves',
																				   'nom_id' => 'id_breve'),
															   'rubriques' => array(
																					'table' => 'spip_rubriques',
																					'nom_id' =>  'id_rubrique'),
															   'auteurs' => array(
																				  'table' => 'spip_forum',
																				  'nom_id' => 'id_auteur')
															   )
									  );
*/		  

$choses_possibles['documents'] = array(
									   'titre_chose' => 'info_documents',
									   'id_chose' => 'id_document',
									   'table_principale' => 'spip_documents',
									   'tables_limite' => array(
																'articles' => array(
																					'table' => 'spip_documents_articles',
																					'nom_id' => 'id_article'),
																'rubriques' => array(
																					 'table' => 'spip_documents_rubriques',
																					 'nom_id' =>  'id_rubrique'),
																'documents' => array(
																					 'table' => 'spip_documents',
																					 'nom_id' =>  'id_document')
																)
									   );


$choses_possibles['messages'] = array(
									  'titre_chose' => 'Messages',
									  'id_chose' => 'id_message',
									  'table_principale' => 'spip_messages',
									  'table_auth' => 'spip_auteurs_messages',
									  'tables_limite' => array(
															   'messages' => array(
																				   'table' => 'spip_messages',
																				   'nom_id' => 'id_message'),
															   'auteurs' => array(
																				  'table' => 'spip_auteurs_messages',
																				  'nom_id' => 'id_auteur')
															   )
									  );

$choses_possibles['evenements'] = array(
									  'titre_chose' => 'Evenements',
									  'id_chose' => 'id_evenement',
									  'table_principale' => 'spip_evenements',
									  'tables_limite' => array(
															   'article' => array(
																				   'table' => 'spip_articles',
																				   'nom_id' => 'id_article')
									  							)
									  );


$choses_possibles['auteurs'] = array(
									  'titre_chose' => 'auteurs',
									  'id_chose' => 'id_auteur',
									  'table_principale' => 'spip_auteurs',
									  'tables_limite' => array(
															   'auteurs' => array(
																				   'table' => 'spip_auteurs',
																				   'nom_id' => 'id_auteur'),
															   'articles' => array(
																				  'table' => 'spip_auteurs_articles',
																				  'nom_id' => 'id_auteur')
															   )
									  );


$choses_possibles['groupes_mots'] = array(
									  'titre_chose' => 'groupes_mots',
									  'id_chose' => 'id_groupe',
									  'table_principale' => 'spip_groupes_mots',
									  'tables_limite' => array(
															   'mots' => array(
																				   'table' => 'spip_mots',
																				   'nom_id' => 'id_mot'),
															   )
									  );

//=============================MOTS=========================================
/*
on ne peut pas vraiment mettre de mots sur les mots comme c'est fait maintenant :(

$choses_possibles['mots'] = array(
									  'titre_chose' => 'mots',
									  'id_chose' => 'id_mot2',
									  'table_principale' => 'spip_mots',
									  'tables_limite' => array(
															   'rubriques' => array(
																				   'table' => 'spip_rubriques',
																				   'nom_id' => 'id_rubrique'),
															   'articles' => array(
																				  'table' => 'spip_articles',
																				  'nom_id' => 'id_article')
															   )
									  );

*/
?>