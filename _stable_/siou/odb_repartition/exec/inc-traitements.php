<?php

include_once(_DIR_PLUGIN_ODB_REPARTITION.'exec/inc-base.php');

/**
 * Répartir les candidats
 * @param array|string $par : par quoi fait-on le filtre ? 'ville'... - peut être un tableau associatif
 * @param string $filtre : valeur du filtre ('' si $par est un tableau)
 * @param int $envoi_centre : id du centre où les candidats correspondants au filtre doivent être envoyés
 * @param string $envoi_salle : type de
 * @param string $lib_centre : libellé de ce centre
 * @param string $annee : filtre sur l'annee
 * @param int $limit : limite le nombre de repartitions
 * @return array(nb_row, msg_info, msg_repartition)
 */
function repartirCandidats($par,$filtre,$envoi_centre,$envoi_salle,$lib_centre,$annee,$limit=0) {

   if(substr_count($lib_centre,"departement")>0) {
      $id_departement=substr($lib_centre,strlen("departement|"));
      $lib_centre=$tab_referentiel['etablissement'][$id_departement][$envoi_centre];
      echo "$id_departement - $envoi_centre - $lib_centre";
   }
   // infos salle
   $tab_capacite=getCapacite($annee,$envoi_centre,$envoi_salle);
   $nb_capacite=count($tab_capacite);
   // numeros de saisie candidats selectionnes
   $tab_candidat = getCandidatsARepartir($annee,$par,$filtre,$limit);
   $nb_rows=count($tab_candidat);
   $dispoMax=0;
   if($nb_capacite>0)
      foreach($tab_capacite as $idSalle=>$tab) {
         $dispoMax=max($tab['dispo'],$dispoMax);
         if($tab['dispo']>=$nb_rows) {
            // cette salle peut contenir les candidats
            $id_salle=$idSalle;
            foreach($tab as $cle=>$val)
               $$cle=$val;
         }
      }
      if($nb_capacite>0) {
         $thead="<th>Type de salle</th><th>Capacit&eacute; de<br/>chaque salle</th><th>Capacit&eacute; totale de ce<br/>type de salle</th><th>Places disponibles <br/>avant cette r&eacute;partition</th></tr>\n";
         foreach($tab_capacite as $idSaisie=>$tab) {
            $tbody[]="<td>".$tab['salle']."</td><td>".$tab['capacite_salle']."</td><td>".$tab['capacite_type']."</td><td>".$tab['dispo']."</td>";
         }
         $msg_info.=odb_html_table("Historique (rappel)",$tbody,$thead);
      } else {
         $msg_info.= KO." - Ce centre n'a pas &eacute;t&eacute; correctement configur&eacute; dans le <A HREF='".generer_url_ecrire('odb_ref')."&table=odb_ref_salle&step2=manuel'>r&eacute;f&eacute;rentiel salle</A>.";
         $nb_rows=0;
      }
      if($nb_rows>0 && $nb_rows<=$dispoMax) {
      	$idTableMax=getIdTableMax($annee,$envoi_centre);
         if($idTableMax!='') {
            $tRang=getRangCandidatDansSalle($annee,$envoi_centre,$envoi_salle);
            $num_salle=$tRang['numSalle'];
            $rangCandidatDansSalle=$tRang['rang']; //rang dans la salle
            $numCan=$tRang['numero']; // numero dans le centre (4 derniers chiffres du id_table)
         } else {
            $num_salle=1;
            $numCan=0;
            $rangCandidatDansSalle=0; // rang du candidat dans la salle
         }
            
         //echo "$sql<br/>numCan cptCan capacite_salle numSalle : [$id] $numCan - $rangCandidatDansSalle - $capacite_salle - $num_salle<br/>";
         $cpt=0; // compteur de candidats affectes
         foreach($tab_candidat as $id_saisie=>$tab) {
            $id_departement=$tab['id_departement'];
            $id_ville=$tab['id_ville'];
            $rangCandidatDansSalle++;
            $numCan++;
            if($rangCandidatDansSalle>$capacite_salle) {
               $num_salle++;
               $rangCandidatDansSalle=1;
            }
            $numCan=str_pad($numCan,4,"0",STR_PAD_LEFT);
            $num_salle=str_pad($num_salle,3,"0",STR_PAD_LEFT);
            $id_table=$envoi_centre.'-'.$salle.$num_salle.'-'.$numCan;
            if($cpt==0) $id_table_premier=$id_table;
            setRepartition($annee,$envoi_centre,$id_saisie,$id_table,$id_salle,$num_salle,$rangCandidatDansSalle);
            $cpt++;
         }
         setSynchroIdTableRepartition2Candidats($annee);
         $msg_repartition= "<b>$nb_rows</b> candidats ont &eacute;t&eacute; r&eacute;partis dans le centre <b>$lib_centre</b>\n";
         $msg_repartition.= "\n<ul>\n\t<li>Premier num&eacute;ro de table g&eacute;n&eacute;r&eacute; : <b>".getIdTableHumain($id_table_premier)."</b></li>";
         $msg_repartition.= "\n\t<li>Dernier num&eacute;ro de table g&eacute;n&eacute;r&eacute; : <b>".getIdTableHumain($id_table)."</b></li>\n</ul>\n";
      } elseif($nb_rows>$dispoMax) $msg_repartition= KO." - Vous avez demand&eacute; &agrave; r&eacute;partir <b>$nb_rows</b> candidats.<br/>Ce centre n'est pas capable d'accueillir plus de <b>$dispoMax</b> candidats, veuillez modifier le nombre de candidats &agrave; r&eacute;partir<br/>\n";
      else $msg_repartition= "Aucun candidat &agrave r&eacute;partir dans le <b>$lib_centre</b>\n";

   $retour['msg_info']=$msg_info;
   $retour['msg_repartition']=$msg_repartition;
   $retour['nb_rows']=$nb_rows;
   
   return($retour);
}

?>
