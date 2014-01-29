<?php
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2010                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

function inc_verifier_document_mode_document_dist($infos){
	$log = "\n";
	if(is_array($infos)) {
		foreach($infos as $key => $value) {
			$log .= "infos[$key] => $value \n";
		}
	}

	$row = sql_fetsel('*', 'spip_types_documents','extension=' . sql_quote($infos['extension']));

	if(is_array($row)) {
		foreach($row as $key => $value) {
			$log .= "row[$key] => $value \n";
		}
	} else {
		$log .= "$row";
	}

	spip_log($log,'mode_document');

	if ($row['interdit'] == 'oui') {
		return _T('types_documents:erreur_extension_interdite',array('nom'=> $infos['fichier'])); // extension interdite à l'upload
	}

	return true;
}