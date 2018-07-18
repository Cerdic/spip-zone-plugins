<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	//C
	'configurations' => 'Ce formulaire va vous permettre de configurer le site de façon générale. ',

	//E
	'explications' => 'Le plugin va lire la configuration de la façon suivante : <br />
	- pour trouver le domaine du secteur : Url du secteur puis url par defaut <br />
	- pour trouver le squelette du secteur : Chercher le dossier suivant le schéma (www.domaine.fr puis domaine.fr) puis dossiers des squelettes du secteur. <br />
	Un domaine peut avoir plusieurs dossiers squelettes, il faut les séparer par ":" (ex:squelettes:squelettes/sous-domaine.domaine.com)',
	'explication_identifiant' => 'Nom informatique : charactères alphanumériques ou «_»',

	// I
	'info_domaine_non' => 'Ce domaine n\'est pas configuré',
	'info_domaine_aucune_url' => 'Pas d\'URL configurée',

	//L
	'label_editer_url' => 'Url par defaut',
	'label_editer_url_rubrique' => 'Url de la rubrique',
	'label_editer_url_secteur' => 'Url du secteur',
	'label_squelette' => 'Dossier des secteurs',
	'label_squelette_rubrique' => 'Dossiers des squelettes de la rubrique',
	'label_squelette_secteur' => 'Dossiers des squelettes du secteur',
	'label_entree_id_domaine' => 'N° du secteur',
	'label_identifiant' => 'Identifiant',

//T
	'titre_multidomaines' => 'Multidomaines',

);