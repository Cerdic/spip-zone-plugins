<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/clients/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'clients_titre' => 'Clients',
	'configurer_titre' => 'Configuration du plugin Clients',
	'configurer_titre_elements' => 'Choisir les éléments constituant une fiche client',

	//E	
	'erreur_inscription_visiteur' => 'Erreur de configuration SPIP : inscription des visiteurs non autorisée',
	'explication_type_civ' => 'Pour en profiter au mieux, il faut surcharger le fichier <code>contacts_et_organisations/prive/contenu/contact.html</code> en dupliquant ce fichier dans <code>squelette/prive/contenu</code> et en remplaçant <code>(#CIVILITE)&lt;/span&gt;</code> par <code>(#VAL{clients:label_}|concat{#CIVILITE}|_T)&lt;/span&gt;</code>.<br />Pour modifier la liste suivante, il suffit de surchager le fichier <code>clients/inc/civilite.php</code> par <code>squelette/inc/civilite.php</code> en nommant la fonction <code>inc_civilite</code>.',
	
	// L
	'label_checkbox' => 'Utiliser un champ checkbox',
	'label_damoiseau' => 'Damoiseau',
	'label_docteur' => 'Docteur',
	'label_elm' => 'Éléments optionnels',
	'label_elm_civ' => 'Liste des civilités',
	'label_fax' => 'Fax',
	'label_input' => 'Utiliser un champ input',
	'label_madame' => 'Madame',	
	'label_mademoiselle' => 'Mademoiselle',
	'label_monsieur' => 'Monsieur',
	'label_obligatoire' => 'Obligatoire ?',
	'label_portable' => 'Portable',
	'label_tel' => 'Téléphone',
	'label_type_civ' => 'Type de civilité',	

	// T
	'texte_exp1' => 'Par défaut, les éléments obligatoires demandés sont : prénom, nom, email, adresse, code postal et ville. La liste suivante permet de compléter ces informations.'
);

?>
