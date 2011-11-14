<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajout du bouton permettant de se rendre sur la page de gestion des tickets
function tickets_ajouterBoutons($boutons_admin) {
	// uniquement si le plugin bandeau n'est pas la (ou SPIP 2.1)
	if(!$boutons_admin['bando_publication']){
		// affiche le bouton dans "Forum" si les forums sont activés, tout le monde peut voir cette page
		if($boutons_admin['forum']){
			$boutons_admin['forum']->sousmenu['tickets'] = new Bouton(
				find_in_path('ticket-24.png', 'prive/themes/spip/images/', false),
				_T('tickets:titre'),
				generer_url_ecrire('tickets')
			);
		}else{
			// Sinon affiche les tickets en sous menu de Edition, aussi accessible pour tout le monde
			$boutons_admin['naviguer']->sousmenu['tickets'] = new Bouton(
				find_in_path('ticket-24.png', 'prive/themes/spip/images/', false),
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
	$ret .= icone_horizontale(_T('tickets:afficher_tickets'), generer_url_ecrire("tickets"), find_in_path("prive/themes/spip/images/ticket-24.png"), "", false);

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
		$ret .= icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), find_in_path("prive/themes/spip/images/ticket-24.png"), "creer.gif", false);
	}
	$ret .= "</div></div>";

	return $ret;
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
 * Insertion dans le pipeline accueil informations de l'etat des tickets
 * @param string $flux
 * @return string $flux
 */
function tickets_accueil_informations($flux){
	global $spip_lang_left;

	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_tickets', '', 'statut', '','', "COUNT(*)<>0");

	$cpt = array();
	$cpt2 = array();
	$defaut = $where ? '0/' : '';
	while($row = sql_fetch($q)) {
	  $cpt[$row['statut']] = $row['cnt'];
	  $cpt2[$row['statut']] = $defaut;
	}

	if ($cpt) {
		if ($where) {
			$q = sql_select("COUNT(*) AS cnt, statut", 'spip_tickets', $where, "statut");
			while($row = sql_fetch($q)) {
				$r = $row['statut'];
				$cpt2[$r] = intval($row['cnt']) . '/';
			}
		}
		$res .= afficher_plus(generer_url_ecrire("tickets",""))."<b>"._T('tickets:info_tickets')."</b>";
		$res .= "<ul style='margin:0px; padding-$spip_lang_left: 20px; margin-bottom: 5px;'>";
		if (isset($cpt['redac'])) $res .= "<li>"._T('tickets:info_tickets_redac').": ".$cpt2['redac'].$cpt['redac'] . '</li>';
		if (isset($cpt['ouvert'])) $res .= "<li><b>"._T('tickets:info_tickets_ouvert').": ".$cpt2['ouvert'] .$cpt['ouvert'] . "</b>" .'</li>';
		$res .= "</ul>";
	}

	$flux .= "<div class='verdana1'>" . $res . "</div>";
	return $flux;
}

/**
 * Insertion dans le pipeline accueil gadgets le bouton de creation d'un ticket
 * @param string $gadget
 * @return string $gadget
 */
function tickets_accueil_gadgets($gadget){

	include_spip('inc/tickets_autoriser');
	if (autoriser('ecrire', 'ticket')) {
		$icone = icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire("ticket_editer","id_ticket=new"), find_in_path("prive/themes/spip/images/ticket-24.png"), "creer.gif", false);

		$colonnes = extraire_balises($gadget, 'td');
		$derniere_colonne = fmod(floor(count($colonnes)/2), 4) == 0 ? true : false;
		if ($derniere_colonne) {
			$gadget .= "<table><tr><td>$icone</td></tr></table>";
		}
		else {
			$gadget = preg_replace(",</tr></table>$,is", "<td>$icone</td></tr></table>", $gadget);
		}
	}
	return $gadget;
}

/**
 * Insertion dans le pipeline affiche milieu de la liste des tickets en cours de rédaction
 * @param string $gadget
 * @return string $gadget
 */
function tickets_affiche_milieu($flux){

	$exec = $flux["args"]["exec"];
	if ($exec == "accueil") {
		$flux['data'] .= '<br class="nettoyeur" />';
		$flux['data'] .= recuperer_fond('prive/contenu/inc_classement_accueil', array());
	}

	return $flux;
}

