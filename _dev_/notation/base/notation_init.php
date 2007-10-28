<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Installer la base si pas deja fait 
*  
**/

function notation_install($action){
include_spip('inc/vieilles_defs');
	switch ($action)
	{	// La base est deja cree ?
		case 'test':
			// Verifier que le champ id_notation est present...
			include_spip('base/abstract_sql');
			$desc = spip_abstract_showtable("spip_notations", '', true);
			return (isset($desc['field']['maj']));
			break;
		// Installer la base
		case 'install':
			include_spip('base/create');
			$desc = spip_abstract_showtable("spip_notations", '', true);
			if (isset($desc['field']['id_notation']))
      { spip_query("ALTER TABLE spip_notations ADD `maj` TIMESTAMP NOT NULL ");
      }
			else
      { include_spip('base/notation');
        creer_base();
      }
			break;
		// Supprimer la base
		case 'uninstall':
			spip_query("DROP TABLE spip_notations");
			spip_query("DROP TABLE spip_notations_articles");
			break;
	}
}	
	
?>