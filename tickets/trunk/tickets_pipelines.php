<?php
/**
 * Plugin Tickets
 * Licence GPL (c) 2008-2013
 *
 * @package SPIP\Tickets\Pipelines
 */
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Insertion dans le pipeline affiche_aguche (SPIP)
 * 
 * Affichage du bouton de création de tickets sur la page ?exec=ticket
 * Affichage de la liste des tickets ouverts et des tickets en cours
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function tickets_affiche_gauche($flux) {
	if (isset($flux["args"]["exec"]) && ($flux["args"]["exec"] == "ticket")) {
		$ret = '';
		
		include_spip('inc/autoriser');
		
		if (autoriser('ecrire', 'ticket')) {
			include_spip('inc/presentation'); # pour icone_horizontale
			$ret .= boite_ouvrir('','simple');
			$ret .= icone_horizontale(_T('tickets:creer_ticket'), generer_url_ecrire('ticket_edit','new=oui'), 'ticket-24.png', 'creer.gif', false);
			$ret .= boite_fermer();
		}
		
		$contexte = array('titre'=>_T('tickets:vos_tickets_en_cours'), 'id_auteur'=>$connect_id_auteur, "statut"=>"redac", 'bloc'=>'_bloc1');
		$options = array("ajax"=>true);
		$page = recuperer_fond('prive/squelettes/inclure/inc_liste_simple', $contexte, $options);
		$ret .= $page;
	
		$contexte = array('titre'=>_T('tickets:tous_tickets_ouverts'), 'statut'=>'ouvert', 'bloc'=>'_bloc2');
		$options = array('ajax'=>true);
		$page = recuperer_fond('prive/squelettes/inclure/inc_liste_simple', $contexte, $options);
		$ret .= $page;
		
		$flux["data"] .= $ret;
	}
	return $flux;
}


/**
 * Insertion dans le pipeline accueil_informations (SPIP)
 * 
 * Affichage de l'etat des tickets
 * 
 * @param string $flux
 * 		Le contexte du pipeline
 * @return string $flux
 * 		Le contexte du pipeline modifié
 */
