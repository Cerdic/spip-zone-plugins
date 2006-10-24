<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */


include_spip('inc/forms');
include_spip("inc/charsets");
include_spip("inc/presentation");

function csv_champ($champ) {
	$champ = preg_replace(',[\s]+,', ' ', $champ);
	$champ = str_replace(',",', '""', $champ);
	return '"'.$champ.'"';
}

function Forms_formater_ligne_csv($ligne,$delim=',') {
	$out = "";
	foreach($ligne as $val){
		if (is_array($val))
			foreach($val as $v) $out .= csv_champ($v).$delim;
		else
			$out .= csv_champ($val).$delim;
	}
	$out = substr($out,0,strlen($out)-strlen($delim))."\r\n";
	return $out;
}

function Forms_formater_ligne($ligne,$format,$separateur){
	if (function_exists($f = "Forms_formater_ligne_$format"))
		return $f($ligne,$separateur);
	else
		return Forms_formater_ligne_csv($ligne,$separateur);
}

function Forms_formater_reponse($ligne, $valeurs, $structure,$format,$separateur) {
	// Prendre les differents champs dans l'ordre
	foreach ($structure as $champ => $t) {
		if (!isset($valeurs[$champ])) {
			$ligne[$champ] = "";
		}
		else{
			$v = $valeurs[$champ];
			if ($t['type']=='multiple'){
				// pour un choix multiple on cree une colonne par reponse potentielle
				foreach($t['choix'] as $choix=>$titre)
					if (in_array($choix,$v))
						$ligne[$champ][$choix] = strval($titre);
					else
						$ligne[$champ][$choix] = "";
			}
			else
				$ligne[] = strval(join(', ', $v));
		}
	}
	return Forms_formater_ligne($ligne,$format,$separateur);
}

