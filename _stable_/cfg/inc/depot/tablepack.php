<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_extrapack: storage serialise dans extra de spip_auteurs ou autre
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

// cfg_extrapack retrouve et met a jour les donnees serialisees dans la colonne extra
// d'une table "objet" $cfg->param->table, par défaut spip_auteurs
// ici, $cfg->param->cfg_id est obligatoire ... peut-être mappé sur l'auteur courant (a voir)
class cfg_tablepack
{
	function cfg_tablepack(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg->param->colonne || ($this->cfg->param->colonne = 'cfg'); // stockage dans quelle colonne de la table sql 
		$this->cfg->param->table || ($this->cfg->param->table = 'spip_auteurs');
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
 		if (!$this->cfg->param->cfg_id) {
			$this->cfg->message = _T('cfg:id_manquant');
			return false;
		}

    	$val = lire_config(array(
    		$this->cfg->param->table,
    		$this->cfg->param->colonne,
    		'',
    		$cles = explode('/', $this->cfg->param->cfg_id),
    		$this->cfg->param->casier ? explode('/', $this->cfg->param->casier) : array()
    	));

		foreach ($this->cfg->champs_id as $i => $name) {
			$val[$name] = $cles[$i];
	    }
	    return $val;
	}
	
// se positionner dans le tableau arborescent
	function & monte_arbre(&$base, $chemin)
	{
		if (!$chemin) {
			return $base;
		}
		foreach (explode('/', $chemin) as $chunk) {
			if (!isset($base[$chunk])) {
				$base[$chunk] = array();
			}
	    	$this->_report[] = array(&$base, $chunk);
	    	$base = &$base[$chunk];
		}
		return $base;
	}
	
// modifier le fragment qui peut etre tout le meta
	function modifier($supprimer = false)
	{
		// retrouver les donnees racines
    	($base = lire_config($args = array(
    		$this->cfg->param->table,
    		$this->cfg->param->colonne,
    		'',
    		$cles = explode('/', $this->cfg->param->cfg_id),
    		array()
    	))) || ($base = array());
    	
    	// modifier ces donnees en fonction des changements
    	$ici = &$base;
    	$this->_report = array();
    	$ici = &$this->monte_arbre($ici, $this->cfg->casier);
		foreach ($this->cfg->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			if ($supprimer) {
				unset($ici[$name]);
			} else {
				$ici[$name] = $this->cfg->val[$name];
			}
	    }
		if ($supprimer) {
			for ($i = count($this->_report); $i--; ) {
				if ($this->_report[$i][0][$this->_report[$i][1]]) {
					break;
				}
				unset($this->_report[$i][0][$this->_report[$i][1]]);
			}
		}
		
		// ecrire les changements
		return ecrire_config($args,  ($base ? $base : "") );

	}
}
?>
