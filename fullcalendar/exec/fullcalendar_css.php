<?php

/* * * * * * * * * * * * * * * * * * * *
 *
 *     - FullCalendar pour SPIP -
 *
 * Gestion des CSS pour les évènements
 *
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 10/10/2011
 *
 */

function exec_fullcalendar_css(){
 include_spip("inc/presentation");
 // vérifier les droits
 global $connect_statut;
 global $connect_toutes_rubriques;
 if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	print _T('avis_non_acces_page');
	exit;
 }

 $HTML=$INFO=$LISTE="";

 # Ajout d'un style

 if(
  isset($_POST['ajouter'])
  && $_POST['action_to_take']=='AddStyle'
  && strlen($_POST['StyleName'])
  && strlen($_POST['bordercolor'])
  && strlen($_POST['bgcolor'])
  && strlen($_POST['textcolor'])
  ){
	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:style_add').".</center><br/>";
 	sql_insert("spip_fullcalendar_styles",
 	"(titre, bordercolor, bgcolor, textcolor)",
 	"(
 	 ".sql_quote($_POST['StyleName']).",
 	 ".sql_quote($_POST['bordercolor']).",
 	 ".sql_quote($_POST['bgcolor']).",
 	 ".sql_quote($_POST['textcolor'])."
 	 )");

 }

 # Modification d'un style

 if(
  isset($_POST['enregistrer'])
  && $_POST['action_to_take']=='UpdateStyle'
  && strlen($_POST['StyleName'])
  && strlen($_POST['bordercolor'])
  && strlen($_POST['bgcolor'])
  && strlen($_POST['textcolor'])
  && $_POST['id_style']
  ){
	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:style_edit').".</center><br/>";
	sql_update('spip_fullcalendar_styles',
		array(
		'titre' => sql_quote($_POST['StyleName']),
		'bordercolor' => sql_quote($_POST['bordercolor']),
		'bgcolor' => sql_quote($_POST['bgcolor']),
		'textcolor' => sql_quote($_POST['textcolor'])
		),
		"id_style=".sql_quote(intval($_POST['id_style']))
	);
 }

 # Effacer un style

 if(
  $_POST['action_to_take']=='del'
  && $_POST['id_style']
  ){
	$INFO.="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:style_del')." ".$_POST['id_style']."</center><br/>";
	sql_delete('spip_fullcalendar_styles', "id_style=".$_POST['id_style']);
 }

 # Récupère les calendriers

 $res = sql_select('*', 'spip_fullcalendar_main');
 if(sql_count($res)){
	$LISTE='<br/>';
	while ($row = sql_fetch($res))
		$LISTE .= "<center class='formulaire_spip'><a href=\"?exec=fullcalendar_edit&id=".$row['id_fullcalendar']."\">".$row['nom']."</a></center><br/>";
 }

 # Style par défaut pour la création

 $ACTION='AddStyle';
 $ID_STYLE='';
 $NOM='';
 $BORDER='#0042c7';
 $TEXT='#141666';
 $BG='#becde9';
 $BUTTON='<input type="submit" name="ajouter" value=" '._T('fullcalendar:add').' " class="fondo" />';

 # Récupère les styles

 $res = sql_select('*', 'spip_fullcalendar_styles');
 $num_style = sql_count($res);
 if(!$num_style) $INFO="<b>"._T('fullcalendar:style_welcome')."</b><br/><br/>"._T('fullcalendar:style_info');
 else {

	$INFO.= "<center>"._T('fullcalendar:vous_avez')." ".$num_style." "._T('fullcalendar:styles')."</center><br/>";
	$HTML = "

	<script type=\"text/javascript\">
	//<!--
	function EffacerStyle(id){
		if(!document.getElementById) return false;
		if(confirm('Effacer ce style ?')){
			document.Formulaire.id_style.value=id;
			document.Formulaire.action_to_take.value='del';
			document.Formulaire.submit();
			return true;
		}
	}

	function ModifierStyle(id){
		if(!document.getElementById) return false;
		document.Formulaire.id_style.value=id;
		document.Formulaire.action_to_take.value='edit';
		document.Formulaire.submit();
		return true;
	}
	//-->
	</script>";

	while ($row = sql_fetch($res)) {

		$id     = $row['id_style'];
		$nom    = $row['titre'];
		$border = $row['bordercolor'];
		$bg     = $row['bgcolor'];
		$text   = $row['textcolor'];

		# Modification d'un style ?

		if(
			$_POST['action_to_take']=='edit'
			&& $id==$_POST['id_style']
		){
			 $ACTION='UpdateStyle';
			 $ID_STYLE=$id;
			 $NOM=$nom;
			 $BORDER=$border;
			 $TEXT=$text;
			 $BG=$bg;
			 $BUTTON='<input type="submit" name="enregistrer" value=" '._T('bouton_enregistrer').' " class="fondo" />';
		}

		$rs = sql_select('id_event', 'spip_fullcalendar_events', 'id_style = '.$id);
		$rw = sql_count($rs);

		if(!$rw) $DELETE="<a href=\"javascript:EffacerStyle('".$id."')\"><img style=\"margin-left:10px;\" src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/css_remove.png' align='right'></a>";
		else {
			$DELETE="&nbsp;&nbsp;"._T('fullcalendar:linked_to')." ".$rw." "._T('fullcalendar:event');
			$DELETE.=($rw>1)?'s.':'.';
		}
		sql_free($rs);

		$HTML.="
		<div style=\"padding:5px;border:1px solid ".$border.";color:".$text.";background-color:".$bg."\">
			<b>".$nom."</b>
			".$DELETE."
			<a href=\"javascript:ModifierStyle('".$id."')\">&nbsp;<img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/css_edit.png' align='right'></a>
		</div><br/>";

 	}
	sql_free($res);
 }

 $commencer_page = charger_fonction('commencer_page', 'inc');
 print $commencer_page(_T('fullcalendar:fullcalendar'), "documents", "forms") ;
 print "<br/><br/>";
 print gros_titre(_T('fullcalendar:gestion_styles'),'',false);
 print debut_gauche ("",true);

 print debut_boite_info(true);
 print "<center><b>"._T('fullcalendar:fullcalendar')."</b></center>";
 print "<br/><center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/fullcalendar.jpg'></center><br/>";
 print $INFO;
 print fin_boite_info(true);

 print debut_cadre_enfonce('',true,'','','');
 print "<table class=\"cellule-h-table\" style=\"vertical-align: middle;\" cellpadding=\"0\"><tbody><tr>
 <td><a href=\"?exec=cfg&cfg=fullcalendar\" class=\"cellule-h\"><span class=\"cell-i\"><img src='../plugins/cfg/cfg-22.png' alt=\"CFG : "._T('fullcalendar:configuration')."\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=cfg&cfg=fullcalendar\" class=\"cellule-h\">CFG - "._T('fullcalendar:configuration')."</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 print debut_cadre_enfonce('',true,'','','');
 print "<table class=\"cellule-h-table\" style=\"vertical-align: middle;\" cellpadding=\"0\"><tbody><tr>
 <td><a href=\"?exec=fullcalendar_add\" class=\"cellule-h\"><span class=\"cell-i\"><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/calendar.png' alt=\""._T('fullcalendar:fullcalendar')." : "._T('fullcalendar:gestion')."\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=fullcalendar_add\" class=\"cellule-h\">FullCalendar - "._T('fullcalendar:gestion')."</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 if(strlen($LISTE)){
	print debut_cadre_enfonce('',true,'',_T('fullcalendar:vos_calendriers'),'');
	print $LISTE;
	print fin_cadre_enfonce(true);
 }

 print creer_colonne_droite('',true);
 print debut_droite("", true);
 print debut_cadre_trait_couleur("", true, "", $titre=_T('fullcalendar:style_title'),"","");