function Forms_formater_les_reponses($id_form, $format, $separateur, $head=true, $traduit=true){
	//
	// Telechargement du tableau de reponses au format CSV ou autre
	// le support d'un autre format ne necessite que l'implementation de la fonction
	// Forms_formater_ligne_xxx avec xxx le nom du format
	//
	$nb_reponses = 0;
	$row = spip_fetch_array(spip_query("SELECT COUNT(*) AS tot FROM spip_forms_donnees WHERE id_form="._q($id_form)." AND statut='valide'"));
	if ($row)	$nb_reponses = $row['tot'];

	if (!$id_form || !Forms_form_administrable($id_form))
		acces_interdit();

	$result = spip_query("SELECT * FROM spip_forms WHERE id_form="._q($id_form));
	if ($row = spip_fetch_array($result)) {
		$titre = $row['titre'];
		$descriptif = $row['descriptif'];
		$type_form = $row['type_form'];
	}

	$charset = $GLOBALS['meta']['charset'];
	$filename = preg_replace(',[^-_\w]+,', '_', translitteration(textebrut(typo($titre))));

	$s = '';

	// Preparer la table de traduction code->valeur & mise en table de la structure pour eviter des requettes
	// a chaque ligne
	$structure = array();
	$trans = array();
	$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." ORDER BY rang");
	while ($row = spip_fetch_array($res)){
		$type = $row['type'];
		$champ = $row['champ'];
		$structure[$champ]=array('type'=>$row['type'],'titre'=>$row['titre']);
		if (($type=='select') OR ($type='multiple')){
			$res2 = spip_query("SELECT * FROM spip_forms_champs_choix WHERE id_form="._q($id_form)." AND champ="._q($champ)." ORDER BY rang");
			while ($row2 = spip_fetch_array($res2))
				$structure[$champ]['choix'][$row2['choix']] = trim(textebrut(typo($row2['titre'])));
		}
		else if ($type == 'mot') {
			$id_groupe = intval($row['extra_info']);
			$res2 = spip_query("SELECT id_mot, titre FROM spip_mots WHERE id_groupe="._q($id_groupe));
			while ($row2 = spip_fetch_array($res2)) {
				$structure[$champ]['choix'][$row2['id_mot']] = trim(textebrut(typo($row2['titre'])));
			}
		}
	}

	if ($head) {
		// Une premiere ligne avec les noms de champs
		$ligne = array();
		$ligne[] = _T("forms:date");
		$ligne[] = _T("forms:page");
		foreach ($structure as $champ => $t) {
			$ligne1[] = $champ;
			$ligne2[] = textebrut(typo($t['titre']));
			if ($t['type']=='multiple'){
				// pour un choix multiple on cree une colonne par reponse potentielle
				$choix = $t['choix'];
				foreach($t['choix'] as $choix=> $v){
					$ligne1[] = $choix;
					$ligne2[] = textebrut(typo($v));
				}
			}
		}
		$s .= Forms_formater_ligne($ligne1,$format,$separateur);
		if ($traduit)
			$s .= Forms_formater_ligne($ligne2,$format,$separateur);
	}

	// Ensuite les reponses
	$fichier = array();
	$id_donnee = 0;
	$result = spip_query("SELECT r.id_donnee, r.date,r.url, c.champ, c.valeur ".
		"FROM spip_forms_donnees AS r LEFT JOIN spip_forms_donnees_champs AS c USING (id_donnee) ".
		"WHERE id_form="._q($id_form)." AND statut='valide' AND c.id_donnee IS NOT NULL ".
		"ORDER BY date, r.id_donnee");
	while ($row = spip_fetch_array($result)) {
		if ($id_donnee != $row['id_donnee']) {
			if ($id_donnee)
				$s .= Forms_formater_reponse($ligne,$valeurs,$structure,$format,$separateur);
			$id_donnee = $row['id_donnee'];
			$date = $row['date'];
			$ligne = array();
			$ligne[] = jour($date).'/'.mois($date).'/'.annee($date);
			$ligne[] = str_replace("&amp;","&",$row['url']);
			$valeurs = array();
		}
		$champ = $row['champ'];
		if ($structure[$champ]['type'] == 'fichier') {
			$fichiers[] = $row['valeur'];
			$valeurs[$champ][] = 'fichiers/'.basename($row['valeur']);
		}
		else {
			$v = $row['valeur'];
			if ($traduit AND isset($structure[$champ][$v])) $v = $structure[$champ][$v];
			else if ($traduit AND isset($structure[$champ]['choix'][$v])) $v = $structure[$champ]['choix'][$v];
			$valeurs[$champ][] = $v;
		}
	}

	// Ne pas oublier la derniere reponse
	if ($id_donnee)
		$s .= Forms_formater_reponse($ligne,$valeurs,$structure,$format,$separateur);
	return $s;
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
	$id_donnee = _request('id_donnee');
	$id_form = _request('id_form');
	$champ = _request('champ');

	if ($id_donnee = intval($id_donnee) AND $champ) {
		$res = spip_query("SELECT id_form FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
		if ($row = spip_fetch_array($res))
			$id_form = $row['id_form'];
		if (!$id_form || !Forms_form_administrable($id_form))
			acces_interdit();
		$res = spip_query("SELECT * FROM spip_forms_champs WHERE id_form="._q($id_form)." AND type='fichier' AND champ="._q($champ));
		if (!$row = spip_fetch_array($res))
			acces_interdit();
		$row = spip_fetch_array(spip_query("SELECT valeur FROM spip_forms_donnees_champs WHERE id_donnee="._q($id_donnee)." AND champ="._q($champ)));
		if (!$row)	acces_interdit();
		
		$fichier = $row['valeur'];
		if ((strpos($fichier, "..")!==FALSE) || !preg_match(',^IMG/,', $fichier))
			acces_interdit();

		$filename = basename($fichier);
		$mimetype = "";
		if (preg_match(',\.([^\.]+)$,', $fichier, $r)) {
			$ext = $r[1];
			$result = spip_query("SELECT * FROM spip_types_documents WHERE extension="._q($ext));
			if ($row = spip_fetch_array($result))
				$mimetype = $row['mime_type'];
		}
		if (!$mimetype) $mimetype = "application/octet-stream";
		$chemin = "../".$fichier;
		if (!is_file($chemin))
			acces_interdit();

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
	
	$titre = _T("forms:telecharger_reponses");
	if (!$delim){
		$icone = "../"._DIR_PLUGIN_FORMS."/img_pack/form-24.png";
	
		debut_page($titre, "documents", "forms");
		debut_gauche();
	
		echo "<br /><br />\n";
		debut_droite();
	
		debut_cadre_relief($icone);
		gros_titre($titre);
		echo "<br />\n";
		echo _T("forms:format_fichier");
		echo "<br />\n";
		// Extrait de la table en commencant par les dernieres maj
		echo generer_url_post_ecrire('forms_telecharger');
		echo form_hidden(self());
		echo "<select name='delim'>\n";
		echo "<option value=','>"._T("forms:csv_classique")."</option>\n";
		echo "<option value=';'>"._T("forms:csv_excel")."</option>\n";
		echo "<option value='TAB'>"._T("forms:csv_tab")."</option>\n";
		echo "</select>";
		echo "<br /><br />\n";
		echo "<input type='submit' name='ok' value='"._T("forms:telecharger")."' />\n";
	
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

	$out = Forms_formater_les_reponses($id_form, "csv", $delim, $head=true, $traduit=true);

	// Excel ?
	if ($delim == ',')
		$extension = 'csv';
	else {
		$extension = 'xls';
		# Excel n'accepte pas l'utf-8 ni les entites html... on fait quoi?
		include_spip('inc/charsets');
		$out = unicode2charset(charset2unicode($s), 'iso-8859-1');
		$charset = 'iso-8859-1';
	}

	if (!count($fichiers)) {
		Header("Content-Type: text/comma-separated-values; charset=$charset");
		Header("Content-Disposition: attachment; filename=$filename.$extension");
		//Header("Content-Type: text/plain; charset=$charset");
		Header("Content-Length: ".strlen($s));
		echo $out;
	} 
	else {
		//
		// S'il y a des fichiers joints, creer un ZIP
		//
		include_spip("inc/pclzip");
		include_spip("inc/session");
	
		$zip = _DIR_TMP."form".$id_form."_".rand().".zip";
		$csv = _DIR_TMP."$filename.$extension";
	
		$f = fopen($csv, "wb");
		fwrite($f, $out);
		fclose($f);
	
		$chemin = _DIR_RACINE;
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
	}
}
?>