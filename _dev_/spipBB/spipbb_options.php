<?php

//modifiez ici les dossiers squelettes dans l'ordre souhaite
//$dossier_squelettes.=":squelettesforum"; a priori inutile avec le plugin

	/*************************************************************************************/
	// Les lignes qui suivent servent à définir les champs extra
	/************************************************************************************/
	$GLOBALS['champs_extra'] = Array (
		'auteurs' => Array (
		"Localisation" => "ligne|propre|Localisation",
		"Emploi" => "ligne|propre|Centres d'intÃ©rÃªt",
		"Loisirs" => "ligne|propre|Loisirs",
		"Numero_ICQ" => "ligne|propre|Contacts chat (ICQ, Skype, etc... )",
				"avatar" => "ligne|propre|URL de votre avatar",
		"signature" => "bloc|brut|Votre signature"
			)
		);
		
	$GLOBALS['champs_extra_proposes'] = Array (
		'auteurs' => Array (
			// tous : par defaut
			'tous' =>  'Localisation|Emploi|Loisirs|Numero_ICQ|signature',
			// les inscrits non admin ont de quoi se faire un avatar, équivalent au logo des auteurs.
			'6forum' => 'Localisation|Emploi|Loisirs|Numero_ICQ|avatar|signature'
			)
	);

?>
