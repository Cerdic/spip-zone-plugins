<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################


function getenfant($leparent,$rubselected){
	static $i = 0, $premier = 1;
	
	$i++;
 	$query="SELECT * FROM spip_rubriques WHERE id_parent='$leparent' ORDER BY titre";
 	$result = spip_query($query);

	while($row=spip_fetch_array($result)){
		$my_rubrique=$row['id_rubrique'];
		$titre=typo($row['titre']);
		$style = "";

		$espace="";
		for ($count=1;$count<$i;$count++){
			$espace.="&nbsp;&nbsp;&nbsp; ";
		}

		switch ($i) {
		case 1:
			$espace= "";
			$style .= "font-weight: bold;";
			break;
		case 2:
			$style .= "color: #202020;";
			break;
		case 3:
			$style .= "color: #404040;";
			break;
		case 4:
			$style .= "color: #606060;";
			break;
		case 5:
			$style .= "color: #808080;";
			break;
		default;
			$style .= "color: #A0A0A0;";
			break;
		}
		if (!(($pospoint = strpos($titre,'.')) === FALSE)) {
			$titre = substr($titre,$pospoint + 2);
		}
		$titre = substr($titre,0, 50); // largeur maxi
		
		//if ($i == 1 && !$premier) {
		$option = "<option value='$my_rubrique' style=\"$style\"";
		
		if ($rubselected == $my_rubrique) {
			$option .= " selected";
		}
		echo "$option>$espace".$titre."\n";
	
		$premier = 0;
		getenfant($my_rubrique,$rubselected);
	}
	$i=$i-1;
}

function getenfantlist($leparent,$rubselected){
	static $i = 0, $premier = 1;
	
	$i++;
 	$query="SELECT * FROM spip_rubriques WHERE id_parent='$leparent' ORDER BY titre";
 	$result = spip_query($query);

	while($row=spip_fetch_array($result)){
		$my_rubrique=$row['id_rubrique'];
		$titre=typo($row['titre'])."<br />";
		$style = "";

		$espace="";
		for ($count=1;$count<$i;$count++){
			$espace.="&nbsp;&nbsp;&nbsp; ";
		}

		switch ($i) {
		case 1:
			$espace= "";
			$style .= "font-weight: bold;";
			break;
		case 2:
			$style .= "color: #202020;";
			break;
		case 3:
			$style .= "color: #404040;";
			break;
		case 4:
			$style .= "color: #606060;";
			break;
		case 5:
			$style .= "color: #808080;";
			break;
		default;
			$style .= "color: #A0A0A0;";
			break;
		}
		
		echo "<span style=\"$style\">$espace".$titre."</span><br /> \n";
		$premier = 0;
		getenfant($my_rubrique,$rubselected);
	}
		echo "<br />";	
	$i=$i-1;
}

function ecrire_rec_mc_conf($nb) {
	$fp = fopen("rec_mc_conf.txt","r+"); // 1.On ouvre le fichier en lecture/�criture
//	$nbcol_actu = fgets($fp,2);       // 2.On r�cup�re le nombre dans le fichier
//	$nbcol=$nb;                     // 3.On recupere le nombre de colonnes
	fseek($fp,0);                     // 4.On se place en d�but de fichier
	fputs($fp,$nb);            // 5.On �crit dans le fichier le nouveau nb
	fclose($fp);   
}

?>
