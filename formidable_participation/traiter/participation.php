<?php
/**
 * Traitement participation à la saisie d'un formulaire
 *
 * @plugin     Formulaires de participation
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableparticipation\traiter\participation
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_participation_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	//$champs = saisies_lister_champs($saisies);

	// saisies dans le formulaire
	if ($options['champ_choix_participation'])
	  $choix_participation = _request($options['champ_choix_participation']);

	if ($options['champ_email_participation'])
	  $email_participation = _request($options['champ_email_participation']);
	
	
	if ($options['champ_nom_participation'])
	  $nom_participation = _request($options['champ_nom_participation']);
	
	
	if ($options['champ_prenom_participation'])
	  $prenom_participation = _request($options['champ_prenom_participation']);
	
	if ($options['champ_organisme_participation'])
	  $organisme_participation = _request($options['champ_organisme_participation']);
		  
	if ($options['champ_choix_participation']){
		  $choix_participation = _request($options['champ_choix_participation']);
		  
		  if ($options['choix_participation_oui'])
		  	  $participation_oui = $options['choix_participation_oui'];
		  
		  if($choix_participation == $participation_oui) $choix_participation='oui';
		  else $choix_participation='non';
	}
	  
	$options = array(
		'id_evenement'=> $options['id_evenement_participation'], //si oui, traitement avec agenda
		'choix_participation' => $choix_participation,
		'email' => $email_participation,
		'nom' => $nom_participation,
		'prenom' => $prenom_participation,
		'organisme' => $organisme_participation,
		'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
		'parrain' => 'form'.$formulaire['id_formulaire'].':'.$formulaire['identifiant'],
		'tracking_id' => $retours['id_formulaires_reponse'],
	);
	
	// fabrique le pipeline traiter_formidableparticipation.
	$pipeline = pipeline('traiter_formidableparticipation',array('args'=>$options,'data'=>$pipeline));

	spip_log("$choix_participation pour $email_participation evenement N°".$options['id_evenement'],"formidable_participation");
	
	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['participation'] = true;
	
	return $retours;
}
