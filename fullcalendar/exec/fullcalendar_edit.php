<?php

/* * * * * * * * * * * * * * * * * * * *
 *
 *     - FullCalendar pour SPIP -
 *
 * Formulaires d'ajout et modification des évènements
 *
 * Auteur : Grégory PASCAL - ngombe at gmail dot com
 * Modifs : 10/10/2011
 *
 */

function exec_fullcalendar_edit(){
 include_spip("inc/presentation");
 // vérifier les droits
 global $connect_statut;
 global $connect_toutes_rubriques;
 if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	print _T('avis_non_acces_page');
	exit;
 }

	# Récupère l'id du calendrier à éditer

	$id_fullcalendar=($_GET['id'])?intval($_GET['id']):intval($_GET['id_calendrier']);
	if(!$id_fullcalendar) die("Erreur dans l'appel de cette page!");

	$HTML=$INFO=$INTERFACE=$LISTE=$LISTE_CALENDRIER='';

	# Récupère les autres calendriers

	$res = sql_select('*', 'spip_fullcalendar_main','id_fullcalendar!='.$id_fullcalendar);
	if(sql_count($res)){
		$LISTE="<br/>";
		while ($row = sql_fetch($res)) {
			$LISTE .= "<center class='formulaire_spip'><a href=\"?exec=fullcalendar_edit&id=".$row['id_fullcalendar']."\">".$row['nom']."</a></center><br/>";
			if($row['type']=='mysql')
			$LISTE_CALENDRIER.="<option value=\"".$row['id_fullcalendar']."\">".$row['nom']."</option>";
		}
	}
	sql_free($res);

	######################
	# Ajout d'un mot clé #
	######################

	if(
		$_POST['action_to_take']=='add_mot'
		&& $_POST['ajouter']
		&& $_POST['id_calendrier']
		&& strlen($_POST['id_mot'])
	){
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_mot_add').".</center><br/>";
		sql_insert('spip_fullcalendar_events', '(id_fullcalendar, lien)', "(".sql_quote(intval($_POST['id_calendrier'])).", ".sql_quote(intval($_POST['id_mot'])).")");
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
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_mot_edit').".</center><br/>";
		sql_update('spip_fullcalendar_events', array('lien' => sql_quote(intval($_POST['id_mot'])) ), "id_fullcalendar=".sql_quote(intval($_POST['id_calendrier'])) );
	}

	########################
	# Ajout d'une rubrique #
	########################

	if(
			$_POST['action_to_take']=='add_rubrique'
			&& $_POST['ajouter']
			&& $_POST['id_calendrier']
			&& strlen($_POST['id_rubrique'])
	){
			$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_rub_add').".</center><br/>";
			sql_insert('spip_fullcalendar_events', '(id_fullcalendar, lien)', "(".sql_quote(intval($_POST['id_calendrier'])).", ".sql_quote(intval($_POST['id_rubrique'])).")");
	}

	########################
	# Modifier la rubrique #
	########################

	if(
			$_POST['action_to_take']=='update_rubrique'
			&& $_POST['ajouter']
			&& $_POST['id_calendrier']
			&& strlen($_POST['id_rubrique'])
	){
			$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_rub_edit').".</center><br/>";
			sql_update('spip_fullcalendar_events', array('lien' => sql_quote(intval($_POST['id_rubrique'])) ), "id_fullcalendar=".sql_quote(intval($_POST['id_calendrier'])) );
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
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_key_add').".</center><br/>";
		sql_insert('spip_fullcalendar_events', '(id_fullcalendar, lien)', "(".sql_quote(intval($_POST['id_calendrier'])).", ".sql_quote($_POST['gcalID']).")");
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
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:action_key_edit').".</center><br/>";
		sql_update('spip_fullcalendar_events', array('lien' => sql_quote(trim($_POST['gcalID'])) ), "id_fullcalendar=".sql_quote(intval($_POST['id_calendrier'])) );
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
		$date = $t['2'].'-'.$t['1'].'-'.$t['0'];
		$t = explode('/',$_POST['Date_Fin']);
		$date_fin = $t['2'].'-'.$t['1'].'-'.$t['0'];
		unset($t);

		sql_insert("spip_fullcalendar_events",
		"(id_fullcalendar, id_style, titre, lien, start, end)",
		"(
		 ".sql_quote(intval($_POST['id_calendrier'])).",
		 ".sql_quote(intval($_POST['id_style'])).",
		 ".sql_quote($_POST['Nom_Evenement']).",
		 ".sql_quote($_POST['Lien_Evenement']).",
		 ".sql_quote($date." ".$_POST['HeureDebut'].":00").",
		 ".sql_quote($date_fin." ".$_POST['HeureFin'].":00")."
		 )");

		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:event_add').".</center><br/>";

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

		sql_update('spip_fullcalendar_events',
			array(
			'id_fullcalendar' => sql_quote($_POST['id_calendrier']),
			'id_style' => sql_quote($_POST['id_style']),
			'titre' => sql_quote($_POST['Nom_Evenement']),
			'lien' => sql_quote(trim($_POST['Lien_Evenement'])),
			'start' => sql_quote($date." ".$_POST['HeureDebut'].":00"),
			'end' => sql_quote($date_fin." ".$_POST['HeureFin'].":00")
			),
			"id_event=".sql_quote(intval($_POST['id_evenement']))
		);

		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:event_edit').".</center><br/>";

	}

	#######################
	# Efface un évènement #
	#######################

	if(
		$_POST['action_to_take']=='del'
		&& $_POST['id_calendrier']
		&& $_POST['id_evenement']
	){
		sql_delete("spip_fullcalendar_events", "id_event=".sql_quote(intval($_POST['id_evenement'])) );
		$INFO="<center><img src='"._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/ok.png'> &nbsp; "._T('fullcalendar:event_del')."</center><br/>";
	}

	#################################
	# Calendrier en cours d'édition #
	#################################

	$req = sql_select('*', 'spip_fullcalendar_main', 'id_fullcalendar='.$id_fullcalendar);
	$num_calendar = sql_count($req);
	if(!$num_calendar) $INFO="<center style=\"color:red\">"._T('fullcalendar:unknown_error')."</center><br/>";
	else {

		$row  = sql_fetch($req);
		$id   = $row['id_fullcalendar'];
		$type = $row['type'];
		$nom  = $row['nom'];
				sql_free($req);

		# Récupère les styles pour les évènements

		$req = sql_select('*', 'spip_fullcalendar_styles');
		$num_style = sql_count($req);
		if(!$num_style) $STYLES=_T('fullcalendar:no_style');
		else {
			$STYLES="<p>"._T('fullcalendar:event_style').":<select name='id_style' id='id_style'>";
			while ($rw = sql_fetch($req)) {
				$STYLES.="<option value=\"".$rw['id_style']."\">".$rw['titre']."</option>";
			}
			sql_free($req);
			$STYLES.="</select></p>";
		}

		$LISTE_CALENDRIER.='<option SELECTED value="'.$id_fullcalendar.'">'.$nom.'</option>';

		if($type=='mysql'){

		# Modification d'un évènement

		if(
			$_POST['action_to_take']=='edit'
			&& $_POST['id_evenement']
		){

			$req = sql_select('*', 'spip_fullcalendar_events', array('id_fullcalendar='.$id_fullcalendar, 'id_event='.intval($_POST['id_evenement'])) );
			$rw = sql_fetch($req);
			sql_free($req);

			$NOM=$rw['titre'];
			$LIEN=$rw['lien'];

			$ACTION='EditEvent';
			$ID_EVENEMENT=$_POST['id_evenement'];
			$DATE = DateFromMysql(substr($rw['start'],0,10));
			$DATE_FIN = DateFromMysql(substr($rw['end'],0,10));
			$START = substr($rw['start'],11,5);
			$END = substr($rw['end'],11,5);
			$BUTTON = "<input type=\"submit\" name=\"enregistrer\" value=\" "._T('bouton_enregistrer')." \" class=\"fondo\" />";

		} else {

			$ACTION='AddEvent';
			$ID_EVENEMENT='';
			$DATE=(strlen($_POST['Date'])==10)?stripslashes($_POST['Date']):Date("d/m/Y");
			$DATE_FIN=(strlen($_POST['Date_Fin'])==10)?stripslashes($_POST['Date_Fin']):Date("d/m/Y");
			$START=(strlen($_POST['HeureDebut'])==5)?stripslashes($_POST['HeureDebut']):Date("H:m");
			$END=(strlen($_POST['HeureFin'])==5)?stripslashes($_POST['HeureFin']):Date("H:m");
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('fullcalendar:add')." \" class=\"fondo\" />";

		}

			# Récupère les style css
			$HTML.="<style type='text/css'>";
			$result = sql_select('*', 'spip_fullcalendar_styles');
			while ( $row = sql_fetch($result)){
				$HTML.=".f_".$row['id_style'].", .f_".$row['id_style']." a {background-color:".$row['bgcolor'].";border-color:".$row['bordercolor'].";color:".$row['textcolor'].";}\n";
				$style[$row['id_style']]['back'] = $row['bgcolor'];
				$style[$row['id_style']]['text'] = $row['textcolor'];
				$style[$row['id_style']]['bord'] = $row['bordercolor'];
			}
			sql_free($result);
			$HTML.="</style>";

			# Récupère les évènements du calendrier MySQL

			$events='';
			$req = sql_select('*', 'spip_fullcalendar_events', 'id_fullcalendar='.$id_fullcalendar, '', 'start ASC');
			$num_events = sql_count($req);
			if(!$num_events) $INFO.=_T('fullcalendar:no_event');
			else {

				$HTML.="

				<script type=\"text/javascript\">
				//<!--
				function EffacerEvenement(id){
					if(!document.getElementById) return false;
					if(confirm('"._T('fullcalendar:delete_event')."')){
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
					<tbody>";

				$entete="<tr>
						<th>"._T('fullcalendar:event_title')."</th>
						<th>"._T('fullcalendar:event_start')."</th>
						<th>"._T('fullcalendar:event_end')."</th>
						<th>&nbsp;</th>
					</tr>";

				while ($row = sql_fetch($req)) {

					$date = substr($row['start'],0,10);
					$date_fin = substr($row['end'],0,10);
					$start = substr($row['start'],11,5);
					$end = substr($row['end'],11,5);
					$class=$url=$lien_start=$lien_end='';

					$fc_date = explode('-',substr($row['start'],0,10));
					$fc_date_fin = explode('-',substr($row['end'],0,10));
					$fc_start = explode(':',substr($row['start'],11,5));
					$fc_end = explode(':',substr($row['end'],11,5));

					if($row['id_style']>0){
						$class=",backgroundColor: '".$style[$row['id_style']]['back']."', textColor: '".$style[$row['id_style']]['text']."', borderColor: '".$style[$row['id_style']]['bord']."'";
					} else $class='';

					if(strlen(trim($row['lien']))){
							$lien_start="<a href=\"".$row['lien']."\">";
							$lien_end="</a>";
					}

					$bloc='';
					$t=substr($date,5,2);
					if($bloc_mois!=$t){
						$bloc_mois=$t;
						$annee=substr($date,0,4);
						$bloc.="<tr class='spip_large'><th colspan=4 style=\"border-bottom:1px solid #000;padding-top:4px;\">
						".MyMonth($t)." ".$annee."
						</th></tr>".$entete;
					}

					$url="url:\"javascript:ModifierEvenement('".$row['id_event']."')\",";

					$events.="{title: '".texte_script($row['titre'])."',start: new Date(".$fc_date[0].", ".($fc_date[1]-1).", ".$fc_date[2].", ".$fc_start[0].", ".$fc_start[1]."),end: new Date(".$fc_date_fin[0].", ".($fc_date_fin[1]-1).", ".$fc_date_fin[2].", ".$fc_end[0].", ".$fc_end[1]."),".$url." allDay: false ".$class."},";

					$HTML.= "$bloc<tr class=\"tr_liste f_".$row['id_style']."\">
								<td class=\"verdana12\">".$lien_start."".$row['titre']."".$lien_end."</td>
								<td class=\"arial1\">".MyDateEnLettre ($date)."<br/>".$start."</td>
								<td class=\"arial1\">".MyDateEnLettre ($date_fin)."<br/>".$end."</td>
								<td>
									<a href=\"javascript:ModifierEvenement('".$row['id_event']."')\"><img src=\""._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/event_edit.png\"></a> &nbsp;
									<a href=\"javascript:EffacerEvenement('".$row['id_event']."')\"><img src=\""._DIR_PLUGIN_FULLCALENDAR."prive/themes/spip/images/event_remove.png\"></a>
								</td>
							 </tr>
							";

				}
				sql_free($req);
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

			var day =$('#Date').datepicker('getDate');
			$('#Date_Fin').datepicker('option','minDate',day);

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
				<p>"._T('fullcalendar:calendar')." : <select name=\"id_calendrier\" style=\"width:84%\">".$LISTE_CALENDRIER."</select></p>
				<p>"._T('fullcalendar:event_from')." : <input type='text' name='Date' id='Date' size='10' value=\"".$DATE."\">
				<input type='text' name='HeureDebut' id='HeureDebut' size='5' value=\"".$START."\">
				</p>
				<p>"._T('fullcalendar:event_to')." : <input type='text' name='Date_Fin' id='Date_Fin' size='10' value=\"".$DATE_FIN."\">
				<input type='text' name='HeureFin' id='HeureFin' size='5' value=\"".$END."\">
				</p>
				<p>"._T('fullcalendar:event_title')." : <input type='text' name='Nom_Evenement' class=\"forml\" style=\"width:98%\" value=\"".$NOM."\"></p>
				".$STYLES."
				<p>"._T('fullcalendar:lien')." : <input type='text' name='Lien_Evenement' class=\"forml\" style=\"width:98%\" value=\"".$LIEN."\"></p>
				</div>
				<div class=\"boutons\">".$BUTTON."</div>
				</form>
			</div>";


		} else if($type=='google') {

		$INFO="<b>"._T('fullcalendar:google_agenda')."</b><br/><br/>"._T('fullcalendar:google_agenda_info')."<br/><br/>".$INFO;

		# Récupère le lien Google dans les évènements

		$req = sql_select('lien', 'spip_fullcalendar_events', 'id_fullcalendar='.$id);
		if(sql_count($req)){ # Une clé est déjà renseignée
			$rw = sql_fetch($req);
			sql_free($req);
			$gcalID=$rw['lien'];
			$URL_AGENDA = "http://www.google.com/calendar/feeds/".$rw['lien']."/public/basic";
			$ACTION='update';
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('fullcalendar:key_update')." \" class=\"fondo\" />";
		} else { # on ajoute la clé
			$ACTION='add';
			$gcalID='';
			$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('fullcalendar:key_save')." \" class=\"fondo\" />";
		}


		$INTERFACE='
			<div class="formulaire_spip formulaire_config">
				<div class="cadre_padding">
				<form action="" name="Formulaire" method="POST">
				<input type="hidden" name="action_to_take" value="'.$ACTION.'">
				<input type="hidden" name="id_calendrier" value="'.$id.'">
				<p><label>'._T('fullcalendar:google_agenda_id').' : <input type="text" name="gcalID" style="width:75%" value="'.$gcalID.'" /></label></p>
				</div>
				<div class="boutons">'.$BUTTON.'</div>
				</form>
			</div>';

		} else if($type=='rub')     {

			$INFO="<b>"._T('fullcalendar:rub_agenda')."</b><br/><br/>"._T('fullcalendar:rub_agenda_info')."<br/><br/>".$INFO;

			# Récupère le secteur utilisé pour les évènements

			$req = sql_select('lien', 'spip_fullcalendar_events', 'id_fullcalendar='.$id);
			if(sql_count($req)){ # Une rubrique est déjà renseignée
					$rw = sql_fetch($req);
					$ID_MOT=intval($rw['lien']);
					sql_free($req);
					$ACTION='update_rubrique';
					$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('fullcalendar:rub_agenda_change')." \" class=\"fondo\" />";
			} else { # on ajoute la clé
					$ACTION='add_rubrique';
					$ID_MOT='';
					$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('bouton_enregistrer')." \" class=\"fondo\" />";
			}

			# Rubriques du site (contenant des articles)

			$LISTE_RUBRIQUES='';

			$req = sql_select(
					array(
							"DISTINCT R.id_rubrique",
							"R.titre" ),
					array(
							"spip_rubriques AS R",
							"spip_articles AS A" ),
					array(
							"R.id_parent=0",
							"R.id_rubrique=A.id_rubrique",
							"R.id_parent=0" ),
					"",
					"R.titre ASC");

			if(sql_count($req)){

					while ($row = sql_fetch($req)) {
							$LISTE_RUBRIQUES.="<option value=\"".$row['id_rubrique']."\"";
							$LISTE_RUBRIQUES.=($row['id_rubrique']==$ID_MOT)?' SELECTED':'';
							$LISTE_RUBRIQUES.=">".$row['titre']."</option>";
					}
					sql_free($req);

			}

			$INTERFACE="
			<div class=\"formulaire_spip formulaire_config\">
					<div class=\"cadre_padding\">
							<form class=\"noajax\" action=\"\" name=\"Formulaire\" method=\"POST\">
							<input type=\"hidden\" name=\"action_to_take\" value=\"".$ACTION."\">
							<input type=\"hidden\" name=\"id_calendrier\" value=\"".$id."\">
							<p>"._T('fullcalendar:rub_agenda_source')." : <select name=\"id_rubrique\">".$LISTE_RUBRIQUES."</select></p>
					</div>
					<div class=\"boutons\">".$BUTTON."</div>
			</form>
			</div>";

			# dans le cas de sous-rubriques, récupère les ids des rubriques filles

			$req = sql_select(
					array(
							"id_rubrique",
							),
					array(
							"spip_rubriques" ),
					array(
					"id_parent='".$ID_MOT."'"
					),
					"",
					"");

			while ($row = sql_fetch($req)) {
				$ID_RUBRIQUES.="'".$row['id_rubrique']."',";
			}

			$ID_RUBRIQUES.="'".$ID_MOT."'";

			# Récupère les article liés à ce secteur/rubrique

			$req = sql_select(
					array(
							"A.id_article",
							"A.titre",
							"A.date",
							"A.date_redac" ),
					array(
							"spip_articles AS A",
							"spip_rubriques AS R" ),
					array(
							"A.id_rubrique IN (".$ID_RUBRIQUES.")",
							"A.id_rubrique=R.id_rubrique" ),
					"",
					"A.date ASC");

			$num_events = sql_count($req);
			if(!$num_events) $INFO.=_T('fullcalendar:no_event');
			else {
					$HTML="
					<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
							<tbody>
							<tr>
									<th>"._T('fullcalendar:event_title')."</th>
									<th>"._T('fullcalendar:event_start')."</th>
									<th>"._T('fullcalendar:event_end')."</th>
							</tr>";
					while ($row = sql_fetch($req)) {
							$date = substr($row['date'],0,10);
							$date_fin = substr($row['date_redac'],0,10);
							$start = substr($row['date'],11,5);
							$end = substr($row['date_redac'],11,5);
							$erreur = ($date_fin=="0000-00-00")?' ortho':'';
							# repère un incohérence dans les dates 
							$test_debut = $row['date'];
							$test_fin = $row['date_redac'];
							$diff = strtotime($test_fin) - strtotime($test_debut); // Ecart en secondes
							if($diff<0){
								$erreur =' ortho';
								$end.="<- <b>"._T('fullcalendar:event_error')."</b>";
							}
							$HTML.= "<tr class=\"tr_liste\">
										<td class=\"verdana12\"><a href=\"?exec=articles&id_article=".$row['id_article']."\">".supprimer_numero($row['titre'])."</a></td>
										<td class=\"arial1\">".MyDateEnLettre ($date)."<br/>".$start."</td>
										<td class=\"arial1".$erreur."\">".MyDateEnLettre ($date_fin)."<br/>".$end."</td>
									 </tr>";
						}
					sql_free($req);
					$HTML.="</table>";
				}

		} else if($type=='article')	{

			$INFO="<b>"._T('fullcalendar:mot_agenda')."</b><br/><br/>"._T('fullcalendar:mot_agenda_info')."<br/><br/>".$INFO;

			# Récupère le mot clé dans les évènements

			$req = sql_select('lien', 'spip_fullcalendar_events', 'id_fullcalendar='.$id);
			if(sql_count($req)){ # Un mot clé est déjà renseigné
				$rw = sql_fetch($req);
				$ID_MOT=intval($rw['lien']);
				sql_free($req);
				$ACTION='update_mot';
				$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('fullcalendar:mot_agenda_change')." \" class=\"fondo\" />";
			} else { # on ajoute la clé
				$ACTION='add_mot';
				$ID_MOT='';
				$BUTTON = "<input type=\"submit\" name=\"ajouter\" value=\" "._T('bouton_enregistrer')." \" class=\"fondo\" />";
			}

			# Mots clés du site (des groupes liés aux articles)

			$LISTE_MOTS='';

			$req = sql_select(
				array(
					"M.id_mot",
					"M.titre" ),
				array(
					"spip_mots AS M",
					"spip_groupes_mots AS G" ),
				array(
					"M.id_groupe=G.id_groupe",
					"G.tables_liees like '%articles%'" ),
				"",
				"M.titre ASC");

			if(sql_count($req)){

				while ($row = sql_fetch($req)) {
					$LISTE_MOTS.="<option value=\"".$row['id_mot']."\"";
					$LISTE_MOTS.=($row['id_mot']==$ID_MOT)?' SELECTED':'';
					$LISTE_MOTS.=">".$row['titre']."</option>";
				}
				sql_free($req);

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

			$req = sql_select(
				array(
					"A.id_article",
					"A.titre",
					"A.date",
					"A.date_redac" ),
				array(
					"spip_articles AS A",
					"spip_mots_articles AS M" ),
				array(
					"M.id_mot='".$ID_MOT."'",
					"A.id_article=M.id_article" ),
				"",
				"A.date ASC");

			$num_events = sql_count($req);
			if(!$num_events) $INFO.=_T('fullcalendar:no_event');
			else {
				$HTML="
				<table width=\"100%\" border=\"0\" cellpadding=\"2\" cellspacing=\"0\">
					<tbody>
					<tr>
						<th>"._T('fullcalendar:event_title')."</th>
						<th>"._T('fullcalendar:event_start')."</th>
						<th>"._T('fullcalendar:event_end')."</th>
					</tr>";

				while ($row = sql_fetch($req)) {
					$date = substr($row['date'],0,10);
					$date_fin = substr($row['date_redac'],0,10);
					$start = substr($row['date'],11,5);
					$end = substr($row['date_redac'],11,5);
					$erreur = ($date_fin=="0000-00-00")?' ortho':'';
					# repère un incohérence dans les dates 
					$test_debut = $row['date'];
					$test_fin = $row['date_redac'];
					$diff = strtotime($test_fin) - strtotime($test_debut); // Ecart en secondes
					if($diff<0){
						$erreur =' ortho';
						$end.="<- <b>"._T('fullcalendar:event_error')."</b>";
					}
					$HTML.= "<tr class=\"tr_liste\">
								<td class=\"verdana12\"><a href=\"?exec=articles&id_article=".$row['id_article']."\">".supprimer_numero($row['titre'])."</a></td>
								<td class=\"arial1\">".MyDateEnLettre ($date)."<br/>".$start."</td>
								<td class=\"arial1".$erreur."\">".MyDateEnLettre ($date_fin)."<br/>".$end."</td>
							 </tr>";
				}
				sql_free($req);
				$HTML.="</table>";
			}

		}

	}

 $commencer_page = charger_fonction('commencer_page', 'inc');
 print $commencer_page(_T('fullcalendar:fullcalendar'), "", "") ;
 print "<br/><br/>";
 print gros_titre(_T('fullcalendar:fullcalendar')." : "._T('fullcalendar:gestion_evenements'),'',false); 
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
 print debut_cadre_trait_couleur("", true, "", $titre=$nom,"","");

 # INTERFACE D'AJOUT

 print $INTERFACE;

 # GESTION DES EVENEMENTS

 if(($type=='mysql'||$type=='article'||$type=='rub')
	 && strlen($HTML)
	){
	print debut_cadre_relief("", false,"", $titre = _T('fullcalendar:tous_evenements'));
	print $HTML;
	print fin_cadre_relief(false);
 }else if(strlen($URL_AGENDA)){
	print debut_cadre_enfonce('',true,'',_T('fullcalendar:flux'),'');
	print _T('fullcalendar:votre_lien')." : <a href='".$URL_AGENDA."'>".$URL_AGENDA."</a>";
	print "<iframe src=\"".$URL_AGENDA."\" style=\"width:98%;height:250px\"></iframe>";
	print fin_cadre_enfonce(true);

 }

 print fin_cadre_trait_couleur(true);
 
 print fin_gauche();
 print fin_page();

}

function MyMonth($v){
	$mois = array("","Janvier","F&eacute;vrier","Mars","Avril","Mai","Juin","Juillet","Ao&ucirc;t","Septembre","Octobre","Novembre","D&eacute;cembre");
	$v=intval($v);
	return $mois[$v];
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
