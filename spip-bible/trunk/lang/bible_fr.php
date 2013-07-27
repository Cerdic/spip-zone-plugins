<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'at'=>'Ancien Testament',
	'langue'=>'Langue',
	'nt'=>'Nouveau Testament',
	'deutero'=>'Livres Deut&eacute;rocanoniques',
	'historique'=>'Historique',
	'version_dispo'=>'Traductions de la Bible disponibles',
	'verset'		=>'Verset',
	'chapitre'		=>'Chapitre',
	'ok'			=>'OK',
	'supprimer'		=>'Supprimer',
	//la config
	'cfg'               =>  'Spip-Bible',
	'cfg_descriptif'    =>  'Page de configuration générale de Spip-Bible. Vous pouvez aussi configurer celle du [presse-papier->?exec=cfg&cfg=bible_pp].',
	'livres_bibles'		=>	'Liste des livres disponibles dans la Bible version  «@trad@ »',
	'cfg_affichage'		=> 	'Réglages d\'affichage',
	'cfg_alias_version'=>'Réglages des alias de version',
	'cfg_trad_defauts'	=>  'Réglages des traductions par défaut',
	'cfg_ref'			=>	'Afficher la r&eacute;f&eacute;rence du passage cit&eacute;',
	'cfg_nommer_trad'	=> "Nommer la traduction",
	'cfg_retour'		=>	'Faire des retour à la ligne entre les versets',
	'cfg_forme_livre'	=> 	'Forme du livre',
	'cfg_forme_livre_abbr'	=>	'Format mixte (conseillé)',
	'cfg_forme_livre_complete'	=> "Complète",
	'cfg_forme_livre_raccourcie'	=> 	'Raccourcie (déconseillée)',
	'cfg_url'		=> "Mettre un lien vers le site source",
	'cfg_traduction_fr'	=>	'Traduction francaise par d&eacute;faut',
	'cfg_traduction_da' =>	'Traduction danoise par d&eacute;faut',
	'cfg_traduction_en' =>	'Traduction anglaise par d&eacute;faut',
	'cfg_traduction_es' =>	'Traduction espagnole par d&eacute;faut',
	'cfg_traduction_de' =>	'Traduction allemande par d&eacute;faut',
	'cfg_traduction_fi' =>	'Traduction finnoise par d&eacute;faut',
	'cfg_traduction_hu' =>	'Traduction magyare par d&eacute;faut',
	'cfg_traduction_it' =>	'Traduction italienne par d&eacute;faut',
	'cfg_traduction_nl' =>	'Traduction néerlandaise par d&eacute;faut',
	'cfg_traduction_no' =>	'Traduction norv&eacute;gienne par d&eacute;faut',
	'cfg_traduction_pl' =>	'Traduction polonaise par d&eacute;faut',
	'cfg_traduction_pt' =>	'Traduction portugaise par d&eacute;faut',
	'cfg_traduction_ru' =>	'Traduction russe par d&eacute;faut',
	'cfg_traduction_sv' =>	'Traduction su&eacute;doise par d&eacute;faut',
	'cfg_traduction_bg' =>  'Traduction bulgare par d&eacute;faut',
	'cfg_configurer'	=>	'Configurer Spip-Bible',
	'cfg_explication'	=>	"Vous pouvez régler la mani&egrave;re dont apparaitra les citation du texte, en l'absence d'argument pass&eacute; au mod&egrave;le",
	'cfg_numeros'		=>	"Afficher les n° de chapitres et de versets dans le corps du texte",
	'pas_livre'			=>"L'abr&eacute;viation du livre demandé n'existe pas dans cette traduction",
	'police_hbo'		=>"Faut-il imposer une police spécial pour l'hébreu ?",
	'traduction_pas_dispo'	=> "La traduction de la Bible demand&eacute;e n'existe pas",
	
	/* le presse papier biblique */
	'presse_papier_titre'      => 'Presse Papier Biblique',
	'presse_papier'            => 'Taper ici le code d\'appel &agrave; un passage biblique',
	'presse_papier_resultat'   => 'Texte correspondant',
	
	/* Le formulaire d'obtention de référence (PP V2)*/
	'form_version'             => 'Version',
	'form_numeros'             => 'Afficher num&eacute;ros chap./v.',
	'form_retour'              => 'Retour-ligne entre v.',
	'form_ref'                 => 'Mettre r&eacute;f&eacute;rences',
	'form_nommer_trad'	   => 'Nommer la traduction',
	'form_url'			=> "Pointer vers le site d'origine",
	'form_forme_livre'	   => 'Forme du livre',
	'form_passage'             => 'Passage',
	'form_ref_incorrecte'          => 'R&eacute;f&eacute;rence incorrecte',
	
	/*cfg du presse-papier*/
	
	'cfg_pp_choix_laisses'     => 'Laisser la possibilité aux r&eacute;dacteurs',
	'cfg_pp_numeros'           => "D'afficher ou non les num&eacute;ros",
	'cfg_pp_ref'               => "D'afficher ou non les r&eacute;f&eacute;rences",
	'cfg_pp_retour'            => "De mettre ou non des retours &agrave; la ligne",
	'cfg_pp_lang_pas_art'      => "De choisir une version dans une langue - non morte - autre que celle de l'article ?",
	'cfg_pp_nommer_trad'		=> "De choisir de nommer ou non la traduction",
	'cfg_pp_forme_livre'		=>"De choisir la forme d'affichage du livre",
	'cfg_pp_url'			=>"De mettre un lien vers le site source",
	'cfg_pp_lang_morte'      => "De choisir une version dans une langue morte",
	"cfg_pp_version_par_lang" => "@lang@ : traductions propos&eacute;es",
	'cfg_pp_pas_bonux'        => 'Cette configuration nécéssite le plugin "SPIP-BONUX"',
	'cfg_pp_descriptif'       => 'Cette page vous permet de configurer le presse-papier de Spip-Bible. Voici comment il s\'affiche avec le configuration actuelle.',
	'cfg_pp'                  => 'Configurer le presse-papier de Spip-Bible'
);


?>
