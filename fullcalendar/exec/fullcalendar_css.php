<?php

/* * * * * * * * * * * * * * * * * * * *
 * 
 *     - FullCalendar pour SPIP -
 * 
 * Gestion des CSS pour les évènements
 * 
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 04/04/2011
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

 $table_prefix = $GLOBALS['table_prefix'] ;

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
	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Ajout d'un nouveau style.</center><br/>";
	$sql = "INSERT INTO ".$table_prefix."_fullcalendar_styles VALUES (
	NULL,
	'".mysql_real_escape_string($_POST['StyleName'])."',
	'".mysql_real_escape_string($_POST['bordercolor'])."',
	'".mysql_real_escape_string($_POST['bgcolor'])."',
	'".mysql_real_escape_string($_POST['textcolor'])."'
	)";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
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
	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Modification d'un style.</center><br/>";
	$sql = "UPDATE ".$table_prefix."_fullcalendar_styles SET 
	titre='".mysql_real_escape_string($_POST['StyleName'])."',
	bordercolor='".mysql_real_escape_string($_POST['bordercolor'])."',
	bgcolor='".mysql_real_escape_string($_POST['bgcolor'])."',
	textcolor='".mysql_real_escape_string($_POST['textcolor'])."'
	WHERE id_style='".$_POST['id_style']."' LIMIT 1;";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
 }

 # Effacer un calendrier
 
 if(
  $_POST['action_to_take']=='del'
  && $_POST['id_style']
  ){
	  
	$INFO.="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Efface le style ".$_POST['id_style']."</center><br/>";
	$sql = "DELETE FROM ".$table_prefix."_fullcalendar_styles WHERE id_style='".$_POST['id_style']."' LIMIT 1;";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());

 }

 # Récupère les calendriers
 
 $sql = "SELECT id_fullcalendar, nom FROM ".$table_prefix."_fullcalendar_main";
 $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
 if(mysql_num_rows($req)){ 
	$LISTE='<br/>';
	while ($row = spip_fetch_array($req))	
		$LISTE .= "<center class='formulaire_spip'><a href=\"?exec=fullcalendar_edit&id=".$row['id_fullcalendar']."\">".$row['nom']."</a></center><br/>";
 }

 # Style par défaut pour la création

 $ACTION='AddStyle';
 $ID_STYLE='';
 $NOM='';
 $BORDER='#0042c7';
 $TEXT='#141666';
 $BG='#becde9';
 $BUTTON='<input type="submit" name="ajouter" value=" Ajouter " class="fondo" />';
 
 # Récupère les styles
 
 $sql = "SELECT * FROM ".$table_prefix."_fullcalendar_styles";
 $req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
 $num_style = mysql_num_rows($req);
 if(!$num_style) $INFO="<b>Vous n'avez pas encore définit de style !</b><br/><br/>Les définition de styles permettent de modifier l'apparence des évènements dans les agendas qui utilisent MySQL comme source de donnée.";
 else { 

	$INFO.= "<center>Vous avez ".$num_style." styles(s)</center><br/>";
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
	
	while ($row = spip_fetch_array($req)) {

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
			 $BUTTON='<input type="submit" name="enregistrer" value=" Enregistrer " class="fondo" />';
		}
		
		$sq = "SELECT 'id_event' FROM ".$table_prefix."_fullcalendar_events WHERE id_style='".$id."'";
		$rq = mysql_query($sq) or die('Erreur SQL !<br>'.$sq.'<br>'.mysql_error()); 
		$rw = mysql_num_rows($rq);
		
		if(!$rw) $DELETE="<a href=\"javascript:EffacerStyle('".$id."')\"><img style=\"margin-left:10px;\" src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/css_remove.png' align='right'></a>";
		else { 
			$DELETE="&nbsp;&nbsp;Lié à ".$rw." évènement";
			$DELETE.=($rw>1)?'s.':'.';
		}
		
		$HTML.="
		<div style=\"padding:5px;border:1px solid ".$border.";color:".$text.";background-color:".$bg."\">
			<b>".$nom."</b>
			".$DELETE."
			<a href=\"javascript:ModifierStyle('".$id."')\">&nbsp;<img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/css_edit.png' align='right'></a>
			
		</div><br/>";

 	}
 
 }

 $commencer_page = charger_fonction('commencer_page', 'inc');
 print $commencer_page(_T('Fullcalendar'), "documents", "forms") ;
 print "<br/><br/>";
 print gros_titre(_T('Gestion du style des évènements'),'',false);
 print debut_gauche ("",true);
 
 print debut_boite_info(true);
 print "<center><b>"._T('FullCalendar')."</b></center>";
 print "<br/><center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/fullcalendar.jpg'></center><br/>";
 print $INFO;
 print fin_boite_info(true);

 print debut_cadre_enfonce('',true,'','','');
 print "<table class=\"cellule-h-table\" style=\"vertical-align: middle;\" cellpadding=\"0\"><tbody><tr>
 <td><a href=\"?exec=cfg&cfg=fullcalendar\" class=\"cellule-h\"><span class=\"cell-i\"><img src='../plugins/cfg/cfg-22.png' alt=\"CFG : configuration\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=cfg&cfg=fullcalendar\" class=\"cellule-h\">CFG - configuration</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 print debut_cadre_enfonce('',true,'','','');
 print "<table class=\"cellule-h-table\" style=\"vertical-align: middle;\" cellpadding=\"0\"><tbody><tr>
 <td><a href=\"?exec=fullcalendar_add\" class=\"cellule-h\"><span class=\"cell-i\"><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/calendar.png' alt=\"Agenda fullcalendar : configuration\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=fullcalendar_add\" class=\"cellule-h\">FullCalendar - Gestion</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 if(strlen($LISTE)){
	print debut_cadre_enfonce('',true,'',_T('Vos calendriers'),'');
	print $LISTE;
	print fin_cadre_enfonce(true);
 }
 
 print creer_colonne_droite('',true);
 print debut_droite("", true);
 print debut_cadre_trait_couleur("", true, "", $titre=_T('Les CSS de FullCalendar pour SPIP'),"","");

# INTERFACE D'AJOUT

 print '
<div class="formulaire_spip formulaire_config">
 <div class="cadre_padding">
 <form action="#" name="Formulaire" method="POST">
 <input type="hidden" name="action_to_take" value="'.$ACTION.'">
 <input type="hidden" name="id_style" value="'.$ID_STYLE.'">
	<p><label> Créer un nouveau style : <input type="text" name="StyleName" style="width:60%" value="'.$NOM.'"></label></p>
	<p><label>Couleurs des bordures <input type="text" name="bordercolor" class="palette" id="_ir_bordercolor" size="7" value="'.$BORDER.'" /></label></p>
	<p><label>Couleur du fond <input type="text" name="bgcolor" class="palette" id="_ir_bgcolor" size="7" value="'.$BG.'" /></label></p>
	<p><label>Couleur du texte <input type="text" name="textcolor" class="palette" id="_ir_textcolor" size="7" value="'.$TEXT.'" /></label></p>
   </div>
	<div class="boutons">'.$BUTTON.'</div>
	</form>
</div>
 ';

 # GESTION DES STYLES
 
 if(strlen($HTML)){
	print debut_cadre_relief("", false,"", $titre = _T('Vos styles'));
	print $HTML;
	print fin_cadre_relief(false);
 }
  
 print fin_cadre_trait_couleur(true);
 print fin_gauche();
 print fin_page();
   
}
?>
