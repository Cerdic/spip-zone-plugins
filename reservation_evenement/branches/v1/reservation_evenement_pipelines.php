<?php
/**
 * Utilisations de pipelines par Réservation Événements
 *
 * @plugin     Réservation Événements
 * @copyright  2013
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

//Afficher les box infos et téléchargement des réservations
function reservation_evenement_affiche_gauche($flux){
    include_spip('inc/presentation');
    $exec=$flux['args']['exec'];
    $objets_affichage=array('rubrique','article','evenement');

    if (in_array($exec,$objets_affichage)){ 
        $contexte=array();
        $contexte['id_article']=intval($flux['args']['id_article'])?$flux['args']['id_article']:'';
        $contexte['id_rubrique']=intval($flux['args']['id_rubrique'])?$flux['args']['id_rubrique']:'';
        $contexte['id_evenement']=intval($flux['args']['id_evenement'])?$flux['args']['id_evenement']:'';    
            
        $flux['data'] .= recuperer_fond('inclure/reservations',$contexte);
    }
    return $flux;
}

/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function reservation_evenement_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .='<br class="nettoyeur"/>'.recuperer_fond('prive/objets/liste/reservations', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('reservation:info_reservations_auteur')
		), array('ajax' => true));

	}
	return $flux;
}

function reservation_evenement_affiche_milieu($flux) {
    $e = trouver_objet_exec($flux['args']['exec']);
    // reservations sur les evenements
    if (!$e['edition'] AND in_array($e['type'], array('evenement'))) {
        $contexte=calculer_contexte();
        $contexte['id_evenement']=_request('id_evenement');
        $contexte['par']='id_evenement';        
        $texte .= recuperer_fond('prive/objets/liste/reservations_details',$contexte,array('ajax'=>'oui'));
        $flux['data'] .= $texte;
    }

    return $flux;
}

?>