<?php

	$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	'titre_onglet' => 'Squelettes par mots-cl&eacute;s',
	'titre_page' => 'Configuration du choix du squelette',
	'gros_titre' => 'Cr&eacute;er des r&egrave;gles pour choisir les squelettes en fonction des mots clef',
	'help' => 'Cette page n\'est accesible qu\'aux administrateur. Vous pouvez creer des r&egrave;gles pour choisir les squelettes de vos page avec des mots clef.

Une r&egrave;gle sp&eacute;cifie:
-# un fond de base,
-# le groupe de mot clef qui contient les mots pour specifier le squelette,
-# le type de l\'&eacute;l&eacute;ment affich&eacute; par cette page.

Les squelettes seront alors nomm&eacute;s {{fond-mot.html}}. Le plugin va d\'abord chercher pour un squelette qui correspond &agrave; un mot clef attach&eacute; &agrave; l\'&eacute;l&eacute;ment et s\'il n\'en trouve pas, il cherchera un squelette qui correspond &agrave; un des mots clefs d\'une des rubriques parentes.

Les auteurs n\'ont alors plus qu\'&agrave; associer un mot du groupe &agrave; l\'&eacute;l&eacute;ment.',
	'reglei' => 'R&egrave;gle @id@',
	'nouvelle_regle' => 'Nouvelle r&egrave;gle',
	'fond' => 'Fond:',
	'groupe' => 'Groupe:',
	'type' => 'Type:',
	'une_possibilite' => '1 squelette :',
	'nb_possibilites' => '@nb@ squelettes :',
	'utiliserasquelette' => 'Cet article utilisera le squelette @squelette@',
	'avertissement' => 'Attention le squelette @squelette@ n\'existe pas, cette r&egrave;gle ne marchera pas tant que le squelette par d&eacute;faut n\'est pas pr&eacute;sent.',
	'label_regle_active' => 'Règle active',
);
?>
