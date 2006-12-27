<?php
/* insert le css et le js externes pour Crayons dans le <head>
 *
 *  Crayons plugin for spip (c) Fil , toggg 2006 -- licence GPL
 */

// Dire rapidement si ca vaut le coup de chercher des droits
function analyse_droits_rapide_dist() {
    if ($GLOBALS['auteur_session']['statut'] != '0minirezo')
        return false;
    else
        return true;
}

// Le pipeline affichage_final, execute a chaque hit sur toute la page
function Crayons_affichage_final($page) {

    // ne pas se fatiguer si le visiteur n'a aucun droit
    if (!(function_exists('analyse_droits_rapide')?analyse_droits_rapide():analyse_droits_rapide_dist()))
        return $page;

    // sinon regarder rapidement si la page a des classes crayon
    if (!strpos($page, 'crayon'))
        return $page;

    // voire un peu plus precisement lesquelles
    include_spip('inc/crayons');
    if (!preg_match_all(_PREG_CRAYON, $page, $regs, PREG_SET_ORDER))
        return $page;
    $wdgcfg = wdgcfg();

    // calculer les droits sur ces crayons
    include_spip('inc/autoriser');
    $droits = array();
    $droits_accordes = 0;
    foreach ($regs as $reg) {
        list(,$crayon,$type,$champ,$id) = $reg;
        if (autoriser('modifier', $type, $id, NULL, array('champ'=>$champ))) {
            $droits['.' . $crayon]++;
            $droits_accordes ++;
        }
    }

    // et les signaler dans la page
    if ($droits_accordes == count($regs))
        $page = Crayons_preparer_page($page, array('*'), $wdgcfg);
    else if ($droits)
        $page = Crayons_preparer_page($page, array_keys($droits), $wdgcfg);

    return $page;
}

function Crayons_preparer_page($page, $droits, $wdgcfg = array()) {

    $jsFormFile = find_in_path('javascript/form.js');
	$jsFile = find_in_path('crayons.js');
    $cssFile = find_in_path('crayons.css');
    $config = var2js(array(
		'imgPath' => dirname(find_in_path('images/pencil.png')),
        'droits' => $droits,

		'txt' => array(
			'error' => _U('crayons:svp_copier_coller'),
			'sauvegarder' => $wdgcfg[msgAbandon] ? _U('crayons:sauvegarder') : ''
		),
		'img' => array(
			'searching' => array(
				'file' => 'searching.gif',
				'txt' => _U('crayons:veuillez_patienter')
			),
			'pencil' => array(
				'file' => 'pencil.png',
				'txt' => _U('crayons:editer')
			),
			'edit' => array(
				'file' => 'edit.png',
				'txt' => _U('crayons:editer_tout')
			),
			'img-changed' => array(
				'file' => 'changed.png',
				'txt' => _U('crayons:deja_modifie')
			)
		),
		'cfg' => $wdgcfg
	));
//    $txtErrInterdit = addslashes(unicode_to_javascript(html2unicode(_T(
//        'crayons:erreur_ou_interdit'))));

    $incHead = <<<EOH

<link rel="stylesheet" href="$cssFile" type="text/css" media="all" />
<script src="{$jsFormFile}" type="text/javascript"></script>
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
    var configCrayons = new cfgCrayons({$config});
</script>
EOH;

    return substr_replace($page, $incHead, strpos($page, '</head>'), 0);
}

// #EDIT{ps} pour appeler le crayon ps ;
// si cette fonction est absente, balise_EDIT_dist() met a vide
function balise_EDIT($p) {
	$p->code = "'crayon '. preg_replace(',^hierarchie$,', 'rubrique', preg_replace(',s$,', '', '"
		. $p->type_requete
		."')) .'-'."
		. sinon(interprete_argument_balise(1,$p), "''")
		.".'-'."
		.champ_sql($p->boucles[$p->nom_boucle ? $p->nom_boucle : $p->id_boucle]->primary, $p).".' '";

	$p->interdire_scripts = false;
	return $p;
}

?>
