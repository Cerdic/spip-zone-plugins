<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
$GLOBALS[$GLOBALS['idx_lang']] = array(
	//B
	'bouton_exporter' => 'Exporter',
	'bouton_importer' => 'Importer',
	
	//E
	'erreur_champ_vide' => 'Eh ! Faut mettre quelque chose !',
	'erreur_champ_erronne' => "Probl&egrave;me d'analyse des donn&eacute;es... Mauvais copier coller ?",
	'exporter' => 'Exporter',
	'exporter_infos' => "L'exportation provoque la cr&eacute;ation d'une description
	des champs extras actifs sur votre site. Enregistrez ce contenu qui vous servira &agrave; 
	recr&eacute;er les champs extras sur un autre site. Cette description ne 
	concerne que la d&eacute;claration des champs, mais en aucun cas leur contenu ; 
	pour d&eacute;placer un site, vous devez donc depuis le site d'origine
	cr&eacute;er une sauvegarde SPIP et exporter les champs extras, 
	puis sur le site de destination, dans l'ordre, activer les m&ecirc;mes plugins,
	importer les champs extras, puis la sauvegarde SPIP.",
		
	//I
	'importation_effectuee' => 'Importation effectu&eacute;e',
	'importation_erreurs' => "Certains champs extras n'ont pas pu &ecirc;tre import&eacute;s.
				Voir spip.log pour plus d'informations.",
	'import_export' => 'Import/Export de Champs Extras',
	'importer_exporter' => 'Importer / Exporter',
	'import_export_lien' => 'Importer ou Exporter des descriptions de champs extras',
	'importer' => 'Importer',
	'importer_infos' => "L'importation utilise une description
	des champs extras r&eacute;alis&eacute;e par le formulaire d'exportation. 
	Collez le contenu qui a &eacute;t&eacute; g&eacute;n&eacute;r&eacute; 
	dans le cadre ci-dessous. Les champs extras ne seront cr&eacute;&eacute;s que 
	si les tables SQL auquels ils correspondent existent. Il est donc
	prudent d'activer les m&ecirc;mes plugins que sur le site d'origine
	(au moins ceux qui cr&eacute;ent de nouveaux objets &eacute;ditoriaux tel que Agenda).",

	//L
	'label_description' => 'Description',
	
	
);
?>