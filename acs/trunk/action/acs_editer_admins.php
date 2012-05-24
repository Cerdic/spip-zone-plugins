<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2008
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/acs_groups');

/**
 * Actions d'édition des admins
 * Admins edit actions
 */
function action_acs_editer_admins() {

  if ($_GET) $R = '$_GET:['.implode(array_keys($_GET), ', ').']=('.implode($_GET, ', ').')';
  else $R = '$_POST:['.implode(array_keys($_POST), ', ').']=('.implode($_POST, ', ').')';

  $securiser_action = charger_fonction('securiser_action', 'inc');
  $arg = $securiser_action();

  $redirect = urldecode(_request('redirect'));
  if ($script = _request('script'))
    $redirect = parametre_url($redirect,'script',$script,'&');
  if ($titre = _request('titre'))
  $redirect = parametre_url($redirect,'titre',$titre,'&');

  acs_log("\n------------------------------------------------------\n".
           'action_acs_editer_admins (appel): arg='.$arg."\n".urldecode($R)."\n\$arg=".urldecode($arg)."\n".
           "------------------------------------------------------");

  if (preg_match(",^\W*(\d+)\W(\w*)\W-(\d+)$,", $arg, $r)) {
    supprimer_admin_et_rediriger($r[2], $r[1], $r[3], parametre_url($redirect,'type',$r[2],'&'));
  }
  elseif (preg_match(",^\W*(\d+)\W(\w*)\W(\d+)$,", $arg, $r)) {
    ajouter_admin_et_rediriger($r[2], $r[1], $r[3], parametre_url($redirect,'type',$r[2],'&'));
  }
  elseif (preg_match(",^\W*(\d+)\W(\w*)$,", $arg, $r)) {
    $nouv_admin = 'nouv_admin_'.$r[1];
    if  ($$nouv_admin = intval(_request('nouv_admin'.'_'.$r[1]))) {
      ajouter_admin_et_rediriger($r[2], $r[1], $$nouv_admin, parametre_url($redirect,'type',$r[2],'&'));
    }
    else if ($cherche = _request('cherche_admin')) {
      if ($p = strpos($redirect, '#')) {
        $ancre = substr($redirect,$p);
        $redirect = substr($redirect,0,$p);
      } else $ancre ='';
      $redirect = parametre_url($redirect,'type',$r[2],'&');
      $res = rechercher_admins($cherche);
      $n = count($res);
      if ($n == 1)
      # Bingo. Signaler le choix fait.
        ajouter_admin_et_rediriger($r[2], $r[1], $res[0], "$redirect&ids=" . $res[0] . "&cherche_admin=" . rawurlencode($cherche) . $ancre);
      # Trop vague. Le signaler.
      elseif ($n > 16)
        redirige_par_entete("$redirect&cherche_admin=$cherche&ids=-1" . $ancre);
      elseif (!$n)
      # Recherche vide (mais faite). Le signaler
        redirige_par_entete("$redirect&cherche_admin=$cherche&ids="  . $ancre);
      else
      # renvoyer un formulaire de choix
        redirige_par_entete("$redirect&cherche_admin=$cherche&ids=" . join(',',$res)  . $ancre);
    }
    else
      acs_log("ERR action_acs_editer_admins pour id=".$$nouv_admin.": args $arg pas compris");
  }
}

function supprimer_admin_et_rediriger($type, $id, $id_admin, $redirect) {
  if ($id == 0) {
    $admins = explode(',', $GLOBALS['meta']['ACS_ADMINS']);
    // Delete admin $id_admin if exists at least one other admin
    if (count($admins) > 0 && $id_admin != 1) {
      for($x=0;$x<count($admins);$x++) {
        $i = array_search($id_admin, $admins);
        if (is_numeric($i)) {
          $admins = array_merge(array_slice($admins, 0, $i ), array_slice($admins, $i+1, count($admins)-1 ));
        }
      }
      ecrire_meta('ACS_ADMINS', implode(',', $admins));
      ecrire_metas();
      acs_log("action_acs_editer_admins (acs): ".$GLOBALS['auteur_session']['id_auteur']."-$id_admin");
    }
  }
  else {
    acs_group_del_admin($id, $id_admin);
  }
  if ($redirect) redirige_par_entete($redirect);
}

// ~ http://doc.spip.org/@ajouter_auteur_et_rediriger (spip 1.9208)
function ajouter_admin_et_rediriger($type, $id, $id_admin, $redirect) {
  if ($id == 0) {
    $admins = explode($GLOBALS['meta']['ACS_ADMINS'],',');
    if (!in_array($id_admin, $admins)) {
      ecrire_meta('ACS_ADMINS', $GLOBALS['meta']['ACS_ADMINS'].','.$id_admin);
      ecrire_metas();
      acs_log("action_acs_editer_admins (acs): ".$GLOBALS['auteur_session']['id_auteur']."+$id_admin");
    }
  }
  else {
    acs_group_add_admin($id, $id_admin);
  }
  if ($redirect) redirige_par_entete($redirect);
}

// ~ http://doc.spip.org/@rechercher_auteurs (spip 1.9208)
function rechercher_admins($cherche_admin)
{
  include_spip('inc/mots');
  include_spip('inc/charsets'); // pour tranlitteration
  $result = spip_query("SELECT id_auteur, nom FROM spip_auteurs where statut='0minirezo'");
  $table_admins = array();
  $table_ids = array();
  while ($row = spip_fetch_array($result)) {
    $table_admins[] = $row["nom"];
    $table_ids[] = $row["id_auteur"];
  }
  return mots_ressemblants($cherche_admin, $table_admins, $table_ids);
}
?>
