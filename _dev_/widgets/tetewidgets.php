<?php
/* insert le css et le js externes pour Widgets dans le <head>
 *
 *  Widgets plugin for spip (c) Fil 2006 -- licence GPL
 */

define('_PREG_WIDGET', ',widget\b[^<>\'"]+\b((article)-(\w+)-(\d+))\b,');

// Dire rapidement si ca vaut le coup de chercher des droits
function analyse_droits_rapide() {
	if ($GLOBALS['auteur_session']['statut'] != '0minirezo')
		return false;
	else
		return true;
}

// fonction d'API manquante a SPIP...
function autoriser_modifs($quoi = 'article', $id = 0) {
	if ($quoi != 'article') {
		echo "pas implemente";
		return false;
	}

	global $connect_id_auteur, $connect_statut;
	$connect_id_auteur = intval($GLOBALS['auteur_session']['id_auteur']);
	$connect_statut = $GLOBALS['auteur_session']['statut'];
	include_spip('inc/auth');
	auth_rubrique($GLOBALS['auteur_session']['id_auteur'], $GLOBALS['auteur_session']['statut']);
	return acces_article($id);
}


// Le pipeline affichage_final, execute a chaque hit sur toute la page
function Widgets_affichage_final($page) {

	// ne pas se fatiguer si le visiteur n'a aucun droit
	if (!analyse_droits_rapide())
		return $page;

	// sinon regarder rapidement si la page a des classes widget
	if (!strpos($page, 'widget'))
		return $page;

	// voire un peu plus precisement lesquelles
	if (!preg_match_all(_PREG_WIDGET,
	$page, $regs, PREG_SET_ORDER))
		return $page;

	// calculer les droits sur ces widgets
	$droits = array();
	foreach ($regs as $reg) {
		list(,$widget,$type,$champ,$id) = $reg;
		if (autoriser_modifs($type, $id))
			$droits[$widget]++;
	}

	// et les signaler dans la page
	if ($droits)
		$page = Widgets_preparer_page($page, join('|', array_keys($droits)));

	return $page;
}

function Widgets_preparer_page($page, $droits) {
	include_spip('inc/charsets');

	$jsFile = find_in_path('widgets.js');
	$cssFile = find_in_path('widgets.css');
	$imgPath = dirname(find_in_path('images/edit.gif'));

	$txtEditer = addslashes(html2unicode(_T(
		'widgets:editer')));
	$txtErrInterdit = addslashes(unicode_to_javascript(html2unicode(_T(
		'widgets:erreur_ou_interdit'))));

	$incHead = <<<EOH

<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
	var configWidgets = new configWidgets({
		'droits':'{$droits}',
		'imgPath':'{$imgPath}',
		'txtEditer':'{$txtEditer}',
		'txtErrInterdit':'{$txtErrInterdit}'
	});
</script >
EOH;

	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

?>
