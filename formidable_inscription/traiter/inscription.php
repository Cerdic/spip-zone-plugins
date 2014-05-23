<?php
/**
 * Traitement inscription à la saisie d'un formulaire
 *
 * @plugin     Formulaires d'inscription
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableinscription\traiter\inscription
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_inscription_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$champs = saisies_lister_champs($saisies);

	// saisies dans le formulaire
	if ($options['champ_choix_inscription'])
	  $choix_inscription = _request($options['champ_choix_inscription']);

	if ($options['champ_email_inscription'])
	  $email_inscription = _request($options['champ_email_inscription']);
	
	
	if ($options['champ_nom_inscription'])
	  $nom_inscription = _request($options['champ_nom_inscription']);
	
	
	if ($options['champ_prenom_inscription'])
	  $prenom_inscription = _request($options['champ_prenom_inscription']);
	
	if ($options['champ_organisme_inscription'])
	  $organisme_inscription = _request($options['champ_organisme_inscription']);
		  
	if ($options['champ_choix_inscription']){
		  $choix_inscription = _request($options['champ_choix_inscription']);
		  
		  if ($options['choix_inscription_oui'])
		  	  $inscription_oui = $options['choix_inscription_oui'];
		  
		  if($choix_inscription == $inscription_oui) $choix_inscription='inscription';
		  else $choix_inscription='desinscription';
	}
	  
	$options = array(
		'choix_inscription' => $choix_inscription,
		'email' => $email_inscription,
		'nom' => $nom_inscription,
		'prenom' => $prenom_inscription,
		'organisme' => $organisme_inscription,
		'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
		'parrain' => 'form'.$formulaire['id_formulaire'].':'.$formulaire['identifiant'],
		'tracking_id' => $id_formulaires_reponse,
		'traiter_inscription' => false,
	);
	
	// fabrique le pipeline traiter_formidableinscription.
	$pipeline = pipeline('traiter_formidableinscription',array('args'=>$options,'data'=>$pipeline));

	spip_log("$choix_inscription pour $email_inscription","formidable_inscription");
	
	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['inscription'] = true;
	
	return $retours;
}
