<?php
function balise_ID_GROUP_dist($p) {
	$_type = $p->type_requete;
	if($_type == 'flickr_photos_getallcontexts') {
	  $t = champ_sql('type',$p);
	  $id = champ_sql('id_contexte',$p);
	  $p->code = "($t == 'pool')?$id:''";
	} else $p->code = champ_sql('id_group',$p);
	return $p;
}
?>
