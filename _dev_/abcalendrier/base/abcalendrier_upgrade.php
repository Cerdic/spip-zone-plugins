<?php

function abcalendrier_install($action){
      //echo "action=$action<br>";
       //die;
      switch ($action){
         case 'test':
            //Contrôle du plugin à chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement installé et à jour, false sinon
              // Verifier que le champ id_mon_plugin est present...
             include_spip('base/abstract_sql');
             $desc = spip_abstract_showtable("spip_breves", '', true);
             return (isset($desc['field']['evento']));
            break;      
         case 'install':
             spip_query("ALTER TABLE spip_breves ADD `evento` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL");
             break;
         case 'uninstall':
            spip_query("ALTER TABLE `spip_breves` DROP `evento`");
            echo 'ciao uninstall';
            break;

      }
   
   }  

?>