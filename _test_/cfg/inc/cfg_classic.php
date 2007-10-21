<?php
/*
 * Plugin cfg : classe cfg_classic: storage a plat (classique) dans spip_meta
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

// cfg_classic retrouve et met a jour les donnees a plat dans spip_meta
class cfg_classic
{
	function cfg_classic(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
    	$val = array();
		foreach ($this->cfg->champs as $name => $def) {
			$val[$name] = lire_config($name);
	    }
	    return $val;
	}

	// modifier chaque enregistrement de meta pour chaque champ
	function modifier($supprimer = false)
	{
		foreach ($this->cfg->champs as $name => $def) {
			if ($supprimer || !$this->cfg->val[$name]) {
			    effacer_meta($name);
			} else {
			    ecrire_meta($name, $this->cfg->val[$name]);
			}
	    }
	    if (version_compare($GLOBALS['spip_version_code'],'1.93','<')) ecrire_metas();
	    return true;
	}
}
?>
