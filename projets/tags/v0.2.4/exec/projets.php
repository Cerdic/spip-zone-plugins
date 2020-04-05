<?php
/**
 * Plugn SPIP-Projet
 * Licence GPL
 *
 * Affichage de la liste des projets (exec=projets)
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

function exec_projets_dist()
{
	exec_projets_args(intval(_request('id_projet')));
}

function exec_projets_args($id_projet)
{
	pipeline('exec_init',array('args'=>array('exec'=>'projets','id_projet'=>$id_projet),'data'=>''));

	$row = sql_fetsel("*", "spip_projets", "id_projet=$id_projet");

	if (!$row
	OR !autoriser('voir', 'projet', $id_projet)) {
		include_spip('inc/minipres');
		echo minipres(_T('projets:aucun_projet'));
	} else {
		$row['titre'] = sinon($row["titre"],_T('info_sans_titre'));

		$res = debut_gauche('accueil',true)
		  .  projets_affiche($id_projet, $row, _request('cherche_auteur'), _request('ids'), _request('cherche_mot'), _request('select_groupe'))
		  . "<br /><br /><div class='centered'>"
		. "</div>"
		. fin_gauche();

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page("&laquo; ". $row['titre'] ." &raquo;", "naviguer", "projets", $row['id_parent']);

		echo debut_grand_cadre(true),
			fin_grand_cadre(true),
			$res,
			fin_page();
	}
}

function projets_affiche($id_projet, $row, $cherche_auteur, $ids, $cherche_mot,  $select_groupe)
{
	global $spip_lang_right, $logo_libelles;

	$id_parent = $row['id_parent'];
	$statut_projet = $row['statut'];
	$titre = $row["titre"];
	$descriptif = $row["descriptif"];
	$texte = $row["texte"];
	$date = $row["date"];

	$statut_rubrique = autoriser('modifier', 'projet', $id_parent);
	$flag_editable = autoriser('modifier', 'projet', $id_projet);

	$iconifier = charger_fonction('iconifier', 'inc');
	$icone = $iconifier('id_projet', $id_projet,'projets', false, $flag_editable);

	$boite = pipeline ('boite_infos', array('data' => '',
		'args' => array(
			'type'=>'projet',
			'id' => $id_projet,
			'row' => $row
		)
	));

	$navigation =
	  debut_boite_info(true). $boite . fin_boite_info(true)
	  . $icone
	  . pipeline('affiche_gauche',array('args'=>array('exec'=>'projets','id_projet'=>$id_projet),'data'=>''));

	$extra = creer_colonne_droite('', true)
	  . pipeline('affiche_droite',array('args'=>array('exec'=>'projets','id_projet'=>$id_projet),'data'=>''))
	  . debut_droite('',true);

	// affecter les globales dictant les regles de typographie de la langue
	changer_typo($row['lang']);

	$actions =
	  ($flag_editable ? bouton_modifier_projets($id_projet, $id_parent, $modif, _T('projets:avis_projets_modifie', $modif), chemin('prive/images/projets-24.gif'), "edit.gif",$spip_lang_right) : "");

	$haut =
		"<div class='bandeau_actions'>$actions</div>"
		. gros_titre($titre, '' , false);

	$onglet_contenu =
	  afficher_corps_projets($id_projet,$row);

	$onglet_proprietes = ((!_INTERFACE_ONGLETS) ? "" :"")
	  . pipeline('affiche_milieu',array('args'=>array('exec'=>'projets','id_projet'=>$id_projet),'data'=>''));

	$res = $navigation
	  . $extra
	  . "<div class='fiche_objet'>"
	  . $haut
	  . afficher_onglets_pages(
	  	array(
	  		'voir' => _T('onglet_contenu'),
	  		'props' => _T('onglet_proprietes')
			),
	  	array(
	    	'props'=>$onglet_proprietes,
	    	'voir'=>$onglet_contenu)
		)
	  . "</div>";
	  
	$res .= pipeline('affiche_enfants',array('args'=>array('exec'=>'projets','id_projet'=>$id_projet),'data'=>''));

	return $res;
}

function bouton_modifier_projets($id_projet, $id_parent, $flag_modif, $mode, $ip, $im, $align='')
{
	if ($flag_modif) {
		return icone_inline(_T('projets:icone_modifier_projet'), generer_url_ecrire("projet_edit","id_projet=$id_projet"), $ip, $im, $align, false)
		. "<span class='arial1 spip_small'>$mode</span>";
	}
	else return icone_inline(_T('projets:icone_modifier_projet'), generer_url_ecrire("projet_edit","id_projet=$id_projet"), chemin("prive/images/projets-24.gif"), "edit.gif", $align);
}

function afficher_corps_projets($id_projet, $row)
{
	$res = '';
	if ($row['statut'] == 'prop') {
		$res .= "<p class='projets_prop'>"._T('projets:text_projets_propose_publication');
		$res.= "</p>";
	}

	$type = 'projet';
	$contexte = array(
		'id'=>$id_projet,
		'id_parent' => $row['id_parent']
	);
	$fond = recuperer_fond("prive/contenu/$type",$contexte);
	// permettre aux plugin de faire des modifs ou des ajouts
	$fond = pipeline('afficher_contenu_objet',
		array(
		'args'=>array(
			'type'=>$type,
			'id_objet'=>$id_projet,
			'contexte'=>$contexte),
		'data'=> ($fond)));

	$res .= "<div id='wysiwyg'>$fond</div>";

	return $res;
}

?>
