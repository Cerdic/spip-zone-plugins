<?php
function action_clevermail_list_subscribers_export(){
	
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$lst_id = intval($arg);
	
	include_spip('inc/autoriser');
	if (autoriser('exporter','cm_list',intval($lst_id))){
		
		// On recupère les abonnés à la liste $lst_id dans la base de donnée
		$result = sql_select('s.sub_email', array('spip_cm_subscribers AS s', 'spip_cm_lists_subscribers AS l'), array('l.lst_id='.$lst_id, 'l.sub_id=s.sub_id'), '', 's.sub_id ASC');
		
		//on ecrit les email suivant ce format : email\r\nemail\r\n...
		while ($row=sql_fetch($result)) {
    		$export .= $row['sub_email']."\r\n";
		}
		
		//on envoi le fichier txt au navigateur
		header('Content-type: text/plain; charset=utf-8');
		header('Content-Disposition: attachment; filename="clevermail_liste_'.$lst_id.'.txt"');
		echo ($export);
	}
	
}
?>