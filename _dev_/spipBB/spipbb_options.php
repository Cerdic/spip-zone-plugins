<?php

//modifiez ici les dossiers squelettes dans l'ordre souhaite
//$dossier_squelettes.=":squelettesforum"; a priori inutile avec le plugin

	/*************************************************************************************/
	// Les lignes qui suivent servent à définir les champs extra
	/************************************************************************************/
	$GLOBALS['champs_extra'] = Array (
		'auteurs' => Array (
			"Localisation" => "ligne|propre|Localisation",
			"Emploi" => "ligne|propre|Centres d'int&eacute;r&eacute;t",
			"Loisirs" => "ligne|propre|Loisirs",
			"Numero_ICQ" => "ligne|propre|Contact chat (ICQ)",
			"Nom_AIM" => "ligne|propre|Contact chat (AIM)",
			"Nom_Yahoo" => "ligne|propre|Contact chat (Yahoo)",
			"Nom_MSNM" => "ligne|propre|Contact chat (MSNM)",
			"avatar" => "ligne|propre|URL de votre avatar",
			"signature" => "bloc|brut|Votre signature"
			)
		);
		
	$GLOBALS['champs_extra_proposes'] = Array (
		'auteurs' => Array (
			// tous : par defaut
			'tous' =>  'Localisation|Emploi|Loisirs|Numero_ICQ|Nom_AIM|Nom_Yahoo|Nom_MSNM|signature',
			// les inscrits non admin ont de quoi se faire un avatar, équivalent au logo des auteurs.
			'6forum' => 'Localisation|Emploi|Loisirs|Numero_ICQ|Nom_AIM|Nom_Yahoo|Nom_MSNM|avatar|signature'
			)
	);

$table_des_traitements['TITRE'][]= 'supprimer_numero(typo(%s))';

?>
