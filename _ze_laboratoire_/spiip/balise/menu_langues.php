<?php
if (!defined("_ECRIRE_INC_VERSION")) return;	#securite


function balise_MENU_LANGUES ($p) {

	return calculer_balise_dynamique($p,'MENU_LANGUES', array('lang'));
}

// s'il n'y a qu'une langue proposee eviter definitivement la balise ?php 
function balise_MENU_LANGUES_stat ($args, $filtres) {
	global $all_langs;
	include_spip('inc/lang');
	if (strpos($all_langs,',') === false) return '';
	return $filtres ? $filtres : $args;
}

// normalement $opt sera toujours non vide suite au test ci-dessus
function balise_MENU_LANGUES_dyn($opt) {
	return menu_langues_formulaire('var_lang_ecrire', $opt);
}

//
// Afficher un menu de selection de langue
// - 'var_lang_ecrire' = langue interface privee,
// - 'var_lang' = langue de l'article, espace public
// - 'changer_lang' = langue de l'article, espace prive
// 
// http://doc.spip.org/@menu_langues
function menu_langues_formulaire($nom_select = 'var_lang', $default = '', $texte = '', $herit = '', $lien='') {
	global $couleur_foncee, $connect_id_auteur;

	$ret = liste_options_langues($nom_select, $default, $herit);

	if (!$ret) return '';

	if (!$couleur_foncee) $couleur_foncee = '#044476';

	if (!$lien)
		$lien = self();

	if ($nom_select == 'changer_lang') {
		$lien = parametre_url($lien, 'changer_lang', '');
		$lien = parametre_url($lien, 'url', '');
		$cible = '';
	} else {
		if (_DIR_RESTREINT) {
			$cible = $lien;
			$lien = generer_url_action('cookie');
		} else {
			$cible = _DIR_RESTREINT_ABS . $lien;
			if (_FILE_CONNECT) {
				include_spip('inc/actions');
				$lien = generer_action_auteur('cookie','var_lang_ecrire');
			} else $lien = generer_url_action('cookie');
		}
	}

	return array('formulaires/formulaire_menu_langues',
		3600,
		array('nom' => $nom_select,
			'url' => $lien,
			'cible' => $cible,
			'texte' => $texte,
			'langues' => $ret,
			'couleur_foncee' => $couleur_foncee
		)
	);
}
?>
