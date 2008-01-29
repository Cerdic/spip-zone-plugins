<?php
function cron_speedsyndic($t) {
       //les sites à syndiquer
       $sites=array(1,5,12,24);
       //TODO : utiliser le plugin CFG...

       include_spip("inc/syndic");
       include_spip("inc/indexation");
       foreach($sites as $id_syndic){
               $retour=syndic_a_jour($id_syndic, 'sus');
               spip_query("UPDATE spip_syndic SET date_index=NOW() WHERE id_syndic=$id_syndic");
               marquer_indexer('spip_syndic', $id_syndic);
       }
       return true;
}
// la cle est la tache, la valeur le temps minimal, en secondes, entre
// deux memes taches
// NE PAS METTRE UNE VALEUR INFERIEURE A 30 (cf ci-dessus)
// Note : en fait mettre absolument une valeur superieure
// au max execution time PHP si j'ai bien compris
function speedsyndic_taches_generales_cron($taches_generales){
	$taches_generales['speedsyndic']=60;
	return $taches_generales;
}
?>