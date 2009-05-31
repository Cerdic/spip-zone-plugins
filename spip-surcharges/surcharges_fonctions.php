<?php


	/**
	 * SPIP-Surcharges
	 *
	 * Copyright (c) 2006-2009 Artégo http://www.artego.fr
	 **/


	function surcharges_filtre_export_csv($texte) {
		$texte = str_replace("\r", "\n", $texte);
		$texte = str_replace("\n\n", "\n", $texte);
		$texte = str_replace("\n\n", "\n", $texte);
		$texte = str_replace("\n", ", ", $texte);
		$texte = str_replace("- ", "", $texte);
		$texte = str_replace('"', '\'', $texte);
		return '"'.$texte.'"';
	}


	function surcharges_filtre_import_csv($texte) {
		include_spip('inc/charsets');
		$charset_source = 'iso-8859-1';
		if (is_utf8($texte))
			$charset_source = 'utf-8';
		$texte = importer_charset($texte, $charset_source);
		$texte = str_replace('"', '', $texte);
		return $texte;
	}
	
	
	function surcharges_exporter_csv($titre, $tableau, $transmettre=true) {
		foreach ($tableau as $ligne) {
			$ligne = array_map('surcharges_filtre_export_csv', $ligne);
			$csv.= implode(';', $ligne)."\n";
		}
		$nom = $titre.'-'.mktime().'.txt';
		$fichier = _DIR_CACHE.$nom;
		$fp = fopen($fichier, 'w');
		$csv = utf8_decode($csv);
		$write = fwrite($fp, $csv);
		fclose($fp);
		if ($transmettre) {
			header("Content-Type: application/csv");
			header("Content-disposition: filename=".$nom);
			$fp = fopen($fichier, 'rb');
			fpassthru($fp);
		}
		return $fichier;
	}
	
	
	function surcharges_importer_csv($fichier_tmp) {
		$nom = 'import-'.mktime().'.txt';
		$fichier = _DIR_CACHE.$nom;
		move_uploaded_file($fichier_tmp, $fichier);
		$handle = fopen($fichier, "r");
		$tableau = array();
		$i = 0;
		if ($handle) {
			while (($data = fgetcsv($handle, 10000, ';', '"')) !== FALSE) {
				$data = array_map('surcharges_filtre_import_csv', $data);
				foreach ($data as $valeur) {
					$tableau[$i][] = $valeur;
				}
				$i++;
			}
			fclose($handle);
		}
		return $tableau;
	}


	/**
	 * balise_MAINTENANT
	 *
	 * @param p
	 * @return YYYY-MM-DD HH:II:SS
	 * @author Pierre Basson
	 **/
	function balise_MAINTENANT($p) {
		$p->code = "calcul_maintenant()";
		$p->interdire_scripts = false;
		return $p;
	}
	

	/**
	 * calcul_maintenant
	 *
	 * @return YYYY-MM-DD HH:II:SS
	 * @author Pierre Basson
	 **/
	function calcul_maintenant() {
		return date('Y-m-d h:i:s');
	}


	/**
	 * traduire_mois
	 *
	 * @param int mois
	 * @return string nom mois
	 * @author Pierre Basson
	 **/
	function traduire_mois($mois) {
		$nom_mois = _T('date_mois_'.intval($mois));
		return $nom_mois;
	}


/** BOUCLE DATES **/

$dates = array(
	"date" => "datetime",
	"debut" => "varchar(7)",
	"fin" => "varchar(7)"
);
$dates_key = array(
	"PRIMARY KEY"	=> "date"
);
$GLOBALS['tables_principales']['spip_dates'] =
	array('field' => &$dates, 'key' => &$dates_key);
$GLOBALS['table_des_tables']['dates'] = 'dates';

