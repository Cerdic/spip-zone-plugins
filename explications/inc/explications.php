<?php

function explications_par_pipeline($pipe,$args) {
  $exec = $args["exec"];
  $qs = array();
  foreach($args as $nom => $valeur)
    if($nom!="exec")
      $qs[] = "$nom=$valeur"; 
  $qs = join("&",$qs);
  
  $res = sql_allfetsel(
    '*',
    'spip_explications',
    'pipeline='._q($pipe).' AND exec='._q($exec)
  );
  
  $webmestre = $GLOBALS["visiteur_session"]["webmestre"]=='oui';
  
  if(!$res && !$webmestre)
    return;
  
  $icone = "messagerie-24.gif";
  $id = 't'.substr(md5($pipe.$args),0,8);
  $bouton = !$icone ? '' : bouton_block_depliable(_T("explications:explications"), true, $id);
  
  $ret = debut_cadre_relief($icone, true, "", $bouton)
	  . debut_block_depliable(true,  $id);
	
	include_spip("inc/actions");
	foreach($res as $r) {
    $corps = "";
    if($webmestre)
      $corps .= ajax_action_auteur("explication","eff-".$r["id_explication"],"explication","#explication-eff-".$r["id_explication"],array("<img src='".chemin_image("croix-rouge.gif")."' style='float:right' />"," style='display:none'")); 
    $corps .= propre($r['texte']);
    if($webmestre)
      $corps = ajax_action_greffe("explication","eff-".$r["id_explication"],$corps);
    $ret .= $corps;
  }  	
	
	if($webmestre) {
  	$ret .= "<script type='text/javascript'>$(function(){
      $('#$id div.explication').hover(function(){
        $(this).children('a').find('span').show();
      },function(){
        $(this).children('a').find('span').hide();
      });
    })</script>";
    $id = 't'.substr(md5($pipe.$args."form"),0,8); 
    $ret .= debut_cadre_relief("", true, "", bouton_block_depliable(_T("explications:ajouter_explication"), false, $id));
    $ret .= debut_block_depliable(false,  $id);
    $ret .= recuperer_fond("fond/ajouter_explication",array("pipeline" => $pipe,"exec" => $exec));
    $ret .= fin_block().fin_cadre_relief(true);
  }
	
	$ret .= fin_block()
    . fin_cadre_relief(true);  
 
  
  return $ret;  
}

