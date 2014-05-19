<?php
/**
 * Traitement paiement apres la saisie d'un formulaire
 *
 * @plugin     Formulaires de paiement
 * @copyright  2014
 * @author     Cédric Morin
 * @licence    GNU/GPL
 * @package    SPIP\Formidablepaiement\traiter\paiement
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_paiement_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);

	// il faut d'abord avoir enregistre
	if (!isset($retours['traitements']['enregistrement'])
	  OR !$id_formulaires_reponse = $retours['id_formulaires_reponse'])
		return $retours;

	$montant_ht = $montant_ttc = 0;
	// recuperer le montant

	// montant fixe/par defaut
	if ($options['montant_fixe']){
		$montant_ht = $montant_ttc = $options['montant_fixe'];
	}
	// saisie dans le formulaire

	if ($options['champ_montant']
	  AND $m = _request($options['champ_montant'])
	  AND is_numeric($m)){
		$montant_ht = $montant_ttc = $m;
	}

	// tva ?
	if (floatval($tva = $options['tva'])){
		if ($options['taxes']=="ttc"){
			$montant_ht = $montant_ttc / ((100+$tva)/100);
		}
		if ($options['taxes']=="ht"){
			$montant_ttc = $montant_ht * ((100+$tva)/100);
		}
	}

	// preparer la transaction
	$options = array(
		'montant_ht' => $montant_ht,
		'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
		'parrain' => 'form'.$formulaire['id_formulaire'].':'.$formulaire['identifiant'],
		'tracking_id' => $id_formulaires_reponse,
	);

	$inserer_transaction = charger_fonction("inserer_transaction","bank");
	$id_transaction = $inserer_transaction($montant_ttc, $options);

	// inserer le form de paiement dans le message_ok
	// il sera deplace dans le html par le pipeline formulaire_fond
	if ($id_transaction
	  AND $hash = sql_getfetsel("transaction_hash","spip_transactions","id_transaction=".intval($id_transaction))){
		$form = recuperer_fond(
			"modeles/formidablepaiement-transaction",
			array(
				'id_transaction'=>$id_transaction,
				'transaction_hash'=>$hash,
				'id_formulaires_reponse' => $id_formulaires_reponse,
			)
		);

		include_spip('inc/securiser_action');
		$id = md5(@getmypid() . secret_du_site());
		$GLOBALS['formidable_post_'.$id] = $form;
	}

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['paiement'] = true;
	return $retours;
}
