<?php
if (!defined("_ECRIRE_INC_VERSION")) return; 
function docbr_install($action){
      //      echo "action=$action<br>";
       //       die;
      switch ($action){
         case 'test':
            //Contrôle du plugin à chaque chargement de la page d'administration
            // doit retourner true si le plugin est proprement installé et à jour, false sinon
              // Verifier que le variable documents_breve est present...
             include_spip('base/abstract_sql');
             $result= spip_query("SELECT * FROM `spip_meta` WHERE `nom`= 'documents_breve' LIMIT 0,1;");
             if(mysql_num_rows($result)>0) return 1;//echo "FALSO";
             
             else return 0;//echo "VERO";
             
            break;      
         case 'install':
               
               //echo "ecco";die;
             spip_query("INSERT INTO `spip_meta` ( `nom` , `valeur` , `impt` , `maj` ) VALUES ('documents_breve', 'oui', 'oui', NOW() );");
             break;
         case 'uninstall':
            docbr_vider_tables();
            break;

      }
   
   }  


   function docbr_vider_tables() {
      include_spip('base/abstract_sql');
      // suppression du variable documents_breve a la table spip_meta
      spip_query("DELETE FROM `spip_meta` WHERE `nom`= 'documents_breve' LIMIT 1;");

   }
   

?>