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
 * Rediriger vers le checkout apres un paiement qui demande confirmation
 * (type paypal express)
 * on saute l'etape de confirmation et on va direct au paiement
 *
 * @param $flux
 * @return mixed
 */
function formidablepaiement_formulaire_charger($flux){

	// gerer le retour paiement avec demande de confirmation
	if (_request('confirm')
		AND $flux['args']['form']=='formidable'
	  AND $id = $flux['args']['args'][0]
		AND isset($_SESSION['id_transaction'])
	  AND $id_transaction = $_SESSION['id_transaction']
	  AND $checkout = _request('checkout')
	  AND $trans = sql_fetsel("*","spip_transactions","id_transaction=".intval($id_transaction))){

		// verifier que la transaction en session est bien associee a ce formulaire
		$id_formulaire = formidable_id_formulaire($id);
		$parrain = "form{$id_formulaire}:";
		if (strncmp($trans['parrain'],$parrain,strlen($parrain))==0){
			// on reaffiche le modele de paiement qui demande confirmation
			$form = recuperer_fond("modeles/formidablepaiement-transaction",array('id_transaction'=>$id_transaction,'transaction_hash'=>$trans['transaction_hash']));
			$flux['data'] =
				"<div class='formulaire_spip formulaire_paiement'>"
				. $form
				. "</div>";

			$css = find_in_path("css/formidablepaiement.css");
			$flux['data'] .= "<style type='text/css'>@import url('".$css."');</style>";

			// Alternative par define ?
			// on redirige directement vers le paiment (url de checkout)
			// car on a rien a reafficher
			#include_spip('inc/headers');
			#redirige_par_entete($checkout);
		}
	}
	return $flux;
}


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

		include_spip('inc/securiser_action');
		$id = md5(@getmypid() . secret_du_site());
		if (isset($GLOBALS['formidable_post_'.$id])
		  AND  $form = $GLOBALS['formidable_post_'.$id]){
			$flux['data'] .=
				"<div class='formulaire_spip formulaire_paiement'>"
				. $form
				. "</div>";

			$css = find_in_path("css/formidablepaiement.css");
			$flux['data'] .= "<style type='text/css'>@import url('".$css."');</style>";
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