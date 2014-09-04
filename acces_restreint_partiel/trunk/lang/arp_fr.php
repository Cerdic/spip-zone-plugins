<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;
 
$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'arp_titre' => 'Accès Restreint Partiel',

	// C
	'cfg_titre_parametrages' => 'Paramétrages',
	'cfg_titre_parametrages_regles' => 'Paramétrage des règles',
	'cfg_mot_regle' => 'Règle',
	'cfg_explication_parametrages_regles' => 'Le plugin vous permet de définir N règles de filtrage ; N étant configurable.<br />Vous pouvez faire référence à ces règles soit directement et de façon prioritaire dans un article en collant un mot-clé (ex : arp_regle1) , soit de façon globale pour l\'ensemble du site (voir ci-dessous). Si pas de mot-clé affecté à l\'article, c\'est la règle globale qui est appliquée.<br />Pour chaque règle, on définit un filtre à appliquer ainsi que le paramètre xxx correspondant.',

	'cfg_label_nregle' => 'Combien de règles désirez-vous ?',
	'cfg_nregle_erreur' => 'Veuillez entrer une valeur numérique pour le nombre de règle',

	'cfg_label_regle_defaut' => 'Quelle règle doit-être appliquée par défaut ?',
	'cfg_explication_regle_defaut' => 'Choisissez une des règles que vous avez définie ci-dessous. Elle sera appliquée à tous les articles restreints, si ces derniers ne possèdent aucune règle assignée par mot-clé',

	'cfg_label_regle_zone' => 'Quelle règle doit-être appliquée à cette zone ?',
	'cfg_explication_regle_zone' => 'Si vide, la règle par défaut sera appliquée. Ne se substitue pas au règle attribuée par mot-clé pour chaque article',
	'cfg_label_regle' => 'Appliquer le filtre',
	'cfg_explication_regle' => 'Choisissez le filtre à appliquer pour cette règle',

	'cfg_label_filtre_ncar' => 'Couper à xxx caractères',
	'cfg_label_filtre_pourcentage' => 'Ne conserver que xxx % de l\'article',
	'cfg_label_filtre_nintertitre' => 'Couper juste avant le xxx ième intertitre',
	'cfg_label_filtre_tout' => 'Tout le texte est filtré (aucun affichage)',
	'cfg_label_filtre_rien' => 'Rien n\'est filtré, le texte est affiché tel que',
	'cfg_label_filtre_que_intertitre' => 'Ne laisser que les intertitres, et remplacer texte par xxx',

	'cfg_label_filtre_param' => 'Paramètre du filtre (xxx)',
	'cfg_explication_filtre_param' => 'Indiquer la valeur du paramètre xxx du filtre appliqué à la règle',

	'cfg_label_filtre_ncar_min' => 'Nombre de caractères minimum',
	'cfg_explication_filtre_ncar_min' => ' Il s\'agit d\'un garde-fou : si après filtrage, l\'article ne contient pas au moins le nombre de caractères indiqué, alors le filtrage est annulé et la totalité de l\'article est affiché. Laisser vide si pas de garde-fou à appliquer.',

	'cfg_titre_parametrages_avantapres' => 'Paramètrages des textes AVANT/APRES',
	'cfg_explication_parametrages_avantapres' => 'Si le texte est modifié par un filtre, vous pouvez forcer l\'affichage de texte AVANT et APRES le texte filtré.<br />Par exemple : Pour lire la suite du texte, abonnez-vous, bla bla bla...<br />Ces textes ne seront affichés que si le texte a été effectivement filtré.<br />Le texte est au format typo SPIP ; tous les raccourcis et modèles sont donc possibles.<br />Ces textes sont facultatifs.<br /> Il est possible de configurer ces textes une fois pour toutes les zones, de façon globale. Ou alors spécifiquement zone par zone.',
	'cfg_texte_aa_global' => 'Textes AVANT/APRES en configuration globale',
	'cfg_label_texte_avant' => 'Texte AVANT',
	'cfg_explication_texte_avant' => 'Indiquer le texte à insérer AVANT un texte qui a été filtré',
	'cfg_label_texte_apres' => 'Texte APRES',
	'cfg_explication_texte_apres' => 'Indiquer le texte à insérer APRES un texte qui a été filtré',

	// T
	'titre_page_configurer_arp' => 'Configurer ARP (Accès Restreint Partiel)',
);

?>