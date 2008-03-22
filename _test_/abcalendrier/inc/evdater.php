<?php
/***************************************************************************
Funzione che prepara la form
***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/texte');
include_spip('inc/actions');
include_spip('inc/date');

// http://doc.spip.org/@inc_dater_dist
function inc_evdater_dist($id, $flag, $statut, $type, $script, $date)
{
   global $spip_lang_left, $spip_lang_right, $options;


   if (ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})( ([0-9]{2}):([0-9]{2}))?", $date, $regs)) {
      $annee = $regs[1];
      $mois = $regs[2];
      $jour = $regs[3];
      $heure = $regs[5];
      $minute = $regs[6];
   }

  if ($flag) {

  // if ($statut == 'publie') {

      $js = "size='1' class='fondl'
onchange=\"findObj_forcer('valider_date_evenement').style.visibility='visible';\"";

      $invite =  "<b><span class='verdana1'>"
      . _T('abcalendrier:date_evenement')
      . '</span> '
      .  majuscules(affdate($date))
      .  "</b>";
      //. aide('artdate');

      $masque = 
        afficher_jour($jour, "name='evenement_jour' $js", true)
      . afficher_mois($mois, "name='evenement_mois' $js", true)
      . afficher_annee($annee, "name='evenement_annee' $js")
      . (($type != 'article')
         ? ''
         : (' - '
         . afficher_heure($heure, "name='evenement_heure' $js")
            . afficher_minute($minute, "name='evenement_minute' $js")))
        . "&nbsp;\n";
      /*??? ajax_action_post("dater"*/
      $res = "<div style='margin: 5px; margin-$spip_lang_left: 20px;'>"
      .  ajax_action_post("evdater", 
               "$id/$type",
               $script,
               "id_$type=$id",
               $masque,
               _T('bouton_changer'),
                   " class='fondo visible_au_chargement' id='valider_date_evenement'", "",
               "&id=$id&type=$type")
      .  "</div>";
   /*??? 'datepub'*/
      $res = block_parfois_visible('date_evenement', $invite, $res, 'text-align: left');

   /*} else {
      $res = "\n<div><b> <span class='verdana1'>"
      . _T('abcalendrier:date_evenement')
      . "</span>\n"
      . majuscules(affdate($date))."</b>"."non pub"."</div>";
      //. majuscules(affdate($date))."</b>".aide('artdate')."</div>";
   }
   */

  } else {

   $res = "<div style='text-align:center;'><b> <span class='verdana1'>"
     . (($statut == 'publie' OR $type != 'article')
      ? _T('abcalendrier:date_evenement')
      : _T('abcalendrier:date_evenement'))
   . "</span> "
   .  majuscules(affdate($date))."</b>"."</div>";
   //.  majuscules(affdate($date))."</b>".aide('artdate')."</div>";


  }

  $res =  debut_cadre_couleur('',true) . $res .  fin_cadre_couleur(true);

  return ajax_action_greffe("dater-$id", $res);
}

?>
