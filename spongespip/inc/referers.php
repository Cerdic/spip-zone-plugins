<?php

$annee = (_request("annee")) ? _request("annee") : date("Y");
$mois = (_request("mois")) ? _request("mois") : date("m");
$jour = (_request("jour")) ? _request("jour") : date("d");
echo "<h3>"._t("Referents pour le mois")." ".$mois." / ".$annee."</h3>";

	echo "<div>
	<ul>
		<li><a href=\"#domaines-referers\">"._t("Domaines referents")."</a></li>
		<li><a href=\"#pages-referers\" >"._t("Pages referentes")."</a></li>
		<li><a href=\"#moteurs-referers\" >"._t("Moteurs de recherche")."</a></li>
	</ul>
	</div>";


include("inc.referers.php");	
?>