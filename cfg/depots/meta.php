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
 * Retrouve et met a jour les donnees a plat dans spip_meta
 */
class cfg_depot_meta
{
	var $champs = array();
	var $champs_id = array();
	var $val = array();
	var $param = array();
	var $messages = array('message_ok'=>array(), 'message_erreur'=>array(), 'erreurs'=>array());
	
	// version du depot
	var $version = 2;

	/**
	 *
	 * @param Array $params
	 */
	function cfg_depot_meta($params=array())
	{
		foreach ($params as $o=>$v) {
			$this->$o = $v;
		}	
	}


	/**
	 * recuperer les valeurs.
	 * 
	 * unserialize : si la valeur est deserialisable, elle est retournee deserialisee
	 * permet a #CONFIG d'obtenir une valeur non deserialisee...
	 * 
	 * @param boolean $unserialize
	 * @return Array
	 */
	function lire($unserialize=true)
	{
    	$val = array();
    	if ($this->champs) {
			foreach ($this->champs as $name => $def) {
				// pour compat cfg, si la meta est deserialisable, la retourner deserialisee
				if ($unserialize && ($a = @unserialize($GLOBALS['meta'][$name])))
					$val[$name] = $a;
				else {
					$val[$name] = $GLOBALS['meta'][$name];
				}
			}
		// si pas d'argument, retourner comme le core serialize($GLOBALS['meta'])
		} else {
			$val = serialize($GLOBALS['meta']);
		}
	    return array(true, $val);
	}


	/**
	 * ecrit chaque enregistrement de meta pour chaque champ
	 *
	 * @return Array
	 */
	function ecrire()
	{
		foreach ($this->champs as $name => $def) {
			ecrire_meta($name, $this->val[$name]);
	    }
	    if (defined('_COMPAT_CFG_192')) ecrire_metas();
	    return array(true, $this->val);
	}
	
	
	/**
	 * supprime chaque enregistrement de meta pour chaque champ
	 * 
	 * @return Array
	 */
	function effacer(){
		foreach ($this->champs as $name => $def) {
			if (!$this->val[$name]) {
			    effacer_meta($name);
			}
	    }
	    if (defined('_COMPAT_CFG_192')) ecrire_metas();
	    return array(true, $this->val);			
	}
	
	
	/**
	 * charger les arguments de lire_config(meta::nom)
	 *
	 * @param string $args # $args = 'nom'; ici
	 * @return boolean
	 */
	function charger_args($args){
		if ($args) $this->champs = array($args=>true);
		return true;	
	}
}
?>
