<?php
// Ajout du bouton permettant de se rendre sur la page de gestion des tickets
function tickets_ajouterBoutons($boutons_admin) {
	// uniquement si le plugin bandeau n'est pas la (ou SPIP 2.1)
	if(!$boutons_admin['bando_publication']){
		// affiche le bouton dans "Forum" si les forums sont activés, tout le monde peut voir cette page
		if($boutons_admin['forum']){
			$boutons_admin['forum']->sousmenu['tickets'] = new Bouton(
				find_in_path('bugs.png', 'imgs/', false),
				_T('tickets:titre'),
				generer_url_ecrire('tickets')
			);
		}else{
			// Sinon affiche les tickets en sous menu de Edition, aussi accessible pour tout le monde
			$boutons_admin['naviguer']->sousmenu['tickets'] = new Bouton(
				find_in_path('bugs.png', 'imgs/', false),
				_T('tickets:titre'),
				generer_url_ecrire('tickets')
			);
		}
	}
	return ($boutons_admin);
}

// Menu des tickets presente a droite ou a gauche de la page
function menu_colonne () {
	$ret = "<div class='cadre cadre-e'><div class='cadre_padding'>";
	$ret .= icone_horizontale(_T('tickets:afficher_tickets'), generer_url_ecrire("tickets"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "", false);

	$contexte = array("titre"=>_T('tickets:vos_tickets_en_cours'), "id_auteur"=>$connect_id_auteur, "statut"=>"redac", "bloc"=>"_bloc1");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;

	$contexte = array("titre"=>_T('tickets:tous_tickets_ouverts'), "statut"=>"ouvert", "bloc"=>"_bloc2");
	$options = array("ajax"=>true);
	$page = recuperer_fond("prive/contenu/inc_liste_simple", $contexte, $options);
	$ret .= $page;

	include_spip('inc/tickets_autoriser');
	if (autoriser('ecrire', 'ticket')) {
		$ret .= icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), _DIR_PLUGIN_TICKETS."imgs/bugs.png", "creer.gif", false);
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

/**
 * Insertion dans le pipeline affiche_aguche
 * @param object $flux
 * @return
 */
function tickets_gauche ($flux) {
	$exec = $flux["args"]["exec"];

	if (($exec == "ticket_afficher") OR ($exec == "ticket_editer")) {
		$data = $flux["data"];

		$ret = menu_colonne();
		$flux["data"] = $data.$ret;
	}
	return $flux;
}

/**
 * Insertion dans le pipeline objets_extensibles (du plugin champs_extras)
 * Permet aux tickets d'avoir des champs supplémentaires
 *
 * @param object $objets
 * @return
 */
function tickets_objets_extensibles($objets){
	return array_merge($objets, array('ticket' => _T('tickets:tickets')));
}

/**
 * Insertion dans le pipeline gouverneur_infos_tables_versions 
 * (utile pour le plugin revisions en 2.1)
 * Permet de gérer les révisions sur les tickets
 *
 * @param object $array
 * @return
 */
function tickets_gouverneur_infos_tables($array){
	$array['spip_tickets'] = array(
								'table_objet' => 'tickets',
								'type' => 'ticket',
								'url_voir' => 'ticket_afficher',
								'texte_retour' => 'tickets:icone_retour_ticket',
								'url_edit' => 'ticket_editer',
								'texte_modifier' => 'tickets:icone_modifier_ticket',
								'icone_objet' => 'ticket',
								'texte_unique' => 'tickets:ticket',
								'texte_multiple' => 'tickets:tickets',
								'champs_versionnes' => array('titre','exemple', 'texte')
							);
	return $array;
}

/**
 * Insertion dans le pipeline revisions_liste_objets du plugin revisions (2.1)
 * Definir la liste des tables possibles
 * @param object $array
 * @return
 */
function tickets_revisions_liste_objets($array){
	$array['tickets'] = 'tickets:tickets';
	return $array;
}
?>