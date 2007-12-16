<?php
$annee = (_request("annee")) ? _request("annee") : date("Y");
$mois = (_request("mois")) ? _request("mois") : date("m");

if(!empty($annee) && !empty($mois) && !empty($jour)) { $format_date_sql = "date='$annee-$mois-$jour'";}
if(!empty($annee) && !empty($mois) && empty($jour))  { $format_date_sql = "'$annee-$mois-01' <= date AND date <= '$annee-$mois-31'";}
if(!empty($annee) && empty($mois) && empty($jour))	 { $format_date_sql = "'$annee-01-01' <= date AND date <= '$annee-12-31'";}

include("inc.pages_vues.php");
include("inc.pages_ent.php");

	
?>