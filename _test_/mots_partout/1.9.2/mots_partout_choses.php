<?php
/***********************************************************************/
/* Définition des choses sur lesquels on peut vouloir mettre des mots clefs*/
/***********************************************************************/

//==========================ARTICLES============================================
global $choses_possibles;

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