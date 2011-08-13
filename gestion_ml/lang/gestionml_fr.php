<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
	if (!defined("_ECRIRE_INC_VERSION")) return;

	$GLOBALS[$GLOBALS['idx_lang']] = array(
		'titre' => 'Gestion ML',
		'description' => 'Gestion de Mailing Lists',
		'titre_cadre' => 'Gestion de Mailing Lists',
		'titre_boite_cfg' => 'Configuration du plugin Gestion ML',
		'boite_info' => 'Cette page vous permet d\'accéder à la gestion des utilisateurs des mailing-lists pour lesquelles le webmestre de ce site vous en a donné l\'autorisation.',
		
		// Exec
		'infos' => 'Information sur la liste',
		'abonnes' => 'Gestion des abonnés',
		'envoyer_mail' => 'Recevoir la liste des abonnés par mail',
		'supprimer_abonne' => 'Supprimer l\'abonné',
		'ajouter_mail' => 'Ajouter un email',
		'btn_ajouter' => 'Ajouter',
		'titre_abonnes' => 'Abonnés de la liste @liste@',
		'aucune_listes_dispo' => 'Aucune liste disponible',
		'titre_listes_dispo' => 'Listes disponibles',
		'titre_parametres' => 'Paramètres de la liste @liste@',
		'confirmer_suppression' => 'Etes-vous sûr de vouloir supprimer cet email ?',
		
		// Config
		'afficher_hebergeurs' => 'Choisir votre hébergeur de listes' ,
		'config_local_legend' => 'Paramétrage du serveur local' ,
		'serveur_local' => 'Serveur local' ,
		'explication_serveur_local' => 'Le nom de votre serveur local si vous avez besoin de tester en local (souvent localhost)' ,
		'config_ovh_legend' => 'Paramétrage OVH' ,
		'serveur_distant' => 'Serveur Soap' ,
		'explication_serveur_distant' => 'Url du fichier wsdl fournie par OVH' ,
		'domaine' => 'Votre domaine' ,
		'explication_domaine' => 'Saisissez votre domaine sans les www' ,
		'identifiant' => 'Identifiant' ,
		'explication_identifiant' => 'Votre identifiant de connexion au serveur SOAP (nic-handle)' ,
		'mot_de_passe' => 'Mot de passe' ,
		'explication_mot_de_passe' => 'Votre mot de passe de connexion au serveur SOAP (mot de passe d\'accès au manager OVH)' ,
		'auteurs_listes_legend' => 'Configuration des listes gérables par auteur' ,
		'liste_de' => '@nom@' ,
		'explication_liste_de' => 'Choisissez la ou les listes que pourra gérer l\'auteur @nom@' ,
	);
?>