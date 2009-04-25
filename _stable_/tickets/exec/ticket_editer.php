<?php
// Traitement de la page d'edition d'un ticket
function exec_ticket_editer () {

	$id_ticket = $_GET["id_ticket"];

	include_spip('inc/presentation');
	include_spip('inc/texte');

	global $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
		
	$contexte = array("id_ticket"=>$id_ticket);
	$contexte["titre"] = "Nouveau ticket";
	$contexte["severite"] = 4;
		
	$query = sql_query("SELECT * FROM spip_tickets WHERE id_ticket = $id_ticket");
	if ($row = sql_fetch($query)) {
		$contexte["titre"] = htmlspecialchars($row["titre"]);
		$contexte["texte"] = htmlspecialchars($row["texte"]);
		$contexte["severite"] = $row["severite"];
		$contexte["type"] = $row["type"];
		$contexte["statut"] = $row["statut"];
		$contexte["id_auteur"] = $row["id_auteur"];
		$contexte["id_assigne"] = $row["id_assigne"];
		$contexte["exemple"] = htmlspecialchars($row["exemple"]);
		$contexte["composant"] = htmlspecialchars($row["composant"]);
		$contexte["jalon"] = htmlspecialchars($row["jalon"]);
		$contexte["version"] = htmlspecialchars($row["version"]);
		$contexte["projet"] = htmlspecialchars($row["projet"]);
	}

	$titre_page = _L('Tickets, syst&egrave;me de suivi de bugs');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_L('Tickets, suivi de bugs') . " - " . $titre_page, $rubrique, $sous_rubrique));

	echo "<br /><br />";
	

	echo debut_gauche("",true);
	echo debut_droite("",true);
	
	$page = recuperer_fond("prive/editer/ticket", $contexte);
	echo $page;

	echo fin_gauche(), fin_page();
}

?>