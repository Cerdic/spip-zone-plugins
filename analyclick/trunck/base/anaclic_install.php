<?php
/**
* Plugin Analyclick
*
* @author: Jean-Marc Viglino (ign.fr)
*
* Copyright (c) 2011
* Logiciel distribue sous licence GNU/GPL.
*
**/

function anaclic_install($action)
{
	switch ($action)
	{	case 'test':
			// Table existe ?
			$desc = sql_showtable("spip_doc_compteurs", true, '');
			return (isset($desc['field']['id_document']));
			break;
		case 'install':
			include_spip('base/create');
			include_spip('base/anaclic');
			creer_base();
			break;
		case 'uninstall':
			sql_drop_table("spip_doc_compteurs");
			sql_drop_table("spip_doc_compteurs_fix");
			break;
	}
}

?>