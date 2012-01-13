<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// Ajout du bouton permettant de se rendre sur la page de gestion des tickets
function tickets_ajouter_boutons($boutons_admin) {
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

/**
 * Insertion dans le pipeline affiche_aguche
 * @param object $flux
 * @return
 */
function tickets_affiche_gauche ($flux) {
	$exec = $flux["args"]["exec"];

	if (($exec == "ticket") OR ($exec == "ticket_edit")) {
		$data = $flux["data"];

		include_spip('inc/autoriser');
	
		$ret = '';
		
		if ($flux['args']['exec'] != 'ticket_edit' && autoriser('ecrire', 'ticket')) {
			include_spip('inc/presentation'); # pour icone_horizontale
			$ret .= boite_ouvrir('','simple');
			$ret .= icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire('ticket_edit','new=oui'), 'ticket-24.png', 'creer.gif', false);
			$ret .= boite_fermer();
		}
		
		$contexte = array('titre'=>_T('tickets:vos_tickets_en_cours'), 'id_auteur'=>$connect_id_auteur, "statut"=>"redac", 'bloc'=>'_bloc1');
		$options = array("ajax"=>true);
		$page = recuperer_fond('prive/contenu/inc_liste_simple', $contexte, $options);
		$ret .= $page;
	
		$contexte = array('titre'=>_T('tickets:tous_tickets_ouverts'), 'statut'=>'ouvert', 'bloc'=>'_bloc2');
		$options = array('ajax'=>true);
		$page = recuperer_fond('prive/contenu/inc_liste_simple', $contexte, $options);
		$ret .= $page;
		
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
	include_spip('inc/presentation');
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
		$afficher_plus = 'afficher_plus_info';
		$plus = "";
		if (!function_exists($afficher_plus))
			$afficher_plus = 'afficher_plus';
		
		$plus = $afficher_plus(generer_url_ecrire("tickets",""));
		$res .= "<h4>$plus"._T('tickets:info_tickets')."</h4>";
		$res .= "<ul class=\"liste-items\">";
		if (isset($cpt['redac'])) $res .= "<li class=\"item\">"._T('tickets:info_tickets_redac').": ".$cpt2['redac'].$cpt['redac'] . "</li>";
		if (isset($cpt['ouvert'])) $res .= "<li class=\"item\">"._T('tickets:info_tickets_ouvert').": ".$cpt2['ouvert'] .$cpt['ouvert'] . "</li>";
		$res .= "</ul>";
	}

	$flux .= "<div class=\"accueil_informations ticket liste\">" . $res . "</div>";
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
		$icone = icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire("ticket_edit","new=oui"), find_in_path("prive/themes/spip/images/ticket-24.png"), "creer.gif", false);

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
				include_spip('action/editer_ticket');
				revision_ticket($id_ticket, array('id_assigne'=>$new_assigne));
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline notifications_destinataires (Forum)
 * Ajoute des destinataires dans les notifications
 * 
 * @param array $flux : le contexte du pipeline
 * @return array $flux : le contexte modifié
 */
function tickets_notifications_destinataires($flux){
	/**
	 * Notification des auteurs de tickets et des assignés et des autres forumeurs lorsque le post est validé
	 */
	if(($flux['args']['quoi'] == 'forumvalide')){
		if(($flux['args']['options']['forum']['objet'] == 'ticket') && ($id_ticket = intval($flux['args']['options']['forum']['id_objet']))){
			/**
			 * On notifie l'id_auteur et l'id_assigné du ticket s'ils ne sont pas l'auteur du post en question
			 */
			$auteurs = sql_fetsel('id_auteur,id_assigne','spip_tickets','id_ticket='.intval($id_ticket).' AND id_auteur !='.intval($flux['args']['options']['forum']['id_auteur']));
			if(is_array($auteurs)){
				foreach($auteurs as $auteur){
					$email = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($auteur));
					$flux['data'][] = $email;
				}
			}
			/**
			 * On notifie les autres forumeurs du ticket
			 * GROUP BY id_auteur
			 */
			$id_forums = sql_select('*','spip_forum','objet='.sql_quote('ticket').' AND id_objet='.intval($id_ticket).' AND id_forum != '.intval($flux['args']['options']['forum']['id_forum']),array('id_auteur'));
			while($forum = sql_fetch($id_forums)){
				$email = sql_getfetsel('email','spip_auteurs','id_auteur='.intval($forum['id_auteur']));
				$flux['data'][] = $email;
			}
		}
	}
	return $flux;
}
?>
