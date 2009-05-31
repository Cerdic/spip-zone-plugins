<?php
$annee = (_request("annee")) ? _request("annee") : date("Y");
$mois = (_request("mois")) ? _request("mois") : date("m");

####################################################################################################
// Affichage des noms d'hotes et adresses IP
// Hostnames and IP addresses display

echo "<h3>"._t("Nom d'hotes et adresses IP pour le mois")." $mois / $annee</h3>";


	echo "<div>
	<ul>
		<li><a href=\"#hotes\">"._t("Noms d'hotes")."</a></li>
		<li><a href=\"#ip\" >"._t("Adresses IP")."</a></li>
	</ul>
	</div>";
	
include("inc.hotes.php");
?>