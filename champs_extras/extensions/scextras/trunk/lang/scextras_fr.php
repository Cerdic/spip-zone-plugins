<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_synchro' => 'Mettre à jour les champs extra',


	// D
	 'datemaj' => ' (MAJ effectuée le @date@)',


	// E
	'erreur_conbase' => 'ERREUR pendant la connexion à la base de référence. Vérifier les paramètres dans le fichier config/scextras_reference',
	'erreur_lecture_ce' => 'ERREUR pendant la lecture des champs extra [@objet@] dans la base de référence. Peut-être que les champs extra ne sont pas encore définis pour les [@objet@] dans la base de référence ?',
	'erreur_lecture_defvide' => 'Info : pas de champs extra définis pour [@objet@].',
	'erreur_majce' => 'ERREUR pendant la maj de la définition du champs extra [@objet@]',


	// M
	'majafaire' => 'La définition dans la base référence est plus récente. La mise à jour est nécessaire.',


	// O
	'objet' => 'Objets éditoriaux',
	'objet_en_cours' => 'Mise à jour de l\'objet : @objet@. ',
	// S
	'scextras_titre' => 'Champs Extras (Synchronisation)',
	'synchroniser'  => 'Synchroniser les champs extras',
	'synchro_ok'    => 'La synchronisation a été effectuée avec SUCCES',
	'synchro_explication_boite_info' => 'Ici vous pouvez lancer la synchronisation de la définition des champs extra. ATTENTION, la synchro efface toutes les anciennes définitions des champs extras et les remplace par celles de la base de référence. Cette opération est irréversible.',
	'ok' => ' Les définitions semblent identiques ou plus récentes sur ce site. Aucune maj n\'est nécessaire.',


	// T
	'titre_page_configurer_scextras' => 'Configuration de SYNCHRO-CEXTRAS',
);

?>