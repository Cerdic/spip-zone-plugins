<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

include_spip('inc/presentation');

function exec_clevermail_lists_subscribers() {

	if (isset($_GET['id'])) {
	    $listId = $_GET['id'];
	} elseif (isset($_POST['id'])) {
	    $listId = $_POST['id'];
	}

	if (isset($_POST['remove']) && strlen($_POST['sub_ids']) > 0) {
        foreach(explode(';', $_POST['sub_ids']) as $subId) {
			if ($subId != '') {
				spip_query("DELETE FROM cm_lists_subscribers WHERE lst_id = ".$listId." AND sub_id = ".$subId);
			}
        }
    } else if(isset($_GET['remove']) && $_GET['sub_id'] > 0) {
    	spip_query("DELETE FROM cm_lists_subscribers WHERE lst_id = ".$listId." AND sub_id = ".$_GET['sub_id']);
    }

	$result = spip_query("SELECT s.sub_id, s.sub_email, l.lsr_mode
            FROM cm_subscribers s, cm_lists_subscribers l
            WHERE s.sub_id = l.sub_id AND l.lst_id = ".$listId."
            ORDER BY s.sub_email");
	$nombre_auteurs = spip_num_rows($result);

	$max_par_page = 30;
	$debut = intval(_request('debut'));
	if ($debut > $nombre_auteurs - $max_par_page)
		$debut = max(0,$nombre_auteurs - $max_par_page);

	list($auteurs, $lettre)= lettres_d_abonnes($result, $debut, $max_par_page);
	$res = abonnes_tranches(afficher_n_abonnes($auteurs), $debut, $lettre, $max_par_page, $nombre_auteurs, 1);

	if (_request('var_ajaxcharset')) ajax_retour($res);


	debut_page("CleverMail Administration", 'configuration', 'cm_index');

		echo debut_gauche('', true);
			include_spip("inc/clevermail_menu");
			echo '<br />';
			include_spip("inc/clevermail_search");
		echo debut_droite('', true);

		debut_cadre_relief();
			echo gros_titre('CleverMail Administration', '', '');
		fin_cadre_relief();

		echo '<form name="subscribers" action="'.generer_url_ecrire('clevermail_lists_subscribers','').'" method="post">'."\n";
		echo '<input type="hidden" name="id" value="'.$listId.'" />'."\n";
		echo '<input type="hidden" name="sub_ids" value="" />'."\n";

		debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png');
			$result = spip_fetch_array(spip_query("SELECT lst_name FROM cm_lists WHERE lst_id = ".$listId));
			echo '<h3>'._T('clevermail:liste_abonnes').' : '.$result['lst_name'].'</h3>';
			echo '<div id="abonnes">'.$res. '</div>'."\n";
		fin_cadre_relief();

		echo '<input type="hidden" name="remove" value="1" />'."\n";
		echo icone_horizontale(_T('clevermail:desabonner_abonnes'), 'javascript:if(confirm("'._T('clevermail:confirme_desabonnement_multiple_lettre').'")){checkbox2input(document.subscribers,"sub_id",document.subscribers.sub_ids);document.subscribers.submit();}', '../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonnes.png', 'supprimer.gif', '', true);
		echo '</form>'."\n";

	fin_page();
}

function lettres_d_abonnes($query, $debut, $max_par_page)
{
	$auteurs = $lettre = array();
	$lettres_nombre_auteurs =0;
	$lettre_prec ="";
	$i = 0;
	while ($auteur = spip_fetch_array($query)) {
		if ($i>=$debut AND $i<$debut+$max_par_page) {
			if ($auteur['statut'] == '0minirezo')
				$auteur['restreint'] = spip_num_rows(spip_query("SELECT id_auteur FROM spip_auteurs_rubriques WHERE id_auteur=".$auteur['id_auteur']));
			$auteurs[] = $auteur;
		}
		$i++;

		$premiere_lettre = strtoupper(spip_substr(extraire_multi($auteur['sub_email']),0,1));
		if ($premiere_lettre != $lettre_prec) {
			$lettre[$premiere_lettre] = $lettres_nombre_auteurs;
		}
		$lettres_nombre_auteurs ++;
		$lettre_prec = $premiere_lettre;
	}

	return array($auteurs, $lettre);
}

function abonnes_tranches($auteurs, $debut, $lettre, $max_par_page, $nombre_auteurs)
{
	global $options, $spip_lang_right;

	$res .= '<tr style="background-color:#DBE1C5">'."\n";
	$res .= '<th><input type="checkbox" onclick="toggle(document.subscribers, \'sub_id\')" /></th>'."\n";
	$res .= '<th>'._T('clevermail:emails').'</th>'."\n";
	$res .= '<th>'._T('clevermail:actions').'</th>'."\n";
	$res .= '</tr>'."\n";

	if ($nombre_auteurs > $max_par_page) {

		for ($j=0; $j < $nombre_auteurs; $j+=$max_par_page) {
			if ($j > 0) 	$pagination .= " | ";

			if ($j == $debut)
				$pagination .= "<b>$j</b>";
			else if ($j > 0)
				$pagination .= abonnes_href($j, "id=".(isset($_GET['id']) ? $_GET['id']: $_POST['id'])."&debut=".$j);
			else
				$pagination .= abonnes_href('0', "");
			if ($debut > $j  AND $debut < $j+$max_par_page){
				$pagination .= " | <b>$debut</b>";
			}
		}

		$res .= "\n<tr style='background-color: #EEE'>";
		$res .=	"<td class='verdana1' colspan='3' style='text-align: center; border-top: 1px solid #CCC;'>";
		foreach ($lettre as $key => $val) {
			if ($val == $debut)
				$res .= "<b>$key</b>\n";
			else
				$res .= abonnes_href($key, "id=".(isset($_GET['id']) ? $_GET['id']: $_POST['id'])."&debut=".$val) . "\n";
		}
		$res .= "</td></tr>\n";
	}

	$nav = '';
	$debut_suivant = $debut + $max_par_page;
	if ($debut_suivant < $nombre_auteurs OR $debut > 0) {
		$nav = "\n<table id='bas' width='100%' border='0'>"
		. "\n<tr bgcolor='white'><td align='left'>";

		if ($debut > 0) {
			$debut_prec = max($debut - $max_par_page, 0);
			$nav .= abonnes_href('&lt;&lt;&lt;',"id=".(isset($_GET['id']) ? $_GET['id']: $_POST['id'])."&debut=".$debut_prec);
		}
		$nav .= "</td><td style='text-align: $spip_lang_right'>";
		if ($debut_suivant < $nombre_auteurs) {
			$nav .= abonnes_href('&gt;&gt;&gt;',"id=".(isset($_GET['id']) ? $_GET['id']: $_POST['id'])."&debut=".$debut_suivant);
		}
		$nav .= "</td></tr></table>\n";
	}

	$res = $nav
	. "\n<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n"
	. $res
	. $auteurs
	. "</table>\n"
	. "<br /><div class='arial1'>"
	. $pagination
	. "</div>";

	return $res;
}

function abonnes_href($clic, $args='', $att='')
{
	$h = generer_url_ecrire('clevermail_lists_subscribers', $args);
	$a = 'abonnes';
	if ($_COOKIE['spip_accepte_ajax'] == 1 )
		$att .= ("\nonclick=" . ajax_action_declencheur($h,$a));

	return "<a href='$h#$a'$att>$clic</a>";
}

function abonnes_href_suppression($clic, $args='', $att='')
{
	$h = generer_url_ecrire("clevermail_lists_subscribers",'remove=1&id='.(isset($_GET['id']) ? $_GET['id']: $_POST['id']).'&debut='.(isset($_GET['debut']) ? $_GET['debut']: $_POST['debut']).'&'.$args);
	$a = 'abonnes';
	if ($_COOKIE['spip_accepte_ajax'] == 1 )
		$att .= ("\nonclick=" . ajax_action_declencheur($h,$a));

	return "<a href='$h#$a'$att>$clic</a>";
}

function afficher_n_abonnes($auteurs) {
	$res = '';
	$nbrow = 0;
	foreach ($auteurs as $row) {
		$res .= '<tr style="background-color: '.($nbrow++%2 ? '#EEE' : '#FFF').';">'."\n";
		$res .= '<td class="arial1" style="border-top: 1px solid #CCC;">'."\n";
		$res .= '<input type="checkbox" name="sub_id" value="'.$row['sub_id'].'" />'."\n";
		$res .= '</td>'."\n";
		$res .= '<td class="verdana1" style="border-top: 1px solid #CCC;">'."\n";
		$res .= $row['sub_email'];
		$res .= '</td>'."\n";
		$res .= '<td class="arial1" style="border-top: 1px solid #CCC;">'."\n";
		$res .= '<a href="'.generer_url_ecrire("clevermail_subscribers_detail","sub_id=".$row['sub_id']).'">'._T('clevermail:modifier').'</a>';
		$res .= ' | '.abonnes_href_suppression(_T('clevermail:desabonner2'), 'sub_id='.$row['sub_id']);
		$res .= '</td>'."\n";
		$res .='</tr>'."\n";
	}
	return $res;
}
?>
