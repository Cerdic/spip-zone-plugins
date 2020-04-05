<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

// Renvoie un tableau avec les familles de polices.
// Ces dernieres proviennent de http://cssfontstack.com/
function polpriv_polices () {
	return array(
		'Calibri' => 'Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif',
		'Century Gothic' => '"Century Gothic", CenturyGothic, AppleGothic, sans-serif',
		'Franklin Gothic Medium' => '"Franklin Gothic Medium", "Franklin Gothic", "ITC Franklin Gothic", Arial, sans-serif',
		'Futura' => 'Futura, "Trebuchet MS", Arial, sans-serif',
		'Gill Sans' => '"Gill Sans", "Gill Sans MT", Calibri, sans-serif',
		'Lucida Grande' => '"Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Geneva, Verdana, sans-serif',
		'Optima / Segoe' => 'Optima, Segoe, "Segoe UI", Candara, Calibri, Arial, sans-serif',
		'Tahoma' => 'Tahoma, Verdana, Segoe, sans-serif',
		'Trebuchet MS' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif',
		'Verdana' => 'Verdana, Geneva, sans-serif',
		'Baskerville' => '"Baskerville Old Face", "Hoefler Text", Garamond, "Times New Roman", serif',
		'Book Antiqua' => '"Book Antiqua", Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif',
		'Cambria' => 'Cambria, Georgia, serif',
		'Garamond' => 'Garamond, Baskerville, "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif',
		'Georgia' => 'Georgia, Times, "Times New Roman", serif',
		'Palatino' => 'Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
		'Rockwell' => 'Rockwell, "Courier Bold", Courier, Georgia, Times, "Times New Roman", serif',
		'Times New Roman' => 'TimesNewRoman, "Times New Roman", Times, Baskerville, Georgia, serif',
		'Courrier New' => '"Courier New", Courier, "Lucida Sans Typewriter", "Lucida Typewriter", monospace',
		'Lucida Sans Typewriter' => '"Lucida Sans Typewriter", "Lucida Console", Monaco, "Bitstream Vera Sans Mono", monospace'
		);
}

// Renvoie un tableau pour saisies radio avec les familles de polices fontface trouvees dans le repertoire polices.
function polpriv_polices_fontface () {
	include_spip('inc/polpriv');
	if ($familles = polpriv_familles_polices_fontface()) {
		foreach ($familles as $famille=>$v) {
			$label = polpriv_formater_label_fontface($famille) . " @font-face";
			$fontface[$label] = $famille;
		}
	}
	return $fontface;
}

function polpriv_formulaire_charger ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		$flux['data']['_polices'] = polpriv_polices(); // polices normales
		$flux['data']['_polices_fontface'] = polpriv_polices_fontface(); // polices fontface
		$flux['data']['police_prive'] = isset($GLOBALS['visiteur_session']['prefs']['police_prive'])?$GLOBALS['visiteur_session']['prefs']['police_prive']:'';
		// inserer dans la page les styles de toutes les polices fontface (et pas dans la feuille de style)
		include_spip('inc/polpriv');
		if ($familles = polpriv_familles_polices_fontface())
			$flux['data']['_style_fontface'] .= polpriv_generer_style_polices_fontface($familles);
	}
	return $flux;
}

// On passe par verifier pour ajouter la police a visiteur_session avant que le formulaire ne soit traite.
function polpriv_formulaire_verifier ($flux) {
	if ($flux['args']['form'] == 'configurer_preferences'){
		if ($police_prive = _request('police_prive')) {
			$GLOBALS['visiteur_session']['prefs']['police_prive'] = ($police_prive=='defaut') ? '' : $police_prive;
		}
	}
	return $flux;
}

function polpriv_recuperer_fond ($flux) {
	if ($flux['args']['fond'] == 'formulaires/configurer_preferences'){
		$polpriv = recuperer_fond('prive/inclure/configurer_police_prive', $flux['args']['contexte']);
		$flux['data']['texte'] = preg_replace('%(<!--extra-->)%is', $polpriv.'$1', $flux['data']['texte']);
	}

	return $flux;
}

function polpriv_header_prive($flux){
	if (isset($GLOBALS['visiteur_session']['prefs']['police_prive'])){
		include_spip('inc/polpriv');
		$police = $GLOBALS['visiteur_session']['prefs']['police_prive'];
		$polices = polpriv_polices();
		$polices_fontface = polpriv_familles_polices_fontface();
		// polices normales
		if (isset($polices[$police]))
			$flux .= "<style type='text/css'>body {font-family: ".$polices[$police].";}</style>";
		// polices fontface
		if (is_array($polices_fontface) AND !empty($polices_fontface)) {
		$polices_fontface = array_keys($polices_fontface);
			if (in_array($police, $polices_fontface)) {
				$famille = polpriv_familles_polices_fontface(array($police));
				$flux .= polpriv_generer_style_polices_fontface($famille);
				$flux .= "<style type='text/css'>body {font-family: '".$police."';}</style>";
			}
		}
	}

	return $flux;
}

?>
