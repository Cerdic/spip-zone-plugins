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
function cfg_verifier_type_idnum($champ, &$cfg){
	if (!is_numeric($cfg->val[$champ])){
		$cfg->ajouter_erreur(_T('cfg:erreur_type_idnum', array('champ'=>$champ)));
	}
	return true;
}

/**
 *
 * @param <type> $champ
 * @param <type> $cfg
 * @return <type> 
 */
function cfg_pre_traiter_type_idnum($champ, &$cfg){
	$cfg->val[$champ] = intval($cfg->val[$champ]);
	return true;
}

?>
