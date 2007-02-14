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
