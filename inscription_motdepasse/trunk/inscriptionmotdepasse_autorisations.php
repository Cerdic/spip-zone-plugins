<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inscriptionmotdepasse_autoriser(){}

function autoriser_loger($faire, $quoi, $id, $qui, $opt) {    
    if ( $qui['statut'] == '5poubelle' or $qui['statut'] == 'nouveau' ) return false;
    return true;
}

