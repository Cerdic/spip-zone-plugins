<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
* Autorisation des boutons
* Si les stats sont activees => dans stat
* sinon => edition
*
**/

function anaclic_autoriser(){}

function autoriser_statistiques_anaclic_bouton_dist($faire, $type, $id, $qui, $opt) 
{	// Les memes que pour les stats
    return autoriser('voirstats', $type, $id, $qui, $opt);
}

// Mettre dans edition si les stats ne sont pas actives
function autoriser_statistiques_anaclic_nav_bouton_dist($faire, $type, $id, $qui, $opt) 
{	return ($GLOBALS['meta']['activer_statistiques']!='oui' && autoriser('statistiques_anaclic_bouton', $type, $id, $qui, $opt));
}

?>