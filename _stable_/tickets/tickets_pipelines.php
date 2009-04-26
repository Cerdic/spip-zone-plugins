<?php
// Ajout du bouton permettant de se rendre sur la page de gestion des tickets
function tickets_ajouterBoutons($boutons_admin) {
		// affiche le bouton dans "Forum", tout le monde peut voir cette page
		$boutons_admin['forum']->sousmenu['tickets'] = new Bouton(
			find_in_path('bugs.png', 'imgs/', false),
			_T('tickets:titre'),
			generer_url_ecrire('tickets')
		);
	return ($boutons_admin);
}

// Menu des tickets presente a droite ou a gauche de la page
function menu_colonne () {
	$ret .= "<div class='cadre cadre-e'><div class='cadre_padding'>";
	$ret .= icone_horizontale(_L('Afficher les tickets'), generer_url_ecrire("tickets"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "", false);

	$contexte = array("titre"=>"Vos tickets en cours de r&eacute;daction", "id_auteur"=>$connect_id_auteur, "statut"=>"redac");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;
	
	$contexte = array("titre"=>"Tous les tickets ouverts", "statut"=>"ouvert");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;

	include_spip('inc/tickets_autoriser');
	if (autoriser('ecrire', 'ticket')) {
		$ret .= icone_horizontale(_L('Cr&eacute;er un ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "creer.gif", false);
	}
	$ret .= "</div></div>";

	return $ret;
}

// Pipeline menu a droite
function tickets_droite ($flux) {
	$exec = $flux["args"]["exec"];
	
	if ($exec == "accueil") {
		$data = $flux["data"];
		
		$ret = menu_colonne();

		$flux["data"] = $data.$ret;
	}
	return $flux;
}

// Pipeline menu a droite
function tickets_gauche ($flux) {
	$exec = $flux["args"]["exec"];
	
	if ($exec == "ticket_afficher") {
		$data = $flux["data"];
		
		$ret = menu_colonne();
		$flux["data"] = $data.$ret;
	}
	return $flux;
}
?>
