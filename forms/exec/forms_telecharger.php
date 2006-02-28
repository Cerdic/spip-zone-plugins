<?php
/*
 * forms
 * version plug-in de spip_form
 *
 * Auteur :
 * Antoine Pitrou
 * adaptation en 182e puis plugin par cedric.morin@yterium.com
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */


include_ecrire('inc_forms');
include_ecrire("inc_charsets");
include_ecrire("inc_presentation");

function csv_champ($champ) {
	$champ = preg_replace(',[\s]+,', ' ', $champ);
	$champ = str_replace(',",', '""', $champ);
	return '"'.$champ.'"';
}

function csv_ligne($ligne) {
	return join(',', array_map('csv_champ', $ligne))."\r\n";
}

function formater_reponse($ligne, $schema, $valeurs) {
	static $groupes, $mots;

	// Prendre les differents champs dans l'ordre
	foreach ($schema as $index => $t) {
		if (!$v = $valeurs[$t['code']]) {
			$ligne[] = "";
			continue;
		}
		$ligne[] = strval(join(', ', $v));
	}
	return csv_ligne($ligne);
}

function acces_interdit() {
	debut_page(_T('avis_acces_interdit'), "documents", "forms");
	debut_gauche();
	debut_droite();
	echo "<strong>"._T('avis_acces_interdit')."</strong>";
	fin_page();
	exit;
}


