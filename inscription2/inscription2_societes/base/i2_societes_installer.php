<?php

include_spip('base/create');
function i2_societes_upgrade($nom_meta_base_version,$version_cible){

	if (isset($GLOBALS['meta'][$nom_meta_base_version]))
		$current_version = $GLOBALS['meta'][$nom_meta_base_version];

	//Si c est une nouvelle installation toute fraiche
	if ($current_version=="0.0") {
		include_spip('base/i2_societes');
		$config_inscription2 = $GLOBALS['meta']['inscription2'];

		if (!is_array(unserialize($config_inscription2))) {
	    	unset($config_inscription2);
		}

		$config_societes =	array(
				'id_societe' => NULL,
				'id_societe_obligatoire' => NULL,
				'id_societe_fiche_mod' => NULL,
				'id_societe_fiche' => NULL,
				'id_societe_table' => NULL
			);

		$config_finale = array_merge(unserialize($config_inscription2),$config_societes);

		ecrire_config('inscription2',$config_finale);

		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('i2_societes_version',$current_version=$version_base,'non');
	}
	if (version_compare($current_version,"0.02","<")){
		sql_alter("TABLE spip_societes ADD maj TIMESTAMP after fax");
		echo "I2_societes @ 0.02<br />";
		ecrire_meta($nom_meta_base_version,$current_version="0.02");
	}
	if (version_compare($current_version,"0.03","<")){
		include_spip('base/i2_societe');
		creer_base();
		maj_societes_3();
		echo "I2_societes @ 0.03<br />";
		ecrire_meta($nom_meta_base_version,$current_version="0.03");
	}
}

function i2_societes_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_societes');
	effacer_meta($nom_meta_base_version);
}

function maj_societes_3(){
	$auteurs_societes = sql_select('id_auteur,id_societe','spip_auteurs_elargis','id_societe != ""');
	while($auteur = sql_fetch($auteurs_societes)){
		if(is_array(unserialize($auteur['id_societe']))){
			foreach(unserialize($auteur['id_societe']) as $societe){
				sql_insertq('spip_auteurs_liens',array('id_auteur'=>$auteur['id_auteur'],'objet'=>'societe','id_objet'=>$societe));
			}
		}else{
			sql_insertq('spip_auteurs_liens',array('id_auteur'=>$auteur['id_auteur'],'objet'=>'societe','id_objet'=>$auteur['id_societe']));
		}
	}
}
?>