function tickets_accueil_informations($flux){
	global $spip_lang_left;
	include_spip('inc/presentation');
	$q = sql_select("COUNT(*) AS cnt, statut", 'spip_tickets', '', 'statut', '','', "COUNT(*)<>0");

	$cpt = $cpt2 = array();
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
 * Insertion dans le pipeline affiche milieu (SPIP) 
 * 
 * Ajout de la liste des tickets en cours de rédaction
 * 
 * @param array $flux
 * 		Le contexte du pipeline
 * @return array $flux
 * 		Le contexte du pipeline modifié
 */
function tickets_affiche_milieu($flux){
	if (isset($flux["args"]["exec"]) && $flux["args"]["exec"] == "accueil") {
		$flux['data'] .= '<br class="nettoyeur" />';
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/inc_classement_accueil', array());
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
 * Insertion dans le pipeline formulaire_charger (SPIP)
 * Si on est dans un formulaire de forum sur un ticket, on ajoute l'id_ticket dans les champs chargés
 * Permet de le récupérer par la suite dans le contexte de recuperer_fond
 * 
 * @param array $flux Le contexte du formulaire
 * @return array $flux Le contexte du formulaire modifié
 */
function tickets_formulaire_charger($flux){
	if (($flux['args']['form'] == 'forum') && ($flux['args']['args'][0] == 'ticket')){
		$flux['data']['objet'] = 'ticket';
		$flux['data']['id_objet'] = $flux['args']['args'][1];
		$flux['data']['id_ticket'] = $flux['args']['args'][1];
		$flux['data']['ticket_statut'] = _request('ticket_statut');
		$flux['data']['id_assigne'] = _request('id_assigne');
	}
	return $flux;
}

/**
 * Insertion dans le pipeline recuperer_fond (SPIP)
 * 
 * Sur le formulaire de forum (public seulement), on ajoute 2 champs quand on commente un ticket :
 * -* La possibilité de changer le statut;
 * -* La possibilité de changer l'assignation
 * Dans le privé, recours au formulaire usuel de l'espace privé, le changement de statut et d'assignation étant
 * directement disponibles dans la page.
 * 
 * @param array $flux Le contexte du pipeline
 */
function tickets_recuperer_fond($flux){
	if ($flux['args']['fond'] == 'formulaires/forum'){
		if(is_numeric($flux['args']['contexte']['id_ticket'])){
			$infos_ticket = sql_fetsel('*','spip_tickets','id_ticket='.intval($flux['args']['contexte']['id_ticket']));
			foreach(array('projet','composant','version','severite','navigateur','tracker','id_assigne','exemple') as $champ){
				if(_request($champ))
					$infos_ticket[$champ] = _request($champ);
			}
			if(_request('ticket_statut'))
				$infos_ticket['ticket_statut'] = _request('ticket_statut');
			if(is_array($infos_ticket)){
				$saisie_ticket = recuperer_fond('inclure/inc-tickets_formulaire_forum',array_merge($flux['args']['contexte'],$infos_ticket));
				$flux['data']['texte'] = preg_replace(",(<fieldset>.*<\/fieldset>),Uims","\\1".$saisie_ticket,$flux['data']['texte'],1);
			}
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline formulaire_verifier (SPIP)
 * 
 * On ajoute nos valeurs des champs de tickets dans la prévisu de forum
 *
 * @param array $flux
 * @return array $flux
 */
function tickets_formulaire_verifier($flux){
	if (($flux['args']['form']=='forum') AND ($flux['args']['args'][0]=='ticket') && isset($flux['data']['previsu'])) {
		$flux['data']['previsu'] .= '<input type="hidden" name="ticket_statut" value="'._request('ticket_statut').'" />';
		foreach(array('projet','composant','version','severite','navigateur','tracker','id_assigne','exemple') as $champ){
			if(_request($champ))
				$flux['data']['previsu'] .= '<input type="hidden" name="'.$champ.'" value="'._request($champ).'" />';
		}
	}
	return $flux;
}


/**
 * Insertion dans le pipeline formulaire_traiter (SPIP)
 * Si on est dans un formulaire de forum sur un ticket, on récupère le statut et l'assignation si présents
 * pour modifier le ticket en conséquence.
 *
 * De plus, si configuration de tickets, on repercute la configuration des documents joints au plugin medias.
 *
 * @param array $flux
 * @return array $flux
 */
function tickets_formulaire_traiter($flux){
	if (($flux['args']['form']=='forum') AND ($flux['args']['args'][0]=='ticket')) {
		include_spip('action/editer_ticket');
		include_spip('inc/autoriser');
		$id_ticket = $flux['args']['args'][1];
		$infos_ticket = sql_fetsel('*','spip_tickets','id_ticket='.intval($id_ticket));
		if(autoriser('modifier','ticket',$id_ticket)){
			$set = array();
			if(($new_statut = _request('ticket_statut')) && ($new_statut != $infos_ticket['statut']))
				$set['statut']= $new_statut;
			/**
			 * On met le $_POST['texte'] à null pour ne pas mettre le texte du forum dans le texte du ticket
			 */
			set_request('texte',NULL);
			ticket_modifier($id_ticket,$set);
		}
		else if(autoriser('assigner','ticket',$id_ticket)){
			if(($new_assigne=_request('id_assigne')) && ($new_assigne != $infos_ticket['id_assigne']))
				ticket_modifier($id_ticket, array('id_assigne'=>$new_assigne));
		}
	}
	if ($flux['args']['form']=='configurer_tickets_general') {
		$config_docs = explode(',',$GLOBALS['meta']['documents_objets']);
		$cle = array_search('spip_tickets', $config_docs);
		if ($cle AND !_request('joindre_fichiers')) unset($config_docs[$cle]);
		if (!$cle AND _request('joindre_fichiers')) $config_docs[] = 'spip_tickets';
		ecrire_meta('documents_objets',implode(',', $config_docs));
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
