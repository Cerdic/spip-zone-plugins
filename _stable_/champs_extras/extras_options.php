<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


// gestion des extras (voir inc_extra pour plus d'informations)
//$champs_extra = false;
$champs_extra_proposes = false;

// #EXTRA
// [(#EXTRA|extra{isbn})]
// ou [(#EXTRA|isbn)] (ce dernier applique les filtres definis dans mes_options)
// Champs extra
// Non documentes, en voie d'obsolescence, cf. ecrire/inc/extra
// http://doc.spip.org/@balise_EXTRA_dist
function balise_EXTRA_dist ($p) {
	$_extra = champ_sql('extra', $p);
	$p->code = $_extra;

	// Gerer la notation [(#EXTRA|isbn)]
	if ($p->fonctions) {
		list($champ,) = $p->fonctions[0];
		include_spip('inc/extra');
		$type_extra = $p->type_requete;

		// ci-dessus est sans doute un peu buggue : si on invoque #EXTRA
		// depuis un sous-objet sans champ extra d'un objet a champ extra,
		// on aura le type_extra du sous-objet (!)
		if (extra_champ_valide($type_extra, $champ)) {
			array_shift($p->fonctions);
			array_shift($p->param);
			// Appliquer les filtres definis par le webmestre
			$p->code = 'extra('.$p->code.', "'.$champ.'")';

			$filtres = extra_filtres($type_extra, $champ);
			if ($filtres) foreach ($filtres as $f)
				$p->code = "$f($p->code)";
		} else {
			if (!function_exists($champ)) {
				spip_log("erreur champ extra |$champ");
				array_shift($p->fonctions);
				array_shift($p->param);
			}
		}
	}

	#$p->interdire_scripts = true;
	return $p;
}


//
// Definition de tous les extras possibles
//
/*
$GLOBALS['champs_extra'] = Array (
	'auteurs' => Array (
			"alim" => "radio|brut|Pr&eacute;f&eacute;rences alimentaires|Veggie,Viande",
			"habitation" => "liste|brut|Lieu|Kuala Lumpur,Cape Town,Uppsala",
			"ml" => "case|propre|Je souhaite m'abonner &agrave; la mailinglist",
			"age" => "ligne|propre|&Acirc;ge du capitaine",
			"biblio" => "bloc|propre|Bibliographie"
		),

	'articles' => Array (
			"isbn" => "ligne|typo|ISBN",
			 "options" => "multiple|brut|Options de cet article|1,2,3,plus"			 
		),
	'syndic' => Array (
			"isbn" => "ligne|typo|ISBN",
			 "options" => "multiple|brut|Options de ce site|1,2,3,plus"			 
		),
	'rubriques' => Array (
			"isbn" => "ligne|typo|ISBN",
			 "options" => "multiple|brut|Options de cette rubrique|1,2,3,plus"			 
		),
	'mots' => Array (
			"isbn" => "ligne|typo|ISBN",
			 "options" => "multiple|brut|Options de ce mot|1,2,3,plus"			 
		),
	'breves' => Array (
			"isbn" => "ligne|typo|ISBN",
			 "options" => "multiple|brut|Options de cette breve|1,2,3,plus"			 
		)
	);
*/
?>