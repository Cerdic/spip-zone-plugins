<?php
/**
 * Plugin OpenID
 * Licence GPL (c) 2007-2010 Edouard Lafargue, Mathieu Marcillaud, Cedric Morin, Fil
 *
 */

$GLOBALS['liste_des_authentifications']['fblogin'] = 'fblogin';

/**
 * Pipeline permettant de modifier le tableau des informations pass�e � l'action
 * finale d'authentification apr�s r�cup�ration des informations du provider
 *
 * cf : inc/fblogin.php
 */
$GLOBALS['spip_pipeline']['fblogin_recuperer_identite'] = '';



?>