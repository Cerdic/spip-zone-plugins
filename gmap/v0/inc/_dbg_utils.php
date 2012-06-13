<?php
/*
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Debug pour SPIP core
 *
 */
 
// Dump d'un champ
function _DBG_DumpField($nom, $f, $file = NULL, $tab = '', $interface = NULL)
{
	if (is_null($f) || !$f)
		spip_log($tab.$nom." = NULL", $file);
	else if (is_array($f))
	{
		spip_log($tab.$nom." tableau :", $file);
		_DBG_DumpArray($nom, $f, $file, $tab."\t", $interface);
	}
	else if (is_object($f))
	{
		if (!$interface && isset($f->type))
			$interface = $f->type;
		if ($interface)
		{
			spip_log($tab.$nom." interface ".$interface." :", $file);
			_DBG_DumpInterface($nom, $f, $file, $tab."\t", $interface);
		}
		else
			spip_log($tab.$nom." = <objet>", $file);
	}
	else if (is_string($f))
	{
		if (strlen($f) > 200)
			$f = substr($f, 0, 200);
		$f = str_replace(array("\r","\n","\t"), array("\\r","\\n","\\t"), $f);
		spip_log($tab.$nom." (".gettype($f).") = ".$f, $file);
	}
	else
		spip_log($tab.$nom." (".gettype($f).") = ".$f, $file);
}

// Dump d'un tableau
function _DBG_DumpArray($nom, $t, $file = NULL, $tab = '', $interface = NULL)
{
	if (!$t)
		spip_log($tab.$nom." = NULL", $file);
	else
	{
		spip_log($tab.$nom.".count = ".count($t), $file);
		foreach ($t AS $key => $value)
			_DBG_DumpField($nom."['".$key."']", $value, $file, $tab, $interface);
	}
}

// Dump d'un objet d'interface SPIP
function _DBG_DumpInterface($nom, $f, $file = NULL, $tab = '', $interface = NULL)
{
	if (!$interface)
		$interface = $f->type;
	if ($interface === "critere")
		_DBG_DumpCritere($nom, $f, $file, $tab);
	else if ($interface === "boucle")
		_DBG_DumpBoucle($nom, $f, $file, $tab);
	else if ($interface === "champ")
		_DBG_DumpChamp($nom, $f, $file, $tab);
	else if ($interface === "texte")
		_DBG_DumpTexte($nom, $f, $file, $tab);
	else
		spip_log($tab.$nom." = <interface ".$interface.">", $file);
}

// Dump d'une structure Critere
function _DBG_DumpCritere($nom, $c, $file = NULL, $tab = '')
{
	spip_log($tab."#### Critere ".$nom." :", $file);
	
	_DBG_DumpField($nom."->op", $c->op, $file, $tab);
	_DBG_DumpField($nom."->not", $c->not, $file, $tab);
	_DBG_DumpField($nom."->exclus", $c->exclus, $file, $tab);
	_DBG_DumpField($nom."->param", $c->param, $file, $tab);
	_DBG_DumpField($nom."->ligne", $c->ligne, $file, $tab);
}

