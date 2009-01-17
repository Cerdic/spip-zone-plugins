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
function exec_flot_stats() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("Statistiques", "", "");
	echo '<script language="javascript" type="text/javascript" src="'._DIR_PLUGIN_FLOT.'jquery.flot.js"></script>';
	?>
	<script id="source" language="javascript" type="text/javascript">
		$(function () {
			var stats =  [{
				label: "Visites",
				data: <?php $select = sql_select("*", "spip_visites");
				$nstats = sql_countsel('spip_visites');
				echo '[';
				$coun = 1;
				while ($ele=sql_fetch($select)){
					$date = $ele['date'];
					$nvis = $ele['visites'];
					$d = explode("-", $date);
					$times = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
					$times = intval($times);
					$times = $times * 1000;
					echo '['.$times.', '.$nvis.']';
						if ($coun<$nstats) {
							$coun++;
							echo ",";
						}
					}
				echo ']';
				?> 
			}];

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
						xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to }
					}));
			});
			
			var plot = $.plot(conteneur, stats, options);

			$("#clearSelection").click(function () {
				plot.clearSelection();
			});
		});
	</script>
	<?php
	echo '<br />';
	echo '<div id="conteneur" style="width:600px;height:300px;border: 1px solid; margin: auto;"></div>';
	echo '<div id="overview" style="width:166px;height:100px"></div>';
	echo '<p><input id="clearSelection" type="button" value="Effacer la selection" />';
	echo '<p><input id="zoom" type="checkbox">Zoomer sur la selection</input></p>';
}
?>