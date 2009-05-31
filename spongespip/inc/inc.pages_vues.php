<?php
	if(strlen($mois) == 1) {$mois = "0".$mois;}
	$table = lire_config("spongespip/prefixe")."_stats_".$annee."_".$mois;
	$req = @mysql_query("SELECT * FROM $table ORDER BY nb_vu DESC LIMIT 0,".lire_config("spongespip/interface/nombre_pages_vues").";");
	
	$i=0;
	
	
	echo "<h3>"._t("Pages les plus vues pour le mois")." ".$mois." / ".$annee."</h3>";
	echo "<div>
	<ul>
		<li><a href=\"#pages-vues\">"._t("Pages vues")."</a></li>
		<li><a href=\"#pages-entree\" >"._t("Pages d'entree")."</a></li>
	</ul>
	</div>";
	echo "<a name=\"pages-vues\"></a>";
	echo "<h2>"._t("Pages les plus vues")." (".lire_config("spongespip/interface/nombre_pages_vues").")</h2>\n";
	echo "<div>\n";
	
	$texte = _t("Les pages les plus vues sont les pages les plus consultees de votre site par l'ensemble des visiteurs. Elles sont classees par nombre de consultation pour la periode en cours. Si vous cliquez sur le nom de la page, vous serez redirige automatiquement vers la page affichee");
//affiche_aide($texte,"themes/".$sps_config['default_theme']."/icones/help.png",$sps_config['aide']);
	
	while($i!=@mysql_num_rows($req))
		{
	$url = @mysql_result($req,$i,"url");
	$url_nb = explode(".",$url);
	$id_doc=$url_nb[0];
	
	$gauche = @mysql_result($req,$i,"url");
	$droite = @mysql_result($req,$i,"nb_vu");
	
	
	affiche_table($gauche,$droite,$gauche,0,$i);
	

	$i++;
		}
	echo "</div>";
	
	?>