<?php
function Corbeille_signature(){
  
	echo "<body>";
	echo "<head>";
	echo "<title>P&eacute;tition</title>";
	echo "</head>";
	echo "<body>";
	
	$req="SELECT id_article, date_time, ad_email, nom_site, nom_email  FROM spip_signatures WHERE id_signature=$id_document";
	$result = spip_query($req);
	$row=spip_fetch_array($result);
	
	echo "Le <b>" . affdate($row[1]) . "</b>,<br />";
	if (! empty($row[5])) echo "<a href=\"mailto:" . $row[5] . $row[4] . "\">";
	echo $row[4];
	if (! empty($row[5])) echo "</a>";
	echo " a sign&eacute; via : <strong>";
	echo $row[2] . "</strong><br />";
	echo " la p&eacute;tition : <strong>";
	$row2=spip_fetch_array(spip_query("SELECT * FROM spip_articles WHERE id_article=$row[0]"));
	echo $row2[2] . "<strong> : " . $row2[5] . "</strong><br />";

	echo "</body>";
	echo "</html>";
}
?>