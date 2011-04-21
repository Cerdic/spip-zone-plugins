<?php
/**
 * Fonction de revision d'une chaine de langue
 * Utile particulièrement pour les crayons
 * 
 * @param int $id_tradlang
 * @param array $c [optional]
 * @return 
 */
function revision_tradlang($id_tradlang,$c=false){
	$invalideur = "id='id_tradlang/$id_tradlang'";

	return modifier_contenu('tradlang', $id_tradlang,
		array(
			'invalideur' => $invalideur,
			'date_modif' => 'ts'
		),
		$c);
}

function str_statut_revision($id_tradlang,$c=false){
	if(isset($c['statut'])){
		include_spip('action/editer_tradlang');
		$statut['statut'] = $c['statut'];
	}
	
	if(is_array($statut)){
		instituer_tradlang($id_tradlang, $statut);
	}
	return modifier_contenu('tradlang', $id_tradlang,
		array(
			'invalideur' => $invalideur,
			'date_modif' => 'ts'
		),
		$c);
}
?>