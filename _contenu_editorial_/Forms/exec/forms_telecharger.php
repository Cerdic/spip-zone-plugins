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

function csv_ligne($ligne,$delim=',') {
	return join($delim, array_map('csv_champ', $ligne))."\r\n";
}

function formater_reponse($ligne, $structure, $valeurs,$delim=',') {
	static $groupes, $mots;

	// Prendre les differents champs dans l'ordre
	foreach ($structure as $index => $t) {
		if (!$v = $valeurs[$t['code']]) {
			$ligne[] = "";
			continue;
		}
		$ligne[] = strval(join(', ', $v));
	}
	return csv_ligne($ligne,$delim);
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
		$query = "SELECT structure FROM spip_forms WHERE id_form=$id_form";
		$result = spip_query($query);
		if ($row = spip_fetch_array($result)) {
			$structure = unserialize($row['structure']);
		}
		$ok = false;
		foreach ($structure as $index => $t) {
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


	$delim = _request('delim');
	if ($delim == 'TAB') $delim = "\t";

	$retour = _request('retour');
	if ($retour!==NULL)
		$retour = urldecode($retour);
	else
		$retour = generer_url_ecrire('forms_tous');
	
	$titre = _L("Telecharger les r&eacute;ponses");
	if (!$delim){
		$icone = "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png";
	
		debut_page($titre, "documents", "forms");
		debut_gauche();
	
		echo "<br /><br />\n";
		debut_droite();
	
		debut_cadre_relief($icone);
		gros_titre($titre);
		echo "<br />\n";
		echo _L("Format du fichier&nbsp;:");
		echo "<br />\n";
		// Extrait de la table en commencant par les dernieres maj
		echo generer_url_post_ecrire('forms_telecharger');
		echo form_hidden(self());
		echo "<select name='delim'>\n";
		echo "<option value=','>"._L("CSV classique (,)")."</option>\n";
		echo "<option value=';'>"._L("CSV pour Excel (;)")."</option>\n";
		echo "<option value='TAB'>"._L("CSV avec tabulations")."</option>\n";
		echo "</select>";
		echo "<br /><br />\n";
		echo "<input type='submit' name='ok' value='T&eacute;l&eacute;charger' />\n";
	
		fin_cadre_relief();
	
	
		//
		// Icones retour
		//
		if ($retour) {
			echo "<br />\n";
			echo "<div align='$spip_lang_right'>";
			icone(_T('icone_retour'), $retour, $icone, "rien.gif");
			echo "</div>\n";
		}
		fin_page();
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
		$structure = unserialize($row['structure']);
	}

	$charset = $GLOBALS['meta']['charset'];
	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

	$s = '';

	// Preparer la table de traduction code->valeur
	$trans = array();
	$types = array();
	foreach ($structure as $index => $t) {
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
	foreach ($structure as $index => $t) {
		$ligne[] = textebrut(typo($t['nom']));
	}
	$s .= csv_ligne($ligne,$delim);


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
				$s .= formater_reponse($ligne, $structure, $valeurs,$delim);
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
			//$valeurs[$champ][] = $GLOBALS['meta']['adresse_site']."/ecrire/forms_telecharger.php?id_reponse=$id_reponse&champ=$champ";
			$valeurs[$champ][] = 'fichiers/'.basename($row['valeur']);
		}
		else if ($v = $trans[$champ][$row['valeur']])
			$valeurs[$champ][] = $v;
		else
			$valeurs[$champ][] = $row['valeur'];
	}

	// Ne pas oublier la derniere reponse
	if ($id_reponse) {
		$s .= formater_reponse($ligne, $structure, $valeurs,$delim);
	}

	// Excel ?
	if ($delim == ',')
		$extension = 'csv';
	else {
		$extension = 'xls';
		# Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
		include_spip('inc/charsets');
		$s = unicode2charset(charset2unicode($s), 'iso-8859-1');
		$charset = 'iso-8859-1';
	}

	if (!count($fichiers)) {
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
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
	$csv = "data/$filename.$extension";

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
