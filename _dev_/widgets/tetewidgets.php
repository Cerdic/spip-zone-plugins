<?php
/* insert le css et le js externes pour Widgets dans le <head>
 *
 *  Widgets plugin for spip (c) Fil , toggg 2006 -- licence GPL
 */

// Dire rapidement si ca vaut le coup de chercher des droits
function analyse_droits_rapide_dist() {
    if ($GLOBALS['auteur_session']['statut'] != '0minirezo')
        return false;
    else
        return true;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
function Widgets_affichage_final($page) {

    // ne pas se fatiguer si le visiteur n'a aucun droit
    if (!(function_exists('analyse_droits_rapide')?analyse_droits_rapide():analyse_droits_rapide_dist()))
        return $page;

    // sinon regarder rapidement si la page a des classes widget
    if (!strpos($page, 'widget'))
        return $page;

    // voire un peu plus precisement lesquelles
    include_spip('inc/widgets');
    if (!preg_match_all(_PREG_WIDGET, $page, $regs, PREG_SET_ORDER))
        return $page;

    // calculer les droits sur ces widgets
    include_spip('inc/autoriser');
    $droits = array();
    $droits_accordes = 0;
    foreach ($regs as $reg) {
        list(,$widget,$type,$champ,$id) = $reg;
        if (autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))) {
            $droits[$widget]++;
            $droits_accordes ++;
        }
    }

    // et les signaler dans la page
    if ($droits_accordes == count($regs))
        $page = Widgets_preparer_page($page, '*');
    else if ($droits)
        $page = Widgets_preparer_page($page, array_keys($droits));

    return $page;
}

function Widgets_preparer_page($page, $droits) {

    $jsFile = find_in_path('widgets.js');
    $cssFile = find_in_path('widgets.css');
    $config = var2js(array(
		'imgPath' => dirname(find_in_path('images/crayon.png')),
        'droits' => $droits,

		'txt' => array(
			'error' => _U('widgets:svp_copier_coller'),
			'sauvegarder' => _U('widgets:sauvegarder')
		),
		'img' => array(
			'searching' => 'searching.gif',
			'edit' => array(
				'file' => 'crayon.png',
				'txt' => _U('widgets:editer')
			),
			'img-changed' => array(
				'file' => 'changed.png',
				'txt' => _U('widgets:deja_modifie')
			)
		)
	));
//    $txtErrInterdit = addslashes(unicode_to_javascript(html2unicode(_T(
//        'widgets:erreur_ou_interdit'))));

    $incHead = <<<EOH

<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
    var configWidgets = new cfgWidgets({$config});
</script >
EOH;

    return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

?>
