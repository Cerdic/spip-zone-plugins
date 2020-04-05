<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// -------------------------------
// Main 
// ------------------------------
function exec_convertisseur() {
	include_spip('inc/presentation');
	include_spip('inc/convertisseur');

	global $spip_lang_right;
	global $log;
	global $spip_version_branche;

	global $conv_formats;
	global $conv_functions_pre;

	$conv_in = '';
	$conv_out = '';

	// convertir le charset ?
	$convert_charset = (_request('convert_charset') == 'true');
	$GLOBALS['auteur_session']['prefs']['convertisseur_cvcharset'] = ($convert_charset) ? 'oui' : 'non';


	// check rights (utile ?)
	global $connect_statut;
	global $connect_toutes_rubriques;
	if ($connect_statut != '0minirezo') {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('convertisseur:convertir_titre'), _T('convertisseur:convertir_titre'), _T('convertisseur:convertir_titre'));
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}


	// ---------------------------------------------------------------------
	// Action ?
	// ---------------------------------------------------------------------

	$out = array();
	$format = '';
	if (isset($_POST['conv_in'])) {

		$conv_textes = array();

		// upload ?
		if (count($_FILES)) {
			include_spip('inc/getdocument');
			include_spip('inc/pclzip');
			include_spip('inc/invalideur');  # pour purger_repertoire()
			foreach ($_FILES as $file) {
				if ($file['size']) {
					chdir('..'); ## dirty
					$a = deplacer_fichier_upload($file['tmp_name'],
												 'tmp/convertisseur.tmp');
					chdir('ecrire/');
					if (!$a) {
						next;
					}

					// traitement specifique des .zip : on les
					// eclate en autant de sources
					if ($file['type'] == 'application/x-zip-compressed'
						or preg_match(',\.zip$,', $file['name'])
					) {
						$zip = new PclZip('../tmp/convertisseur.tmp');
						if (!$list = $zip->listContent()) {
							$log = 'erreur zip';
						} else {
							$tmp = sous_repertoire(_DIR_TMP, 'convertisseur');
							if ($tmp == _DIR_TMP) {
								die('erreur creation repertoire temporaire');
							}
							define('_tmp_dir', $tmp);
							purger_repertoire($tmp);
							// extraire dans le temporaire
							$zip->extract(
								PCLZIP_OPT_PATH, _tmp_dir,
								PCLZIP_CB_PRE_EXTRACT,
								'callback_admissibles'
							);
							// lire les fichiers temporaires
							foreach (glob(_tmp_dir . '*') as $f) {
								if (lire_fichier($f, $tmp)
									and strlen(trim($tmp))
								) {
									$conv_textes[$f] = $tmp;
								}
							}
						}
					} // fichier simple
					elseif (lire_fichier('../tmp/convertisseur.tmp', $tmp)) {
						$conv_textes[$file['name']] = $tmp;
					}
				}
			}
		}

		// Pas de fichier : on regarde le POST
		if (!count($conv_textes)) {
			$conv_textes[] = _request('conv_in');
		}

		if ($convert_charset) {
			include_spip('inc/charsets');
			foreach ($conv_textes as $i => $t) {
				$conv_textes[$i] = importer_charset($t, $charset);
			}
		}

		// detection du format
		if (isset($_POST['format'])) {
			$format = trim(strip_tags($_POST['format']));

			// on le memorise dans les prefs de l'auteur
			// pour permettre de proposer le meme la prochaine fois
			$GLOBALS['auteur_session']['prefs']['convertisseur_format'] = $format;

			// Traitement et conversion de chaque texte soumis, dans un tableau
			foreach ($conv_textes as $f => $conv_in) {
				$tmp = conversion_format($conv_in, $format);
				$out[$f] = nettoyer_format($tmp);
				if ($id_rubrique = intval(_request('id_parent'))) {
					$id_article = inserer_conversion($out[$f], $id_rubrique, $f);
				}
				$article[$f] = $id_article;
			}
			if (is_string($conv_formats[$format])) {
				$conv_in = '';
			}             // sur les formats extract, ne pas retourner texte intro (fichier binaire)


		}


		// enregistrer les prefs de l'auteur
		spip_query('UPDATE spip_auteurs SET prefs='
				   . _q(serialize($GLOBALS['auteur_session']['prefs']))
				   . ' WHERE id_auteur=' . intval($GLOBALS['auteur_session']['id_auteur'])
		);

	} // fin action


	// ---------------------------------------------------------------------------
	// HTML output
	// ---------------------------------------------------------------------------
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('convertisseur:convertir_titre'), _T('convertisseur:convertir_titre'), _T('convertisseur:convertir_titre'));
	echo debut_gauche('', true);
	echo debut_boite_info(true) . _T('convertisseur:convertir_desc');
	echo fin_boite_info(true);

	echo debut_droite('', true);


	echo $log;
	echo "<form method='post' enctype='multipart/form-data'>\n";

	if ($out) {
		echo "<div style='background-color:#E6ECF9;padding:8px 3px;margin-bottom:5px'>" . _T('convertisseur:convertir_en');
		if (isset($conv_formats[$format])) {
			echo '<strong>' . _T("convertisseur:$format") . "</strong>\n";
		}

		foreach ($out as $f => $texte) {
			if ($f) {
				echo '<h2>' . basename($f) . "</h2>\n";
			}
			echo "<textarea name='conv_out' cols='65' rows='12'>" . entites_html($texte) . "</textarea><br />\n";

			if (isset($article[$f])) {
				// spip 3
				if ($spip_version_branche > '3') {
					echo '<div>article ' . $article[$f] . ": <a href='" . generer_url_ecrire('article_edit', 'id_article=' . $article[$f]) . "'>&#233;diter</a></div>\n";
				} else {
					echo '<div>article ' . $article[$f] . ": <a href='" . generer_url_ecrire('articles_edit', 'id_article=' . $article[$f]) . "'>&#233;diter</a></div>\n";
				}
			}
		}

		echo "</div>\n";
	}

	echo '<h3>' . _T('convertisseur:texte_a_convertir') . "</h3>\n";

	// format memorise pour avoir le selected dans le menu
	if (!$format) {
		$format = isset($GLOBALS['auteur_session']['prefs']['convertisseur_format']) ? $GLOBALS['auteur_session']['prefs']['convertisseur_format'] : '';
	}
	echo '<p>';
	echo _T('convertisseur:from');
	echo "<select name='format'>\n";
	foreach ($conv_formats as $k => $val) {
		if ($format == $k) {
			$selected = " selected='selected'";
		} else {
			$selected = '';
		}
		echo "<option value='$k'$selected>" . _T("convertisseur:$k") . "</option>\n";
	}
	echo "</select></p>\n";


	echo _T('convertisseur:texte_a_copier') . "<br />\n";

	$conv_in = entites_html(substr($conv_in, 0, 40000));
	echo "<textarea name='conv_in' cols='65' rows='12'>$conv_in</textarea><br />\n";


	echo "<div style='float:$spip_lang_right;'>";
	echo _T('convertisseur:texte_fichier') . "<br />\n";
	echo "<input type='file' name='upload' />\n";
	echo "</div>\n";

	echo "<br style='clear:both;' />\n";

	echo "<p align='right'><small>Il est possible de convertir plusieurs fichiers en une seule fois, en les regroupant dans une archive ZIP</small></p>\n";

	echo '<h5>' . _T('convertisseur:options') . "</h5>\n";

	$checked = ($GLOBALS['auteur_session']['prefs']['convertisseur_cvcharset'] == 'oui')
		? ' checked="checked"'
		: '';
	echo "<label><input type='checkbox' value='true' name='convert_charset'$checked
	/>" . _T('convertisseur:convertir_utf') . "\n";
	echo "</label>\n";


	// Ajouter sous forme d'article dans la rubrique
	if (
		function_exists('charger_fonction')
		and $chercher_rubrique = charger_fonction('chercher_rubrique', 'inc', true)
	) {
		echo '<p/><div><label>Choisissez une rubrique si vous voulez insérer le résultat de la conversion dans un nouvel article sur le site :';
		echo $chercher_rubrique(null, 'rubrique', null);
		echo "</label></div>\n";
	}

	echo "<p style='float:right;'><input type='submit' value='" . _T('convertisseur:convertir') . "'></p>\n";
	echo "</form>\n";


	echo fin_gauche(), fin_page();
}
