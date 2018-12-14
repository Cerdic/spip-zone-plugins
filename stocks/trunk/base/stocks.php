<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function stocks_declarer_tables_interfaces($interface) {
	// 'spip_' dans l'index de $tables_principales
	$interface['table_des_tables']['stocks'] = 'stocks';
	$interface['tables_jointures']['spip_produits'][] = 'stocks';

	return $interface;
}

function stocks_declarer_tables_objets_sql($tables) {
	// Déclarer la table stocks
	$tables['spip_stocks'] = array(
		'type' => 'stock',
		'principale' => 'oui',
		'field' => array(
			'id_stock' => 'bigint(21) NOT NULL',
			'id_objet' => 'bigint(21) NOT NULL DEFAULT 0',
			'objet' => 'varchar(255) NOT NULL DEFAULT ""',
			'quantite' => 'bigint(21) NOT NULL',
			'maj' => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_stock',
			'KEY id_objet' => 'id_objet, objet'
		),
		'champs_editables' => array('quantite', 'objet', 'id_objet'),
		'champs_versionnes' => array('quantite', 'objet', 'id_objet')
	);
	// Ajouter un statut epuise aux produits
	array_set_merge($tables, 'spip_produits', array(
			'statut_textes_instituer'=> array(
	      'prepa'    => 'texte_statut_en_cours_redaction',
				'prop'     => 'texte_statut_propose_evaluation',
				'publie'   => 'texte_statut_publie',
				'refuse'   => 'texte_statut_refuse',
				'poubelle' => 'texte_statut_poubelle',
	      'epuise'   => 'stocks:texte_statut_epuise'
			),
      'statut'=> array(
				array(
					'champ'     => 'statut',
					'publie'    => 'publie,epuise',
					'previsu'   => 'publie,prop,prepa,epuise',
					'post_date' => 'date',
					'exception' => array('statut','tout')
				)
			),
      'statut_images' => array(
          'prepa'    => '../images/puce-preparer-8.png',
          'prop'     => '../images/puce-proposer-8.png',
          'publie'   => '../images/puce-publier-8.png',
          'refuse'   => '../images/puce-refuser-8.png',
          'poubelle' => '../images/puce-supprimer-8.png',
          'epuise' 	 => '../images/puce-epuise-8.png',
        )
	));
	return $tables;
}
