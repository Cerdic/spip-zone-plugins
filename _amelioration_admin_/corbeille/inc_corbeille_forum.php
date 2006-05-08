<?php

function Corbeille_forum(){
	echo "<body>";
	echo "<head>";
	echo "<title>Forum</title>";
	echo "</head>";
	echo "<body>";
	
	$req="SELECT id_forum, date_heure, titre, texte, auteur, email_auteur FROM spip_forum WHERE id_forum=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result);
	
	echo "Le " . affdate($row[1]) . ", ";
	if (! empty($row[5])) echo "<a href=\"mailto:" . $row[5] . "\">";
	echo $row[4];
	if (! empty($row[5])) echo "</a>";
	echo " a &eacute;crit :<br /><br /><strong>";
	echo $row[2] . "</strong><br /><br /><p align=justify>" . $row[3] . "</p>";
	
	echo "</body>";
	echo "</html>";
	
}
?>