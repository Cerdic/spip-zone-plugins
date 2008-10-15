<?php
/*
 * Plugin messagerie
 * Licence GPL
 * (c) 2008 C.Morin Yterium
 *
 */

// le pipeline messagerie_destiner permet d'ajouter/filtrer les destinataires en fonction des droits du visiteur
$GLOBALS['spip_pipeline']['messagerie_destiner'] = '';
$GLOBALS['spip_pipeline']['messagerie_signer_message'] = '';

/**
 * Verifier l'autorisation de choisir le destinataire d'un message
 *
 * @param unknown_type $faire
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opts
 * @return unknown
 */
function autoriser_message_destiner_dist($faire, $quoi, $id, $qui, $opts){
	// par defaut, le champ destinataire est toujours active
	return true;
}
/**
 * Verifier l'autorisation d'envoyer un message a tout le monde.
 * en indiquant 'general' comme destinataire
 * peut etre redefini par 
 * define('_EMAIL_GENERAL','general@domaine.org');
 * dans mes_options.php
 *
 * @param unknown_type $faire
 * @param unknown_type $quoi
 * @param unknown_type $id
 * @param unknown_type $qui
 * @param unknown_type $opts
 * @return unknown
 */
function autoriser_message_destiner_general_dist($faire, $quoi, $id, $qui, $opts){
	// par defaut, seuls les admins ont le droit de spammer
	return $qui['statut']=='0minirezo';
}

#define('_URL_ENVOYER_MESSAGE','art4');
#define('_REDIRECT_POST_ENVOI_MESSAGE','art3');

?>