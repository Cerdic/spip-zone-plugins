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
	spip_log('Appel à tradlang_revision');
	spip_log("id_tradlang = $id_tradlang");
	spip_log($c);
	return modifier_contenu('tradlang', $id_tradlang,
		array(
			'date_modif' => 'ts'
		),
		$c);
}
?>