<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;


include_spip('inc/precharger_objet');

function inc_precharger_camera_dist($id_camera, $id_rubrique=0, $lier_trad=0) {
	return precharger_objet('camera', $id_camera, $id_rubrique, $lier_trad, 'titre');
}

// fonction facultative si pas de changement dans les traitements
function inc_precharger_traduction_camera_dist($id_camera, $id_rubrique=0, $lier_trad=0) {
	return precharger_traduction_objet('camera', $id_camera, $id_rubrique, $lier_trad, 'titre');
}



?>