# INTERFACE D'AJOUT

 print '
<div class="formulaire_spip formulaire_config">
 <div class="cadre_padding">
 <form action="#" name="Formulaire" method="POST">
 <input type="hidden" name="action_to_take" value="'.$ACTION.'">
 <input type="hidden" name="id_style" value="'.$ID_STYLE.'">
	<p><label>'._T('fullcalendar:style_name').' : <input type="text" name="StyleName" style="width:60%" value="'.$NOM.'"></label></p>
	<p><label>'._T('fullcalendar:style_border').' : <input type="text" name="bordercolor" class="palette" id="_ir_bordercolor" size="7" value="'.$BORDER.'" /></label></p>
	<p><label>'._T('fullcalendar:style_background').' : <input type="text" name="bgcolor" class="palette" id="_ir_bgcolor" size="7" value="'.$BG.'" /></label></p>
	<p><label>'._T('fullcalendar:style_text').' : <input type="text" name="textcolor" class="palette" id="_ir_textcolor" size="7" value="'.$TEXT.'" /></label></p>
   </div>
	<div class="boutons">'.$BUTTON.'</div>
	</form>
</div>
 ';

 # GESTION DES STYLES

 if(strlen($HTML)){
	print debut_cadre_relief("", false,"", $titre = _T('fullcalendar:vos_styles'));
	print $HTML;
	print fin_cadre_relief(false);
 }

 print fin_cadre_trait_couleur(true);
 print fin_gauche();
 print fin_page();

}
?>
