<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
	if (!defined("_ECRIRE_INC_VERSION")) return;

	$GLOBALS[$GLOBALS['idx_lang']] = array(
		'titre' => 'Gestion ML',
		'description' => 'Gestion de Mailing Lists',
		'titre_cadre' => 'Gestion de Mailing Lists',
		'titre_boite_cfg' => 'Configuration du plugin Gestion ML',
		'boite_info' => 'Cette page vous permet d\'acc&eacute;der &agrave; la gestion des utilisateurs des mailing-lists pour lesquelles le webmestre de ce site vous en a donn&eacute; l\'autorisation.',
		
		// Exec
		'infos' => 'Information sur la liste',
		'abonnes' => 'Gestion des abonn&eacute;s',
		'envoyermail' => 'Recevoir la liste des abonn&eacute;s par mail',
		'supprimer' => 'Supprimer l\'abonn&eacute;',

		// Config
		'afficher_hebergeurs' => 'Choisir votre h&eacute;bergeur de listes' ,
		'config_local_legend' => 'Param&eacute;trage du serveur local' ,
		'serveur_local' => 'Serveur local' ,
		'explication_serveur_local' => 'Le nom de votre serveur local si vous avez besoin de tester en local (souvent localhost)' ,
		'config_ovh_legend' => 'Param&eacute;trage OVH' ,
		'serveur_distant' => 'Serveur Soap' ,
		'explication_serveur_distant' => 'Url du fichier wsdl fournie par OVH' ,
		'domaine' => 'Votre domaine' ,
		'explication_domaine' => 'Saisissez votre domaine sans les www' ,
		'identifiant' => 'Identifiant' ,
		'explication_identifiant' => 'Votre identifiant de connexion au serveur SOAP (nic-handle)' ,
		'mot_de_passe' => 'Mot de passe' ,
		'explication_mot_de_passe' => 'Votre mot de passe de connexion au serveur SOAP (mot de passe d\'acc&egrave;s au manager OVH)' ,
		'auteurs_listes_legend' => 'Configuration des listes g&eacute;rables par auteur' ,
		'liste_de' => '@nom@' ,
		'explication_liste_de' => 'Choisissez la ou les listes que pourra g&eacute;rer l\'auteur @nom@' ,
	);
?>