//
// Telechargement d'un fichier particulier
//
function exec_forms_telecharger(){
	global $id_reponse;
	global $id_form;

	if ($id_reponse = intval($id_reponse) AND $champ) {
		$query = "SELECT id_form FROM spip_reponses WHERE id_reponse=$id_reponse";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$id_form = $row['id_form'];
		}
		if (!$id_form || !Forms_form_administrable($id_form)) {
			acces_interdit();
		}
		$query = "SELECT schema FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$schema = unserialize($row['schema']);
		}
		$ok = false;
		foreach ($schema as $index => $t) {
			if ($t['code'] == $champ) {
				$ok = ($t['type'] == 'fichier');
				break;
			}
		}
		if (!$ok) {
			acces_interdit();
		}
		$query = "SELECT valeur FROM spip_reponses_champs WHERE id_reponse=$id_reponse AND champ='$champ'";
		$result = spip_query($query);
		list($fichier) = spip_fetch_array($result);
		if (is_int(strpos($fichier, "..")) || !preg_match(',^IMG/,', $fichier)) {
			acces_interdit();
		}
		$filename = basename($fichier);
		$mimetype = "";
		if (preg_match(',\.([^\.]+)$,', $fichier, $r)) {
			$ext = $r[1];
			$query = "SELECT * FROM spip_types_documents WHERE extension='".addslashes($ext)."'";
			$result = spip_query($query);
			if ($row = spip_fetch_array($result)) {
				$mimetype = $row['mime_type'];
			}
		}
		if (!$mimetype) $mimetype = "application/octet-stream";
		$chemin = "../".$fichier;
		if (!is_file($chemin)) {
			acces_interdit();
		}

		Header("Content-Type: $mimetype");
		Header("Content-Disposition: inline; filename=$filename");
		Header("Content-Length :".filesize($chemin));
		readfile($chemin);
		exit;
	}



	//
	// Telechargement du tableau de reponses au format CSV
	//
	$id_form = intval($id_form);
	if ($id_form) {
		$query = "SELECT COUNT(*) FROM spip_reponses WHERE id_form=$id_form AND statut='valide'";
		$result = spip_query($query);
		list($nb_reponses) = spip_fetch_array($result);
	}
	else $nb_reponses = 0;

	if (!$id_form || !Forms_form_administrable($id_form)) {
		acces_interdit();
	}

	$query = "SELECT * FROM spip_forms WHERE id_form=$id_form";
	$result = spip_query($query);
	if ($row = spip_fetch_array($result)) {
		$id_form = $row['id_form'];
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$sondage = $row['sondage'];
		$schema = unserialize($row['schema']);
	}

	$charset = lire_meta('charset');
	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

	$s = '';

	// Preparer la table de traduction code->valeur
	$trans = array();
	$types = array();
	foreach ($schema as $index => $t) {
		$code = $t['code'];
		$type = $t['type'];
		$types[$code] = $type;
		$trans[$code] = array();

		if ($type == 'select' || $type == 'multiple') {
			$trans[$code] = $t['type_ext'];
		}
		else if ($type == 'mot') {
			$id_groupe = intval($t['type_ext']['id_groupe']);
			$query = "SELECT id_mot, titre FROM spip_mots WHERE id_groupe=$id_groupe";
			$result = spip_query($query);
			while ($row = spip_fetch_array($result)) {
				$id_mot = $row['id_mot'];
				$titre = $row['titre'];
				$trans[$code][$id_mot] = trim(textebrut(typo($titre)));
			}
		}
	}

	// Une premiere ligne avec les noms de champs
	$ligne = array();
	$ligne[] = _L("Date");
	foreach ($schema as $index => $t) {
		$ligne[] = textebrut(typo($t['nom']));
	}
	$s .= csv_ligne($ligne);


	// Ensuite les reponses
	$fichier = array();
	$id_reponse = 0;
	$query = "SELECT r.id_reponse, r.date, c.champ, c.valeur ".
		"FROM spip_reponses AS r LEFT JOIN spip_reponses_champs AS c USING (id_reponse) ".
		"WHERE id_form=$id_form AND statut='valide' AND c.id_reponse IS NOT NULL ".
		"ORDER BY date, r.id_reponse";
	$result = spip_query($query);
	while ($row = spip_fetch_array($result)) {
		if ($id_reponse != $row['id_reponse']) {
			if ($id_reponse) {
				$s .= formater_reponse($ligne, $schema, $valeurs);
			}
			$id_reponse = $row['id_reponse'];
			$ligne = array();
			$valeurs = array();
			$date = $row['date'];
			$ligne[] = jour($date).'/'.mois($date).'/'.annee($date);
		}
		$champ = $row['champ'];
		if ($types[$champ] == 'fichier') {
			$fichiers[] = $row['valeur'];
			//$valeurs[$champ][] = lire_meta('adresse_site')."/ecrire/forms_telecharger.php?id_reponse=$id_reponse&champ=$champ";
			$valeurs[$champ][] = 'fichiers/'.basename($row['valeur']);
		}
		else if ($v = $trans[$champ][$row['valeur']])
			$valeurs[$champ][] = $v;
		else
			$valeurs[$champ][] = $row['valeur'];
	}

	// Ne pas oublier la derniere reponse
	if ($id_reponse) {
		$s .= formater_reponse($ligne, $schema, $valeurs);
	}

	if (!count($fichiers)) {
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.csv");
		//Header("Content-Type: text/plain; charset=$charset");
		Header("Content-Length: ".strlen($s));
		echo $s;
		exit;
	}

	//
	// S'il y a des fichiers joints, creer un ZIP
	//
	include_ecrire("pclzip.lib");
	include_ecrire("inc_session");

	$zip = "data/form".$id_form."_".rand().".zip";
	$csv = "data/$filename.csv";

	$f = fopen($csv, "wb");
	fwrite($f, $s);
	fclose($f);

	$chemin = "../";
	$fichiers = $chemin.join(",$chemin", $fichiers);

	$archive = new PclZip($zip);
	$archive->add($csv, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_ADD_PATH, $filename);
	$archive->add($fichiers, PCLZIP_OPT_REMOVE_ALL_PATH, PCLZIP_OPT_ADD_PATH, $filename.'/fichiers');

	Header("Content-Type: application/zip");
	Header("Content-Disposition: attachment; filename=$filename.zip");
	Header("Content-Length: ".filesize($zip));
	readfile($zip);

	@unlink($csv);
	@unlink($zip);

	exit;
}
?>
