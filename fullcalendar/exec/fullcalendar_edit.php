<?php

/* * * * * * * * * * * * * * * * * * * *
 * 
 *     - FullCalendar pour SPIP -
 * 
 * Formulaires d'ajout et modification des évènements
 * 
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 04/04/2011
 * 
 */

function exec_fullcalendar_edit(){

	$table_prefix = $GLOBALS['table_prefix'] ;
	
	# vérifie les droit
	
	include_spip("inc/presentation");
	
	global $connect_statut;
	global $connect_toutes_rubriques;
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {             
		print _T('avis_non_acces_page');
		exit;
	}

	# Récupère l'id du calendrier à éditer
	
	$id_fullcalendar=($_GET['id'])?$_GET['id']:$_GET['id_calendrier'];
	if(!$id_fullcalendar) die("Erreur dans l'appel de cette page!");

	$HTML=$INFO=$INTERFACE=$LISTE='';

	# Récupère les autres calendriers
	$LISTE_CALENDRIER='';
	$sql = "SELECT * FROM ".$table_prefix."_fullcalendar_main WHERE id_fullcalendar!='".$id_fullcalendar."'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
	if(mysql_num_rows($req)){
		$LISTE="<br/>";
		while ($row = spip_fetch_array($req)) {
			$LISTE .= "<center class='formulaire_spip'><a href=\"?exec=fullcalendar_edit&id=".$row['id_fullcalendar']."\">".$row['nom']."</a></center><br/>";
			if($row['type']=='mysql')
			$LISTE_CALENDRIER.="<option value=\"".$row['id_fullcalendar']."\">".$row['nom']."</option>";
		}
	}
 
	##############################
	# Ajout d'un évènement MySQL #
	##############################
	
	if(
		$_POST['action_to_take']=='AddEvent'
		&& strlen($_POST['Nom_Evenement'])
		&& $_POST['id_calendrier']
	){
		
		$t = explode('/',$_POST['Date']);
		$date = $t[2].'-'.$t[1].'-'.$t[0]; 
		$t = explode('/',$_POST['Date_Fin']);
		$date_fin = $t[2].'-'.$t[1].'-'.$t[0];
		unset($t);
		
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Ajout d'un nouvel évènement.</center><br/>";
		$sql = "INSERT INTO ".$table_prefix."_fullcalendar_events VALUES (
		NULL,
		'".$_POST['id_calendrier']."',
		'".$_POST['id_style']."',
		'".mysql_real_escape_string($_POST['Nom_Evenement'])."',
		'".mysql_real_escape_string($_POST['Lien_Evenement'])."',
		'".$date." ".$_POST['HeureDebut'].":00',
		'".$date_fin." ".$_POST['HeureFin'].":00'
		)";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}
	
	######################
	# Ajout d'un mot clé #
	######################
	
	if(
		$_POST['action_to_take']=='add_mot'
		&& $_POST['ajouter']
		&& $_POST['id_calendrier']
		&& strlen($_POST['id_mot'])
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Ajout d'une mot clé pour cet agenda.</center><br/>";
		$sql = "INSERT INTO ".$table_prefix."_fullcalendar_events VALUES (NULL,'".$_POST['id_calendrier']."','','','".$_POST['id_mot']."','','')";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}

	#######################
	# Modifier le mot clé #
	#######################
	
	if(
		$_POST['action_to_take']=='update_mot'
		&& $_POST['ajouter']
		&& $_POST['id_calendrier']
		&& strlen($_POST['id_mot'])
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Mise à jour du mot clé pour cet agenda.</center><br/>";
		$sql = "UPDATE ".$table_prefix."_fullcalendar_events SET lien='".$_POST['id_mot']."' WHERE id_fullcalendar='".$_POST['id_calendrier']."' LIMIT 1";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}
	
	##########################
	# Ajout d'une clé Google #
	##########################
	
	if(
		$_POST['action_to_take']=='add'
		&& $_POST['ajouter']
		&& $_POST['id_calendrier']
		&& strlen($_POST['gcalID'])
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Ajout d'une clé Google Agenda.</center><br/>";
		$sql = "INSERT INTO ".$table_prefix."_fullcalendar_events VALUES (NULL,'".$_POST['id_calendrier']."','','','".mysql_real_escape_string($_POST['gcalID'])."','','')";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}

	##########################
	# Modifier la clé Google #
	##########################
	
	if(
		$_POST['action_to_take']=='update'
		&& $_POST['ajouter']
		&& $_POST['id_calendrier']
		&& strlen($_POST['gcalID'])
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Mise à jour de la clé pour cet agenda.</center><br/>";
		$sql = "UPDATE ".$table_prefix."_fullcalendar_events SET lien='".mysql_real_escape_string(trim($_POST['gcalID']))."' WHERE id_fullcalendar='".$_POST['id_calendrier']."' LIMIT 1";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}

	###############################
	# Modification d'un évènement #
	###############################
	
	if(
		$_POST['action_to_take']=='EditEvent'
		&& strlen($_POST['Nom_Evenement'])
		&& strlen($_POST['id_evenement'])
		&& $_POST['id_calendrier']
	){
		
		$t = explode('/',$_POST['Date']);
		$date = $t[2].'-'.$t[1].'-'.$t[0]; 
		$t = explode('/',$_POST['Date_Fin']);
		$date_fin = $t[2].'-'.$t[1].'-'.$t[0];
		unset($t);
		
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Modification d'un évènement.</center><br/>";
		$sql = "UPDATE ".$table_prefix."_fullcalendar_events 
		SET id_fullcalendar='".$_POST['id_calendrier']."',
		id_style='".$_POST['id_style']."',
		titre='".mysql_real_escape_string($_POST['Nom_Evenement'])."',
		lien='".mysql_real_escape_string($_POST['Lien_Evenement'])."',
		start='".$date." ".$_POST['HeureDebut'].":00',
		end='".$date_fin." ".$_POST['HeureFin'].":00'
		WHERE id_event='".$_POST['id_evenement']."'";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());   
	}
	
	#######################
	# Efface un évènement #
	#######################
	
	if(
		$_POST['action_to_take']=='del'
		&& $_POST['id_calendrier']
		&& $_POST['id_evenement']
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/ok.png'> &nbsp; Efface un évènement !</center><br/>";
		$sql = "DELETE FROM ".$table_prefix."_fullcalendar_events WHERE id_fullcalendar='".$_POST['id_calendrier']."' AND id_event='".$_POST['id_evenement']."' LIMIT 1;";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());	    

	}
	
	#################################
	# Calendrier en cours d'édition #
	#################################
	
	$sql = "SELECT * FROM ".$table_prefix."_fullcalendar_main WHERE id_fullcalendar='".$id_fullcalendar."'";
	$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
	$num_calendar = mysql_num_rows($req);
	if(!$num_calendar) $INFO="<center style=\"color:red\">Ce calendrier n'existe plus !</center><br/>";
	else {

		$row  = spip_fetch_array($req);
		$id   = $row['id_fullcalendar'];
		$type = $row['type'];
		$nom  = $row['nom'];

		# Récupère les styles pour les évènements
	
		$sql = "SELECT * FROM ".$table_prefix."_fullcalendar_styles";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
		$num_style = mysql_num_rows($req);
		if(!$num_style) $STYLES="Vous n'avez pas définit de style pour vos évènements, il seront donc affichés avec les couleurs par défaut. Pour créer un nouveau style d'évènement <a href=\"?exec=fullcalendar_css\">cliquez ici</a>.";
		else {
			$STYLES="<p>Style de l'évènement:<select name='id_style' id='id_style'>";
			while ($rw = spip_fetch_array($req)) {
				$STYLES.="<option value=\"".$rw['id_style']."\">".$rw['titre']."</option>";
			}
			$STYLES.="</select></p>";
		}
		
		$LISTE_CALENDRIER.='<option SELECTED value="'.$id_fullcalendar.'">'.$nom.'</option>';

		if($type=='mysql'){

		# Modification d'un évènement

		if(
			$_POST['action_to_take']=='edit'
			&& $_POST['id_evenement']
		){
			
			$sql = "SELECT * FROM ".$table_prefix."_fullcalendar_events WHERE id_fullcalendar='".$id_fullcalendar."' AND id_event='".$_POST['id_evenement']."'";
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
			$rw = spip_fetch_array($req);
			
			$NOM=$rw['titre'];
			$LIEN=$rw['lien'];
			
			$ACTION='EditEvent';
			$ID_EVENEMENT=$_POST['id_evenement'];
			$DATE = DateFromMysql(substr($rw['start'],0,10));
			$DATE_FIN = DateFromMysql(substr($rw['end'],0,10));
			$START = substr($rw['start'],11,5);
			$END = substr($rw['end'],11,5);
			$BUTTON = "<input type=\"submit\" name=\"enregistrer\" value=\" Enregistrer \" class=\"fondo\" />";	
			
		} else {
			
			$ACTION='AddEvent';
			$ID_EVENEMENT='';
			$DATE=(strlen($_POST['Date'])==10)?stripslashes($_POST['Date']):Date("d/m/Y");
			$DATE_FIN=(strlen($_POST['Date_Fin'])==10)?stripslashes($_POST['Date_Fin']):Date("d/m/Y");
			$START=(strlen($_POST['HeureDebut'])==5)?stripslashes($_POST['HeureDebut']):Date("H:m");
			$END=(strlen($_POST['HeureFin'])==5)?stripslashes($_POST['HeureFin']):Date("H:m");
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" Ajouter \" class=\"fondo\" />";
			
		}


			# Récupère les évènements du calendrier MySQL

			$events='';
			$sql = "SELECT * FROM ".$table_prefix."_fullcalendar_events WHERE id_fullcalendar='".$id_fullcalendar."' ORDER BY start ASC";
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
			$num_events = mysql_num_rows($req);
			if(!$num_events) $INFO.="Aucun évènement dans ce calendrier!";
			else {
				$HTML="
				
				<script type=\"text/javascript\">
				//<!--
				function EffacerEvenement(id){
					if(!document.getElementById) return false;
					if(confirm('Effacer cet évènement ?')){
						document.Formulaire.id_evenement.value=id;
						document.Formulaire.action_to_take.value='del';
						document.Formulaire.submit();
						return true;
					} 
				}
				
				function ModifierEvenement(id){
					if(!document.getElementById) return false;				
					document.Formulaire.id_evenement.value=id;
					document.Formulaire.action_to_take.value='edit';
					document.Formulaire.submit();
					return true;
				}
				//-->
				</script>
			
				<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
					<tbody>
					<tr>
						<th>Nom</th>
						<th>Début</th>
						<th>Fin</th>
						<th>&nbsp;</th>
					</tr>";
				while ($row = spip_fetch_array($req)) {
					
					$date = substr($row['start'],0,10);
					$date_fin = substr($row['end'],0,10);
					$start = substr($row['start'],11,5);
					$end = substr($row['end'],11,5);
					$class=$url=$lien_start=$lien_end='';

					$fc_date = explode('-',substr($row['start'],0,10));
					$fc_date_fin = explode('-',substr($row['end'],0,10));
					$fc_start = explode(':',substr($row['start'],11,5));
					$fc_end = explode(':',substr($row['end'],11,5));
					
					if(strlen($row['id_style']))
						$class=",className: 'f_".mysql_real_escape_string(trim($row['id_style']))."'";

					if(strlen(trim($row['lien']))){
							$lien_start="<a href=\"".$row['lien']."\">";
							$lien_end="</a>";
					}

					$url="url:\"javascript:ModifierEvenement('".$row['id_event']."')\",";

					$events.="{title: '".mysql_real_escape_string($row['titre'])."',start: new Date(".$fc_date[0].", ".($fc_date[1]-1).", ".$fc_date[2].", ".$fc_start[0].", ".$fc_start[1]."),end: new Date(".$fc_date_fin[0].", ".($fc_date_fin[1]-1).", ".$fc_date_fin[2].", ".$fc_end[0].", ".$fc_end[1]."),".$url." allDay: false ".$class."},";
					
					$HTML.= "<tr class=\"tr_liste\">
								<td class=\"verdana12\">".$lien_start."".$row['titre']."".$lien_end."</td>
								<td class=\"arial1\">".MyDateEnLettre ($date)."<br/>".$start."</td>
								<td class=\"arial1\">".MyDateEnLettre ($date_fin)."<br/>".$end."</td>
								<td>
									<a href=\"javascript:ModifierEvenement('".$row['id_event']."')\"><img src=\""._DIR_PLUGIN_FULLCALENDAR."img_pack/event_edit.png\"></a> &nbsp; 
									<a href=\"javascript:EffacerEvenement('".$row['id_event']."')\"><img src=\""._DIR_PLUGIN_FULLCALENDAR."img_pack/event_remove.png\"></a>
								</td>
							 </tr>
							";


				}
				
				$HTML.="</table>";
				
				$events=substr($events,0,strlen($events)-1);

			}


		$INTERFACE="	
			<script type='text/javascript'>
			$(document).ready(function() {

			$('#HeureFin').timepicker();
			
			$('#HeureDebut').timepicker();

			$.datepicker.regional['fr'] = {
				closeText: 'Fermer',
                prevText: '&#x3c;Préc',
                nextText: 'Suiv&#x3e;',
                currentText: 'Courant',
                monthNames: ['Janvier','F&eacute;vrier','Mars','Avril','Mai','Juin',
                'Juillet','Ao&ucirc;t','Septembre','Octobre','Novembre','D&eacute;cembre'],
                monthNamesShort: ['Jan','F&eacute;v','Mar','Avr','Mai','Jun',
                'Jul','Ao&ucirc;','Sep','Oct','Nov','Déc'],
                dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
                dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
                weekHeader: 'Sm',
                dateFormat: 'dd/mm/yy',
                firstDay: 1,
                isRTL: false,
                showMonthAfterYear: false,
                yearSuffix: ''
            };
            
			$.datepicker.setDefaults($.datepicker.regional['fr']);

			$('#Date').datepicker($.extend({},
				$.datepicker.regional['fr'], {
				onSelect: function(date) {
						var day =$('#Date').datepicker('getDate');
						$('#Date_Fin').datepicker('option','minDate',day);
						$('#Date_Fin').datepicker('setDate',day);
					}, 
					showStatus: true,
					showWeeks: true,
					highlightWeek: true, 
					showOn: 'both',
					numberOfMonths: 1,
					firstDay: 1,
					buttonImage:'"._DIR_PLUGIN_FULLCALENDAR."css/calendar.png',
					buttonImageOnly: true,
					showAnim: 'scale', 
					showOptions: { 
						origin: ['top', 'left'] 
					}
				}));

			$('#Date_Fin').datepicker($.extend({},
				$.datepicker.regional['fr'], { 
					showStatus: true,
					showWeeks: true,
					highlightWeek: true, 
					showOn: 'both',
					numberOfMonths: 1,
					firstDay: 1,
					buttonImage:'"._DIR_PLUGIN_FULLCALENDAR."css/calendar.png',
					buttonImageOnly: true,
					showAnim: 'scale', 
					showOptions: { 
						origin: ['top', 'left'] 
					}
				}));


			$('#calendar_aff').fullCalendar({
					theme: 0,
					firstDay: 1,
					defaultView: 'month',
					aspectRatio: 1.80,
					header: { left: 'prev,next', center: 'title', right: 'today, month, agendaWeek, agendaDay' },
					editable: false,
					events: [
					".$events."
					],
					weekends: true,
					titleFormat: {
						month: 'MMMM yyyy',
						week: \"d [MMMM] [ yyyy]{  -  d MMMM yyyy}\",
						day: 'dddd d MMMM yyyy'
					},
					columnFormat: {
						month: 'dddd',
						week: 'dddd d',
						day: 'dddd d MMMM'
					},
					timeFormat: {
						month:      \"\",
						agendaDay:  \"H:mm{ - H:mm}\",
						agendaWeek: \"H(:mm){ - H:mm}\",
						basicWeek:  \"H(:mm){ - H:mm}\",
						basicDay:   \"H:mm{ - H:mm}\",
						'': 'H(:mm)'
					}
			});

		";
				
		if($rw['id_style'])	$INTERFACE .= "$(\"select#id_style option[value='".$rw['id_style']."']\").attr(\"selected\", \"selected\");";
			
		$INTERFACE.="
			});
			</script>
			<div id='calendar_aff'></div><br/>
			<div class=\"formulaire_spip formulaire_config\">
				<div class=\"cadre_padding\">		
					
				<form class=\"noajax\" action=\"\" name=\"Formulaire\" method=\"POST\">
				<input type=\"hidden\" name=\"action_to_take\" value=\"".$ACTION."\">
				<input type=\"hidden\" name=\"id_evenement\" value=\"".$ID_EVENEMENT."\">
				<p>Calendrier : <select name=\"id_calendrier\">".$LISTE_CALENDRIER."</select></p>

				<p>Du : 
				<input type='text' name='Date' id='Date' size='10' value=\"".$DATE."\">
				<input type='text' name='HeureDebut' id='HeureDebut' size='5' value=\"".$START."\">
				&nbsp;&nbsp; Au : <input type='text' name='Date_Fin' id='Date_Fin' size='10' value=\"".$DATE_FIN."\">
				<input type='text' name='HeureFin' id='HeureFin' size='5' value=\"".$END."\"></p>

<p>Titre de l'évènement : <input type='text' name='Nom_Evenement' class=\"forml\" style=\"width:98%\" value=\"".$NOM."\"></p>

				<p>Lien : <input type='text' name='Lien_Evenement' class=\"forml\" style=\"width:98%\" value=\"".$LIEN."\"></p>

				".$STYLES."
		
				</div>
				<div class=\"boutons\">".$BUTTON."</div>
				</form>
			</div>";


		} else if($type=='google') {
		
		$INFO="<b>Agenda Google</b><br/><br/>Vérifier bien que votre agenda est <u>public</u> puis renseignez l'ID dans le formulaire ci-contre.<br/><br/>".$INFO;
		
		# Récupère le lien Google dans les évènements
		
		$sql = "SELECT lien FROM ".$table_prefix."_fullcalendar_events WHERE id_fullcalendar='".$id."' LIMIT 1";
		$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
		if(mysql_num_rows($req)){ # Une clé est déjà renseignée
			$rw = spip_fetch_array($req);
			$gcalID=$rw['lien'];
			$URL_AGENDA = "http://www.google.com/calendar/feeds/".$rw['lien']."/public/basic";
			$ACTION='update';
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" Modifier cette clé \" class=\"fondo\" />";
		} else { # on ajoute la clé
			$ACTION='add';
			$gcalID='';
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" Enregistrer la clé \" class=\"fondo\" />";	
		}

		
		$INTERFACE='
			<div class="formulaire_spip formulaire_config">
				<div class="cadre_padding">

				<form action="" name="Formulaire" method="POST">
				<input type="hidden" name="action_to_take" value="'.$ACTION.'">
				<input type="hidden" name="id_calendrier" value="'.$id.'">
				<p><label>ID de l\'agenda : <input type="text" name="gcalID" style="width:75%"	value="'.$gcalID.'" /></label></p>

				</div>
				<div class="boutons">'.$BUTTON.'</div>
				</form>
			</div>
		
		 ';
		
		} else if($type=='article'){

			$INFO="<b>Agenda d'articles avec mot clé</b><br/><br/>Sélectionnez le mot clé à lier aux articles qui serviront d'évènements pour ce calendrier.<br/><br/>".$INFO;
			
			# Récupère le mot clé dans les évènements
			
			$sql = "SELECT lien FROM ".$table_prefix."_fullcalendar_events WHERE id_fullcalendar='".$id."' LIMIT 1";
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
			if(mysql_num_rows($req)){ # Un mot clé est déjà renseigné
				$rw = spip_fetch_array($req);
				$ID_MOT=$rw['lien'];
				$ACTION='update_mot';
				$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" Modifier ce mot clé \" class=\"fondo\" />";
			} else { # on ajoute la clé
				$ACTION='add_mot';
				$ID_MOT='';
				$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" Enregistrer \" class=\"fondo\" />";	
			}

			# Liste des mots clés du site (dans un groupe lié aux articles)
			
			$LISTE_MOTS='';
			
			$sql = "SELECT
			 M.id_mot,
			 M.titre 
			FROM
			 ".$table_prefix."_mots M,
			 ".$table_prefix."_groupes_mots G
			WHERE 
			 M.id_groupe=G.id_groupe AND
			 G.tables_liees like '%articles%'
			ORDER BY M.titre ASC";
			
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error());
			if(mysql_num_rows($req)){
				while ($row = spip_fetch_array($req)) {
					$LISTE_MOTS.="<option value=\"".$row['id_mot']."\"";
					$LISTE_MOTS.=($row['id_mot']==$ID_MOT)?' SELECTED':'';
					$LISTE_MOTS.=">".$row['titre']."</option>";
				}
			}
			
			$INTERFACE="
			<div class=\"formulaire_spip formulaire_config\">
				<div class=\"cadre_padding\">
					<form class=\"noajax\" action=\"\" name=\"Formulaire\" method=\"POST\">
					<input type=\"hidden\" name=\"action_to_take\" value=\"".$ACTION."\">
					<input type=\"hidden\" name=\"id_calendrier\" value=\"".$id."\">
					<p>Mot clé utilisé pour générer les évènements de ce calendrier : <select name=\"id_mot\">".$LISTE_MOTS."</select></p>		
				</div>
				<div class=\"boutons\">".$BUTTON."</div>
			</form>
			</div>";
			
			# Récupère les article liés à ce mot clé

			$sql = "SELECT
				 A.id_article,
				 A.titre,
				 A.date,
				 A.date_redac
				FROM
				 spip_articles A,
				 spip_mots_articles M
				WHERE
				 M.id_mot='".$ID_MOT."' AND
				 A.id_article=M.id_article
				 ORDER BY A.date ASC";
			$req = mysql_query($sql) or die('Erreur SQL !<br>'.$sql.'<br>'.mysql_error()); 
			$num_events = mysql_num_rows($req);
			if(!$num_events) $INFO.="Aucun évènement dans ce calendrier!";
			else {
				$HTML="
				
				<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
					<tbody>
					<tr>
						<th>Article</th>
						<th>Début</th>
						<th>Fin</th>
					</tr>";
				while ($row = spip_fetch_array($req)) {
					
					$date = substr($row['date'],0,10);
					$date_fin = substr($row['date_redac'],0,10);
					$start = substr($row['date'],11,5);
					$end = substr($row['date_redac'],11,5);
					$erreur = ($date_fin=="0000-00-00")?' ortho':'';
					$HTML.= "<tr class=\"tr_liste\">
								<td class=\"verdana12\"><a href=\"?exec=articles&id_article=".$row['id_article']."\">".$row['titre']."</a></td>
								<td class=\"arial1\">".MyDateEnLettre ($date)."<br/>".$start."</td>
								<td class=\"arial1".$erreur."\">".MyDateEnLettre ($date_fin)."<br/>".$end."</td>
							 </tr>
							";
				}
				$HTML.="</table>";
			}
			
		}

	}


 $commencer_page = charger_fonction('commencer_page', 'inc');
 print $commencer_page(_T('Fullcalendar'), "", "") ;
 print "<br/><br/>";
 print gros_titre(_T('Gestion des évènements'),'',false);
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

 print debut_cadre_enfonce('',true,'','','');
 print "<table class=\"cellule-h-table\" style=\"vertical-align: middle;\" cellpadding=\"0\"><tbody><tr>
 <td><a href=\"?exec=fullcalendar_css\" class=\"cellule-h\"><span class=\"cell-i\"><img src='"._DIR_PLUGIN_FULLCALENDAR."img_pack/edit_css.png' alt=\"Styles de fullcalendar : configuration\"></span></a></td>
 <td class=\"cellule-h-lien\"><a href=\"?exec=fullcalendar_css\" class=\"cellule-h\">CSS - configuration</a></td>
 </tr></tbody></table>";
 print fin_cadre_enfonce(true);

 if(strlen($LISTE)){
	print debut_cadre_enfonce('',true,'',_T('Vos calendriers'),'');
	print $LISTE;
	print fin_cadre_enfonce(true);
 }
  
 print creer_colonne_droite('',true);
 print debut_droite("", true);
 print debut_cadre_trait_couleur("", true, "", $titre=$nom,"","");

 # INTERFACE D'AJOUT

 print $INTERFACE;

 # GESTION DES EVENEMENTS

 if(($type=='mysql'||$type=='article')
	 && strlen($HTML)
	){
	print debut_cadre_relief("", false,"", $titre = _T('Tous les évènements de ce calendrier'));      
	print $HTML;
	print fin_cadre_relief(false);
 }else if(strlen($URL_AGENDA)){
	print debut_cadre_enfonce('',true,'',_T('Aperçu du flux'),'');
	print "Votre lien : <a href='".$URL_AGENDA."'>".$URL_AGENDA."</a>";
	print "<iframe src=\"".$URL_AGENDA."\" style=\"width:98%;height:250px\"></iframe>";
	print fin_cadre_enfonce(true);
	
 }

 print fin_cadre_trait_couleur(true);
 print fin_gauche();
 print fin_page();
   
}

