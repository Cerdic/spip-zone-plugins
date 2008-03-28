<?php

// -------------------------------
// Main 
// ------------------------------
function exec_convertisseur(){

	include_spip("inc/vieilles_defs");
	define('_SIGNALER_ECHOS', false); // on sait qu'on est vieux-code
	if (!function_exists('sql_quote')) {
	  function sql_quote($q) { return _q($q); }
	}

	
	include_spip("inc/presentation");
	include_spip('inc/convertisseur');

	global $spip_lang_right;
	global $log;

	global $conv_formats;
	global $conv_functions_pre;

  $conv_in  = "";  
  $conv_out = "";

  // check rights (utile ?)
  global $connect_statut;
	global $connect_toutes_rubriques;
  if ($connect_statut != '0minirezo') {    
		echo debut_page(_T("convertisseur:convertir_titre"), "naviguer", "plugin");
		echo _T('avis_non_acces_page');
		echo fin_page();
		exit;
	}



	// ---------------------------------------------------------------------
	// Action ?
	// ---------------------------------------------------------------------

	if (isset($_POST['conv_in'])) {

		$conv_textes = array();

		// upload ?
		if (!count($_FILES)) {
			$conv_textes[] = _request('conv_in');
		}
		else {
			include_spip('inc/getdocument');
			include_spip('inc/pclzip');
			include_spip('inc/invalideur');  # pour purger_repertoire()
			foreach ($_FILES as $file) {
				chdir('..'); ## dirty
				$a = deplacer_fichier_upload($file['tmp_name'],
					'tmp/convertisseur.tmp');
				chdir('ecrire/');
				if (!$a) next;

				// traitement specifique des .zip : on les
				// eclate en autant de sources
				if ($file['type'] == 'application/x-zip-compressed'
				OR preg_match(',\.zip$,', $file['name'])) {
					$zip = new PclZip('../tmp/convertisseur.tmp');
					if (!$list = $zip->listContent()) {
						$log = 'erreur zip';
					} else {
						$tmp = sous_repertoire(_DIR_TMP,'convertisseur');
						if ($tmp == _DIR_TMP) die('erreur creation repertoire temporaire');
						define('_tmp_dir', $tmp);
						purger_repertoire($tmp);
						// extraire dans le temporaire
						$zip->extract(
							PCLZIP_OPT_PATH, _tmp_dir,
							PCLZIP_CB_PRE_EXTRACT,
							'callback_admissibles'
						);
						// lire les fichiers temporaires
						foreach (glob(_tmp_dir.'*') as $f) {
							if (lire_fichier($f, $tmp)
							AND strlen(trim($tmp)))
								$conv_textes[$f] = $tmp;
						}
					}
				}

				// fichier simple
				else
				if (lire_fichier('../tmp/convertisseur.tmp', $tmp))
					$conv_textes[$file['name']] = $tmp;

			}
		}

		// convertir le charset ?
		$convert_charset = (_request('convert_charset') == 'true');
		$GLOBALS['auteur_session']['prefs']['convertisseur_cvcharset'] = $convert_charset;
		if ($convert_charset) {
			include_spip('inc/charsets');
			foreach ($conv_textes as $i => $t)
				$conv_textes[$i] = importer_charset($t, $charset);
		}

		// detection du format
		if (isset($_POST['format'])) {
			$format = trim(strip_tags($_POST['format']));

			// on le memorise dans les prefs de l'auteur
			// pour permettre de proposer le meme la prochaine fois
			$GLOBALS['auteur_session']['prefs']['convertisseur_format'] = $format;

			// enregistrer les prefs de l'auteur
			spip_query('UPDATE spip_auteurs SET prefs='
				._q(serialize($GLOBALS['auteur_session']['prefs']))
				.' WHERE id_auteur='.intval($GLOBALS['auteur_session']['id_auteur'])
			);


			// Traitement et conversion de chaque texte soumis, dans un tableau
			$out = array();
			foreach ($conv_textes as $f => $conv_in) {
				$tmp = conversion_format($conv_in, $format);
				$out[$f] = nettoyer_format($tmp);
				if ($id_rubrique = intval(_request('id_parent')))
					$id_article = inserer_conversion($out[$f], $id_rubrique, $f);
					$article[$f] = $id_article;
			}

		}
	} // fin action


  // ---------------------------------------------------------------------------
  // HTML output 
  // ---------------------------------------------------------------------------
	echo debut_page(_T("convertisseur:convertir_titre"), "naviguer", "plugin");	
  debut_gauche();
	echo debut_boite_info();
	echo _T("convertisseur:convertir_desc");
	echo fin_boite_info();
	
	echo debut_droite();
	echo $log;
	echo "<form method='post' enctype='multipart/form-data'>\n";

	if ($out) {
		echo "<div style='background-color:#E6ECF9;padding:8px 3px;margin-bottom:5px'>"._T("convertisseur:convertir_en");
	   if (isset($conv_formats[$format])) echo "<strong>"._T("convertisseur:$format")."</strong>\n";

		foreach ($out as $f => $texte) {
			if ($f) echo "<h2>".basename($f)."</h2>\n";
			echo "<textarea name='conv_out' cols='65' rows='12'>".entites_html($texte)."</textarea><br />\n";

			if (isset($article[$f]))
				echo "<div>article ".$article[$f].": <a href='".generer_url_ecrire('articles_edit', 'id_article='.$article[$f])."'>&#233;diter</a></div>\n";
		}

		echo "</div>\n";
	}

	echo "<h3>"._L("Votre texte &agrave; convertir :")."</h3>\n";


	// format memorise pour avoir le selected dans le menu
	if (!$format)
		$format = $GLOBALS['auteur_session']['prefs']['convertisseur_format'];
	echo "<p>";
	echo _T("convertisseur:from");
	echo "<select name='format'>\n";
	foreach ($conv_formats as $k=>$val) {  
		if ($format==$k)
			$selected = " selected='selected'";
		else
			$selected = "";
		echo "<option value='$k'$selected>"._T("convertisseur:$k")."</option>\n";
	}
	echo "</select></p>\n";


	echo _L("Copiez-le ci-dessous :")."<br />\n";

	$conv_in = entites_html(substr($conv_in,0,40000));
	echo "<textarea name='conv_in' cols='65' rows='12'>$conv_in</textarea><br />\n";


	echo "<div style='float:$spip_lang_right;'>";
	echo _L("ou choisissez un fichier :")."<br />\n";
	echo "<input type='file' name='upload' />\n";
	echo "</div>\n";

	echo "<br style='clear:both;' />\n";

	echo "<p align='right'><small>Il est possible de convertir plusieurs fichiers en une seule fois, en les regroupant dans une archive ZIP</small></p>\n";

	echo "<h5>"._L('Options:')."</h5>\n";

	$checked = $GLOBALS['auteur_session']['prefs']['convertisseur_cvcharset']
		? ' checked="checked"'
		: '';
	echo "<label><input type='checkbox' value='true' name='convert_charset'$checked
	/>"._L("convertir en UTF-8")."\n";
	echo "</label>\n";


	// Ajouter sous forme d'article dans la rubrique
	if (
	function_exists('charger_fonction')
	AND $chercher_rubrique = charger_fonction('chercher_rubrique', 'inc', true)) {
		echo "<p/><div><label>Choisissez une rubrique si vous voulez insérer le résultat de la conversion dans un nouvel article sur le site :";
		echo $chercher_rubrique(null,'rubrique',null);
		echo "</label></div>\n";
	}

  echo "<p style='float:right;'><input type='submit' value='". _T("convertisseur:convertir")."'></p>\n";   
  echo "</form>\n"; 

  
  echo fin_page();
}
?>
