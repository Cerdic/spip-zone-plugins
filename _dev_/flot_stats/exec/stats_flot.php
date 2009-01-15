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
function exec_stats_flot() {
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("Statistiques", "", "");
	?>
	<script language="javascript" type="text/javascript" src="../jquery.flot.js"></script>
	<script id="source" language="javascript" type="text/javascript">
		$(function () {
		var stats =  <?php $select = sql_select("*", "spip_visites");
	$nstats = sql_countsel('spip_visites');
	echo '[';
	$coun = 1;
	while ($ele=sql_fetch($select)){
		$date = $ele['date'];
		$nvis = $ele['visites'];
		$d = explode("-", $date);
		$times = mktime(0, 0, 0, $d[1], $d[2], $d[0]);
		echo '['.$times.', '.$nvis.']';
		if ($coun<$nstats) {
			$coun++;
			echo ",";
		}
	}
	echo ']';
	?>;
	
    $.plot($("#conteneur"),
           [ { data: stats, label: "Visites",lines: { show: true }, points: { show: true } },],
           { xaxis: { mode: 'time' },
             yaxis: { min: 0 },
             legend: { position: 'ne', timeformat: "%y/%m/%d" } });
});
</script>
	<?php
	echo '<br />';
	echo '<div id="conteneur" style="width:600px;height:300px;border: 1px solid; margin: auto;"></div>';

}
?>
