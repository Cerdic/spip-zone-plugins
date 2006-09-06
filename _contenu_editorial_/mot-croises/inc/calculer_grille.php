<?php
function calcul_tableau_grille2($texte){
	$texte = trim($texte);
	
	
	
	$tableau = explode("\r",$texte);
	

	$j =0;
	
	foreach ($tableau as $i){	//ligne par ligne
		
		
		
		$tableau[$j] = explode('|',$i);
		array_shift($tableau[$j]);
		array_pop($tableau[$j]);
		$j++;
		
		}
	
	return $tableau;}


function calcul_tableau_grille($texte){// cree un tableau qui va contenir la valeau de l'ensemble des cellules.
    
    

    $a_supprimer=array(" class=\"row_even\"",
    	' class="row_odd"',
    	" class=\"row_even\"",
    	"</tr>",
    	"</td>",
		'</tbody>.*',
		"\n"); 
	//liste des elements à supprimer afin de n'avoir plus que de <tr> et <td> dans le corps du texte + elemnt 
	
	foreach ($a_supprimer as $a){
        
       	$texte = eregi_replace($a,'',$texte);
                                        
                                    }
    
    
    
    
    $tableau =explode('<tr>',$texte); //on va scinder la grille en ligne puis mettre cela dans un tableau
    array_shift($tableau); //suprression de ce qu'il y avant la grille
    
    foreach ($tableau as $id =>$ligne){// on va scinder chaque ligne du tableau (qui correspondent Ã  une ligne chacune) en un tableau contenant chaque cellule de la ligne
        
        $tableau[$id] = explode('<td>',$ligne);
        array_shift($tableau[$id]);   // suppresion de ce qu'il y avant la ligne                                 
                                    }
    return $tableau;
    }
?>
