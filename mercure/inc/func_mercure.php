<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| diverses fonctions communes ...            |
+--------------------------------------------+
*/

# initialise metas sur install ou MaJ
function initialise_metas_mercure($old_vers=''){
	$metas=array();
	
	if($old_vers) {
		foreach($GLOBALS['mercure'] as $k => $v) {
			# corriger version
			if($k=='version') {
				$metas[$k]=$GLOBALS['mercure_plug_version'];
			}
			else {
				$metas[$k]=$v;
			}
		}
	}
	else {
		$metas['version']=$GLOBALS['mercure_plug_version'];
	}	

  $metas['first_use'] = TRUE;
	$metas['menu'] = 'configuration';
  $metas['maj_connectes'] = 2; 
   	
  if($db = sqlite_open(':memory:')){
	  $metas['bdd'] = 'bdd';
    sqlite_close($db);    
  }else{
	  $metas['bdd'] = 'txt';
  }
  // ce sera toujours au format texte car avec SQLite
  // cela plante un peu sur les " et les ' : \" et \'
  $metas['bdd'] = 'txt';
	$metas['item_limit'] = 1000;
	$metas['time_limit'] = 0;

  $metas['refresh'] = 1000; 
  $metas['nb_lignes'] = 15; 
  
  $metas['notify'] = TRUE;
  $metas['notify_sound'] = 9;
  $metas['notify_volume'] = 100;

	$chaine = serialize($metas);
	ecrire_meta('mercure',$chaine);
	ecrire_metas();
	
	# on relit ..
	$GLOBALS['mercure'] = @unserialize($GLOBALS['meta']['mercure']);
}

# affiche le logo mercure + gros titre
function entete_page() {
	$aff = debut_boite_info(true);
	$aff.= "<div style='float:left; margin-right:5px; min-height:55px;'>" 
		. "<img src='"._DIR_IMG_MERCURE."mercure_48.png' alt='mercure' />"
		. "</div>";
	$aff.= gros_titre(_T('mercure:mercure_titre'),'',false);
	$aff.= '<div style="clear:both;"></div>'
		. '<div class="cell_info verdana2">'
		. '<img src="'._DIR_IMG_MERCURE.'mercure_clock.png" align="absmiddle" title="'._T('mercure:heure_locale').'" />&nbsp;&nbsp;'
		. '<span id="montre"></span>'
		. '</div>'
		. '<p class="space_10"></p>'
		. '<script language="JavaScript">
		    function affiche_montre(){
          date=new Date();
          jour = date.getDate();
          mois = (date.getMonth())+1;
          annee = date.getFullYear();
          heure=date.getHours();
          minute=date.getMinutes();
          seconde=date.getSeconds();
          if (jour<10) jour="0"+jour;
          if (mois<10) mois="0"+mois;
          if (heure<10) heure="0"+heure;
          if (minute<10) minute="0"+minute;
          if (seconde<10) seconde="0"+seconde;
          document.getElementById("montre").innerHTML = jour+"/"+mois+"/"+annee+" "+heure+":"+minute+":"+seconde+"<br />";
		      setTimeout("affiche_montre()",1000);
        }
        affiche_montre();
       </script>'	
		. fin_boite_info(true);
	return $aff;
}

# bouton retour haut de page
function bouton_retour_haut() {
	return $aff= "<div style='float:right; margin-top:6px;' class='icone36' title='"
				. _T('mercure:haut_page')."'>\n"
				. "<a href='#haut_page'>"
				. "<img src='"._DIR_IMG_PACK."spip_out.gif' border='0' align='absmiddle' />\n"
				. "</a></div>"
				. "<div style='clear:both;'></div>\n";
}

# generer liste des onglets
function onglets_mercure($actif) {
	# script => icone
	$pages=array('mercure_pg' => _DIR_IMG_MERCURE.'mercure.png',
				'mercure_doc' => _DIR_IMG_MERCURE.'mercure_doc.png',
				'mercure_conf' => _DIR_IMG_MERCURE.'mercure_config.png',
				'mercure_remove' => _DIR_IMG_MERCURE.'mercure_remove.png'
				);
	$res='';
	foreach($pages as $exec => $icone) {
		$res.= onglet(_T('mercure:onglet_'.$exec),generer_url_ecrire($exec), $exec,($actif==$exec?$exec:''),$icone);
	}
	$aff=debut_onglet().$res.fin_onglet()."<p class='space_20'></p>";
	return $aff;
}