/**
 * Insertion dans le pipeline forum_objets_depuis_env (plugin forums balises/formulaire_forum.php)
 * 
 * Permet de récupérer l'id du ticket dans le formulaire de forum
 * 
 * @param array $objets
 * @return array $objets
 */
function tickets_forum_objets_depuis_env($objets){
	$objets['ticket'] = id_table_objet('ticket');
	return $objets;
}

/**
 * Insertion dans le pipeline declarer_url_objets (SPIP ecrire/inc/urls)
 * 
 * Ajoute les tickets comme objet pouvant avoir des urls spécifiques (propres...)
 * 
 * @param array $flux Les objets ayant des urls
 * @return array $flux
 */
function tickets_declarer_url_objets($flux){
	$flux[] = 'ticket';
	return $flux;
}

/**
 * Insertion dans le pipeline rechercher_liste_des_champs (SPIP)
 *
 * Ajoute les tickets dans la recherche
 *
 * @param array $tables
 * @return array $tables
 */
function tickets_rechercher_liste_des_champs($tables){
	$tables['ticket'] = array(
		'titre' => 8,
		'texte' => 5
	);
	return $tables;
}

/**
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * Si on est dans un formulaire de forum sur un ticket, on ajoute l'id_ticket dans les champs chargés
 * Permet de le récupérer par la suite dans le contexte de recuperer_fond
 * 
 * @param array $flux Le contexte du formulaire
 */
function tickets_formulaire_charger($flux){
	$args = $flux['args'];
	$form = $flux['args']['form'];
	if ($form == 'forum'){
		if($args['args'][4] == 'id_ticket'){
			$flux['data']['objet'] = 'ticket';
			$flux['data']['id_objet'] = $args['args'][6];
			$flux['data']['id_ticket'] = $args['args'][6];
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * Sur le formulaire de forum, on ajoute 2 champs quand on commente un ticket :
 * -* La possibilité de changer le statut;
 * -* La possibilité de changer l'assignation
 * 
 * @param array $flux Le contexte du pipeline
 */
function tickets_recuperer_fond($flux){
	$args = $flux['args'];
	$fond = $args['fond'];
	if ($fond == 'formulaires/forum'){
		if(is_numeric($args['contexte']['id_ticket'])){
			$infos_ticket = sql_fetsel('statut,id_assigne','spip_tickets','id_ticket='.intval($args['contexte']['id_ticket']));
			if(_request('id_assigne')){
				$infos_ticket['id_assigne'] = _request('id_assigne');
			}
			if(_request('statut')){
				$infos_ticket['statut'] = _request('statut');
			}
			if(is_array($infos_ticket)){
				$saisie_ticket = recuperer_fond('inclure/inc-formulaire_forum',array_merge($args['contexte'],$infos_ticket));
				$flux['data']['texte'] = preg_replace(",(<fieldset.*<\/fieldset>),Uims","\\1".$saisie_ticket,$flux['data']['texte'],1);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * Si on est dans un formulaire de forum sur un ticket, on récupère le statut et l'assignation si présents
 * pour les notifications de forums des tickets en 2.1
 *
 * @param array $flux
 * @return array $flux
 */
function tickets_formulaire_traiter($flux){
	if (($flux['args']['form']=='forum') AND ($flux['args']['args'][3]=='ticket')) {
		if($flux['args']['args'][3] == 'ticket'){
			include_spip('action/editer_ticket');
			$id_ticket = $flux['args']['args'][6];
			$infos_ticket = sql_fetsel('*','spip_tickets','id_ticket='.intval($id_ticket));
			if(($new_statut = _request('statut')) && ($new_statut != $infos_ticket['statut'])){
				instituer_ticket($id_ticket,array('statut'=>$new_statut));
			}
			if(($new_assigne=_request('id_assigne')) && ($new_assigne != $infos_ticket['id_assigne'])){
				revision_ticket($id_ticket, array('id_assigne'=>$new_assigne));
			}
		}
		if ($notifications = charger_fonction('notifications', 'inc')) {
			$notifications('commenterticket', $flux['args']['args'][6],
			array(
					'id_auteur' => id_assigne,
					'texte' => texte
			)
			);
		}
	}
	return $flux;
}
?>
