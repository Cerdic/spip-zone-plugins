<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

//
function debut_bloc_lien_page()
	{
	echo "<div style='margin-top:2px;' class='bouton36blanc' 
		onMouseOver=\"changeclass(this,'bouton36gris')\"
		onMouseOut=\"changeclass(this,'bouton36blanc')\">\n";
	}
//
function fin_bloc()
	{
	echo "</div>\n";
	}

// lien page TiSpiP-sKeLeT : Aide en ligne
function bloc_ico_aide_ligne()
	{
	debut_bloc_lien_page();
	echo "<a href=' http://www.etab.ac-caen.fr/bureaudestests/TiSpip/spip.php?article189' title='"._T('rmc:title_aide_01')."' target='_blank'>";
	echo "<img src='"._DIR_IMG_PACK."racine-24.gif' border='0' align='absmiddle'>&nbsp;";
	echo "<span class='verdana2'>"._T('rmc:aide')."</span>";
	echo "</a>";
	fin_bloc();
	}

// --
function debut_band_titre($coul, $police="", $arg="")
	{
	global $couleur_foncee;
	$color = ($coul == $couleur_foncee) ? "white" : "#000000";
	if(!$police) { $police="verdana2"; }
	echo "<div class='bande_titre ".$police." ".$arg."' style='background-color:".$coul."; color:".$color."'>\n";
	}
// --
function debut_boite_erreur($message)
	{
	echo "<div style='border:4px solid #c00; color:#c00; text-align:center; font-weight:bold;padding:10px;-moz-border-radius:10px'>";
	echo $message;
	echo "</div>";
	}
function debut_tableau_mots($id)
	{
	$sql="select * in spip_mots where id_groupe=$id";
	$result=spip_query($sql);
	echo "<table style=\"width:100%; text-align:left;\" border=\"1\" cellpadding=\"2\" cellspacing=\"2\">";
	echo  "<tbody><tr>";
		while ($row=spip_fetch_array($result)){
			echo   "<td>".$row['titre']."</td>";
		}
	echo  "</tbody></tr>";
	echo "</table>";
	}

function debut_table_mots()
	{
	global $couleur_foncee, $couleur_claire;
	$sql="select titre from spip_mots where id_groupe=$id";
	$result=spip_query($sql);
	echo "<table style=\"width:100%; text-align:left; border:1px solid ".$couleur_foncee."; font-size:.8em\"  cellpadding=\"2\" cellspacing=\"0\">";
	echo  "<tbody>";
		while ($row=spip_fetch_array($result)){
	echo  "<tr style=\"border:1px solid ".$couleur_foncee."; background-color:#ccc; font-wheight:small\"  >";
			echo   "<td>".$row['titre']."</td><td></td>";
	echo  "</tr>";
		}
	echo  "</tbody>";
	echo "</table>";
	}
function debut_ligne_table_mots($titre)
	{
	global $couleur_foncee, $couleur_claire;
	echo  "<tr style=\"border:1px solid ".$couleur_foncee."; background-color:#ccc; font-wheight:small\"  >";
			echo   "<td>".$row['titre']."</td><td></td>";
	echo  "</tr>";
	}
function fin_table_mots()
	{
	echo  "</tbody>";
	echo "</table>";
}


/*
<table style="width: 100%; text-align:left;" border="1" cellpadding="2" cellspacing="2">
<tbody>
<tr>
<td><br></td><td><br></td>
</tr>
</tbody>
</table>
*/




?>
