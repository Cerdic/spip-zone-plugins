<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/actions');

function exec_ticket_afficher_dist()
{
	exec_ticket_args(intval(_request('id_ticket')));
}

// Traitement de la page d'affichage et de modification d'un ticket
function exec_ticket_args ($id_ticket) {

	pipeline('exec_init',array('args'=>array('exec'=>'tickets_afficher','id_ticket'=>$id_ticket),'data'=>''));

	$row = sql_fetsel("*", "spip_tickets", "id_ticket=$id_ticket");

	if (!$row
	OR !autoriser('voir', 'ticket', $id_ticket)) {
		include_spip('inc/minipres');
		echo minipres(_T('tickets:acces_interdit'));
	} else {
		$row['titre'] = sinon($row["titre"],_T('info_sans_titre'));

		$res = debut_gauche('accueil',true)
		  .  tickets_affiche($id_ticket, $row)
		  . "<br /><br /><div class='centered'>"
		. "</div>"
		. fin_gauche();

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page("&laquo; ". $row['titre'] ." &raquo;", "naviguer", "ticket_afficher");

		echo debut_grand_cadre(true),
			fin_grand_cadre(true),
			$res,
			fin_page();
	}
}

function tickets_affiche($id_ticket, $row){
	global $spip_lang_right, $dir_lang;

	$statut_ticket = $row['statut'];
	$titre = $row["titre"];
	$texte = $row["texte"];
	$date = $row["date"];

	$titre_page = _T('tickets:page_titre');

	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";

	$flag_editable = autoriser('ecrire', 'ticket', $id_ticket);

	$boite = pipeline ('boite_infos', array('data' => '',
		'args' => array(
			'type'=>'ticket',
			'id' => $id_ticket,
			'row' => $row
		)
	));

	$navigation =
	  debut_boite_info(true). $boite . fin_boite_info(true)
	  . pipeline('affiche_gauche',array('args'=>array('exec'=>'ticket_afficher','id_ticket'=>$id_ticket),'data'=>''));

	$extra = creer_colonne_droite('', true)
	  . pipeline('affiche_droite',array('args'=>array('exec'=>'ticket_afficher','id_ticket'=>$id_ticket),'data'=>''))
	  . debut_droite('',true);;

	$actions =
	  ($flag_editable ? bouton_modifier_tickets($id_ticket, $modif, _T('tickets:avis_projet_modifie', $modif),  find_in_path('imgs/bugs.png'), "edit.gif",$spip_lang_right) : "");

	$haut =
		"<div class='bandeau_actions'>$actions</div>"
		. gros_titre($titre, '' , false);

	$onglet_contenu =
	  afficher_corps_tickets($id_ticket,$row);

	$onglet_proprietes = ((!_INTERFACE_ONGLETS) ? "" :"")
		.recuperer_fond('prive/proprietes/ticket',array('id_ticket'=>$id_ticket))
		.pipeline('affiche_milieu',array('args'=>array('exec'=>'ticket_afficher','id_ticket'=>$id_ticket),'data'=>''));

	$onglet_discuter = recuperer_fond('prive/contenu/ticket_commenter',array('id_ticket'=>$id_ticket));

	return
		  $navigation
		  . $extra
		  . "<div class='fiche_objet'>"
		  . $haut
		  . afficher_onglets_pages(
		  	array(
		  		'voir' => _T('onglet_contenu'),
		  		'props' => _T('onglet_proprietes'),
				'discuter' => _T('onglet_discuter')
				),
		  	array(
		    	'props'=>$onglet_proprietes,
		    	'voir'=>$onglet_contenu,
				'discuter' => _INTERFACE_ONGLETS?$onglet_discuter:""
			))
		  . "</div>"
		  . (_INTERFACE_ONGLETS?"":$onglet_discuter);


	echo $page;

	echo fin_gauche(), fin_page();
}

// http://doc.spip.org/@bouton_modifier_articles
function bouton_modifier_tickets($id_ticket, $flag_modif, $mode, $ip, $im, $align='')
{
	if ($flag_modif) {
		return icone_inline(_T('tickets:icone_modifier_ticket'), generer_url_ecrire("ticket_editer","id_ticket=$id_ticket"), $ip, $im, $align, false)
		. "<span class='arial1 spip_small'>$mode</span>";
	}
	else return icone_inline(_T('tickets:icone_modifier_ticket'), generer_url_ecrire("ticket_editer","id_ticket=$id_ticket"), find_in_path('imgs/bugs.png'), "edit.gif", $align);
}

// http://doc.spip.org/@afficher_corps_articles
function afficher_corps_tickets($id_ticket, $row)
{
	$res = '';

	$type = 'ticket';
	$contexte = array("id_ticket"=>$id_ticket);
	$fond = recuperer_fond("prive/contenu/ticket_afficher", $contexte);

	// permettre aux plugin de faire des modifs ou des ajouts
	$fond = pipeline('afficher_contenu_objet',
		array(
		'args'=>array(
			'type'=>$type,
			'id_objet'=>$id_ticket,
			'contexte'=>$contexte),
		'data'=> ($fond)));

	$res .= "<div id='wysiwyg'>$fond</div>";

	return $res;
}
?>