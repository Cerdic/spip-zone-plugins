<?php


function rendu_recherche_mot_video_exec($tab,$type){
	$retour ="";
	$lien = $type;
	if ($type == "actuphonore") $lien = "actu";
	foreach($tab as $key => $value){
	
		$date = $tab[$key]["date"];
		$titre = $tab[$key]["titre"];
		$desc = couper($tab[$key]["descriptif"],70);
		
		// Element visualisation, edition , suppression
		$voir ="<span class='resul' onclick='show_omm($key,\"$type\")'>Voir</span>";
		$sup="";
		
		// on test si on peux supprimer ou pas cette omm
		$sql = "select id from association where id_lien = $key and type='$type'";
		$res = spip_query($sql);
		if(spip_num_rows($res)==0) $sup = "&nbsp;|&nbsp;<span class='resul' onclick='delete_omm($key,\"$type\")'>Supprimer</span>";
		$img = "<img src='../plugins/assoc/img/$type"."_"."mini.png'/>&nbsp;&nbsp;";
		
		
		$retour .= "<tr id='tr$key'>";
		$retour .= "<td>$date</td>";
		$retour .= "<td class='titre' value='$key'>$titre</td>";
		$retour .= "<td>$desc</td>";
		$retour .= "<td><span class='masquer' id='la_video$key'><span></td>";
		$retour .= "<td>$img$voir &nbsp;|&nbsp;<a href='?exec=edit_$type&id_$lien=$key'>Editer</a>$sup</td>";
		$retour .= "</tr>";
	}
	if ($retour !=""){
		$retour = "<table><tr class='titre_tab_omm'><td width='100'>Date</td><td width='120'>Titre</td>
				   <td width='190'>Texte</td><td width='10'></td><td width='200'></td></tr>".$retour."</table>";
	}
	return $retour;
}



?>