<?
function params_spip_to_params_html($texte){
	//a partir d'un tableau serialisé, retourne des <param name="cle" value="valeur" 
	$ne_pas_garder=array( 	// tableau de ce qu'il n'est pas nécéssaire de garder
	'id_document',
	'params' ,			  	// on pou!rra supprimer cette ligne quand fil aura virer #ENV{params} 
	'date',
	'date_redac','align','largeur','hauteur'
	) ;	

	
	
	
	$tableau = unserialize($texte);
	
	$texte = "";
	foreach ($tableau as $i => $j){
		if (!in_array($i,$ne_pas_garder)) 
			{
			$texte .= "<param name='".$i."' value='".$j."' />";
			}
		}
	
	return $texte;
	}

function params_spip_to_attributs_html($texte){
	//a partir d'un tableau serialisé, retourne des attributs html
	
	$ne_pas_garder=array( 	// tableau de ce qu'il n'est pas nécéssaire de garder
	'id_document',
	'params' ,			  	// on pou!rra supprimer cette ligne quand fil aura virer #ENV{params} 
	'date',
	'date_redac','align'
	) ;	
	
	$tableau = unserialize($texte);
	
	$texte = "";
	foreach ($tableau as $i => $j){
		
		if (!in_array($i,$ne_pas_garder)) 
			{
			$texte .= $i."='".$j."' "; 
			}
		}
	
	return $texte;
	}

	

?>