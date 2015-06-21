<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function exporter_tableau($tableau, $colonnes, $separateur="|") {
	if ($tableau) {
		$contenu = '';
		if ($colonnes) {
			foreach ($colonnes as $_colonne) {
				$contenu .= $_colonne . $separateur;
			}
			$contenu = rtrim($contenu, $separateur) . "\n";
		}

		foreach ($tableau as $_ligne) {
			for ($i=0; $i <= count($colonnes); $i++ ) {
				$contenu .= $_ligne[$i] . $separateur;
			}
			$contenu = rtrim($contenu, $separateur) . "\n";
		}

		$dossier_csv = sous_repertoire(_DIR_TMP, 'langonet');
		$dossier_csv = sous_repertoire($dossier_csv, 'export');
		$fichier_csv = $dossier_csv . 'occurrences_' . date("Ymd_His") . '.csv';
		if ($contenu)
			ecrire_fichier($fichier_csv, $contenu);
	}
}

?>