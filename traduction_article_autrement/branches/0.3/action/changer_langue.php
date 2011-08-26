<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function action_changer_langue_dist() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	$id_article = intval(_request('arg'));

		$row = sql_fetsel("id_rubrique", "spip_articles", "id_article=$id_article");
	
		$id_rubrique =$row['id_rubrique'];		
		
	if (!autoriser('modifier','article',$id_article)) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('action/referencer_traduction');
		instituer_langue_article($id_article, $id_rubrique) ;
	}
	
}
?>