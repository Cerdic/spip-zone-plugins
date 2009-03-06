<?php


function tickets_ajouterBoutons($boutons_admin) {
	if($GLOBALS['connect_statut'] == "0minirezo") {
	// affiche le bouton dans "Edition"
		$boutons_admin['forum']->sousmenu['tickets'] = new Bouton(
			find_in_path('bugs.png', 'imgs/', false),
			_T('tickets:titre'),
			generer_url_ecrire('tickets')
		);
	}
	return ($boutons_admin);
}


function afficher_les_tickets () {
		$ret .= "<div class='cadre cadre-e'><div class='cadre_padding'>";
		$ret .= icone_horizontale(_L('Afficher les tickets'), generer_url_ecrire("tickets"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "", false);

			$contexte = array("titre"=>"Vos tickets en cours de r&eacute;daction", "id_auteur"=>$connect_id_auteur, "statut"=>"redac", "court"=>"oui");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;

			
			$contexte = array("titre"=>"Tous les tickets ouverts", "statut"=>"ouvert", "court" => "oui");
			$page = recuperer_fond("prive/contenu/inc_liste_tickets", $contexte);
			$ret .= $page;


		$ret .= icone_horizontale(_L('Cr&eacute;er un ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "creer.gif", false);
		$ret .= "</div></div>";

	return $ret;
}

function tickets_droite ($flux) {
	$exec = $flux["args"]["exec"];
	
	
	if ($exec == "accueil") {
		$data = $flux["data"];
		
		$ret = afficher_les_tickets();

		$flux["data"] = $data.$ret;
			
	}
	return $flux;
}

function tickets_gauche ($flux) {
	$exec = $flux["args"]["exec"];
	
	
	if ($exec == "ticket_afficher") {
		$data = $flux["data"];
		
		$ret = afficher_les_tickets();
		$flux["data"] = $data.$ret;
			
	}
	return $flux;
}

?>
