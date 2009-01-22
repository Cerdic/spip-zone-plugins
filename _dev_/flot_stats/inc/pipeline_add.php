<?php
include_spip('inc/statistiques_new');
function flot_head_prive($flux){
	
	$chemin_flot = _DIR_PLUGIN_FLOT.'jquery.flot.js';
	$courbe_visites = "'".courbe_visites()."'";
	$courbe_moyenne = "'".courbe_moyenne()."'";
	$show_points = show_points();
	$mode_axis = "'".'time'."'";
	$position_legend = "'".'ne'."'";
	$flux .= "<script language='javascript' type='text/javascript' src=".$chemin_flot."></script>";
	$preparer_flux = '<script type="text/javascript">';
	$preparer_flux = $preparer_flux.'$(function () {
			var stats =  [
				{
					label: "Visites",
					data: '.$courbe_visites.'
				},
				{
					label: "Moyenne",
					data: '.$courbe_moyenne.' 
				}
				];';
	$preparer_flux = $preparer_flux.'var options = { 
				lines: { show: true }, 
				points: { show: '.$show_points.' },
				xaxis: { mode: '.$mode_axis.', timeformat: "%d/%m/%y"   },
				yaxis: { min: 0  },
				legend: { position: '.$position_legend.'},
				selection: { mode: "xy" }
			};';
	$preparer_flux = $preparer_flux.'var conteneur = $("#conteneur");';
	
	$preparer_flux = $preparer_flux.'conteneur.bind("plotselected", function (event, ranges) {
				$("#selection").text(ranges.xaxis.from.toFixed(1) + " to " + ranges.xaxis.to.toFixed(1));
				var zoom = $("#zoom").attr("checked");
				if (zoom)
					plot = $.plot(conteneur, stats,
					$.extend(true, {}, options, {
						xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
						yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
					}));
			});';
	$preparer_flux = $preparer_flux.'$("#clearSelection").click(function () {
				plot.clearSelection();
			});';
	$preparer_flux = $preparer_flux.'});';
	$preparer_flux = $preparer_flux.'</script>';
	
	
	
	
	$flux .= $preparer_flux;

	return $flux;
}


?>
