<?php
/**
 * Traitement abonnement à la saisie d'un formulaire
 *
 * @plugin     Formulaires d'abonnement
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableabonnement\traiter\abonnement
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_abonnement_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);

	// saisies dans le formulaire
	if ($options['champ_choix_abo'])
	  $choix_abo = _request($options['champ_choix_abo']);

	if ($options['champ_email_abo'])
	  $email_abo = _request($options['champ_email_abo']);
	
	
	if ($options['champ_nom_abo'])
	  $nom_abo = _request($options['champ_nom_abo']);
	
	
	if ($options['champ_prenom_abo'])
	  $prenom_abo = _request($options['champ_prenom_abo']);
	
	if ($options['champ_organisme_abo'])
	  $organisme_abo = _request($options['champ_organisme_abo']);
		  
	if ($options['champ_choix_abo']){
		  $choix_abo = _request($options['champ_choix_abo']);
		  
		  if ($options['choix_abo_oui'])
		  	  $abo_oui = $options['choix_abo_oui'];
		  
		  if($choix_abo == $abo_oui) $choix_abo='abonnement';
		  else $choix_abo='desabonnement';
	}
	  
	$options = array(
		'choix_abo' => $choix_abo,
		'email' => $email_abo,
		'nom' => $nom_abo,
		'prenom' => $prenom_abo,
		'organisme' => $organisme_abo,
		'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
		'parrain' => 'form'.$formulaire['id_formulaire'].':'.$formulaire['identifiant'],
		'tracking_id' => $id_formulaires_reponse,
		'traiter_abonnement' => false,
	);
	
	// fabrique le pipeline traiter_formidableabonnement.
	$pipeline = pipeline('traiter_formidableabonnement',array('args'=>$options,'data'=>$pipeline));

	spip_log("$choix_abo pour $email_abo","formidable_abo");
	
	return $retours;
}
