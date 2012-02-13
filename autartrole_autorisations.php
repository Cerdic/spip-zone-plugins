<?php
/**
 * Plugin ARTicle-AUTeur-ROLE pour Spip 2.0-2.1
 * Licence GPL (c) 20012-02-02 - GilCot
 */


if (!defined('_ECRIRE_INC_VERSION'))
	return;

// fonction pour le pipeline, n'a rien a effectuer
function autartrole_autoriser()
{
}

// declarations d'autorisations
function autoriser_autartrole_bouton_dist($faire, $type, $id, $qui, $opt)
{
	return autoriser('voir', 'autartrole', $id, $qui, $opt);
}

function autoriser_autartrole_voir_dist($faire, $type, $id, $qui, $opt)
{
	return true;
}

?>