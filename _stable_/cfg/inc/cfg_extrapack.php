<?php

/*
 * Plugin CFG pour SPIP
 * (c) toggg 2007, distribue sous licence GNU/GPL
 * Documentation et contact: http://www.spip-contrib.net/
 *
 * classe cfg_extrapack: storage serialise dans extra de spip_auteurs ou autre
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/cfg_tablepack");

// cfg_extrapack retrouve et met a jour les donnees serialisees dans la colonne extra
// d'une table "objet" $cfg->table, par défaut spip_auteurs
// ici, $cfg->cfg_id est obligatoire ... peut-être mappé sur l'auteur courant (a voir)
class cfg_extrapack extends cfg_tablepack
{
	function cfg_extrapack(&$cfg, $opt = array())
	{
		$cfg->colonne = 'extra';
		parent::cfg_tablepack($cfg, $opt);
	}
}
?>
