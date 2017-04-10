<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function etiquettes_vue_tags($rien, $type_objet, $id_objet){

	include_spip('base/abstract_sql');
	include_spip('base/connect_sql');

	$reponse = sql_select(
		'mots.id_mot, mots.titre',
		array('mots' => 'spip_mots', 'liaison' => 'spip_mots_liens'),
		array(
			//array('=', 'mots.type', _q('tags')),
			array('=', 'liaison.objet', sql_quote(objet_type($type_objet))),
			array('=', 'liaison.id_objet', $id_objet),
			array('=', 'mots.id_mot', 'liaison.id_mot')
		),
		"",
		"mots.titre"
	);
	
	$liste_ul = "";
	while ($mot = sql_fetch($reponse)){

		$liste_ul .= "<li><a href=\"".generer_url_entite($mot['id_mot'], 'mot')."\" rel=\"tag\">".$mot['titre']."</a></li>\n";

	}
	if ($liste_ul)
		$liste_ul = "<h2>"._T('public:mots_clefs')."</h2>\n<ul>\n".$liste_ul."</ul>";

	return $liste_ul;
	
}

?>