/* Formate un DATETIME mysql en locale fr */

function MyDateEnLettre ($MyDate,$digit=5){
 $jour = array("","Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche");
 $day_arr = array("","Mon","Tue","Wed","Thu","Fri","Sat","Sun");
 $mois = array("","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");
 $t=explode('-', $MyDate);
 $Y=$t[0];
 $M=(strlen($t[1])<2)?"0".$t[1]:$t[1];
 $D=(strlen($t[2])<2)?"0".$t[2]:$t[2];
 $day=date("D", mktime(0,0,0,$M,$D,$Y));
 $date=$day.":".$D.":".$M.":".$Y;
 $date_tmp = explode(':', $date);
 $day = $date_tmp[0];
 for ($d=1;$d<=7;$d++) 
  if(strstr($day,$day_arr[$d])) $n=$d;
 if($digit>=1) $return  = $jour[$n].' ';
 if($digit>=2) $return .= $date_tmp[1]+0;
 if($digit>=3) $return .= ($date_tmp[1]==1)?'er':'';
 if($digit>=4) $return .= ' '.$mois[$date_tmp[2]+0];
 if($digit>=5) $return .= ' '.$date_tmp[3];
 return $return;
}

/* Formate une date mysql vers locale fr */

function DateFromMysql ($date){
 $t=explode('-', $date);
 return $t['2'].'/'.$t['1'].'/'.$t['0'];
}

?>
