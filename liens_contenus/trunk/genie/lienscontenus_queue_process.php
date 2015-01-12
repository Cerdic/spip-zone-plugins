<?php
function genie_lienscontenus_queue_process_dist($t, $verbose = 'no') {
	$queued = sql_select("*", "spip_liens_contenus_todo", "", "", "date_added", "0,20");
	while ($objet = sql_fetch($queued)) {
        lienscontenus_referencer_liens($objet['type_objet_contenant'], $objet['id_objet_contenant']);
        sql_delete("spip_liens_contenus_todo", "type_objet_contenant="._q($objet['type_objet_contenant'])." AND id_objet_contenant="._q($objet['id_objet_contenant']));
	}
	return 1;
}

