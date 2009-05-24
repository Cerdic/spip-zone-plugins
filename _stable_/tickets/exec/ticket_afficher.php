<?php
// Traitement de la page d'affichage et de modification d'un ticket
function exec_ticket_afficher () {
	global $connect_id_auteur;
	
	$id_ticket = _request('id_ticket');
	
	include_spip('inc/presentation');
	
	// Ajout d'un message au ticket
	if (_request("poster_message_ticket")) {
		$id_ticket = _request("poster_message_ticket");
		$texte = _request("texte");
		
		sql_insertq("spip_tickets_forum", 
			array("id_ticket" => $id_ticket, "texte" => $texte, "id_auteur" => $connect_id_auteur,  "date" => "NOW()"));
	}
	
	$titre_page = _T('tickets:page_titre');
	
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page($titre_page, $rubrique, $sous_rubrique));
	
	echo "<br /><br />";
	
	echo debut_gauche("",true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'ticket_afficher'),'data'=>''));
	
	echo debut_droite("",true);
	
	$contexte = array("id_ticket"=>$id_ticket);
	$page = recuperer_fond("prive/contenu/ticket_afficher", $contexte);
	
	echo $page;
	
	echo fin_gauche(), fin_page();
}
?>