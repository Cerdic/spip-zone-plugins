<?php
/**
 * Traitement identification à la saisie d'un formulaire
 *
 * @plugin     Formulaires d'identification
 * @copyright  2014
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * @package    SPIP\Formidableidentification\traiter\identification
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

function traiter_identification_dist($args, $retours){
	$formulaire = $args['formulaire'];
	$options = $args['options'];
	$saisies = unserialize($formulaire['saisies']);
	$traitements = unserialize($formulaire['traitements']);
	$id_formulaires_reponse = $retours['id_formulaires_reponse'];
	$champs = saisies_lister_champs($saisies);
	$mode = '6forum';
	if(_request('formidable_identification') == 'on' && !isset($GLOBALS['visiteur_session']['id_auteur']) && _request('formulaire_identification_email') && autoriser('inscrireauteur', $mode, 0)){
		$options = array(
				'choix_identification' => $choix_identification,
				'email' => $email_identification,
				'nom' => $nom_identification,
				'id_auteur' => (isset($GLOBALS['visiteur_session']['id_auteur'])?$GLOBALS['visiteur_session']['id_auteur']:0),
				'tracking_id' => $retours['id_formulaires_reponse'],
		);
		include_spip('inc/filtres');
		include_spip('inc/autoriser');

		$mail_complet = _request('formulaire_identification_email');
		$nom = _request('formulaire_identification_nom') ? _request('formulaire_identification_nom') : $mail_complet;

		$inscrire_auteur = charger_fonction('inscrire_auteur','action');
		$desc = $inscrire_auteur($mode, $mail_complet, $nom, array('id'=>$id,'redirect'=> parametre_url(self(),'validation_inscription','ok')));
		if(intval($id_formulaires_reponse) > 0 && isset($desc['id_auteur']) && intval($desc['id_auteur']) > 0){
			sql_updateq('spip_formulaires_reponses',array('id_auteur' => $desc['id_auteur'],'statut' => 'attente'),'id_formulaires_reponse = '.intval($id_formulaires_reponse));
		}
	}
	

	$pipeline = pipeline('traiter_formidable_identification',array('args'=>$options,'data'=>$pipeline));

	// noter qu'on a deja fait le boulot, pour ne pas risquer double appel
	$retours['traitements']['identification'] = true;
	
	return $retours;
}

/**
 * Surcharge de la fonction envoyer_inscription_dist (dans action/inscrire_auteur.php) afin de rediriger vers la page du formulaire
 */
function envoyer_inscription($desc, $nom, $mode, $options=array()) {

	$contexte = array_merge($desc,$options);
	$contexte['nom'] = $nom;
	$contexte['mode'] = $mode;
	$contexte['url_confirm'] = generer_url_action('confirmer_inscription','',true,true);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'],'email',$desc['email']);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'],'jeton',$desc['jeton']);
	$contexte['url_confirm'] = parametre_url($contexte['url_confirm'],'redirect',$options['redirect']);

	$message = recuperer_fond('modeles/mail_inscription',$contexte);
	$from = (isset($options['from'])?$options['from']:null);
	$head = null;
	return array("", $message,$from,$head);
}