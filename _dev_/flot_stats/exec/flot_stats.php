<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/statistiques');
include_spip('inc/statistiques_new');
function exec_flot_stats() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("Statistiques", "", "");
	echo '<script language="javascript" type="text/javascript" src="'._DIR_PLUGIN_FLOT.'jquery.flot.js"></script>';
	?>
	<script id="source" language="javascript" type="text/javascript">
		$(function () {
			var stats =  [
				{
					label: "Visites",
					data: <?php $select = sql_select("*", "spip_visites");
					$nstats = sql_countsel('spip_visites');
					echo '[';
					$coun = 1;
					while ($ele=sql_fetch($select)){
						$date_vi = $ele['date'];
						$nvis = $ele['visites'];
						echo '['.rendre_date($date_vi).', '.$nvis.']';
							if ($coun<$nstats) {
								$coun++;
								echo ",";
							}
						}
					echo ']';
					?> 
				},
				{
					label: "Moyenne",
					data: <?php $select = sql_select("*", "spip_visites");
					$nstats = sql_countsel('spip_visites');
					echo '[';
					$coun = 1;
					$mtotal = 0;
					while ($ele=sql_fetch($select)){
						$mdate = $ele['date'];
						$mnvis = intval($ele['visites']);
						$mtotal = $mnvis + $mtotal;
						$mtotal = intval($mtotal);
						$moy = $mtotal / $coun;
						echo '['.rendre_date($mdate).', '.$moy.']';
							if ($coun<$nstats) {
								$coun++;
								echo ",";
							}
						}
					echo ']';
					?> 
				}
				];
				
			var options = { 
				lines: { show: true }, 
				points: { show: <?php $nstats = sql_countsel('spip_visites'); if ($nstats > 365) { echo "false";} else { echo "true";}?> },
				xaxis: { mode: 'time', timeformat: "%d/%m/%y"   },
				yaxis: { min: 0  },
				legend: { position: 'ne'},
				selection: { mode: "xy" }
			};
			
			var conteneur = $("#conteneur");

			conteneur.bind("plotselected", function (event, ranges) {
				$("#selection").text(ranges.xaxis.from.toFixed(1) + " to " + ranges.xaxis.to.toFixed(1));
				var zoom = $("#zoom").attr("checked");
				if (zoom)
					plot = $.plot(conteneur, stats,
					$.extend(true, {}, options, {
						xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
						yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
					}));
			});
			
			var plot = $.plot(conteneur, stats, options);

			$("#clearSelection").click(function () {
				plot.clearSelection();
			});
		});
	</script>
	<?php
	echo '<div id="conteneur" style="width:600px;height:300px;border: 1px solid; margin: auto;"></div>';
	echo 'Total : '.$mtotal.'<br />';
	$select = sql_select("*", "spip_visites","","","`date` DESC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Aujourd\'hui : '.$ojr['visites'].'<br />';
					}
	$select = sql_select("*", "spip_visites","","","`visites` DESC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Maximum : '.$ojr['visites'].'<br />';
					}
	$select = sql_select("*", "spip_visites","visites!=0","","`visites` ASC",1);
					while ($ojr=sql_fetch($select)){
						echo ' Minimum : '.$ojr['visites'].'<br />';
					}
	$mcompte = sql_countsel('spip_visites','visites!=0');
	$moyenne = $mtotal / $mcompte;
	$moyenne = round($moyenne);
	echo ' Moyenne : '.$moyenne;
	echo '<p><input id="clearSelection" type="button" value="Effacer la selection" />';
	echo '<p><input id="zoom" type="checkbox">Zoomer sur la selection</input></p>';
}
?>