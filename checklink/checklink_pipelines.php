<?php

include_spip('inc/indexation');
include_spip('base/checklink');
include_spip('inc/checklink');
include_spip('inc/texte');

function checklink_pre_enregistre_contenu($flux){
	static $objet_traite=array();
	if (!isset($flux['args']['id_objet']) || !isset($flux['args']['table']))
		return; // rien a faire ici ...
	// renseigner la table
	$id_objet = $flux['args']['id_objet'];
	$table = $flux['args']['table'];
	$id_table = id_index_table($table);
	
	// si on a pas commence a traiter cet objet, marquer tous ses liens existants comme obsolete
	if (!count($objet_traite)) checklink_verifier_base();
	if (!isset($objet_traite[$id_table]) OR !isset($objet_traite[$id_table][$id_objet]) ){
		spip_query("UPDATE spip_liens SET obsolete='oui' WHERE id_table=$id_table AND id_objet=$id_objet");
		$objet_traite[$id_table][$id_objet] = true;
	}
	
	// passer le contenu dans propre pour transformer les liens internes et les modeles eventuels
	$letexte = propre(join(' ',$flux['data']));
	checklink_extrait_liens($id_table,$id_objet,$letexte);
	return $flux;
}

?>