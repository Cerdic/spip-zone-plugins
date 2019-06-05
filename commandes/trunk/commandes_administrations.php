<?php

/**
 * Fichier gérant l'installation et désinstallation du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Installation
 */
// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Fonction d'installation et de mise à jour du plugin Commandes.
 *
 * @uses commandes_id_premier_webmestre()
 * @uses commandes_lister_statuts()
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @param string $version_cible
 *     Version du schéma de données dans ce plugin (déclaré dans paquet.xml)
 * @return void
 * */
function commandes_upgrade($nom_meta_base_version, $version_cible) {
	include_spip('commandes_fonctions');
	include_spip('inc/config');
	$maj = array();

	$config = lire_config('commandes');
	if (!is_array($config)) {
		$config = array();
	}

	$id_webmestre = commandes_id_premier_webmestre();

	$config = array_merge(array(
	    'duree_vie' => '',
	    'activer' => '',
	    'quand' => array_keys(commandes_lister_statuts()),
	    'expediteur' => 'webmaster',
	    'expediteur_webmaster' => $id_webmestre,
	    'expediteur_administrateur' => '',
	    'expediteur_email' => '',
	    'vendeur' => 'webmaster',
	    'vendeur_webmaster' => $id_webmestre,
	    'vendeur_administrateur' => '',
	    'vendeur_email' => '',
	    'client' => 'on'
		), $config);

	$maj['create'] = array(
	    array(
		'maj_tables', array('spip_commandes', 'spip_commandes_details', 'spip_commandes_liens'),
	    ),
	    array(
		'ecrire_config', 'commandes', $config
	    ),
	);

	$maj['0.2'] = array(
	    array('maj_tables', array('spip_commandes_details'))
	);


	$maj['0.3'] = array(
	     array(
	     	     'ecrire_config', 'commandes', array('duree_vie' => 3600)
	    ),
	);

	$maj['0.4'] = array(
	    array('sql_alter', 'TABLE spip_commandes ADD mode varchar(25) not null default ""')
	);

	$maj['0.5.0'] = array(
	    array(
		'sql_updateq',
		'spip_commandes_details',
		array('statut' => 'attente'),
		array(
		    'statut = ""',
		    'id_commande IN (select id_commande from spip_commandes where statut in ("encours","attente","paye","partiel","erreur"))',
		),
	    ),
	    array(
		'sql_updateq',
		'spip_commandes_details',
		array('statut' => 'envoye'),
		array(
		    'statut = ""',
		    'id_commande IN (select id_commande from spip_commandes where statut="envoye")',
		),
	    ),
	    array(
		'sql_updateq',
		'spip_commandes_details',
		array('statut' => 'retour'),
		array(
		    'statut = ""',
		    'id_commande IN (select id_commande from spip_commandes where statut in ("retour","retour_partiel"))',
		)
	    ),
	);

	// Ajouter une table de liens pour les commandes
	$maj['0.6.0'] = array(
	    array('maj_tables', array('spip_commandes_liens')),
	);

	// Ajouter des champs (bank_uid, echeances_type, echeances) pour gérer d'éventuels renouvellements bancaires automatiques
	$maj['0.7.0'] = array(
	    array('maj_tables', array('spip_commandes')),
	);

	// Ajout du champ source
	$maj['0.7.1'] = array(
	    array('maj_tables', array('spip_commandes'))
	);

	// TVA à taux réduit 1,05% pour les DOM, il faut 4 décimales pour le champ taxe
	$maj['0.7.2'] = array(
	    array('maj_tables', array('spip_commandes')),
	    array('sql_alter', 'TABLE spip_commandes_details CHANGE taxe taxe DECIMAL(4,4) NULL DEFAULT NULL')
	);

	// Corriger les UID bancaires manquant dans les commandes
	$maj['0.7.4'] = array(
		array('commandes_maj_0_7_4'),
	);
	// ajout du champ echeances_date_debut
	$maj['0.7.5'] = array(
	    array('maj_tables', array('spip_commandes')),
	);
	// ajout du champ taxe_exoneree_raison
	$maj['0.7.6'] = array(
	    array('maj_tables', array('spip_commandes')),
	);
	// ajout du champ reduction
	$maj['0.7.7'] = array(
	    array('maj_tables', array('spip_commandes_details')),
	);
	// passer en decimal plutôt que float
	$maj['0.7.8'] = array(
		array('sql_alter', 'TABLE spip_commandes_details CHANGE prix_unitaire_ht prix_unitaire_ht DECIMAL(20,6) NOT NULL DEFAULT 0'),
	);
	// refaire la même màj car celleux qui avaient installé à neuf depuis avaient toujours float
	$maj['0.7.9'] = array(
		array('sql_alter', 'TABLE spip_commandes_details CHANGE prix_unitaire_ht prix_unitaire_ht DECIMAL(20,6) NOT NULL DEFAULT 0'),
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Replacer les UID bancaires dans les commandes à partir des transactions
function commandes_maj_0_7_4() {
	// On récupère toutes les commandes qui ont un renouvellement récurent
	if ($commandes_recurentes = sql_allfetsel('id_commande', 'spip_commandes', 'echeances_type!=""')) {
		$commandes_recurentes = array_map('reset', $commandes_recurentes);

		foreach ($commandes_recurentes as $id_commande) {
			$id_commande = intval($id_commande);
			// On récupère l'UID chez le prestataire
			if ($abo_uid = sql_getfetsel('abo_uid', 'spip_transactions', 'id_commande = '.$id_commande)) {
				// On le copie dans la commande
				sql_updateq('spip_commandes', array('bank_uid'=>$abo_uid), 'id_commande = '.$id_commande);
			}
		}
	}
}

/**
 * Fonction de désinstallation du plugin Commandes.
 *
 * @param string $nom_meta_base_version
 *     Nom de la meta informant de la version du schéma de données du plugin installé dans SPIP
 * @return void
 * */
function commandes_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_commandes,spip_commandes_details,spip_commandes_liens");

	# Nettoyer les versionnages et forums
	sql_delete("spip_versions", sql_in("objet", array('commande')));
	sql_delete("spip_versions_fragments", sql_in("objet", array('commande')));

	effacer_meta($nom_meta_base_version);
}
