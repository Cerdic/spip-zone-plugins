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


?>