<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


include_spip ('inc/meta');
function action_sauve_config_dist(){
	if ($GLOBALS['connect_statut'] == "0minirezo")
	{
		if (_request('editer_table_spip') == true)
		{
			ecrire_meta('edittable_editer_table_spip','1');
		}else{
			ecrire_meta('edittable_editer_table_spip','0');
		}
		
		if (_request('prefix_a_cacher'))
		{
			if ($tab_prefix = explode(',',_request('prefix_a_cacher'));)
			{
				ecrire_meta('edittable_prefix_a_cacher','_request('prefix_a_cacher')');
			}
		}
	}
}

?>
