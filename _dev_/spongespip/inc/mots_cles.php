<?php
$annee = (_request("annee")) ? _request("annee") : date("Y");
$mois = (_request("mois")) ? _request("mois") : date("m");

echo "<h3>"._t("Mots-cles pour le mois")." $mois / $annee</h3>";


include("inc.mots_cles.php");
?>