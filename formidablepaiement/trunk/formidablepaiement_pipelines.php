<?php
/**
 * Utilisations de pipelines par Formulaires de paiement
 *
 * @plugin     Formulaires de paiement
 * @copyright  2014
 * @author     Cédric Morin
 * @licence    GNU/GPL
 * @package    SPIP\Formidablepaiement\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Mise en forme du formulaire de paiement post-saisie
 *
 * @param array $flux
 * @return array
 */
function formidablepaiement_formulaire_fond($flux){

	if ($flux['args']['form']=='formidable'
	  AND $id = $flux['args']['args'][0]
	  AND $flux['args']['je_suis_poste']){

		if (($p1 = strpos($flux['data'],"<!--formidablepaiement-transaction-->"))!==false
			AND ($p2 = strpos($flux['data'],"<!--//formidablepaiement-transaction-->"))!==false
		  AND $p2>$p1){
			$length = $p2-$p1+39;
			$form = substr($flux['data'],$p1,$length);
			$flux['data'] = substr_replace($flux['data'],"",$p1,$length);
			$flux['data'] .=
				"<div class='formulaire_spip formulaire_paiement'>"
				. $form
				. "</div>";

			$css = find_in_path("css/formidablepaiement.css");
			$flux['data'] .= "<style type='text/css'>@import url('$css');</style>";
		}
	}
	return $flux;
}

/**
 * Ajouter le message de retour post-paiement
 * @param array $flux
 * @return array
 */
function formidablepaiement_bank_traiter_reglement($flux){

	// si c'est une transaction associee a un form
	if ($id_transaction = $flux['args']['id_transaction']
	  AND preg_match(",form\d+:,",$flux['args']['avant']['parrain'])
	  AND $id_formulaires_reponse = $flux['args']['avant']['tracking_id']){

		$reponse = sql_fetsel('*','spip_formulaires_reponses','id_formulaires_reponse='.intval($id_formulaires_reponse));
		$formulaire = sql_fetsel('*','spip_formulaires','id_formulaire='.intval($reponse['id_formulaire']));

		$traitements = unserialize($formulaire['traitements']);
		if ($message = trim($traitements['paiement']['message'])){
			include_spip("inc/texte");
			$flux['data'] .= propre($message);
		}
	}

	return $flux;
}

function formidablepaiement_affiche_enfants($flux){

	if ($flux['args']['objet']=='formulaires_reponse'
	  AND $id_formulaires_reponse = $flux['args']['id_objet']){

		$reponse = sql_fetsel('*','spip_formulaires_reponses','id_formulaires_reponse='.intval($id_formulaires_reponse));

		$where = "parrain LIKE ".sql_quote('form'.$reponse['id_formulaire'].':%')." AND tracking_id=".intval($id_formulaires_reponse);
		$flux['data'] .= recuperer_fond("prive/objets/liste/transactions",array('where'=>$where));
	}
	return $flux;
}

function formidablepaiement_affiche_milieu($flux){
	if ($flux['args']['exec']=='formulaires_reponses'
	  AND $id_formulaire = $flux['args']['id_formulaire']){

		$where = "parrain LIKE ".sql_quote('form'.$id_formulaire.':%');
		$flux['data'] .= recuperer_fond("prive/objets/liste/transactions",array('where'=>$where));
	}
	return $flux;
}