<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/spip_piwik/spip_piwik_2_0/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Cr&eacute;er le site',

	// C
	'cfg_description_piwik' => 'Ici vous pouvez indiquer votre identifiant piwik, ainsi que l\'adresse du serveur g&eacute;rant vos statistiques.',
	'cfg_erreur_recuperation_data' => 'Il y a une erreur de communication avec le serveur, veuillez v&eacute;rifier l\'adresse et le token',
	'cfg_erreur_token' => 'Votre token d\'identification est invalide',
	'cfg_erreur_user_token' => 'La correspondance Nom d\'utilisateur / Token n\'est pas correcte.',

	// E
	'explication_adresse_serveur' => 'Entrez l\'adresse sans "http://" ni "https://" ni slash final',
	'explication_creer_site' => 'Le lien ci-dessous vous permet de cr&eacute;er un site sur le serveur Piwik qui sera disponible ensuite dans la liste. V&eacute;rifiez que vous avez bien configur&eacute; l\'adresse et le nom de votre site SPIP avant de cliquer, ce sont ces informations qui seront utilis&eacute;es.',
	'explication_exclure_ips' => 'Pour exclure plusieurs adresses, s&eacute;parez les par des points virgules',
	'explication_identifiant_site' => 'La liste des sites disponibles sur le serveur Piwik a &eacute;t&eacute; r&eacute;cup&eacute;r&eacute;e automatiquement gr&agrave;ce aux informations soumises. S&eacute;lectionnez dans la liste ci-dessous celui qui vous convient',
	'explication_mode_insertion' => 'Il existe deux modes d\'insertion dans les pages du code n&eacute;cessaire au bon fonctionnement du plugin. Par le pipeline "insert_head" (m&eacute;thode automatique mais peu configurable), ou par l\'insertion d\'une balise (m&eacute;thode manuelle en insérant dans le pied de vos pages la balise #PIWIK) qui, quant &agrave; elle est pleinement configurable.',
	'explication_restreindre_statut_prive' => 'Choisissez ici les statuts d\'utilisateurs qui ne seront pas comptabilisés dans les statistiques dans l\'espace priv&eacute;',
	'explication_restreindre_statut_public' => 'Choisissez ici les statuts d\'utilisateurs qui ne seront pas comptabilisés dans les statistiques dans la partie publique',
	'explication_token' => 'Le token d\'identification est disponible dans vos pr&eacute;f&eacute;rences personnelles ou dans la partie API de votre serveur Piwik',

	// I
	'info_aucun_site_compte' => 'Aucun site n\'est associ&eacute; &agrave; votre compte Piwik.',
	'info_aucun_site_compte_demander_admin' => 'Vous devez demander &agrave; un administrateur de votre serveur Piwik d\'ajouter un site correspondant.',

	// L
	'label_adresse_serveur' => 'Adresse URL du serveur (https:// ou http://)',
	'label_comptabiliser_prive' => 'Comptabiliser les visites de l\'espace priv&eacute;',
	'label_creer_site' => 'Cr&eacute;er un site sur le serveur Piwik',
	'label_exclure_ips' => 'Exclure certaines adresses IP',
	'label_identifiant_site' => 'L\'identifiant de votre site sur le serveur Piwik',
	'label_mode_insertion' => 'Mode d\'insertion dans les pages publiques',
	'label_piwik_user' => 'Compte utilisateur Piwik',
	'label_restreindre_auteurs_prive' => 'Restreindre certains utilisateurs connect&eacute;s (priv&eacute;)',
	'label_restreindre_auteurs_public' => 'Restreindre certains utilisateurs connect&eacute;s (public)',
	'label_restreindre_statut_prive' => 'Restreindre certains statuts d\'utilisateurs dans l\'espace priv&eacute;',
	'label_restreindre_statut_public' => 'Restreindre certains statuts d\'utilisateurs dans la partie publique',
	'label_token' => 'Token d\'identification sur le serveur',

	// M
	'mode_insertion_balise' => 'Insertion par la balise #PIWIK (modification n&eacute;cessaire de vos squelettes)',
	'mode_insertion_pipeline' => 'Insertion automatique par le pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Votre identifiant',
	'textes_url_piwik' => 'Votre serveur piwik'
);

?>
