<?php
include_spip('inc/statistiques_new');
function flot_insert_head($flux){
	
	$chemin_flot = _DIR_PLUGIN_FLOT.'jquery.flot.js';
	$courbe_visites = courbe_visites();
	$courbe_moyenne = courbe_moyenne();
	$show_points = show_points();
	$mode_axis = 'time';
	$position_legend = 'ne';
	$conteneur = '$.plot(conteneur, stats, options);';
	$flux .= "<script language='javascript' type='text/javascript' src=".$chemin_flot."></script>";
	$flux .= <<<EOF
		<script type="text/javascript">
			<!--
$(function () {
			var stats =  [
				{
					label: "Visites",
					data: '$courbe_visites'
				},
				{
					label: "Moyenne",
					data: '$courbe_moyenne' 
				}
				];
			var options = { 
				lines: { show: true }, 
				points: { show: '$show_points' },
				xaxis: { mode: '$mode_axis', timeformat: "%d/%m/%y"   },
				yaxis: { min: 0  },
				legend: { position: '$position_legend'},
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
			
			var plot = $conteneur

			$("#clearSelection").click(function () {
				plot.clearSelection();
			});
		});
			-->
		</script>
EOF;

	return $flux;
}


?>
