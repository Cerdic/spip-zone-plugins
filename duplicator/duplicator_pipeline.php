<?php

/***************************************************************************\
 * Plugin Duplicator pour Spip 2.0
 * Licence GPL (c) 2010 - Apsulis
 * Duplication de rubriques et d'articles
 *
\***************************************************************************/


function duplicator_boite_infos($flux){
	$type = $flux['args']['type'];
	if ($id = intval($flux['args']['id']))
		$flux['data'] .= icone_horizontale(_T('duplicator:icone_dupliquer'), generer_url_ecrire('duplicator',"id_objet=$id&type=$type"), "", "../"._DIR_PLUGIN_DUPLICATOR."/images/duplicator.gif", false);
	return $flux;
}
?>