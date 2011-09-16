<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt
#
# Sauvegarder / restaurer les variables ACS
# Save / restore ACS variables

include_spip('inc/actions');
include_spip('inc/acs_presentation');

function inc_acs_sr() {
	global $spip_lang_right;
	
	$repertoire = _DIR_DUMP.'acs/';
	
	// Sauvegarder
	$nom_fichier = lire_meta('acsSet').date("ymdHi", lire_meta("acsDerniereModif"));
	$file = $repertoire.$nom_fichier;
	$res = '<span class="onlinehelp">
    <b><u>'._T('acs:save').' :</u></b>'.acs_help_call('acs_save_bloc').
    '<br />'.
	  acs_help_div('acs_save_bloc', _T('ecrire:texte_admin_tech_01', array('dossier'=>'<i>'.ltrim($repertoire, '.').'</i>', 'img' => 'IMG/_acs'))).'
    </span>
    <br />
    <label for="acs_save_vars">'._T('bouton_radio_sauvegarde_non_compressee', array('fichier' => '')).'</label>
    <table>
      <tr>
        <td>'.ltrim($repertoire, '.').'</td>
        <td><input name="nom_sauvegarde" id="nom_sauvegarde" size="128" value="'.$nom_fichier.'" class="forml" /></td>
        <td>&nbsp;<b>.</b>php</td>
      </tr>
    </table>
    <input type="hidden" name="save" value="go!" />';
	$save = ajax_action_post('acs_sr', 0, 'acs', 'onglet=adm', $res, _T('acs:save'), 'class="fondo visible" id="valider_acs_save"', ' style="float: '.$spip_lang_right.';"');
	
	// Restaurer. L'affichage de ce bloc requiert le droit SPIP "effacer la base de donnée" (si le plugin Autorité est installé)
	$liste_dump = preg_files($repertoire,'\.php?$',50,false);
	$selected = end($liste_dump);
	$n = strlen($repertoire);
	$tl = $tt = $td = array();
	$f = "";
	$i = 0;
	foreach($liste_dump as $fichier){
		$i++;
		$d = filemtime($fichier);
		$t = filesize($fichier);
		$s = ($fichier==$selected);
		$class = 'row_'.alterner($i, 'even', 'odd');
		$fichier = substr($fichier, $n);
		$tl[]= acs_liste_sauvegardes($i, $fichier, $class, $s, $d, $t);
		$td[] = $d;
		$tt[] = $t;
	}
	if ($tl) {
  	$head = '<tr><th></th><th><a >'._T('info_nom').'</a></th><th><a >'._T('taille_octets', array('taille' => '')).
  		'</th><th><a >'._T('public:date').'</a></th></tr>';
  	$res = '<br style="clear: both;" /><br /><hr /><br /><span class="onlinehelp">
  	  <b><u>'._T('acs:restore').' :</u></b>.'.acs_help_call('acs_restore_bloc').'
  	  <br />'.
  	  acs_help_div('acs_restore_bloc', _T('ecrire:texte_restaurer_sauvegarde', array('dossier'=>'<i>'.ltrim($repertoire, '.').'</i>'))).
      '</span><br /><table class="spip">'.$head.join ('', $tl).'</table><input type="hidden" name="restore" value="go!" />';
  
  	$restore = ajax_action_post('acs_sr', 0, 'acs', 'onglet=adm', $res, _T('acs:restore'), 'class="fondo visible" id="valider_acs_restore"', ' style="float: '.$spip_lang_right.';"');
	}
	return $save.$restore;
}
function acs_liste_sauvegardes($key, $fichier, $class, $selected, $date, $taille) {
	$fichier = substr($fichier,0,-4);
	return "\n<tr class='$class'><td><input type='radio' name='archive' value='"
		. $fichier
		. "' id='dump_$key' "
		. ($selected?"checked='checked' ":"")
		. "/></td><td>\n<label for='dump_$key'>"
		. str_replace('/', ' / ', $fichier)
		. "</label></td><td style='text-align: right'>"
		. taille_en_octets($taille)
		. '</td><td>'
		. affdate_heure(date('Y-m-d H:i:s',$date))
		. '</td></tr>';
}

?>