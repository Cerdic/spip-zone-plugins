<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_export_stats_charger_dist(){

	$valeurs = array(
		'bannieres' => $bannieres,
		'debut' => $debut,
		'fin' => $fin,
		'format_export' => $format_export,
	);

	return $valeurs;
}

function formulaires_export_stats_verifier_dist(){

	$erreurs = 	array();
	$valeurs = array(
		"bannieres" => _request('bannieres'),
		"debut" => _request('debut'),
		"fin" => _request('fin'),
		'format_export' => _request('format_export'),
	);

	if ($valeurs[bannieres]!=''){
		if(!preg_match_all("#^[1-9]+(,[0-9]+)*$#", $valeurs[bannieres], $resultat))
			$erreurs['bannieres'] = _T('bannieres:erreur_liste_bannieres');
	}

	if ($valeurs[debut]!='00/00/0000'){
		if (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $valeurs[debut], $m) == 0 || checkdate($m[3], $m[1], $m[4]) == 0) 
		$erreurs['debut'] = _T('bannieres:erreur_debut_non_valide');
	}

	if ($valeurs[fin]!='00/00/0000'){
		if (preg_match('#^([0-9]{2})([/-])([0-9]{2})\2([0-9]{4})$#', $valeurs[fin], $m) == 0 || checkdate($m[3], $m[1], $m[4]) == 0)
		$erreurs['fin'] = _T('bannieres:erreur_fin_non_valide');
	}

	return $erreurs;
}

function formulaires_export_stats_traiter_dist(){

	$valeurs = array(
		"bannieres" => _request('bannieres'),
		"debut" => _request('debut'),
		"fin" => _request('fin'),
		'format_export' => _request('format_export'),
	);

	/*foreach($valeurs as $clef => $valeur){
		spip_log("clef : $clef => $valeur","test_export");
	}*/

	// On exporte seulement ce qui est demandé
	$where = array();
	// date de début spécifiée
		if ($valeurs['debut']!="00/00/0000") {
			$debut = explode("/",$valeurs[debut]);
				// verif
				$jour = $debut[0];
				$mois = $debut[1];
				$annee = $debut[2];
				spip_log("jour : $jour - mois : $mois - annee $annee","test_export");
			$date_debut = $debut[2].'-'.$debut[1].'-'.$debut[0].' 00:00:00';
				spip_log("debut : $date_debut","test_export");
		$where[] .= 'date>'.sql_quote($date_debut);
			$test = $where[0];
				spip_log("where : $test","test_export");
		}
	// date de fin spécifiée
		if ($valeurs['fin']!="00/00/0000") {
			$fin = explode("/",$valeurs[fin]);
				// verif
				$jour = $fin[0];
				$mois = $fin[1];
				$annee = $fin[2];
				spip_log("jour : $jour - mois : $mois - annee $annee","test_export");
			$date_fin = $fin[2].'-'.$fin[1].'-'.$fin[0].' 23:59:59';
				spip_log("fin : $date_fin","test_export");
		$where[] .= 'date<'.sql_quote($date_fin);
			$testb = $where[1];
				spip_log("where : $testb","test_export");
		}
	
	// Liste des bannieres spécifiées
	if ($valeurs['bannieres']!=''){
		$liste_bannieres = explode(",",$valeurs[bannieres]);
		$where[] = sql_in('id_banniere',$liste_bannieres);
	}

	$ressource = sql_select('*',  'spip_bannieres_suivi',  $where);

// Nom du fichier

if ($valeurs['debut']!="00/00/0000" || $valeurs['fin']!="00/00/0000" || $valeurs['bannieres']!='')
$partiel = "_partielles";

$fichier = "statistiques_bannieres".$partiel."_du_".date("d-m-Y");

// Format d'export
if($valeurs['format_export'] == 'virgule')
$format = ',';
if($valeurs['format_export'] == 'point-virgule')
$format = ';';
if($valeurs['format_export'] == 'tab')
$format = 'TAB';

// On exporte (fonction plugin bonux)
include_spip('inc/exporter_csv');
inc_exporter_csv_dist($fichier, $ressource, $format);


	return ;
}

?>
