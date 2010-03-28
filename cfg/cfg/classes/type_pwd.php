<?php
/**
 * Plugin générique de configuration pour SPIP
 *
 * @license    GNU/GPL
 * @package    plugins
 * @subpackage cfg
 * @category   outils
 * @copyright  (c) toggg, marcimat 2007-2008
 * @link       http://www.spip-contrib.net/
 * @version    $Id$
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * 
 * @param <type> $champ
 * @param <type> $cfg
 * @return <type>
 */
function cfg_verifier_type_pwd($champ, &$cfg) {
	if (strlen($cfg->val[$champ]) < 5){
		$cfg->ajouter_erreur($champ, _T('cfg:erreur_type_pwd', array('champ'=>$champ)));
	}
	return true;
}


?>
