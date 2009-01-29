<?php

function abcalendrier_install($action){
      //echo "action=$action<br>";
       //die;
      switch ($action){
         case 'test':
            //Contr�le du plugin � chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement install� et � jour, false sinon
              // Verifier que le champ id_mon_plugin est present...
             include_spip('base/abstract_sql');
             if (version_compare($GLOBALS['spip_version_code'],'1.9300','>=')) {
               $desc = sql_showtable("spip_breves", true);
             } else {
                $desc = spip_abstract_showtable("spip_breves", '', true);
             }

             
             
             return (isset($desc['field']['evento']));
            break;      
         case 'install':
             spip_query("ALTER TABLE spip_breves ADD `evento` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
             break;
         case 'uninstall':
            ABCalendrier_vider_tables();
            break;

      }
   
   }  


   function ABCalendrier_vider_tables() {
      include_spip('base/abstract_sql');
      // suppression du champ evento a la table spip_breves
      spip_query("ALTER TABLE spip_breves DROP evento");
//      effacer_meta('agenda_base_version');
//      ecrire_metas();
   }
   

?>