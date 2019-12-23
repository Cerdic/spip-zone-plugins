<?php 

	if (!defined("_ECRIRE_INC_VERSION")) return;

	// Ajouter le répertoire pour les scenari si il n'existe pas
	if (!is_dir(_DIR_IMG.'scenari')) mkdir(_DIR_IMG.'scenari');

	$list="<tr><td>"._T('scenari:empty')."</td></tr>";

	// Liste les scenari disponibles
	$ls=liste_scenari(_DIR_IMG.'scenari/');

	// Menu prévisualisation des raccourcis typo + effacement
	if(sizeof($ls)){
		$list='';
		sort($ls);
		foreach($ls as $n => $v){
			$list.="
			<tr>
			<td><h3><a href=\"".find_in_path(_DIR_IMG."scenari/".$v)."\" target='_blank'>
			<img src=\"".find_in_path("images/scenari-16.png")."\">&nbsp;scenari@".$v."@
			</h3></a></td><td width=\"1%\"><input type='image' src=\"".find_in_path('images/supprimer.gif')."\" class=\"efface_scenari\" alt='".$v."'></td>
			</tr>";
		}
	}

	print "
	<!--<h2>"._T('scenari:available')."</h2>-->
	<div class=liste-objets>
		<table class='spip'>
		".$list."
		</table>
	</div>
	";

?>
