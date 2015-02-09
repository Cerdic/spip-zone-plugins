<?php


########################################################################################################################
//Champs des objets susceptibles de contenir des liens, soit au sein d'un contenu (type 0) soit un lien unique (type 1)#
########################################################################################################################

function linkcheck_champs_a_traiter($table=''){

	 $tab_champs = array(	'bio' 		=> 1,
							'chapo'		=> 1,
							'descriptif'=> 1,
							'message'	=> 1,
							'ps'		=> 1,
							'texte'		=> 1,
							'virtuel'	=> 0,
							'url_syndic'=> 0,
							'url_site'	=> 0,
							'url' 		=> 0 );
	
	return $tab_champs;
}



#################################################################################
//Tables de la base de données qui peuvent contenir des liens, et leur singulier#
#################################################################################

function linkcheck_tables_a_traiter(){
	return array('auteur', 'rubrique','article','syndic','breve','mot');

}



######################################################################################################################
//Association d'un etat de lien avec le premier chiffre des codes de statut http (0) et avec le statut d'un objet (1)#
######################################################################################################################

function linkcheck_etats_liens(){
	return array(
							0 => array('1' => 'malade',
									   '2' => 'ok',
									   '3' => 'deplace', 
									   '4' => 'mort', 
									   '5' => 'malade'),
									  
							1 => array('publie' => 'ok', 
									   'prepa' => 'malade', 
									   'prop' => 'malade', 
									   'refuse' => 'malade', 
									   'poubelle' => 'mort')
						);
}



?>
