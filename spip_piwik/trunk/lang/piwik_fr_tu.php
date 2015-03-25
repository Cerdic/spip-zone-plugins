<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/piwik?lang_cible=fr_tu
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_creer_site' => 'Créer le site',
	'action_recuperer_liste' => 'Récupérer la liste des sites',

	// C
	'cfg_description_piwik' => 'Ici tu peux indiquer ton identifiant piwik, ainsi que l’adresse du serveur gérant tes statistiques.',
	'cfg_erreur_recuperation_data' => 'Il y a une erreur de communication avec le serveur, vérifie l’adresse et le token',
	'cfg_erreur_token' => 'Ton token d’identification est invalide',
	'cfg_erreur_user_token' => 'La correspondance Nom d’utilisateur / Token n’est pas correcte.',

	// E
	'explication_adresse_serveur' => 'Entre l’adresse sans "http://" ni "https://" ni slash final',
	'explication_creer_site' => 'Le lien ci-dessous te permet de créer un site sur le serveur Piwik qui sera disponible ensuite dans la liste. Vérifie que tu as bien configuré l’adresse et le nom de votre site SPIP avant de cliquer, ce sont ces informations qui seront utilisées.',
	'explication_exclure_ips' => 'Pour exclure plusieurs adresses, sépare les par des points virgules',
	'explication_identifiant_site' => 'La liste des sites disponibles sur le serveur Piwik a été récupérée automatiquement gràce aux informations soumises. Sélectionne dans la liste ci-dessous celui qui te convient',
	'explication_mode_insertion' => 'Il existe deux modes d’insertion dans les pages du code nécessaire au bon fonctionnement du plugin. Par le pipeline "insert_head" (méthode automatique mais peu configurable), ou par l’insertion d’une balise (méthode manuelle en insérant dans le pied de tes pages la balise #PIWIK) qui, quant à elle est pleinement configurable.',
	'explication_recuperer_liste' => 'Le lien ci-dessous te permet de récupérer la liste des sites que ton compte peut administrer sur le serveur Piwik.',
	'explication_restreindre_statut_prive' => 'Choisis ici les statuts d’utilisateurs qui ne seront pas comptabilisés dans les statistiques dans l’espace privé',
	'explication_restreindre_statut_public' => 'Choisis ici les statuts d’utilisateurs qui ne seront pas comptabilisés dans les statistiques dans la partie publique',
	'explication_token' => 'Le token d’identification est disponible dans tes préférences personnelles ou dans la partie API de ton serveur Piwik',

	// I
	'info_aucun_site_compte' => 'Aucun site n’est associé à ton compte Piwik.',
	'info_aucun_site_compte_demander_admin' => 'Tu dois demander à un administrateur de ton serveur Piwik d’ajouter un site correspondant.',

	// L
	'label_adresse_serveur' => 'Adresse URL du serveur (https:// ou http://)',
	'label_comptabiliser_prive' => 'Comptabiliser les visites de l’espace privé',
	'label_creer_site' => 'Créer un site sur le serveur Piwik',
	'label_exclure_ips' => 'Exclure certaines adresses IP',
	'label_identifiant_site' => 'L’identifiant de ton site sur le serveur Piwik',
	'label_mode_insertion' => 'Mode d’insertion dans les pages publiques',
	'label_piwik_user' => 'Compte utilisateur Piwik',
	'label_recuperer_liste' => 'Récupérer la liste des sites sur le serveur Piwik',
	'label_restreindre_auteurs_prive' => 'Restreindre certains utilisateurs connectés (privé)',
	'label_restreindre_auteurs_public' => 'Restreindre certains utilisateurs connectés (public)',
	'label_restreindre_statut_prive' => 'Restreindre certains statuts d’utilisateurs dans l’espace privé',
	'label_restreindre_statut_public' => 'Restreindre certains statuts d’utilisateurs dans la partie publique',
	'label_token' => 'Token d’identification sur le serveur',

	// M
	'mode_insertion_balise' => 'Insertion par la balise #PIWIK (modification nécessaire de tes squelettes)',
	'mode_insertion_pipeline' => 'Insertion automatique par le pipeline "insert_head"',

	// P
	'piwik' => 'Piwik',

	// T
	'texte_votre_identifiant' => 'Ton identifiant',
	'textes_url_piwik' => 'Ton serveur piwik'
);

?>