# signature plugin
function signature_plugin() {
	$aff="<p class='space_10'></p>"
		. debut_boite_info(true)
		. _T('mercure:signature_plugin',array('version'=>$GLOBALS['mercure_plug_version']))."\n"
		. fin_boite_info(true);
	return $aff;
}

# encart son on-off et tous les messages
function encart_commandes($sound) {
	$aff='<p class="space_10"></p>'
		. debut_boite_info(true);
  if($sound == 'on'){
    $aff .=	
		        '<a href="#" onclick="
            var xhr_sound_on = null; 
            if(window.XMLHttpRequest)
              xhr_sound_on = new XMLHttpRequest(); 
            else if(window.ActiveXObject){
              try {
                xhr_sound_on = new ActiveXObject(\'Msxml2.XMLHTTP\');
              }
              catch (e) {
                   xhr_sound_on = new ActiveXObject(\'Microsoft.XMLHTTP\');
	            }
            }else { 
              alert(\'Votre navigateur ne supporte pas les objets XHR-XMLHttpRequest...\'); 
              xhr_sound_on = false; 
            } 
            if( xhr_sound_on != false){
              xhr_sound_on.open(\'GET\',
                             \''._URL_AJAX_MERCURE.'?action=sound_on\',
                             true);
              xhr_sound_on.onreadystatechange = function(){
                if ( xhr_sound_on.readyState == 4 ){
                  window.location = \''.generer_url_ecrire('mercure_pg').'\';
                }
              }
              xhr_sound_on.send(null);
            }            
            "><img src="'._DIR_IMG_MERCURE.'sound.png" border="0" alt="" align="top"> '._T('mercure:sound_on').'</a>&nbsp;'.($_SESSION['mercure_notify_sound'] == 'on' ? '<img src="'._DIR_IMG_MERCURE.'checked.png" border="0" alt="" align="top">':'').'<br />'
		      . '<a href="#" onclick="
            var xhr_sound_off = null; 
            if(window.XMLHttpRequest)
              xhr_sound_off = new XMLHttpRequest(); 
            else if(window.ActiveXObject){
              try {
                xhr_sound_off = new ActiveXObject(\'Msxml2.XMLHTTP\');
              }
              catch (e) {
                   xhr_sound_off = new ActiveXObject(\'Microsoft.XMLHTTP\');
	            }
            }else { 
              alert(\'Votre navigateur ne supporte pas les objets XHR-XMLHttpRequest...\'); 
              xhr_sound_off = false; 
            } 
            if( xhr_sound_off != false){
              xhr_sound_off.open(\'GET\',
                             \''._URL_AJAX_MERCURE.'?action=sound_off\',
                             true);
              xhr_sound_off.onreadystatechange = function(){
                if ( xhr_sound_off.readyState == 4 ){
                  window.location = \''.generer_url_ecrire('mercure_pg').'\';
                }
              }
              xhr_sound_off.send(null);
            }            
          "><img src="'._DIR_IMG_MERCURE.'sound_off.png" border="0" alt="" align="top"> '._T('mercure:sound_off').'</a>&nbsp;'.($_SESSION['mercure_notify_sound'] == 'off' ? '<img src="'._DIR_IMG_MERCURE.'checked.png" border="0" alt="" align="top">':'').'<br />';
		}
	$aff .= '<a href="#" onclick="window.open(\''._URL_AJAX_MERCURE.'?action=get_all\',\'Conversations\',\'menubar=no, status=no, scrollbars=yes\');"><img src="'._DIR_IMG_MERCURE.'mercure_users.png" border="0" alt="" align="top"> '._T('mercure:all_messages').'</a><br />'
		. fin_boite_info(true);
	return $aff;
}

# récupérer le pseudo et le nom de l'auteur connecté
function get_nick_and_name_auteur( $id_auteur ){
    $infos = array();
    $q=spip_query("SELECT nom,login ".
			"FROM spip_auteurs ".
			"WHERE id_auteur = $id_auteur "
			);	
	  if(spip_num_rows($q)) {
		  while($r=spip_fetch_array($q)) {
		    $infos['nom'] = $r['nom'];
		    $infos['pseudo'] = $r['login'];
		  }
    }
    return($infos);
}

# personnes connectés à cet instant
function redacteurs_connectes(){
	global $connect_id_auteur;
  $aff = debut_boite_info(true);
  # nombre de personnes depuis 15 mn ()
  # inc/auth.php update-set en_ligne => NOW() : "moment" de session !
  # voir ecrire/action:logout.php
  # spip update-set 'en_ligne' datetime -15 mn au logout de session !!??!!
  # aff' nbr corresp aux auteurs affiches par spip en bandeau sup !
  $q=spip_query("SELECT COUNT(DISTINCT id_auteur) AS nb, statut ".
			"FROM spip_auteurs ".
			"WHERE en_ligne > DATE_SUB( NOW(), INTERVAL 60 MINUTE) ".
			"AND statut IN ('0minirezo', '1comite', '6forum') ". // limite statuts spip (autres!)
			"AND id_auteur != $connect_id_auteur ".
			"GROUP BY statut"
			);	
	if(spip_num_rows($q)) {
		$aff.= '<strong>'._T('mercure:personnes_en_ligne').'</strong><br />';
		While($r=spip_fetch_array($q)) {
			if($r['statut'] == '0minirezo') { $stat=_T('mercure:abrv_administrateur'); }
			elseif ($r['statut']=='1comite') { $stat=_T('mercure:abrv_redacteur'); }
			elseif ($r['statut']=='6forum') { $stat=_T('mercure:abrv_visiteur'); }
			$aff.= bonhomme_statut(array('statut'=>$r['statut'])).' '.$stat.' : '.$r['nb'].'<br />';
		}
    # le détail des pseudos  
    $aff .= '<div align="right"><a href="?exec=mercure_pg" title="Refresh"><img src="'._DIR_IMG_MERCURE.'refresh.gif" border="0" alt="Refresh"></a></div><strong>'._T('mercure:personnes_en_detail').'</strong><br />';
    $q=spip_query("SELECT id_auteur,nom,login,maj,en_ligne,statut ".
			"FROM spip_auteurs ".
			"WHERE en_ligne > DATE_SUB( NOW(), INTERVAL 60 MINUTE) ".
			"AND id_auteur != $connect_id_auteur ".
			"GROUP BY login"
			);	
	  if(spip_num_rows($q)) {
		while($r=spip_fetch_array($q)) {
		  $aff.= '<li>'.bonhomme_statut(array('statut'=>$r['statut'])).' <a href="#" onclick="window.open(\''.generer_action_auteur("editer_message","normal/".$r['id_auteur']).'\',\'Message\',\'menubar=no, status=no, scrollbars=yes, width=1024, height=800\');" title="'.$r['nom'].' - '.$r['en_ligne'].'">'.$r['login'].'</a> <img src="'._DIR_IMG_MERCURE.'icon_away';
      // 2009-07-08 17:09:53
      $delta_temps = (time() -
            mktime(     
                  substr($r['maj'],11,2), // H
                  substr($r['maj'],14,2), // i
                  substr($r['maj'],17,2), // s
                  substr($r['maj'],5,2),  // m
                  substr($r['maj'],8,2),  // d
                  substr($r['maj'],0,4)  // Y
                  ) )/60;
      if( $delta_temps > 15){
        $aff .= '3.gif" alt="inactive > 15 mn" title="inactive '.round($delta_temps);
      }elseif( $delta_temps > 10){
        $aff .= '2.gif" alt="inactive > 10 mn" title="inactive '.round($delta_temps);
      }elseif( $delta_temps > 5){
        $aff .= '1.gif" alt="inactive > 5 mn" title="inactive '.round($delta_temps);
      }else{
        $aff .= '0.png" alt="active" title="active '.round($delta_temps);
      } 
			$aff.= ' mn" border="0" align="top"></li>';
		}
	 }
	}
	else {
		$aff.= _T("mercure:aucune_personne_en_ligne")."\n";
	}
	$aff .= fin_boite_info(true);
  return $aff; 
}

function espace_de_discussion(){
	global $connect_id_auteur;
  $auteur = array();
  $auteur = get_nick_and_name_auteur( $connect_id_auteur );
  
  $aff = '<script language="JavaScript">
		      function insertion_code(bbcode){
            document.getElementById("message").value += " "+bbcode+" ";
		      }
          function update_board(){		      
                    var xhr_board = null; 
                    if(window.XMLHttpRequest){
                      xhr_board = new XMLHttpRequest();
                    }   
                    else if(window.ActiveXObject){
                      try {
                        xhr_board = new ActiveXObject("Msxml2.XMLHTTP");
                      }
                      catch (e) {
                        xhr_board = new ActiveXObject("Microsoft.XMLHTTP");
	                    }
                    }else{ 
                      alert("Votre navigateur ne supporte pas les objets XHR-XMLHttpRequest..."); 
                      xhr_board = false; 
                    } 
                    if( xhr_board != false){
                      xhr_board.open("GET",
                                     "'._URL_AJAX_MERCURE.'index.php?action=get_board&type='.$GLOBALS['mercure']['bdd'].
                                      '&nb_a_lire='.$GLOBALS['mercure']['nb_lignes'].
                                      '&sound2play='._URL_SOUND_MERCURE.'notify_'.$GLOBALS['mercure']['notify_sound'].'.wav'.
                                      '&sound_volume='.$GLOBALS['mercure']['notify_volume'].
                                      '&item_limit='.$GLOBALS['mercure']['item_limit'].
                                      '&time_limit='.$GLOBALS['mercure']['time_limit'].
                                      '",
                                      true);
                      xhr_board.onreadystatechange = function(){
                        if ( xhr_board.readyState == 4 ){
                          document.getElementById("discussion").innerHTML = xhr_board.responseText;
                        }
                      }  
                      xhr_board.send(null);
                    }
                    setTimeout("update_board()",'.$GLOBALS['mercure']['refresh'].');
          }
          update_board();
		      </script>
          <div class="cell_info verdana2" id="discussion">
          BOARD is loading...
          </div>          
          <div class="cell_info verdana2">
          <form id="tchat_input">
            <!-- <LABEL FOR="send_button" ACCESSKEY="'._T('mercure:send_accesskey').'"> -->
            <center>
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_smile.gif" border="0" onclick="insertion_code(\':-)\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_grin.gif" border="0" onclick="insertion_code(\':-D\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_lol.gif" border="0" onclick="insertion_code(\':lol\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_razz.gif" border="0" onclick="insertion_code(\':-p\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_wink.gif" border="0" onclick="insertion_code(\';-)\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_yes.gif" border="0" onclick="insertion_code(\':-b\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_sad.gif" border="0" onclick="insertion_code(\':-(\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_unsure.gif" border="0" onclick="insertion_code(\':-s\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_confused.gif" border="0" onclick="insertion_code(\':-=\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_suprised.gif" border="0" onclick="insertion_code(\':-o\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_shocked.gif" border="0" onclick="insertion_code(\':-|\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_cool.gif" border="0" onclick="insertion_code(\':B\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_huh.gif" border="0" onclick="insertion_code(\':-h\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_mad.gif" border="0" onclick="insertion_code(\':-x\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_sick.gif" border="0" onclick="insertion_code(\':o(\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_red.gif" border="0" onclick="insertion_code(\':red\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_wub.gif" border="0" onclick="insertion_code(\':love\')">
            <img src="'._DIR_IMG_MERCURE.'smiley/sm_kiss.gif" border="0" onclick="insertion_code(\':kiss\')">
            </center>
            <table border="0">
              <tr>
                <td align="center" valign="center">
                  <textarea id="message" name="message" cols="48" rows="3"></textarea>
                </td>
                <td align="center" valign="center">
                  <input type="button" id="send_button" onclick="
                    var xhr_message_send = null; 
                    if(window.XMLHttpRequest){
                      xhr_message_send = new XMLHttpRequest();
                    }   
                    else if(window.ActiveXObject){
                      try {
                        xhr_message_send = new ActiveXObject(\'Msxml2.XMLHTTP\');
                      }
                      catch (e) {
                        xhr_message_send = new ActiveXObject(\'Microsoft.XMLHTTP\');
	                    }
                    }else{ 
                      alert(\'Votre navigateur ne supporte pas les objets XHR-XMLHttpRequest...\'); 
                      xhr_message_send = false; 
                    } 
                    if( xhr_message_send != false){
                      date = new Date();
                      realPHPtime = Math.round( date.getTime() / 1000 );
                      mess = document.getElementById(\'message\').value;
                      
                      // MyExp = new RegExp(\'É\',\'gi\'); mess = mess.replace(MyExp,\'&Eacute;\'); 
                      // MyExp = new RegExp(\'é\',\'gi\'); mess = mess.replace(MyExp,\'&eacute;\'); 
                      // MyExp = new RegExp(\'È\',\'gi\'); mess = mess.replace(MyExp,\'&Egrave;\'); 
                      // MyExp = new RegExp(\'è\',\'gi\'); mess = mess.replace(MyExp,\'&egrave;\'); 
                      // MyExp = new RegExp(\'Ë\',\'gi\'); mess = mess.replace(MyExp,\'&Euml;\'); 
                      // MyExp = new RegExp(\'ë\',\'gi\'); mess = mess.replace(MyExp,\'&euml;\'); 
                      // MyExp = new RegExp(\'Ê\',\'gi\'); mess = mess.replace(MyExp,\'&Ecirc;\'); 
                      // MyExp = new RegExp(\'ê\',\'gi\'); mess = mess.replace(MyExp,\'&ecirc;\'); 
                      // MyExp = new RegExp(\'À\',\'gi\'); mess = mess.replace(MyExp,\'&Agrave;\'); 
                      // MyExp = new RegExp(\'à\',\'gi\'); mess = mess.replace(MyExp,\'&agrave;\'); 
                      // MyExp = new RegExp(\'Â\',\'gi\'); mess = mess.replace(MyExp,\'&Acirc;\'); 
                      // MyExp = new RegExp(\'â\',\'gi\'); mess = mess.replace(MyExp,\'&acirc;\'); 
                      // MyExp = new RegExp(\'Á\',\'gi\'); mess = mess.replace(MyExp,\'&Aacute;\'); 
                      // MyExp = new RegExp(\'ä\',\'gi\'); mess = mess.replace(MyExp,\'&auml;\'); 
                      // MyExp = new RegExp(\'Ä\',\'gi\'); mess = mess.replace(MyExp,\'&Auml;\'); 
                      // MyExp = new RegExp(\'Î\',\'gi\'); mess = mess.replace(MyExp,\'&Icirc;\'); 
                      // MyExp = new RegExp(\'î\',\'gi\'); mess = mess.replace(MyExp,\'&icirc;\'); 
                      // MyExp = new RegExp(\'Ù\',\'gi\'); mess = mess.replace(MyExp,\'&Ugrave;\'); 
                      // MyExp = new RegExp(\'ù\',\'gi\'); mess = mess.replace(MyExp,\'&ugrave;\'); 
                      // MyExp = new RegExp(\'Û\',\'gi\'); mess = mess.replace(MyExp,\'&Ucirc;\'); 
                      // MyExp = new RegExp(\'û\',\'gi\'); mess = mess.replace(MyExp,\'&ucirc;\'); 
                      // MyExp = new RegExp(\'Ô\',\'gi\'); mess = mess.replace(MyExp,\'&Ocirc;\'); 
                      // MyExp = new RegExp(\'ô\',\'gi\'); mess = mess.replace(MyExp,\'&ocirc;\'); 
                      // MyExp = new RegExp(\'ö\',\'gi\'); mess = mess.replace(MyExp,\'&ouml;\'); 
                      // MyExp = new RegExp(\'Ö\',\'gi\'); mess = mess.replace(MyExp,\'&Ouml;\'); 
                      // MyExp = new RegExp(\'Ç\',\'gi\'); mess = mess.replace(MyExp,\'&Ccedil;\'); 
                      // MyExp = new RegExp(\'ç\',\'gi\'); mess = mess.replace(MyExp,\'&ccedil;\'); 
                      // MyExp = new RegExp(\'Ÿ\',\'gi\'); mess = mess.replace(MyExp,\'&Yuml;\'); 
                      // MyExp = new RegExp(\'ÿ\',\'gi\'); mess = mess.replace(MyExp,\'&yuml;\'); 
                      // MyExp = new RegExp(\'Š\',\'gi\'); mess = mess.replace(MyExp,\'&Scaron;\'); 
                      // MyExp = new RegExp(\'š\',\'gi\'); mess = mess.replace(MyExp,\'&scaron;\'); 
                      // MyExp = new RegExp(\'Œ\',\'gi\'); mess = mess.replace(MyExp,\'&OElig;\'); 
                      // MyExp = new RegExp(\'œ\',\'gi\'); mess = mess.replace(MyExp,\'&oelig;\'); 
                      // MyExp = new RegExp(\'Ž\',\'gi\'); mess = mess.replace(MyExp,\'%8E\'); 
                      // MyExp = new RegExp(\'ž\',\'gi\'); mess = mess.replace(MyExp,\'%9E\'); 
                      // MyExp = new RegExp(\'ß\',\'gi\'); mess = mess.replace(MyExp,\'&szlig;\'); 
                      // MyExp = new RegExp(\'ñ\',\'gi\'); mess = mess.replace(MyExp,\'%F1\'); 
                      // MyExp = new RegExp(\'Ñ\',\'gi\'); mess = mess.replace(MyExp,\'%D1\'); 
                      // MyExp = new RegExp(\'~\',\'gi\'); mess = mess.replace(MyExp,\'&tilde;\'); 
                      // MyExp = new RegExp(\'€\',\'gi\'); mess = mess.replace(MyExp,\'&euro;\'); 
                      // MyExp = new RegExp(\'<\',\'gi\'); mess = mess.replace(MyExp,\'&lt;\'); 
                      // MyExp = new RegExp(\'>\',\'gi\'); mess = mess.replace(MyExp,\'&gt;\'); 

                      // MyExp = new RegExp(\'\+\',\'gi\'); mess = mess.replace(MyExp,\'|[{\');
                      MyExp = new RegExp(\'\n\',\'gi\'); mess = mess.replace(MyExp,\'[|{\');                    
                      MyExp = new RegExp(\'%\',\'gi\'); mess = mess.replace(MyExp,\'[{|\');
                      MyExp = new RegExp(\'&\',\'gi\'); mess = mess.replace(MyExp,\'{|[\');
                      MyExp = new RegExp(\'#\',\'gi\'); mess = mess.replace(MyExp,\'{[|\');
                                           
                      xhr_message_send.open(\'GET\',
                                     \''._URL_AJAX_MERCURE.'index.php?action=send_message&type='.$GLOBALS['mercure']['bdd'].
                                        '&nom='.$auteur['pseudo'].
                                        '&time=\'+realPHPtime+\'&message=\'+encodeURIComponent(mess),
                                      true);
                      xhr_message_send.onreadystatechange = function(){
                        if ( xhr_message_send.readyState == 4 ){
                          document.getElementById(\'message\').value = \'\';
                          document.getElementById(\'message\').focus();                          
                        }
                      }
                      xhr_message_send.send(null);
                    }
                  " value="'._T('mercure:bouton_envoyer').'" accesskey="'._T('mercure:send_accesskey').'"><br>
                </td>
              </tr>
            </table>                      
          </form>
          </div>
          ';

  return $aff;
}
?>
