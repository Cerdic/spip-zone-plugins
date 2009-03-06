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


			$contexte = array();
			$page = recuperer_fond("prive/contenu/inc_messages_ticket", $contexte);
			$ret .= $page;

			
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

	$titre_page = _T('tickets:titre_liste');
	// Permet entre autres d'ajouter les classes à la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = "forum";
	$sous_rubrique = "tickets";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(
		_T('tickets:titre_liste').' - '._T('tickets:titre'),
		$rubrique,
		$sous_rubrique
	);
	
	// Valeur par défaut du contexte
	$contexte = array(
		'classement' => 'asuivre'
	);
	// On écrase par l'environnement
	$contexte = array_merge($contexte, $_GET, $_POST);
	
	echo recuperer_fond('prive/contenu/tickets', $contexte);
	
	echo fin_page();
}

?>
