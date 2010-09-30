<?php
function dot2_migrer_sites($blog_id,$id_rubrique){
	$crud = charger_fonction('crud','action');
	$dc_link = sql_select('link_title,link_href,link_position','dc_link',array("`blog_id`=".sql_quote($blog_id)));
	
	while($site = sql_fetch($dc_link)){
		$nom_site = $site['link_position']."0. ".$site['link_title'];
		$url_site = $site['link_href'];
		
		#aller on ajoute en BDD !
		$resultat = $crud('create','syndic',null,array('id_rubrique'=>$id_rubrique,'nom_site'=>$nom_site,'url_site'=>$url_site,'statut'=>'publie'));
		$id_site  = $resultat['result']['id'];
		spip_log("Ajout du site $id_site ($nom_site - $url_site)",'dot2_migration_site');
	}
	
	return $id_rubrique;
}


?>