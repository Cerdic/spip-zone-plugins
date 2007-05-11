<?php
/*
 * Plugin cfg : classe cfg_php: storage dans un fichier php
 *
 * Auteur : bertrand@toggg.com
 * © 2007 - Distribue sous licence LGPL
 *
 */

// cfg_php retrouve et met a jour les donnees serialisees dans spip_meta
class cfg_php
{
	function cfg_php(&$cfg, $opt = array())
	{
		$this->cfg = &$cfg;
		foreach ($opt as $o=>$v) {
			$this->$o = $v;
		}
		$this->cfg->fichier || ($this->cfg->fichier =
			_DIR_VAR . 'cfg/' . $this->cfg->nom . '.php');
	}
	
// recuperer les valeurs, utilise la fonction commune lire_config() de cfg_options.php
	function lire()
	{
		$cfg = null;

    	@include $this->cfg->fichier;

    	if (!$cfg) {
    		return array();
    	}
    	$this->_report = array();
    	$cfg = &$this->monte_arbre($cfg, $this->cfg->nom);
    	$cfg = &$this->monte_arbre($cfg, $this->cfg->casier);
    	$cfg = &$this->monte_arbre($cfg, $this->cfg->cfg_id);
    	if ($this->cfg->cfg_id) {
    		$cles = explode('/', $this->cfg->cfg_id);
			foreach ($this->cfg->champs_id as $i => $name) {
				$cfg[$name] = $cles[$i];
		    }
    	}
	    return $cfg;
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
		$base = null;
    	@include $this->cfg->fichier;
    	if (!$base) {
    		$base = array();
    	}

    	$ici = &$base;
    	$this->_report = array();
    	$ici = &$this->monte_arbre($ici, $this->cfg->casier);
    	$ici = &$this->monte_arbre($ici, $this->cfg->cfg_id);
		foreach ($this->cfg->champs as $name => $def) {
			if (isset($def['id'])) {
				continue;
			}
			if ($supprimer) {
				unset($ici[$name]);
			} else {
				$ici[$name] = isset($def['typ']) && ($cnv = $this->cfg->types[$def['typ']][2]) ?
					$cnv($this->cfg->val[$name]) : $this->cfg->val[$name];
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
		if ($supprimer && !$base) {
		    unlink($this->cfg->fichier);
		} else {
			if (!is_dir($dir = dirname($this->cfg->fichier))) {
				mkdir($dir);
			}
			$fp = @fopen($this->cfg->fichier, "w");
			if ($fp === false) {
				$this->cfg->message = $this->cfg->fichier . ': ' .
						 _T('cfg:erreur_open_w_fichier');
				return false;
			}
			$code = '<?php
/**************
* Config ecrite par cfg le ' . date('r') . ' pour ' . $this->cfg->nom_config() . '
* 
* NE PAS EDITER MANUELLEMENT !
***************/

$cfg["' . $this->cfg->nom . '"] = ' . var_export($base, true) . ';
?>
';
			fputs($fp, $code);
			@fclose($fp);
	    }
	    return true;
	}
}
?>
