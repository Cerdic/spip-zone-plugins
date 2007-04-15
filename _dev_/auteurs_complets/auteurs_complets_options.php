<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_AUTEURS_COMPLETS',(_DIR_PLUGINS.end($p)));
include_spip('base/auteurs_complets');

function autoriser_auteur_modifier($faire, $type, $id, $qui, $opt) {

	// Un redacteur peut modifier ses propres donnees mais ni son login
	// ni son statut (qui sont le cas echeant passes comme option)
	if (in_array($qui['statut'], array('1comite', '6forum'))) {
		if ($opt['statut'] OR $opt['restreintes'])
			return false;
		else if ($id == $qui['id_auteur'])
			return true;
		else
			return false;
	}

	// Un admin restreint peut modifier/creer un auteur non-admin mais il
	// n'a le droit ni de le promouvoir admin, ni de changer les rubriques
	if ($qui['restreint']) {
		if ($opt['statut'] == '0minirezo'
		OR $opt['restreintes']) {
			return false;
		} else {
			if ($id == $qui['id_auteur']) {
				if ($opt['statut'])
					return false;
				else
					return true;
			}
			else if ($id_auteur = intval($id)) {
				$s = spip_query("SELECT statut FROM spip_auteurs WHERE id_auteur=$id_auteur");
				if ($t = spip_fetch_array($s)
				AND $t['statut'] != '0minirezo')
					return true;
				else
					return false;
			}
			// id = 0 => creation
			else
				return true;
		}
	}
	return true;
}

?>