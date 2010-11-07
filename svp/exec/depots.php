<?php
/**
 * Plugn SPIP-Projet
 * Licence GPL
 *
 * Affichage d'un depot avec un bouton pour modification eventuel (exec=depots)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

function exec_depots_dist()
{
	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo minipres();
	}
	else
		exec_depots_args(intval(_request('id_depot')));
}

function exec_depots_args($id_depot)
{
	pipeline('exec_init',array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''));

	if (!$depot = sql_fetsel("*", "spip_depots", "id_depot=$id_depot")) {
		include_spip('inc/minipres');
		echo minipres(_T('svp:message_nok_aucun_depot'));
	} 
	else {
		$depot['titre'] = sinon($depot['titre'], _T('svp:titre_nouveau_depot'));

		$page = debut_gauche('accueil', true)
			.  afficher_depot($id_depot, $depot)
			. "<br /><br /><div class='centered'>"
			. "</div>"
			. fin_gauche();

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page('&laquo; ' . $depot['titre'] . ' &raquo;', 'naviguer', 'depots');

		echo debut_grand_cadre(true),
			afficher_lien_gerer_depots(),
			fin_grand_cadre(true),
			$page,
			fin_page();
	}
}

function afficher_lien_gerer_depots() {
	
	$lien = '<ul dir="' . lang_dir($GLOBALS['spip_lang']) . '" class="verdana3" id="chemin">' .
			'<li><span class="bloc">' .
			'<a class="racine on" href="' . generer_url_ecrire('depots_gerer') . '" title="' . _T('svp:bulle_aller_gerer_depots') . '">' .
			_T('svp:lien_gerer_depots') .
			'</a>' .
			'</span></li>' .
			'</ul>';
	return $lien;
}

function afficher_depot($id_depot, $depot) {
	global $spip_lang_right, $logo_libelles;

	$titre = $depot['titre'];
	$descriptif = $depot['descriptif'];
	$type = $depot['type'];
	$url = $depot['url_paquets'];
	$nbr_paquets = $depot['nbr_paquets'];
	$nbr_plugins = $depot['nbr_plugins'];
	$nbr_autres = $depot['nbr_autres'];
	$maj = $depot['maj'];

	$iconifier = charger_fonction('iconifier', 'inc');
	$icone = $iconifier('id_depot', $id_depot, 'depots', false, true);

	$boite = pipeline ('boite_infos', 
				array('data' => '', 'args' => array('type'=>'depot', 
													'id_depot' => $id_depot, 
													'nbr_paquets' => $nbr_paquets,
													'nbr_plugins' => $nbr_plugins,
													'nbr_autres' => $nbr_autres,
													'maj' => $maj)));

	$navigation = debut_boite_info(true). $boite . fin_boite_info(true)
		. $icone
		. pipeline('affiche_gauche',array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''));

	$extra = creer_colonne_droite('', true)
		. afficher_autres_depots($id_depot)
		. pipeline('affiche_droite',array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''))
		. debut_droite('',true);

	$actions = icone_inline(_T('svp:bouton_modifier_depot'), generer_url_ecrire("depots_edit","id_depot=$id_depot"), chemin("prive/themes/spip/images/depot-24.png"), "edit.gif", $spip_lang_right);

	$haut = "<div class='bandeau_actions'>$actions</div>" . gros_titre($titre, '' , false);

	$onglet_contenu = afficher_corps_depot($id_depot, $depot);
	$onglet_proprietes = ((!_INTERFACE_ONGLETS) ? "" :"")
		. pipeline('affiche_milieu',array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''));

	$page = $navigation
		. $extra
		. "<div class='fiche_objet'>"
		. $haut
		. afficher_onglets_pages(
		array(
			'voir' => _T('onglet_contenu'),
			'props' => _T('onglet_proprietes')
			),
		array(
			'props' => $onglet_proprietes,
			'voir' => $onglet_contenu)
		)
		. "</div>";
	  
	$page .= pipeline('affiche_enfants', array('args'=>array('exec'=>'depots','id_depot'=>$id_depot),'data'=>''));

	return $page;
}

function afficher_autres_depots($id_depot) {
	$bloc = recuperer_fond("prive/navigation/depots_autres", array('id_depot' => $id_depot));
	return $bloc;
}

function afficher_corps_depot($id_depot, $depot) {
	$corps = '';
	$type = 'depot';
	$info_type_depot = ($depot['type']) ? _T('svp:info_type_depot_'.$depot['type']) : '';
	$contexte = array('id_depot'=>$id_depot, 'info_type'=>$info_type_depot);
	$fond = recuperer_fond("prive/contenu/$type",$contexte);
	// Permettre a d'autres plugins de faire des modifs ou des ajouts
	$fond = pipeline('afficher_contenu_objet',
					array('args'=>array('type'=>$type, 'id_objet'=>$id_depot, 'contexte'=>$contexte),
						'data'=> ($fond)));

	$corps .= "<div id='wysiwyg'>$fond</div>";

	return $corps;
}

?>
