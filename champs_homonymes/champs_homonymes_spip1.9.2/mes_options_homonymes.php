<?php
/*************************************************************************************/
// Ces champs extra servent de test pour vérifier la synchronisation des champs extra
// Vous pouvez modifier ce fichier pour définir vos propres champs extra
/************************************************************************************/
$plus= 'Plus 2';
$GLOBALS['champs_extra'] = Array (
	'auteurs' => Array (
			"plus" => "ligne|typo|Plus"
		),
	'articles' => Array (
			"plus" => "ligne|typo|$plus"
		),	
	'rubriques' => Array (
			"plus" => "liste|brut|<multi>Estimation[en]Valuation
</multi>| ,<multi>Bon travail[en]Good job</multi>,<multi>Passable[en]Not so good
</multi>"
		),
	'breves' => Array (
			"plus" => "liste|brut|<multi>Estimation[en]Valuation
</multi>|<multi>Bon travail[en]Good job</multi>,<multi>Passable[en]Not so good
</multi>"
		),
	'sites' => Array (
						"plus" => "ligne|typo|Plus"
		),
	'syndic' => Array (
						"plus" => "ligne|typo|Plus"
		),
	'mots' => Array (
			"plus" => "ligne|typo|Plus"
		)
	);
	
?>