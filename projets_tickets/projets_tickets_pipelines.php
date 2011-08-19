<?php
/**
 * Insertion dans le pipeline affiche_droite
 *
 * Affiche un bloc avec la liste des tickets associés au projet et un lien
 * vers la création d'un ticket associé au projet.
 *
 * @param object $flux
 * @return
 */
function projets_tickets_affiche_droite($flux){
 	if(($flux['args']['exec'] == 'projets') && (in_array('ticket',lire_config('projet/ligatures/objets',array())))){
 		include_spip('inc/autoriser');
 		$id_projet = $flux['args']['id_projet'];
 		$flux['data'] .= recuperer_fond('prive/listes/tickets_projets',array('id_projet'=>$id_projet,'titre' => _T('projets_tickets:tickets_associes')));
 		if (autoriser('ecrire', 'ticket') && autoriser('modifier','projet')) {
 			$flux['data'] .= debut_cadre_enfonce('',true)
 				.icone_horizontale(_T('tickets:creer_ticket'), parametre_url(generer_url_ecrire('ticket_editer','new=oui&id_projet='.$id_projet),'retour',self()), chemin('prive/themes/spip/images/ticket-24.png'), 'creer.gif', false)
 				.fin_cadre_enfonce(true);
 		}
 	}
	return $flux;
}

function projets_tickets_affiche_enfants($flux){
	if(($flux['args']['exec'] == 'projets') && (in_array('ticket',lire_config('projet/ligatures/objets',array())))){
		include_spip('inc/autoriser');
		$id_projet = $flux['args']['id_projet'];
		$flux['data'] .= recuperer_fond('prive/contenu/inc-affiche_enfants', array('id_projet'=>$id_projet,'titre' => _T('projets_tickets:tickets_associes'), 'bloc' => 'bloc_tickets'));
		if (autoriser('ecrire', 'ticket')) {
			$flux['data'] .= icone_inline(_T('tickets:creer_ticket'), parametre_url(generer_url_ecrire('ticket_editer','new=oui&id_projet='.$id_projet),'retour',self()), chemin('prive/themes/spip/images/ticket-24.png'), 'creer.gif', 'right');
		}
	}
	return $flux;
}

/**
 * Insertion dans le pipeline editer_contenu_objet (lors du chargement du CVT)
 *
 * @param object $flux
 * @return
 */
function projets_tickets_editer_contenu_objet($flux){
	if (($flux['args']['type']=='ticket') && (in_array('ticket',lire_config('projet/ligatures/objets',array())))) {
		$flux['args']['contexte']['id_projet'] = sql_getfetsel('id_projet', 'spip_projets_liens', 'objet="ticket" AND id_objet=' . sql_quote($flux['args']['contexte']['id_ticket']));
		if(!intval($flux['args']['contexte']['id_projet'])){
			$flux['args']['contexte']['id_projet'] = intval(_request('id_projet'));
		}
		$projet = recuperer_fond('formulaires/inc-projets_tickets', $flux['args']['contexte']);
		$flux['data'] = preg_replace('%(<li class="editer_severite(.*?)</li>)%is', '$1' . "\n" . $projet, $flux['data']);
	}
	return $flux;
}

/**
 * Insertion dans le pipeline de post-edition d'un ticket
 */
function projets_tickets_pre_edition($flux){
	$id_ticket = $flux{'args'}['id_objet'];
	if(($flux['args']['table'] == 'spip_tickets') && ($flux['args']['action'] == 'modifier') && (in_array('ticket',lire_config('projet/ligatures/objets',array())))){
		$id_projet = $_POST['id_projet'];
		$associer_projet = charger_fonction('associer_projet','action');
		$associer_projet($id_projet,'ticket',$id_ticket);
	}
	return $flux;
}
?>