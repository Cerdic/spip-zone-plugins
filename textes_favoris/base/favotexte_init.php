<?php
/**
* Plugin SSO v.0.1
* par Bernard Blazin
* 
* Copyright (c) 2008
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Installer la base si pas deja fait 
*  
**/

function favotexte_install($action){
	switch ($action)
	{	// La base est deja cree ?
		case 'test':
			// Verifier que le champ id_notation est present...
			include_spip('base/abstract_sql');
			$desc = sql_showtable($table,$serveur,$table_spip);
			return (isset($desc['field']['maj']));
			break;
		// Installer la base
		case 'install':
			include_spip('base/create');
			$desc = sql_showtable($table,$serveur,$table_spip);
			if (isset($desc['field']['id_favtxt']))
      { spip_query("ALTER TABLE spip_favtextes ADD `maj` TIMESTAMP NOT NULL ");
      }
			else
      { include_spip('base/favotexte');
        creer_base();
      }
			break;
		// Supprimer la base
		case 'uninstall':
			spip_query("DROP TABLE spip_favtextes");
			break;
	}
}	
	
?>