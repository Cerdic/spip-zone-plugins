<?php

function anaclic_autoriser(){}

/** autorisation des boutons
	Si les stats sont activees => dans stat
	sinon => edition
*/
function autoriser_statistiques_anaclic_bouton_dist($faire, $type, $id, $qui, $opt) 
{	// Les memes que pour les stats
    return autoriser('voirstats', $type, $id, $qui, $opt);
}

// Mettre dans edition si les stats ne sont pas actives
function autoriser_statistiques_anaclic_nav_bouton_dist($faire, $type, $id, $qui, $opt) 
{	return ($GLOBALS['meta']['activer_statistiques']!='oui' && autoriser('statistiques_anaclic_bouton', $type, $id, $qui, $opt));
}

?>