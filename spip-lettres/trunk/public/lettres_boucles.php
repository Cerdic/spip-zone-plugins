<?php


/**
 * SPIP-Lettres
 *
 * Copyright (c) 2006-2009
 * Agence Artégo http://www.artego.fr
 *  
 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
 *  
 **/


function boucle_LETTRES_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$mstatut = $id_table .'.statut';
	if (!isset($boucle->modificateur['criteres']['statut'])) {
		if (!defined('_VAR_PREVIEW') OR !_VAR_PREVIEW) {
			if (!isset($boucle->modificateur['tout']))
				$boucle->modificateur['criteres']['statut'] = true;
		}
	}
	return calculer_boucle($id_boucle, $boucles); 
}


?>
