<?php
// Traitement de la page d'edition d'un ticket

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_ticket_editer() {

	$id_ticket = _request('id_ticket') ? _request('id_ticket') : 'new';

	$contexte = array('id_ticket'=>$id_ticket);

	if(intval($id_ticket)){
		$contexte['titre'] = sql_getfetsel("titre","spip_tickets","id_ticket=$id_ticket");
	}else{
		$contexte['titre'] = _T('ticketskiss:creer_ticket');
	}

	$titre_page = _T('ticketskiss:page_titre');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "ticketskiss";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page.' - '.$contexte['titre'], $rubrique, $sous_rubrique));

	echo "<br /><br />";

	echo debut_gauche("",true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'ticket_afficher'),'data'=>''));

	echo debut_droite("",true);

	$page = recuperer_fond("prive/editer/ticket", $contexte);
	echo $page;

	echo fin_gauche(), fin_page();
}

?>