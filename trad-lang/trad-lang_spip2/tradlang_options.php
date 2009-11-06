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
?>