<?php
include_spip('base/abstract_sql');
include_spip('base/create');

function mesfavoris_install($action) {

 switch ($action)
 {  // La base est deja cree ?
    case 'test':
       // Verifier que le champ id_mon_plugin est present...
       
       $desc = sql_showtable("spip_favtextes");
       return (isset($desc['field']['id_favtxt']));
       break;
     // Installer la base
     case 'install':
		sql_create("spip_favtextes",array(
										"id_favtxt" => "bigint(21) NOT NULL  auto_increment",
										"id_auth"   => "int(11) NOT NULL default '0'",
										"id_texte"   => "int(11) NOT NULL default '0'",
										),
										array(
										'PRIMARY KEY' => "id_favtxt",
										'KEY authors'	  => "id_auth"
										)
		);
       break;
     // Supprimer la base
     case 'uninstall':
       sql_drop_table("spip_favtextes",'','',true);
       break;
	}

}

?>