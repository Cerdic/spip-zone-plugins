<?php
/* insert le css et le js externes pour Crayons dans le <head>
 *
 *  Crayons plugin for spip (c) Fil, toggg 2006-2007 -- licence GPL
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
    if (strpos($page, 'crayon')===FALSE)
        return $page;

    // voir un peu plus precisement lesquelles
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
    if ($droits_accordes == count($regs)) // tous les droits
        $page = Crayons_preparer_page($page, '*', $wdgcfg);
    else if ($droits) // seulement certains droits, preciser lesquels
        $page = Crayons_preparer_page($page, join(',',array_keys($droits)), $wdgcfg);

    return $page;
}

function Crayons_preparer_page($page, $droits, $wdgcfg = array()) {
	lang_select($GLOBALS['auteur_session']['lang']);

	$jsFile = generer_url_public('crayons.js');
	include_spip('inc/filtres'); // rien que pour direction_css() :(
	$cssFile = direction_css(find_in_path('crayons.css'));

    $config = var2js(array(
		'imgPath' => dirname(find_in_path('images/crayon.png')),
        'droits' => $droits,
    'dir_racine' => _DIR_RACINE,

		'txt' => array(
			'error' => _U('crayons:svp_copier_coller'),
			'sauvegarder' => $wdgcfg['msgAbandon'] ? _U('crayons:sauvegarder') : ''
		),
		'img' => array(
			'searching' => array(
				'file' => 'searching.gif',
				'txt' => _U('crayons:veuillez_patienter')
			),
			'crayon' => array(
				'file' => 'crayon20.png',
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

	$pos_head = strpos($page, '</head>');
	if ($pos_head === false)
		return $page;

    $incHead = <<<EOH

<link rel="stylesheet" href="{$cssFile}" type="text/css" media="all" />
<script src="{$jsFile}" type="text/javascript"></script>
<script type="text/javascript">
    var configCrayons = new cfgCrayons({$config});
</script>
EOH;

    return substr_replace($page, $incHead, $pos_head, 0);
}

// #EDIT{ps} pour appeler le crayon ps ;
// si cette fonction est absente, balise_EDIT_dist() met a vide
function balise_EDIT($p) {
	$p->code = "classe_boucle_crayon('"
		. $p->boucles[$p->nom_boucle ? $p->nom_boucle : $p->id_boucle]->type_requete
		."',"
		.sinon(interprete_argument_balise(1,$p),"''")
		.","
		.champ_sql($p->boucles[$p->nom_boucle ? $p->nom_boucle : $p->id_boucle]->primary, $p)
		.").' '";

	$p->interdire_scripts = false;
	return $p;
}

// Donne la classe crayon en fonction
// - du type de la boucle
// (attention aux exceptions pour #EDIT dans les boucles HIERARCHIE et SITES)
// - du champ demande (vide, + ou se terminant par + : (+)classe type--id)
// - de l'id courant
function classe_boucle_crayon($type_boucle, $champ, $id) {
	$type_boucle = $type_boucle[strlen($type_boucle) - 1] == 's' ?
			substr($type_boucle, 0, -1) : 
			str_replace(
				array('hierarchie', 'syndication'),
				array('rubrique',   'site'),
				$type_boucle);

	$plus = '';
	if ($champ && $champ[strlen($champ) - 1] == '+') {
		$champ = substr($champ, 0, -1);
		if ($champ) {
			$plus = " $type_boucle--$id";
		}
	}
	return 'crayon ' . $type_boucle . '-' . $champ . '-' . $id . $plus;
}

?>
