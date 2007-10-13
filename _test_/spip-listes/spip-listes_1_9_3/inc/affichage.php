<?php

/******************************************************************************************/
/* SPIP-listes est un système de gestion de listes d'information par email pour SPIP      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net , http://bloog.net            */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Générale GNU publiée par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Générale GNU                    */
/* en même temps que ce programme ; si ce n'est pas le cas, écrivez à la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.                   */
/******************************************************************************************/
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

include_spip('inc/spiplistes_api');


function spiplistes_onglets ($rubrique, $onglet, $return = false) {

	$result = "";
	
	if ($rubrique == _SPIPLISTES_RUBRIQUE){
		$result = ""
			. "<br />"
			. debut_onglet()
			. onglet(_T('spiplistes:Casier_a_courriers'), generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."stock_hyperlink-mail-and-news-24.gif")
			. onglet(_T('spiplistes:Listes_de_diffusion'), generer_url_ecrire(_SPIPLISTES_EXEC_LISTES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif")
			. onglet(_T('spiplistes:Suivi_des_abonnements'), generer_url_ecrire(_SPIPLISTES_EXEC_ABONNES_LISTE), $rubrique
				, $onglet, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."addressbook-24.gif")
			. fin_onglet()
		;
	}

	if($return) return($result);
	else echo($result);
}


function spiplistes_boite_autocron ($return = false) { 
	@define('_SPIP_LISTE_SEND_THREADS',1);
	include_spip('genie/spiplistes_cron');
	if (cron_spiplistes_cron($time)) return; // rien a faire
	
	// initialise les options
	foreach(array('opt_simuler_envoi') as $key) {
		$$key = __plugin_lire_s_meta($key, 'spiplistes_preferences');
	}

/*
	$res = spip_query("SELECT COUNT(a.id_auteur) AS n 
		FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='"._SPIPLISTES_STATUT_ENCOURS."'");
	$n = 0;
*/
	$res = spip_query("SELECT SUM(c.total_abonnes) AS n 
		FROM spip_auteurs_courriers AS a JOIN spip_courriers AS c ON c.id_courrier=a.id_courrier WHERE c.statut='"._SPIPLISTES_STATUT_ENCOURS."'");
	if ($row = spip_fetch_array($res))
		$n = intval($row['n']);
spiplistes_log("AUTOCRON nb courries prets envoi $n", LOG_DEBUG);

	if($n > 0) {
		$result = ""
			. "<br />"
			. debut_boite_info(true)
			. "<div style='font-weight:bold;text-align:center'>"._T('spiplistes:envoi_en_cours')."</div>"
			. "<div style='padding : 10px;text-align:center'><img src='"._DIR_PLUGIN_SPIPLISTES."img_pack/48_import.gif'></div>"
			. "<div id='meleuse'>"
			.	(
					($total = spiplistes_nb_courriers_en_cours())
					?	""
						. "<p align='center' id='envoi_statut'>"._T('spiplistes:envoi_en_cours')." "
						. "<strong id='envois_restants'>$n</strong>/<span id='envois_total'>$total</span> (<span id='envois_restant_pourcent'>"
						. round($n/$total*100)."</span>%)</p>"
					:	""
				)
			// message si simulation d'envoi	
			.	(
					($opt_simuler_envoi == 'oui') 
					? "<div style='color:white;background-color:red;text-align:center;line-height:1.4em;'>"._T('spiplistes:Mode_simulation')."</div>\n" 
				: ""
				)
			;
		
		$href = generer_action_auteur('spiplistes_envoi_lot','envoyer');

		for ($i=0;$i<_SPIP_LISTE_SEND_THREADS;$i++) {
			$result .= "<span id='proc$i' class='processus' name='$href'></span>";
		}
		if (_request('exec')==_SPIPLISTES_EXEC_COURRIERS_LISTE) {
			$result .= "<a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIERS_LISTE)."' id='redirect_after'></a>";
		}
		$result .= ""
			. "</div>"
			. "<script><!--
		var target = $('#envois_restants');
		var total = $('#envois_total').html();
		var target_pc = $('#envois_restant_pourcent');
		function redirect_fin(){
			redirect = $('#redirect_after');
			if (redirect.length>0){
				href = redirect.attr('href');
				setTimeout('document.location.href = \"'+href+'\"',0);
			}
		}
		jQuery.fn.runProcessus = function(url) {
			var proc=this;
			var href=url;
			$(target).load(url,function(data){
				restant = $(target).html();
				pourcent=Math.round(restant/total*100);
				$(target_pc).html(pourcent);
				if (Math.round(restant)>0)
					$(proc).runProcessus(href);
				else
					redirect_fin();
			});
		}
		$('span.processus').each(function(){
			var href = $(this).attr('name');
			$(this).html(ajax_image_searching).runProcessus(href);
			//run_processus($(this).attr('id'));
		});
		//--></script>"
			. "<p>"._T('spiplistes:texte_boite_en_cours')."</p>" 
			. "<p align='center'><a href='".generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_GERER,'change_statut=publie&id_courrier='.$id_mess)."'>["._T('annuler')."]</a></p>"
			. fin_boite_info(true)
			;
	}

	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_debut_raccourcis ($titre = "", $raccourcis = true, $return = false) {
  
  $result = ""
		. ($raccourcis ? creer_colonne_droite('', true) : "")
		. debut_cadre_enfonce('', true)
		. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>$titre</span>"
		. "<br />"
		;
	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_fin_raccourcis ($return = false) {
	$result = ""
		. fin_cadre_enfonce(true)
		;
	if($return) return($result);
	else echo($result);
}

// From SPIP-Listes-V: CP:20070923
function spiplistes_boite_raccourcis ($return = false) {
	global $connect_id_auteur;
	
	$result = ""
		// Les raccourcis
		. spiplistes_debut_raccourcis(_T('titre_cadre_raccourcis'), true)
		. "<ul class='verdana2' style='list-style: none;padding:1ex;margin:0;'>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouveau_courrier')
			, generer_url_ecrire(_SPIPLISTES_EXEC_COURRIER_EDIT,'new=oui&type=nl')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."courriers_brouillon-24.png"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:Nouvelle_liste_de_diffusion')
			, generer_url_ecrire(_SPIPLISTES_EXEC_LISTE_EDIT,'new=oui')
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."reply-to-all-24.gif"
			,"creer.gif"
			,false
			)
		. "</li>\n"
		. "<li>"
		. icone_horizontale(
			_T('spiplistes:import_export')
			, generer_url_ecrire(_SPIPLISTES_EXEC_IMPORT_EXPORT)
			, _DIR_PLUGIN_SPIPLISTES_IMG_PACK."listes_inout.png"
			,""
			,false
			)
		. "</li>\n"
		;
	if($connect_id_auteur == 1) {
		$result .= ""
			. "<li>"
			. icone_horizontale(
				_T('titre_admin_tech')
				, generer_url_ecrire(_SPIPLISTES_EXEC_MAINTENANCE)
				, "administration-24.gif"
				,""
				,false
				)
			. "</li>\n"
			;
	}
	$result .= ""
		. "</ul>\n"
		. spiplistes_fin_raccourcis(true)
		;
	
	if($return) return($result);
	else echo($result);
}

function spiplistes_boite_info_spiplistes($return=false) {
	$result = ""
		// colonne gauche boite info
		. "<br />"
		. debut_boite_info(true)
		. _T('spiplistes:_aide')
		. fin_boite_info(true)
		;
	if($return) return($result);
	else echo($result);
}

// adapté de abomailman ()
// MaZiaR - NetAktiv
// tech@netaktiv.com
 

// Afficher l'arbo
function  spiplistes_arbo_rubriques($id_rubrique,  $rslt_id_rubrique="") {
	global $ran;
	$ran ++;
	
	$marge="&nbsp;&nbsp;&nbsp;|";
	for ($g=0;$g<$ran;$g++) {
		if (($ran-1)==0) {
			$marge="&bull;";
		}
		else {
			$marge .="-"; 
		}
	}
	$marge .="&nbsp;";

	$rqt_rubriques = spip_query ("SELECT id_rubrique, id_parent, titre FROM spip_rubriques WHERE id_parent='".$id_rubrique."'");
	while ($row = spip_fetch_array($rqt_rubriques)) {
		$id_rubrique = $row['id_rubrique'];
		$id_parent = $row['id_parent'];
		$titre = $row['titre'];
		$arbo .="<option value='".$id_rubrique."'>" . $marge  . supprimer_numero (typo($titre)) . "</option>";
		$arbo .= spiplistes_arbo_rubriques($id_rubrique,   $rslt_id_parent);
	}
	
	return $arbo;
	
}




/******************************************************************************************/
/* SPIP-Listes est un systeme de gestion de listes d'abonnes et d'envoi d'information     */
/* par email pour SPIP. http://bloog.net/spip-listes                                      */
/* Copyright (C) 2004 Vincent CARON  v.caron<at>laposte.net                               */
/*                                                                                        */
/* Ce programme est libre, vous pouvez le redistribuer et/ou le modifier selon les termes */
/* de la Licence Publique Generale GNU publiee par la Free Software Foundation            */
/* (version 2).                                                                           */
/*                                                                                        */
/* Ce programme est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,       */
/* ni explicite ni implicite, y compris les garanties de commercialisation ou             */
/* d'adaptation dans un but specifique. Reportez-vous à la Licence Publique Generale GNU  */
/* pour plus de détails.                                                                  */
/*                                                                                        */
/* Vous devez avoir reçu une copie de la Licence Publique Generale GNU                    */
/* en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la                  */
/* Free Software Foundation,                                                              */
/* Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.                   */
/******************************************************************************************/
?>