function boucle_DATES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

    foreach($boucle->criteres as $critere) {
		if($critere->op!='=') continue;
		$val= calculer_liste($critere->param[1],
		                     array(), $boucles, $boucle->id_parent);

		switch($critere->param[0][0]->texte) {
		case 'debut': $debut= $val; break;
		case 'fin'  : $fin  = $val; break;
		}
	}

  	$liste= "calcule_dates($debut,$fin)";

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= $liste;
	foreach(\$l as \$k) {
		\$Pile[\$SP]['date'] = \$k;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function calcule_dates($debut, $fin) {
	eregi("^(.{4})-(.{2})$", $debut, $regs);
	$annee_debut	= $regs[1];
	$mois_debut		= $regs[2];
	eregi("^(.{4})-(.{2})$", $fin, $regs);
	$annee_fin		= $regs[1];
	$mois_fin		= $regs[2];
	$tableau = array();
	for ($annee = $annee_fin; $annee >= $annee_debut; $annee--) {
		if ($annee == $annee_debut) {
			for ($mois = 12; $mois >= $mois_debut; $mois--) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		} else if ($annee == $annee_fin) {
			for ($mois = $mois_fin; $mois > 0; $mois--) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		} else {
			for ($mois = 12; $mois > 0; $mois--) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		}
	}
	return $tableau;
}


/** BOUCLE ANNEES **/

$annees = array(
	"annee" => "varchar(4)",
	"debut" => "varchar(7)",
	"fin" => "varchar(7)"
);
$annees_key = array(
	"PRIMARY KEY"	=> "date"
);
$GLOBALS['tables_principales']['spip_annees'] =
	array('field' => &$annees, 'key' => &$annees_key);
$GLOBALS['table_des_tables']['annees'] = 'annees';

function boucle_ANNEES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

    foreach($boucle->criteres as $critere) {
		if($critere->op!='=') continue;
		$val= calculer_liste($critere->param[1],
		                     array(), $boucles, $boucle->id_parent);

		switch($critere->param[0][0]->texte) {
		case 'debut': $debut= $val; break;
		case 'fin'  : $fin  = $val; break;
		}
	}

  	$liste= "calcule_annees($debut,$fin)";

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= $liste;
	foreach(\$l as \$k) {
		\$Pile[\$SP]['annee'] = \$k['annee'];
		\$Pile[\$SP]['debut'] = \$k['debut'];
		\$Pile[\$SP]['fin'] = \$k['fin'];
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function calcule_annees($debut, $fin) {
	for ($annee = $fin; $annee >= $debut; $annee--) {
		$tableau[] = array(	'annee' => $annee,
							'debut' => $annee.'-01',
							'fin' => $annee.'-12'
							);
	}
	return $tableau;
}


/** BOUCLE DATES_CROISSANTES **/

$dates_croissantes = array(
	"date" => "datetime",
	"debut" => "varchar(7)",
	"fin" => "varchar(7)"
);
$dates_croissantes_key = array(
	"PRIMARY KEY"	=> "date"
);
$GLOBALS['tables_principales']['spip_dates_croissantes'] =
	array('field' => &$dates_croissantes, 'key' => &$dates_croissantes_key);
$GLOBALS['table_des_tables']['dates_croissantes'] = 'dates_croissantes';

function boucle_DATES_CROISSANTES($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

    foreach($boucle->criteres as $critere) {
		if($critere->op!='=') continue;
		$val= calculer_liste($critere->param[1],
		                     array(), $boucles, $boucle->id_parent);

		switch($critere->param[0][0]->texte) {
		case 'debut': $debut= $val; break;
		case 'fin'  : $fin  = $val; break;
		}
	}

  	$liste= "calcule_dates_croissantes($debut,$fin)";

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= $liste;
	foreach(\$l as \$k) {
		\$Pile[\$SP]['date'] = \$k;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function calcule_dates_croissantes($debut, $fin) {
	eregi("^(.{4})-(.{2})$", $debut, $regs);
	$annee_debut	= $regs[1];
	$mois_debut		= $regs[2];
	eregi("^(.{4})-(.{2})$", $fin, $regs);
	$annee_fin		= $regs[1];
	$mois_fin		= $regs[2];
	$tableau = array();
	for ($annee = $annee_debut; $annee <= $annee_fin; $annee++) {
		if ($annee == $annee_debut) {
			for ($mois = $mois_debut; $mois <= 12; $mois++) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		} else if ($annee == $annee_fin) {
			for ($mois = 1; $mois <= $mois_fin; $mois++) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		} else {
			for ($mois = 1; $mois <= 12; $mois++) {
				$timestamp = mktime (0, 0, 0, $mois, 1, $annee);
				$tableau[] = date('Y-m-d h:i:s', $timestamp);
			}
		}
	}
	return $tableau;
}



/** BOUCLE ALPHABET **/

$alphabet = array(
	"lettre" => "char(1)"
);
$alphabet_key = array(
	"PRIMARY KEY"	=> "lettre"
);
$GLOBALS['tables_principales']['spip_alphabet'] =
	array('field' => &$alphabet, 'key' => &$alphabet_key);
$GLOBALS['table_des_tables']['alphabet'] = 'alphabet';

function boucle_ALPHABET($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];

	if (count($boucle->separateur))
	  $code_sep= ("'". ereg_replace("'","\'",join('',$boucle->separateur)) ."'");
	else
	  $code_sep="''";

  	$liste= "calcule_alphabet()";

	$code=<<<CODE
	\$SP++;
	\$code=array();
	\$l= $liste;
	foreach(\$l as \$k) {
		\$Pile[\$SP]['lettre'] = \$k;
		\$code[]=$boucle->return;
	}
	\$t0= join($code_sep, \$code);
	return \$t0;
CODE;

	return $code;
}

function calcule_alphabet() {
	return array('a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');
}


?>