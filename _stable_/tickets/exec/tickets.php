<?php



function liste_tickets() {
	global $connect_id_auteur;

			$contexte = array("titre"=>"Vos tickets en cours de r&eacute;daction", "id_auteur"=>$connect_id_auteur, "statut"=>"redac");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;
			
			/*
			$contexte = array("titre"=>"Vos tickets ouverts", "id_auteur"=>1, "statut"=>"ouvert");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;
			*/
			
			$contexte = array("titre"=>"Les tickets qui vous sont assign&eacute;s", "id_assigne"=>$connect_id_auteur, "statut"=>"ouvert");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;
			
			$contexte = array("titre"=>"Tous les tickets ouverts", "statut"=>"ouvert");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;

			$align = "right";
			$ret .= icone_inline(_L('Créer un nouveau ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "creer.gif", $align);

			$ret .= "<div class='nettoyeur'></div>";
			
			$contexte = array("titre"=>"Tous les tickets r&eacute;solus", "statut"=>"resolu");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;

			$contexte = array("titre"=>"Tous les tickets ferm&eacute;s", "statut"=>"ferme");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;

			
			
			return $ret;
		
}



function exec_tickets () {

	include_spip('inc/presentation');
	include_spip('inc/mots');
	include_spip('inc/spiplistes_api');
	include_spip('inc/spiplistes_api_presentation');

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		;

	$titre_page = _L('Tickets, syst&egrave;me de suivi de bugs');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_L('Tickets, suivi de bugs') . " - " . $titre_page, $rubrique, $sous_rubrique));

	echo "<br /><br />";
	echo gros_titre($titre_page, '', false);
	

	echo debut_gauche("",true);
	echo debut_droite("",true);
	
	echo liste_tickets();
	
	echo fin_gauche(), fin_page();
}

?>