<?php
/**
 * Scripts liés à l'administration des base de donées pour Réservation Événements.
 *
 * @plugin     Réservation Événements
 * @copyright  2013 -
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Reservation_evenement\Pipelines
 */

 /**
 * Actualisation du champs donnees_auteur pour l'harmoniser avc la nouvelle manière d'encodage.
 * 
 * @return void
 */
function update_donnees_auteurs() {
	$sql = sql_select('id_reservation,donnees_auteur', 'spip_reservations', 'id_auteur = 0');
	
	    //les champs extras auteur
    include_spip('cextras_pipelines');

    if(function_exists('champs_extras_objet')){
		$search = array();
		$replace = array ();		
        $champs_extras_auteurs=champs_extras_objet(table_objet_sql('auteur'));
		
        foreach($champs_extras_auteurs as $value){
        	$search[] = $value['options']['label'];
        	$replace[] = $value['options']['nom'];			
        }
		
    }
	
	while($data = sql_fetch($sql)) {
		$count = '';
		if (isset($data['donnees_auteur'])) {
			$data['donnees_auteur'] = str_replace ($search ,$replace , $data['donnees_auteur'] , $count);
		}
		
		if ($count>0) {
			spip_log($data,'teste');
			sql_updateq( 'spip_reservations',array('donnees_auteur' => $data['donnees_auteur']), 'id_reservation='.$data['id_reservation']);
		}
		
	}
	
	return;
}
