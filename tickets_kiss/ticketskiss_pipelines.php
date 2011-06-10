<?php
// Ajout du bouton permettant de se rendre sur la page de gestion des ticketskiss
function ticketskiss_ajouterBoutons($boutons_admin) {
	// uniquement si le plugin bandeau n'est pas la (ou SPIP 2.1)
	if(!$boutons_admin['bando_publication']){
		// affiche le bouton dans "Forum" si les forums sont activés, tout le monde peut voir cette page
		if($boutons_admin['forum']){
			$boutons_admin['forum']->sousmenu['ticketskiss'] = new Bouton(
				find_in_path('bugs.png', 'imgs/', false),
				_T('ticketskiss:titre'),
				generer_url_ecrire('ticketskiss')
			);
		}else{
			// Sinon affiche les ticketskiss en sous menu de Edition, aussi accessible pour tout le monde
			$boutons_admin['naviguer']->sousmenu['ticketskiss'] = new Bouton(
				find_in_path('bugs.png', 'imgs/', false),
				_T('ticketskiss:titre'),
				generer_url_ecrire('ticketskiss')
			);		
		}
	}
	return ($boutons_admin);
}

// Menu des ticketskiss presente a droite ou a gauche de la page
function menu_colonne () {
	$ret = "<div class='cadre cadre-e'><div class='cadre_padding'>";
	$ret .= icone_horizontale(_T('ticketskiss:afficher_ticketskiss'), generer_url_ecrire("ticketskiss"), _DIR_PLUGIN_TICKETSKISS."imgs/bugs.png", "", false);

	$contexte = array("titre"=>_T('ticketskiss:vos_ticketskiss_en_cours'), "id_auteur"=>$connect_id_auteur, "statut"=>"redac", "bloc"=>"_bloc1");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;
	
	$contexte = array("titre"=>_T('ticketskiss:tous_ticketskiss_ouverts'), "statut"=>"ouvert", "bloc"=>"_bloc2");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;

	include_spip('inc/ticketskiss_autoriser');
	if (autoriser('ecrire', 'ticket')) {
		$ret .= icone_horizontale(_T('ticketskiss:creer_ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), _DIR_PLUGIN_TICKETSKISS."imgs/bugs.png", "creer.gif", false);
	}
	$ret .= "</div></div>";

	return $ret;
}

// Pipeline menu a droite
function ticketskiss_droite ($flux) {
	$exec = $flux["args"]["exec"];
	
	if ($exec == "accueil") {
		$data = $flux["data"];
		
		$ret = menu_colonne();

		$flux["data"] = $data.$ret;
	}
	return $flux;
}

// Pipeline menu a droite
function ticketskiss_gauche ($flux) {
	$exec = $flux["args"]["exec"];
	
	if (($exec == "ticket_afficher") OR ($exec == "ticket_editer")) {
		$data = $flux["data"];
		
		$ret = menu_colonne();
		$flux["data"] = $data.$ret;
	}
	return $flux;
}

// champs extras 2
function ticketskiss_objets_extensibles($objets){
	return array_merge($objets, array('ticket' => _T('ticketskiss:ticketskiss')));
}
?>
