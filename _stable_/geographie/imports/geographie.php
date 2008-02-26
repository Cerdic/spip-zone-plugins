<?php
function imports_geographie_dist(){
	// importer les pays
	include_spip('imports/pays');
	include_spip('inc/charset');
	foreach($GLOBALS['liste_pays'] as $k=>$p)
		sql_insertq('spip_geo_pays',array('id_pays'=>$k,'nom'=>unicode2charset(html2unicode($p))));
	
	// importer les regions/dept/communes francaise
	$id_pays = 70;
	$fichiers = preg_files(_DIR_PLUGIN_GEOGRAPHIE . "imports/",'[.]txt$');
	sort($fichiers);
	foreach($fichiers as $fichier) {
		lire_fichier($fichier,$table);
		if ($table = unserialize($table)) {
			foreach($table as $region=>$departements){
				$id_region = sql_insertq('spip_geo_regions',array('nom'=>$region,'id_pays'=>$id_pays));
				foreach($departements as $departement=>$communes) {
					$abbr = reset($communes);
					$abbr = substr($abbr['insee'],0,3);
					if (substr($abbr,0,1)=='0')
						$abbr = substr($abbr,1);
					$id_departement = sql_insertq('spip_geo_departements',array('nom'=>$departement,'abbr'=>$abbr,'id_region'=>$id_region));
					foreach($communes as $commune) {
						sql_insertq('spip_geo_communes',array('nom'=>unicode2charset($commune['nom']),'id_commune'=>$commune['insee'],'code_postal'=>$commune['cp'],'latitude'=>$commune['lat'],'longitude'=>$commune['long'],'id_departement'=>$id_departement,'id_pays'=>$id_pays));
					}
				}
			}
		}
	}
	// donner un coup de menage dans les communes car certaines ne sont pas liees au bon dept
	$res = sql_select('id_departement,abbr','spip_geo_departements','');
	while ($row = sql_fetch($res)){
		sql_updateq('spip_geo_communes',array('id_departement'=>$row['id_departement']),"id_commune LIKE '0".$row['abbr']."%'");
	}
	
}
?>