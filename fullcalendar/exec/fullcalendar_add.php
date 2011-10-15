<?php

/* * * * * * * * * * * * * * * * * * * *
 *
 *     - FullCalendar pour SPIP -
 *
 * Formulaires d'ajout et modification
 *
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 10/10/2011
 *
 */

function exec_fullcalendar_add(){
 include_spip("inc/presentation");
 // vérifier les droits
 global $connect_statut;
 global $connect_toutes_rubriques;
 if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	print _T('avis_non_acces_page');
	exit;
 }

 $HTML=$INFO="";

 # Ajout d'un calendrier

 if(
  isset($_POST['ajouter'])
  && $_POST['action_to_take']=='add'
  && strlen($_POST['CalName'])
  ){
 	sql_insert("spip_fullcalendar_main","(type, nom)","(".sql_quote($_POST['CalSource']).", ".sql_quote($_POST['CalName']).")");
 	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:calendar_add').".</center><br/>";
 }

 # Modification d'un calendrier

 if(
  isset($_POST['enregistrer'])
  && $_POST['action_to_take']=='update'
  && strlen($_POST['CalName'])
  ){
	sql_update('spip_fullcalendar_main', array('nom' => sql_quote($_POST['CalName'])), "id_fullcalendar=".sql_quote(intval($_POST['id_calendrier'])) );
	$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:calendar_edit').".</center><br/>";
 }

 # Effacer un calendrier

 if(
  isset($_POST['action_to_take'])
  && $_POST['action_to_take']=='del'
  &&isset($_POST['id_calendrier'])
  && $_POST['id_calendrier']
  ){
	sql_delete("spip_fullcalendar_events", "id_fullcalendar=".$_POST['id_calendrier']);
	$INFO=_T('fullcalendar:calendar_del_events')." ".$_POST['id_calendrier']."<br/><br/>";
	sql_delete("spip_fullcalendar_main", "id_fullcalendar=".$_POST['id_calendrier']);
	$INFO.=_T('fullcalendar:calendar_del_events')." ".$_POST['id_calendrier']."<br/><br/>";
 }

 # Création par défaut

 $NOM='';
 $BUTTON='<input type="submit" name="ajouter" value=" '._T('fullcalendar:add').' " class="fondo" />';
 $SOURCE='<select name="CalSource" id="CalSource">
			<option value="mysql">'._T('fullcalendar:source_mysql').'</option>
			<option value="article">'._T('fullcalendar:source_art').'</option>
			<option value="rub">'._T('fullcalendar:source_rub').'</option>
			<option value="google">'._T('fullcalendar:source_google').'</option>
		</select>';
 $ACTION='add';
 $TEXT=_T('fullcalendar:calendar_add');
 $ID='';

 # Récupère les calendriers

 $res = sql_select('*', 'spip_fullcalendar_main');
 $num_calendar = sql_count($res);

 if(!$num_calendar) $INFO="
 <b>"._T('fullcalendar:welcome_title')."</b><br/>
 <br/>"._T('fullcalendar:welcome_text')."</center><br/>
 ";
 else {
	$LISTE='<br/>';
	$class='row_odd';
	$INFO.= "<center>"._T('fullcalendar:vous_avez')." ".$num_calendar." ";
	$INFO.=($num_calendar==1)?_T('fullcalendar:calendar'):_T('fullcalendar:calendars');
	$INFO.="</center><br/>";
	$HTML = "

	<script type=\"text/javascript\">
	//<!--
	function EffacerCalendrier(id){
		if(!document.getElementById) return false;
		if(confirm('"._T('fullcalendar:calendar_del')." ?')){
			document.Formulaire.id_calendrier.value=id;
			document.Formulaire.action_to_take.value='del';
			document.Formulaire.submit();
			return true;
		}
	}

	function ModifierCalendrier(id){
		if(!document.getElementById) return false;
		document.Formulaire.id_calendrier.value=id;
		document.Formulaire.action_to_take.value='edit';
		document.Formulaire.submit();
		return true;
	}

	//-->
	</script>


	<table class='spip' style='width:100%'>
	<tr class='row_first'>
		<th>"._T('fullcalendar:calendar')."</th>
		<th>Source</th>
		<th>"._T('fullcalendar:shortcut')."</th>
		<th>&nbsp;</th>
	</tr>";

	$icons['mysql']="<img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/mysql-24.png'>";
	$icons['google']="<img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/google-24.png'>";
	$icons['rub']='<img src="../prive/images/rubrique-24.gif">';
	$icons['article']='<img src="../prive/images/mot-cle-24.gif">';

	while ($row = sql_fetch($res)) {

		$id   = $row['id_fullcalendar'];
		$type = $row['type'];
		$icon=$icons[$type];
		$nom  = $row['nom'];

		if(
			$_POST['action_to_take']=='edit'
			&& $id==$_POST['id_calendrier']
		){
			$NOM=$nom;
			$BUTTON='<input type="submit" name="enregistrer" value=" '._T('bouton_enregistrer').' " class="fondo" />
			<input type="submit" name="cancel" value=" '._T('bouton_annuler').' " class="fondo" />';
			$SOURCE='<b>'.$type.'</b>';
			$ACTION='update';
			$ID=$id;
			$TEXT=_T('fullcalendar:calendar_edit');
		}

		$LISTE .= "<center class='formulaire_spip'><a href=\"?exec=fullcalendar_edit&id=".$id."\">".$nom."</a></center><br/>";
 		$nom = "<a href=\"?exec=fullcalendar_edit&id=".$id."\">".$nom."</a>";
		$HTML.="
		<tr class='".$class."'>
			<td style='text-align:center'><b>".$nom."</b></td>
			<td style='text-align:center'>".$icon."</td>
			<td style='text-align:center'><b>&lt;fullcalendar".$id."&gt;</b></td>
			<td style='text-align:center;width:44px;'>
				<a href=\"javascript:ModifierCalendrier('".$id."')\"><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/calendar_edit.png'></a> &nbsp;
				<a href=\"javascript:EffacerCalendrier('".$id."')\"><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/calendar_remove.png'></a>
			</td>
		</tr>";
		$class=($class=='row_odd')?'row_even':'row_odd';
 	}
 	sql_free($res);
 	$HTML.="</table>";
 }

 $commencer_page = charger_fonction('commencer_page', 'inc');
 print $commencer_page(_T('fullcalendar:fullcalendar'), "", "") ;
 print "<br/><br/>";
 print gros_titre(_T('fullcalendar:fullcalendar')." : "._T('fullcalendar:gestion_calendriers'),'',false); 
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
 <td><a href=\"?exec=fullcalendar_css\" class=\"cellule-h\"><span class=\"cell-i\"><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/edit_css.png' alt=\"CSS : "._T('fullcalendar:configuration')."\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=fullcalendar_css\" class=\"cellule-h\">CSS - "._T('fullcalendar:configuration')."</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 if(strlen($LISTE)){
	print debut_cadre_enfonce('',true,'',_T('fullcalendar:vos_calendriers'),'');
	print $LISTE;
	print fin_cadre_enfonce(true);
 }

 print creer_colonne_droite('',true);
 print debut_droite("", true);
 print debut_cadre_trait_couleur("", true, "", $titre=_T('fullcalendar:plugin_name'),"","");

# INTERFACE D'AJOUT

 print '
<div class="formulaire_spip formulaire_config">
 <div class="cadre_padding">
 <form action="#" name="Formulaire" method="POST">
 <input type="hidden" name="action_to_take" value="'.$ACTION.'">
 <input type="hidden" name="id_calendrier" value="'.$ID.'">
	<p><label>'.$TEXT.' : <input type="text" name="CalName" style="width:60%" value="'.$NOM.'"></label></p>
	<p><label>'._T('fullcalendar:source_select').' : '.$SOURCE.'</label>
	</p>
   </div>
	<div class="boutons">'.$BUTTON.'</div>
	</form>
</div>
 ';

 # GESTION DES CALENDRIERS

 if(strlen($HTML)){
	print debut_cadre_relief("", false,"", $titre = _T('Vos calendriers'));
	print $HTML;
	print fin_cadre_relief(false);
 }

 print fin_cadre_trait_couleur(true);
 print fin_gauche();
 print fin_page();

}
?>