// Dump d'une structure Boucle
function _DBG_DumpBoucle($nom, $b, $file = NULL, $tab = '')
{
	spip_log($tab."#### Boucle ".$nom." :", $file);
	
	_DBG_DumpField($nom."->type_requete", $b->type, $file, $tab);
	_DBG_DumpField($nom."->id_boucle", $b->id_boucle, $file, $tab);
	_DBG_DumpField($nom."->id_parent", $b->id_parent, $file, $tab);
	_DBG_DumpField($nom."->avant", $b->avant, $file, $tab);
	_DBG_DumpField($nom."->milieu", $b->milieu, $file, $tab);
	_DBG_DumpField($nom."->apres", $b->apres, $file, $tab);
	_DBG_DumpField($nom."->altern", $b->altern, $file, $tab);
	_DBG_DumpField($nom."->lang_select", $b->lang_select, $file, $tab);
	_DBG_DumpField($nom."->type_requete", $b->type_requete, $file, $tab);
	_DBG_DumpField($nom."->table_optionnelle (si ? dans <BOUCLE_x(table ?)>)", $b->table_optionnelle, $file, $tab);
	_DBG_DumpField($nom."->sql_serveur", $b->sql_serveur, $file, $tab);
	_DBG_DumpField($nom."->param", $b->param, $file, $tab);
	_DBG_DumpField($nom."->criteres", $b->criteres, $file, $tab, "critere");
	_DBG_DumpField($nom."->separateur", $b->separateur, $file, $tab);
	_DBG_DumpField($nom."->jointures", $b->jointures, $file, $tab);
	_DBG_DumpField($nom."->jointures_explicites", $b->jointures_explicites, $file, $tab);
	_DBG_DumpField($nom."->doublons", $b->doublons, $file, $tab);
	_DBG_DumpField($nom."->partie", $b->partie, $file, $tab);
	_DBG_DumpField($nom."->total_parties", $b->total_parties, $file, $tab);
	_DBG_DumpField($nom."->mode_partie", $b->mode_partie, $file, $tab);
	_DBG_DumpField($nom."->externe (appel a partir d'une autre boucle (recursion))", $b->externe, $file, $tab);
	
	spip_log($tab."## champs pour la construction de la requete SQL :", $file);
	_DBG_DumpField($nom."->select", $b->select, $file, $tab);
	_DBG_DumpField($nom."->from", $b->from, $file, $tab);
	_DBG_DumpField($nom."->from_type", $b->from_type, $file, $tab);
	_DBG_DumpField($nom."->where", $b->where, $file, $tab);
	_DBG_DumpField($nom."->join", $b->join, $file, $tab);
	_DBG_DumpField($nom."->having", $b->having, $file, $tab);
	_DBG_DumpField($nom."->limit", $b->limit, $file, $tab);
	_DBG_DumpField($nom."->group", $b->group, $file, $tab);
	_DBG_DumpField($nom."->order", $b->order, $file, $tab);
	_DBG_DumpField($nom."->default_order", $b->default_order, $file, $tab);
	_DBG_DumpField($nom."->date", $b->date, $file, $tab);
	_DBG_DumpField($nom."->hash", $b->hash, $file, $tab);
	_DBG_DumpField($nom."->in", $b->in, $file, $tab);
	_DBG_DumpField($nom."->sous_requete", $b->sous_requete, $file, $tab);
	_DBG_DumpField($nom."->hierarchie", $b->hierarchie, $file, $tab);
	_DBG_DumpField($nom."->statut (definition/surcharge du statut des elements retournes)", $b->statut, $file, $tab);
	
	spip_log($tab."## champs pour la construction du corps PHP :", $file);
	_DBG_DumpField($nom."->show", $b->show, $file, $tab);
	_DBG_DumpField($nom."->id_table", $b->id_table, $file, $tab);
	_DBG_DumpField($nom."->primary", $b->primary, $file, $tab);
	_DBG_DumpField($nom."->return", $b->return, $file, $tab);
	_DBG_DumpField($nom."->numrows", $b->numrows, $file, $tab);
	_DBG_DumpField($nom."->cptrows", $b->cptrows, $file, $tab);
	_DBG_DumpField($nom."->ligne", $b->ligne, $file, $tab);
	_DBG_DumpField($nom."->descr", $b->descr, $file, $tab);
	_DBG_DumpField($nom."->modificateur", $b->modificateur, $file, $tab);

	spip_log($tab."## obsoletes, conserves provisoirement pour compatibilite :", $file);
	_DBG_DumpField($nom."->tout", $b->tout, $file, $tab);
	_DBG_DumpField($nom."->plat", $b->plat, $file, $tab);
	_DBG_DumpField($nom."->lien", $b->lien, $file, $tab);
}

// Dump d'une structure Champ
function _DBG_DumpChamp($nom, $p, $file = NULL, $tab = '')
{
	spip_log($tab."#### Champ ".$nom." :", $file);
	
	_DBG_DumpField($nom."->nom_champ", $p->nom_champ, $file, $tab);
	_DBG_DumpField($nom."->nom_boucle", $p->nom_boucle, $file, $tab);
	_DBG_DumpField($nom."->avant", $p->avant, $file, $tab);
	_DBG_DumpField($nom."->apres", $p->apres, $file, $tab);
	_DBG_DumpField($nom."->etoile", $p->etoile, $file, $tab);
	_DBG_DumpField($nom."->param", $p->param, $file, $tab);
	_DBG_DumpField($nom."->fonctions", $p->fonctions, $file, $tab);
	_DBG_DumpField($nom."->id_boucle", $p->id_boucle, $file, $tab);
	// Pas dumpé pour problème de récursion
	//_DBG_DumpField($nom."->boucles", $p->boucles, $file, $tab);
	_DBG_DumpField($nom."->type_requete", $p->type_requete, $file, $tab);
	_DBG_DumpField($nom."->code", $p->code, $file, $tab);
	_DBG_DumpField($nom."->interdire_scripts", $p->interdire_scripts, $file, $tab);
	_DBG_DumpField($nom."->descr", $p->descr, $file, $tab);
	_DBG_DumpField($nom."->ligne", $p->ligne, $file, $tab);
}

// Dump d'une structure Texte
function _DBG_DumpTexte($nom, $t, $file = NULL, $tab = '')
{
	spip_log($tab."#### Texte ".$nom." :", $file);
	
	_DBG_DumpField($nom."->texte", $t->texte, $file, $tab);
	_DBG_DumpField($nom."->avant", $t->avant, $file, $tab);
	_DBG_DumpField($nom."->apres", $t->apres, $file, $tab);
	_DBG_DumpField($nom."->ligne", $t->ligne, $file, $tab);
}



?>