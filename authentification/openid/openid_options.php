<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

$GLOBALS['liste_des_authentifications']['openid'] = 'openid';

/**
 * Pipeline permettant de modifier le tableau des informations passée à l'action
 * finale d'authentification après récupération des informations du provider
 * 
 * cf : inc/openid.php
 */
$GLOBALS['spip_pipeline']['openid_recuperer_identite'] = '';

/**
 * Pipeline permettant de modifier l'url de redirection de l'action
 * finale d'identification pour y ajouter en paramètre les champs demandés
 * 
 * cf : action/inscrire_openid.php
 */
$GLOBALS['spip_pipeline']['openid_inscrire_redirect'] = '';
?>