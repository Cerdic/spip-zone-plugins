<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Interfaces des tables feedback pour le compilateur
 *
 * @param array $interfaces
 * @return array
 */
function feedback_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['feedbacks'] = 'feedbacks';

	$interfaces['exceptions_des_tables']['feedbacks']['id_secteur'] = 'id_rubrique';
	$interfaces['exceptions_des_tables']['feedbacks']['date'] = 'date_heure';
	// $interfaces['exceptions_des_tables']['feedbacks']['nom_site'] = 'lien_titre';
	// $interfaces['exceptions_des_tables']['feedbacks']['url_site'] = 'lien_url';

	// $interfaces['table_des_traitements']['LIEN_TITRE'][]= _TRAITEMENT_TYPO;
	// $interfaces['table_des_traitements']['LIEN_URL'][]= 'vider_url(%s)';
	$interfaces['table_des_traitements']['TITLE']['feedbacks'] = "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	$interfaces['table_des_traitements']['TEXTE']['feedbacks'] = "safehtml(".str_replace("%s","interdit_html(%s)",_TRAITEMENT_RACCOURCIS).")";
	
	return $interfaces;
}


function feedback_declarer_tables_objets_sql($tables){
	$tables['spip_feedbacks'] = array(
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'feedback:feedback',
		'texte_objet' => 'feedback:feedback',
		'texte_modifier' => 'feedback:icone_modifier_feedback',
		'texte_creer' => 'feedback:icone_nouveau_feedback',
		'info_aucun_objet'=> 'feedback:info_aucun_feedback',
		'info_1_objet' => 'feedback:info_1_feedback',
		'info_nb_objets' => 'feedback:info_nb_feedback',
		'texte_logo_objet' => 'feedback:logo_feedback',
		'texte_langue_objet' => 'feedback:titre_langue_feedback',
		'titre' => 'titre, lang',
		'date' => 'date_heure',
		'principale' => 'oui',
		'field'=> array(
			"id_feedback"	=> "bigint(21) NOT NULL",
			"date_heure"	=> "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			"titre"	=> "text DEFAULT '' NOT NULL",
			"texte"	=> "longtext DEFAULT '' NOT NULL",
			// "lien_titre"	=> "text DEFAULT '' NOT NULL",
			// "lien_url"	=> "text DEFAULT '' NOT NULL",
			"statut"	=> "varchar(6)  DEFAULT '0' NOT NULL",
			"id_rubrique"	=> "bigint(21) DEFAULT '0' NOT NULL",
			"lang"	=> "VARCHAR(10) DEFAULT '' NOT NULL",
			"langue_choisie"	=> "VARCHAR(3) DEFAULT 'non'",
			"maj"	=> "TIMESTAMP"
		),
		'key' => array(
			"PRIMARY KEY"	=> "id_feedback",
			"KEY id_rubrique"	=> "id_rubrique",
		),
		'join' => array(
			"id_feedback"=>"id_feedback",
			"id_rubrique"=>"id_rubrique"
		),
		'statut' =>  array(
			array(
				'champ'=>'statut',
				'publie'=>'publie',
				'previsu'=>'publie,prop',
				'exception'=>'statut'
			)
		),
		'texte_changer_statut' => 'feedback:entree_feedback_publiee',
		'aide_changer_statut' => 'feedbackstatut',
		'statut_titres' => array(
			'prop' => 'feedback:titre_feedback_proposee',
			'publie' => 'feedback:titre_feedback_publiee',
			'refuse' => 'feedback:titre_feedback_refusee',
		),
		'statut_textes_instituer' => 	array(
			'prop' => 'feedback:item_feedback_proposee', //_T('texte_statut_propose_evaluation')
			'publie' => 'feedback:item_feedback_validee', //_T('texte_statut_publie')
			'refuse' => 'feedback:item_feedback_refusee', //_T('texte_statut_refuse')
		),

		'rechercher_champs' => array(
		  'titre' => 8, 'texte' => 2 /*, 'lien_titre' => 1, 'lien_url' => 1 */
		),
		'rechercher_jointures' => array(
			'document' => array('titre' => 2, 'descriptif' => 1)
		),
		'champs_versionnes' => array('id_rubrique', 'titre'/*, 'lien_titre', 'lien_url'*/, 'texte'),
	);

	return $tables;
}


?>
