<?php
//
// inc/tableau.php
//

function afficher_tableau($tableau){
	echo '<table>';
	foreach($tableau as $cle=>$valeur)
	{
		echo '<tr><td>'.$cle.'</td><td>'.$valeur.'</td></tr>';
	} 
	echo '</table>';
}

function afficher_tableau_div($tableau){
	echo '<div>';
	foreach($tableau as $cle=>$valeur)
	{
		echo '<span><b>'.$cle.'</b><br /><em>'.$valeur.'</em></span><br />';
	} 
	echo '</div>';
}

function afficher_tableau_complex_div($tableau,$table){
	$res_desc_table = spip_query("DESC ".$table.";");
	$desc_table = spip_fetch_array($res_desc_table);
	echo '<div>';
	while ($description_table = mysql_fetch_array($res_desc_table)){
		/*switch ($description_table['Type']){
			case 'autre chause' :
				break;
				
			default :
				echo $description_table['Field'].'<br />';
				echo'<textarea style="width: 480px;" class="forml" rows="5" cols="40" name='.$description_table['Field'].'>'.$tableau[$description_table['Field']].'</textarea><br />';
				break;
			
		}*/
		if (($description_table['Type'] == 'longblob') OR (strlen($tableau[$description_table['Field']]) > 100) ){
			echo $description_table['Field'].'<br />';
			echo'<textarea style="width: 480px;" class="forml" rows="5" cols="40" name='.$description_table['Field'].'>'.$tableau[$description_table['Field']].'</textarea><br />';
		}else{
			echo $description_table['Field'].'<br />';
			echo'<input type="text" class="forml" rows="5" cols="40" name='.$description_table['Field'].' value="'.$tableau[$description_table['Field']].'"><br />';
		}
		
	}
	echo '</div>';
}

function afficher_tableau_test($tableau){
	//echo '<table>';
	nl2br(var_dump($tableau));
	//echo '</table>';
}

function editer_tableau($tableau){
	echo '<table>';
	foreach($tableau as $cle=>$valeur)
	{
		echo '<tr><td>'.$cle.'</td><td><input type="text" value="'.$valeur.'" name="'.$cle.'" /></td></tr>';
	} 
	echo '</table>';
}

function editer_tableau_div($tableau){
	foreach($tableau as $cle=>$valeur)
	{
		echo '<div><b>'.$cle.'</b><br /><input type="text" class="formo" value="'.$valeur.'" name="'.$cle.'" /></div><br />';
	} 
	echo '</table>';
	


}
?>
