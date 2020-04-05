<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_core_/plugins/statistiques/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_titre' => 'Configuration',
	'cfg_champ_objets_label' => 'Objets',
	'cfg_champ_objets_explication' => 'Choix des objets éditoriaux sur lesquels activer les statistiques.
	Si tous les objets sont décochés et que les statistiques sont activées,
	les autres types de pages seront toujours pris en compte.',

	// I
	'info_afficher_visites_objets_plus_visites' => 'Les plus visités depuis le début :',
	'info_visites_objets_plus_populaires' => 'Les plus populaires :',
	'info_visites_objets_derniers' => 'Les derniers publiés :',
	'info_0_referer' => 'Aucun lien entrant',
	'info_1_referer' => '1 lien entrant',
	'info_nb_referers' => '@nb@ lien entrants',
	'info_0_visite' => 'Aucune visite',
	'info_1_visite' => '1 visite',
	'info_nb_visites' => '@nb@ visites',
	
	// O
	'onglet_visites_toutes' => 'Toutes les visites',
	'onglet_referers_tous' => 'Tous les referers',
	'onglet_tous_objets' => 'Tous les objets',

	//T
	'texte_repartition_objets_rubriques' => 'Seuls les objets pouvant être rangés dans des rubriques sont pris en compte.',
);
