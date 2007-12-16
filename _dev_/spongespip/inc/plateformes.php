<?php
$annee = (_request("annee")) ? _request("annee") : date("Y");
$mois = (_request("mois")) ? _request("mois") : date("m");

#########################################################

	echo "<h3>"._t("Plateformes pour le mois")." ".$mois." / ".$annee."</h3>";
	
	echo "<div>
	<ul>
		<li><a href=\"#navigateurs\">"._t("Navigateurs")."</a></li>
		<li><a href=\"#agregateurs\" >"._t("Agregateurs")."</a></li>
		<li><a href=\"#os\" >"._t("Systemes d'exploitation")."</a></li>
	</ul>
	</div>";
	####################################################################################################
	// Affichage des navigateurs
	// Browsers display

include("inc.user_agents.php");
?>