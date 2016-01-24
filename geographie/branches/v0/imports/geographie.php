<?php
function imports_geographie_dist(){
	// importer les pays
	include_spip('imports/pays');
	include_spip('inc/charsets');

	sql_insertq_multi('spip_geo_pays',$GLOBALS['liste_pays']);

	// importer les regions/dept/communes francaise
	$id_pays = 70;
	$fichiers = preg_files(_DIR_PLUGIN_GEOGRAPHIE . "imports/",'[.]txt$');
	sort($fichiers);
	foreach($fichiers as $fichier) {
		lire_fichier($fichier,$table);
		if ($table = unserialize($table)) {
			foreach($table as $region=>$departements){
				$id_region = sql_insertq('spip_geo_regions',array('nom'=>unicode2charset(html2unicode($region)),'id_pays'=>$id_pays));
				foreach($departements as $departement=>$communes) {
					$abbr = reset($communes);
					$abbr = substr($abbr['insee'],0,3);
					if (substr($abbr,0,1)=='0') {
						$abbr = substr($abbr,1);
					}
					$id_departement = sql_insertq('spip_geo_departements',array('nom'=>unicode2charset(html2unicode($departement)),'abbr'=>$abbr,'id_region'=>$id_region));
					$tabcom=array();
					foreach($communes as $commune) {
						if ($commune['insee']) // par securite pour eviter une insertion vide
							$tabcom[]=array('nom'=>unicode2charset($commune['nom']),'insee'=>$commune['insee'],'code_postal'=>$commune['cp'],'latitude'=>$commune['lat'],'longitude'=>$commune['long'],'id_departement'=>$id_departement,'id_pays'=>$id_pays);
					}
					sql_insertq_multi('spip_geo_communes',$tabcom);
				}
			}
		}
	}
	// donner un coup de menage dans les communes car certaines ne sont pas liees au bon dept
	$res = sql_select('id_departement,abbr','spip_geo_departements','');
	while ($row = sql_fetch($res)){
		if ($row['abbr'])
			sql_updateq('spip_geo_communes',array('id_departement'=>$row['id_departement']),"insee LIKE '0".$row['abbr']."%'");
	}

}
