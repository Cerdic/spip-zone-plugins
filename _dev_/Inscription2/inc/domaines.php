<?php
	/*SI le pkugin acces restreint n'est pas installé, inscription2 ne prendra pas en compte le tableau 'zones'
	si il est bien installé, il faut alors definir les zones qu'on veut atribuer à chaque domaine, par son ID de zone....
	
	Si on veut laisser un domaine ouvert à tous... il faut juste definir un nom de domaine... et laisser le tableau 'sites' vide ( array() ) 
	Si on veut pas attribuer une zone il faut laisser le tableau zones vide... ( array() ) 
	
	FICHIER A REDEFINIR DANS LE DOSSIER SQUELETTES/INC....
	*/
	
	
	$domaine = array('mondomaine' => array(
										'zones' => array(/*'4','5','6'*/), #ID de zones (acces restreint) 
										'sites' => array(/*'monsite1.com', 'monsite1.fr'*/) #liste de sites
									),
					'mondomaine2' =>  array(
										'zones' => array(/*'1','2','3'*/),
										'sites' => array(/*'monsite2.com', 'monsite2.fr'*/)
									)
				);
?>