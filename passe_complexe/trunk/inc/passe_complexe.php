<?php

//on combine une liste de mots, separe par des virgules ou espace pour en faire une liste quoter: ,"mot1", "mot3"
function passe_complexe_quote_common($list) {
	$commons = preg_split('/[ ,]/',$list);
	$return = '';
	for ($i = 0; $i < count($commons); $i++) {
	if($commons[$i] && count($commons[$i]) > 0)
		$return .= ",'".str_replace("'","\\'",$commons[$i])."'";
	}
	return $return;
}

//creer le javascript a ajouter au header pour que ca marche
function passe_complexe_generer_javascript($selecteur) {
	$minchar = lire_config('passe_complexe/length', _PASS_LONGUEUR_MINI);
	$common_cfg = lire_config('passe_complexe/common');
	$showpercent = lire_config('passe_complexe/showpercent');
	if (count($common_cfg) <= 0) {
		$common_cfg = '';
	} else {
		$common_cfg = ','.$common_cfg;
	}

	$flux = '<script type="text/javascript" src="'.generer_url_public('password.js').'"></script>';
	$flux .= '<script type="text/javascript"><!--
	$(document).ready(function() {
		// Default behavior
		$("'.$selecteur.'").password({
			showPercent: '.$showpercent.',
			showText: true, // shows the text tips
			animate: true, // whether or not to animate the progress bar on input blur/focus
			minimumLength: '.$minchar.', // minimum password length (below this threshold, the score is 0)
			common: [
				"motdepasse","123456","123","1234","azerty",
				"'.$GLOBALS['auteur_session']['nom'].'"' //le nom de l'auteur ne devrait pas se trouver dans le password
				.', "'.$GLOBALS['auteur_session']['login'].'"' //ni son login
				.passe_complexe_quote_common(
				 _T('passe_complexe:common') //la liste definit pour la langue de l'utilisateur
				 .$common_cfg //la liste definit par la config cfg
				 .','.$GLOBALS['auteur_session']['nom_site'] //le nom du site de l'auteur
				 .','.textebrut($GLOBALS['meta']['nom_site'])) //le nom du site sur lequel on est
			.'] // mots interdit
		});
	});
	--></script>';
	return $flux;
}
