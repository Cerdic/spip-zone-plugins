<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/actions');
include_spip('inc/mots');
//include_spip('public/assembler'); //pour recuperer_fond

// http://doc.spip.org/@inc_editer_mots_dist
function inc_editer_mots($objet, $id_objet, $cherche_mot, $select_groupe, $flag, $visible = false) {

	if ($GLOBALS['meta']["articles_mots"] == 'non')	return '';

	$visible = ($visible OR $cherche_mot OR ($flag === 'ajax'));

	///////////////////////////
	// MODIFICATION
	///////////////////////////


	include_spip("mots_partout_choses");
	global $choses_possibles;

    if ($objet == 'syndic') $table=$objet;
	    else $table=$objet.'s';

	//bloc de modification du core : on utilise chose possibles et pas des if
	if (isset($choses_possibles[$table])) {
		if ($choses_possibles[$table]['id_chose'])
			$table_id = $choses_possibles[$table]['id_chose'];
		else $table_id = 'id_'.$objet;
		if ($choses_possibles[$table]['objet']) $objet=$choses_possibles[$table]['objet'];
		else $objet = substr($table,0,-1);
		if ($choses_possibles[$table]['url_base']) $url_base=$choses_possibles[$table]['url_base'];
		else $url_base = $table;
		if (is_array($id_objet)){
			foreach($id_objet as $key => $value)
		    	if ($key=='id_objet') $_id_objet=$id_objet['id_objet'];
	            else $url_base.=$value;
		        $id_objet=$_id_objet;
		}
	}

	//Ceci n'est plus vraiment utile dans le cas de la squeletisation
	$cpt = sql_countsel("spip_mots AS mots, spip_mots_$table AS lien", "lien.$table_id=$id_objet AND mots.id_mot=lien.id_mot");

	if (!$cpt) {
		if (!$flag) return;
		$cpt = sql_fetch(editer_mots_droits("COUNT(*) AS n", "$table = 'oui'"));

		if (!$cpt['n']) return;
	}
	//////////////////////

	//preparation de l'utilisation de recuperer_fond a ce niveau
	//definition du contexte

	//$squel=recup_squelette_motspartout("listemot_".$table);
	//$contexte_liste=array("table"=>$table,"id_objet"=>$id_objet,"visible"=>$visible,"simplifie"=>$simplifie);
	//$res=recuperer_fond($squel,$contexte_liste);


	//
	// Preparer l'affichage
	//

	// La reponse
	$reponse = '';
	$modifier = false;
	if ($flag AND $cherche_mot) {
		list($reponse, $nouveaux_mots) = recherche_mot_cle($cherche_mot, $select_groupe, $objet, $id_objet, $table, $table_id, $url_base);
		foreach($nouveaux_mots as $nouv_mot) {
			if ($nouv_mot!='x') {
				$modifier |= inserer_mot("spip_mots_$table", $table_id, $id_objet, $nouv_mot);
			}
		}
		if ($modifier) {
			pipeline('post_edition',
				array(
					'args' => array(
					'table' => 'spip_'.$table,
					'id_objet' => $id_objet
					),
				'data' => null
				)
			);
		}
	}

	$form = afficher_mots_cles($flag, $objet, $id_objet, $table, $table_id, $url_base, $visible);
	// Envoyer titre + div-id + formulaire + fin
	$bouton = _T('titre_mots_cles').aide ("artmots");
	if ($flag)
		$bouton = bouton_block_depliable($bouton,'incertain',"lesmots-".$table.$id_objet); // on n'est pas sur de l'etat du bloc, le bouton se mettra dans le bon etat au chargement
	$res = debut_cadre_enfonce("mot-cle-24.gif", true, "", $bouton)
	  . $reponse
	  . $form
	  . fin_cadre_enfonce(true);

	return ajax_action_greffe("editer_mots", $id_objet, $res);
}


function chercher_inserer_mot($cherche_mot, $select_groupe, $objet, $id_objet, $nom, $table_id, $url_base)
{
	$modifier = false;

	list($reponse, $nouveaux_mots) = recherche_mot_cle($cherche_mot, $select_groupe, $objet, $id_objet, $nom, $table_id, $url_base);
	foreach($nouveaux_mots as $nouv_mot) {
		if ($nouv_mot!='x') {
			$modifier |= inserer_mot("spip_mots_$nom", $table_id, $id_objet, $nouv_mot);
		}
	}
	if ($modifier) {
		pipeline('post_edition',
				array(
					'args' => array(
					'table' => $table,
					'id_objet' => $id_objet
					),
				'data' => null
				)
			);
	}
	return $reponse;
}


// http://doc.spip.org/@inserer_mot
function inserer_mot($table, $table_id, $id_objet, $id_mot)
{
	$r = sql_countsel($table, "id_mot=$id_mot AND $table_id=$id_objet");

	if (!$r) sql_insertq($table, array('id_mot' =>$id_mot,  $table_id => $id_objet));

	return $r;
}

// http://doc.spip.org/@recherche_mot_cle
function recherche_mot_cle($cherche_mots, $id_groupe, $objet, $id_objet, $table, $table_id, $url_base)
{
	/////////////////////
	//MODIFICATION
	/////////////////////
	/*if ($table == 'articles') $ou = _T('info_l_article');
	else*/ if ($table == 'breves') $ou = _T('info_la_breve');
	else if ($table == 'rubriques') $ou = _T('info_la_rubrique');
	else $ou = _T('info_l_'.$objet);
	/////////////////////

	$result = sql_select("id_mot, titre", "spip_mots", "id_groupe=" . sql_quote($id_groupe));

	$table_mots = array();
	$table_ids = array();
	while ($row = sql_fetch($result)) {
			$table_ids[] = $row['id_mot'];
			$table_mots[] = $row['titre'];
	}

	$nouveaux_mots = array();
	$res = '';

	foreach (split(" *[,;] *", $cherche_mots) as $cherche_mot) {
	  if  ($cherche_mot) {
		$resultat = mots_ressemblants($cherche_mot, $table_mots, $table_ids);
		$res .= "<br />" . debut_boite_info(true);
		if (!$resultat) {
			$res .= "<b>"._T('info_non_resultat', array('cherche_mot' => htmlspecialchars($cherche_mot)))."</b><br />";
		}
		else if (count($resultat) == 1) {
			$n = $resultat[0];
			$nouveaux_mots[] = $n;
			$t = sql_getfetsel("titre", "spip_mots", "id_mot=$n");
			$res .= "<b>"._T('info_mot_cle_ajoute')." $ou : </b><br />\n<ul>";
			$res .= "\n<li><span class='verdana1 spip_small'><b><span class='spip_medium'>".typo($t)."</span></b></span></li>";
			$res .= "\n</ul>";
		}
		else $res .= affiche_mots_ressemblant($cherche_mot, $objet, $id_objet, $resultat, $table, $table_id, $url_base);

/*		if (autoriser('modifier','groupemots',$id_groupe)) {
			$titre = rawurlencode($cherche_mot);
			$res .= "<div style='width: 200px;'>";
			$res .= icone_horizontale(_T('icone_creer_mot_cle'), generer_url_ecrire("mots_edit","new=oui&id_groupe=$id_groupe&ajouter_id_article=$id_objet&table=$table&table_id=$table_id&titre=$titre&redirect=" . generer_url_retour($url_base, "$table_id=$id_objet")), "mot-cle-24.gif", "creer.gif", false);
			$res .= "</div> ";
		}
*/

		$res .= fin_boite_info(true) . "<br />";
	  }
	}
	return array($res, $nouveaux_mots);
}

// http://doc.spip.org/@afficher_mots_cles
function afficher_mots_cles($flag_editable, $objet, $id_objet, $table, $table_id, $url_base, $visible)
{

	$requete = array('SELECT' => "mots.id_mot, mots.titre , mots.id_groupe", 'FROM' => "spip_mots AS mots, spip_mots_$table AS lien", 'WHERE' => "lien.$table_id=$id_objet AND mots.id_mot=lien.id_mot", 'GROUP BY' => "mots.type, mots.titre",  'ORDER BY' => "mots.type, mots.titre");

	$cle = http_img_pack('petite-cle.gif', "", "width='23' height='12'");
	$ret = generer_url_retour($url_base, "$table_id=$id_objet#mots");
	$largeurs = array('25', '', '', '');
	$styles = array('arial11', 'arial2', 'arial2', 'arial1');

	$presenter_liste = charger_fonction('presenter_liste', 'inc');

	// cette variable est passe par reference et recevra les valeurs du champ indique
	$les_mots = 'id_mot';
	$res = 	$presenter_liste($requete, 'editer_mots_un', $les_mots, array($cle, $flag_editable, $id_objet, $objet, $ret, $table, $table_id, $url_base), false, $largeurs, $styles);

	if ($flag_editable)
	  $res .= formulaire_mots_cles($id_objet, $les_mots, $table, $table_id, $url_base, $visible, $objet);

	return $res;
}

// http://doc.spip.org/@formulaire_mot_remplace
function formulaire_mot_remplace($id_groupe, $id_mot, $url_base, $table, $table_id, $objet, $id_objet)
{
	$result = sql_select("id_mot, titre", "spip_mots", "id_groupe = $id_groupe", "", "titre");

	$s = '';

	while ($row_autres = sql_fetch($result)) {
		$id = $row_autres['id_mot'];
		$le_titre_mot = supprimer_tags(typo($row_autres['titre']));
		$selected = ($id == $id_mot) ? " selected='selected'" : "";
		$s .= "\n<option value='$id'$selected> $le_titre_mot</option>";
	}

	$ancre = "valider_groupe_".$table."-".$id_groupe."-".$id_objet;
	// forcer le recalcul du noeud car on est en Ajax
	$jscript1 = "findObj_forcer('choisir-$ancre').style.visibility='visible';";

	$corps = "\n<select name='nouv_mot' id='nouv_mot$id_groupe' onchange=\"$jscript1\""
	. " class='fondl spip_xx-small' style='width:90px;'>"
	. $s
	. "</select>\n&nbsp;" ;

	$t =  _T('bouton_changer');

	return ajax_action_post('editer_mots', "$id_objet,$id_mot,$table,$table_id,$objet", $url_base, "$table_id=$id_objet",$corps, $t, " class='fondo spip_xx-small visible_au_chargement' id='choisir-$ancre'", "", "&id_objet=$id_objet&objet=$objet");
}

// http://doc.spip.org/@formulaire_mots_cles
function formulaire_mots_cles($id_objet, $les_mots, $table, $table_id, $url_base, $visible, $objet) {
	global  $spip_lang, $spip_lang_right;

	//Yoann
	if(!$table) $table="articles"; //nous evite d'avoir des plantages
	//TODO a tester en enlevant ca
	//FIN YOANN

	$cond_mots_vus = !$les_mots ? '' :
	  sql_in('id_mot', $les_mots);

	$id_groupes_vus = array();
	$flag_tous = 1;
	if ($cond_mots_vus) {

		$droit = substr($GLOBALS['visiteur_session']['statut'],1);
		$q = sql_select("M.id_groupe, G.$droit", "spip_mots AS M LEFT JOIN spip_groupes_mots AS G ON M.id_groupe=G.id_groupe", $cond_mots_vus, "M.id_groupe");
		while($r = sql_fetch($q)) {
			$id_groupes_vus[]= $r['id_groupe'];
			$flag_tous &= ($r[$droit] === 'oui');
		}
		$cond_id_groupes_vus = (" OR (unseul = 'oui' AND " . sql_in('id_groupe', $id_groupes_vus, 'NOT')." ) ");
	} else {
		$cond_id_groupes_vus = '';
	}

	$nb_groupes = sql_fetch(editer_mots_droits('count(*) AS n', "$table = 'oui' AND obligatoire = 'oui'$cond_id_groupes_vus"));
	$nb_groupes = $nb_groupes['n'];

	$res = debut_block_depliable($visible OR ($nb_groupes > 0),"lesmots-".$table.$id_objet);
	if ($flag_tous AND count($les_mots)>= 3) {
		$res .= "<div style='text-align: right' class='arial1'>"
		  . ajax_action_auteur('editer_mots', "$id_objet,-1,$table,$table_id,$objet", $url_base, "$table_id=$id_objet", array(_T('info_retirer_mots'),''),"&id_objet=$id_objet&objet=$objet")
		. "</div><br />\n";
	}

	$result = editer_mots_droits("id_groupe,unseul,obligatoire,titre, ".sql_multi ("titre", $spip_lang), "$table = 'oui' AND (unseul != 'oui'  $cond_id_groupes_vus) ORDER BY multi");

	// Afficher un menu par groupe de mots non vu
	$ajouter ='';
	$cond_mots_vus = !$les_mots ? '' :
	  (" AND " . sql_in('id_mot', $les_mots, 'NOT'));

	while ($row = sql_fetch($result)) {

	  $ancre=$table."-".$id_groupe."-".$id_objet;

		if ($menu = menu_mots($row, $id_groupes_vus, $cond_mots_vus,$ancre)) {
			$id_groupe = $row['id_groupe'];
			list($corps, $clic) = $menu;


  			$ajouter .= ajax_action_post('editer_mots',
  				"$id_objet,,$table,$table_id,$objet",
  				$url_base,
  				"$table_id=$id_objet",
  				$corps,
  				$clic,
  				" class='visible_au_chargement fondo spip_xx-small' id='valider_groupe_$ancre'", "",
  				"&id_objet=$id_objet&objet=$objet&select_groupe=$id_groupe");
		}
	}

	//if( ($table=="articles" || $table=="rubriques")){

	  if ($ajouter) {
  		$message = "<span class='verdana1'><b>"._T('titre_ajouter_mot_cle')."</b></span>\n";
  		$res .= "<div>"
  			. $ajouter
  			."</div>\n" ;
  	} else $message ='';

  	// Creer un nouveau mot ?
  	if (autoriser('modifier','groupemots')) {
  		$titre = _request('cherche_mot')
  			? "&titre=".rawurlencode(_request('cherche_mot')) : '';
  		$titre = _T('icone_creer_mot_cle');
  		$titres = array(
  			'articles'=>'icone_creer_mot_cle',
  			'breves'=>'icone_creer_mot_cle_breve',
  			'rubriques'=>'icone_creer_mot_cle_rubrique',
  			'sites'=>'icone_creer_mot_cle_site'			);
  		$bouton_ajouter = icone_horizontale_display(isset($titres[$table])?_T($titres[$table]):_T('icone_creer_mot_cle'), generer_url_ecrire("mots_edit","new=oui&ajouter_id_article=$id_objet&table=$table&table_id=$table_id&redirect=" . generer_url_retour($url_base, "$table_id=$id_objet")), "mot-cle-24.gif", "creer.gif", false)
  		. "\n";
  	} else $bouton_ajouter = '';

  	if ($message OR $bouton_ajouter) {
  		$res .= "$message<br />$bouton_ajouter";
  	}
	//}
	return $res . fin_block();
}


// http://doc.spip.org/@menu_mots
function menu_mots($row, $id_groupes_vus, $les_mots,$ancre="")
{
	$id_groupe = $row['id_groupe'];

	$n = sql_countsel("spip_mots", "id_groupe=$id_groupe" . $les_mots);
	// MODIFICATION
	//dans le cas ou on a pas de mots clefs associes au groupe (principe de l'arborescence)
	//on va prendre les sous-groupe comme etant signe d'affichage
	if (!$n) {
		$n = sql_countsel("spip_groupes_mots", "id_parent=$id_groupe");
		if(!$n) return '';
	}
	//FIN

	$titre = textebrut(typo($row['titre']));
	$titre_groupe = entites_html($titre);
	$unseul = $row['unseul'] == 'oui';
	$obligatoire = $row['obligatoire']=='oui' AND !in_array($id_groupe, $id_groupes_vus);

	$res = '';
	// forcer le recalcul du noeud car on est en Ajax
	$rand = rand(0,10000); # pour antifocus & ajax
	$ancre = "valider_groupe_".$ancre;


	$jscript1 = "findObj_forcer('$ancre').style.visibility='visible';";
	$jscript2 = "if(!antifocus_mots['$rand-$id_groupe']){this.value='';antifocus_mots['$rand-$id_groupe']=true;}";

	if ($n > 50) {
		$jscript = "onfocus=\"$jscript1 $jscript2\"";

		if ($obligatoire)
			$res .= "<input type='text' name='cherche_mot' id='cherche_mot$id_groupe' class='fondl' style='background-color:#E86519;' value=\"$titre_groupe\" size='20' $jscript />";
		else if ($unseul)
			$res .= "<input type='text' name='cherche_mot' id='cherche_mot$id_groupe' class='fondl' style='background-color:#cccccc;' value=\"$titre_groupe\" size='20' $jscript />";
		else
			$res .= "<input type='text' name='cherche_mot' id='cherche_mot$id_groupe'  class='fondl'  value=\"$titre_groupe\" size='20' $jscript />";

		$res .= "<input type='hidden' name='select_groupe'  value='$id_groupe' />&nbsp;";
		return array($res, _T('bouton_chercher'));
	} else {

		$jscript = "onchange=\"$jscript1\"";

		if ($obligatoire)
			$res .= "<select name='nouv_mot' id='nouv_mot$id_groupe' size='1' style='background-color:#E86519;' class='fondl' $jscript>";
		else if ($unseul)
			$res .= "<select name='nouv_mot' id='nouv_mot$id_groupe' size='1' style=' background-color:#cccccc;' class='fondl' $jscript>";
		else
			$res .= "<select name='nouv_mot' id='nouv_mot$id_groupe' size='1' class='fondl' $jscript>";

		//on a le groupe de mots de base
		$res .= "\n<option value='x' style='font-variant: small-caps;'>$titre</option>";

		$result = sql_select("id_mot, type, titre", "spip_mots", "id_groupe =$id_groupe " . $les_mots, "", "titre");


		//on boucle sur les mots du groupe de base
		while($row = sql_fetch($result)) {
			$res .= "\n<option value='" .$row['id_mot'] .
				"'>&nbsp;&nbsp;&nbsp;" .
				textebrut(typo($row['titre'])) .
				"</option>";
		}
	}
	//ARBORESCENCE : on va rajouter dans le select les sous-groupes ainsi que leurs mots clefs
	$res.=select_sous_menu_groupe_mots($id_groupe,$table);  //gestion de la hierarchie des mots clefs
	$res .= "</select>&nbsp;";
	return array($res, _T('bouton_choisir'));

}

// http://doc.spip.org/@select_sous_menu_groupe_mots
function select_sous_menu_groupe_mots($id_groupe,$table='articles',$niveau=0){
//fonction recursive qui permet de recuperer larborescence des groupes de mots et les mots
return;
	global $spip_lang,$connect_statut,$cond_id_groupes_vus;
	//$query="SELECT id_groupe,titre, ".creer_objet_multi ("titre", $spip_lang)." FROM spip_groupes_mots WHERE `".$table."` = 'oui' AND ".substr($connect_statut,1)." = 'oui' ".($cond_id_groupes_vus?" AND (unseul != 'oui'   OR (unseul = 'oui' AND id_groupe NOT IN ($cond_id_groupes_vus)) )":"")." AND id_parent=".$id_groupe." ORDER BY multi";

     //boucle sur les sous groupes
		//$result_sous_groupes = spip_query($query);



		//hum hum
		if(!$table) $table="articles";


if($table!="articles" && $table!="rubriques") $cond_table=$table." = 'oui' AND ";
else  $cond_table="tables_liees REGEXP '(^|,)$table($|,)' AND ";

if($cond_id_groupes_vus){
		$result_sous_groupes = editer_mots_droits("id_groupe,titre, ".sql_multi ("titre", $spip_lang), " (unseul != 'oui'  OR (unseul = 'oui' AND id_groupe NOT IN ($cond_id_groupes_vus))) ORDER BY multi");
}else {
  $result_sous_groupes = editer_mots_droits("id_groupe,titre, ".sql_multi ("titre", $spip_lang), "$table = 'oui' AND (unseul != 'oui'  OR (unseul = 'oui')) ORDER BY multi");
}

		 while ($row = sql_fetch($result_sous_groupes)) {
		     $res .= "\n<option value='" .$row['id_groupe'] .
				"'>".str_repeat("&nbsp;&nbsp;",$niveau) .
				textebrut(typo($row['titre'])) .
				"</option>";
				//BOUCLES sur les mots de chaque sous groupe
				//$result = spip_query("SELECT id_mot, type, titre,".creer_objet_multi ("titre", $spip_lang)." FROM spip_mots WHERE id_groupe =".$row['id_groupe']." ORDER BY type, titre");
				$result = sql_select("id_mot, type, titre", "spip_mots", "id_groupe =$id_groupe " . ($les_mots ? "AND id_mot NOT IN ($les_mots) " : '') .  "", "", "titre");

				while($row2 = sql_fetch($result)) {
    			     $res .= "\n<option value='" .$row2['id_mot'] .
    				"'>".str_repeat("&nbsp;&nbsp;",$niveau)."&nbsp;-&gt;" .
    				textebrut(typo($row2['titre'])) .
    				"</option>";
                }

				$res.=select_sous_menu_groupe_mots($row['id_groupe'],$table,$niveau+1);
		 }
        return $res;
}

/*function recup_squelette_motspartout($squel){

	if($fond=find_in_path($squel)) return $fond;
	else {//on va aller chercher l'interface g�n�rale
		$squel="fond/listemot_general";
		return find_in_path($squel);
	}

}*/

// Fonction verifiant que l'auteur a le droit de modifier un groupe de mots.
// Fondee sur l'egalite du nom du statut et du nom du champ.
// Il vaudrait mieux rajouter une table des statuts (ou un groupe de mots)
// et un table de jointure entre ca et la table des groupes de mots.

// http://doc.spip.org/@editer_mots_droits
function editer_mots_droits($select, $cond)
{
	
	$droit = substr($GLOBALS['visiteur_session']['statut'],1);
	return sql_select("$select", "spip_groupes_mots", "$droit = 'oui' AND $cond");
}


function creer_mot($table, $id_objet, $table_id, $url_base, $mot='', $id_groupe=0)
{
	static $titres = array(
			'articles'=>'icone_creer_mot_cle',
			'breves'=>'icone_creer_mot_cle_breve',
			'rubriques'=>'icone_creer_mot_cle_rubrique',
			'sites'=>'icone_creer_mot_cle_site'
			);

	if (!($id_groupe ?
		autoriser('modifier','groupemots', $id_groupe) :
		autoriser('modifier','groupemots'))
	    )
		return '';

	$legende = isset($titres[$table])
	  ? _T($titres[$table])
	  : _T('icone_creer_mot_cle');

	$args = "new=oui&ajouter_id_article=$id_objet&table=$table&table_id=$table_id"
	. (!$mot ? '' : ("&titre=".rawurlencode($mot)))
	. (!$id_groupe ? '' : ("&id_groupe=".intval($id_groupe)))
	. "&redirect=" . generer_url_retour($url_base, "$table_id=$id_objet");

	return icone_horizontale_display($legende, generer_url_ecrire("mots_edit", $args), "mot-cle-24.gif", "creer.gif", false);
}


// http://doc.spip.org/@editer_mots_un
function editer_mots_un($row, $own)
{
	$puce_statut = charger_fonction('puce_statut', 'inc');

	list ($cle, $flag_editable, $id_objet, $objet, $ret, $table, $table_id, $url_base) = $own;

	$id_mot = $row['id_mot'];
	$titre_mot = $row['titre'];
	$id_groupe = $row['id_groupe'];

	$url = generer_url_ecrire('mots_edit', "id_mot=$id_mot&redirect=$ret");
	// On recupere le typo_mot ici, et non dans le mot-cle lui-meme;
	// sinon bug avec arabe

	

	if ($table!="documents"){ //interface simplifiée documents
		$groupe = typo(sql_getfetsel("titre", "spip_groupes_mots", "id_groupe = $id_groupe"));
		if(autoriser('modifier', 'groupemots', $id_groupe) ){
			$groupe = "<a href='" . generer_url_ecrire("mots_type","id_groupe=$id_groupe") . "'>$groupe</a>";
		}
	}

	$retire = $unseul = '';

	if ($flag_editable) {
		$r = editer_mots_droits('unseul', "id_groupe = $id_groupe");
		if ($r) {
			$unseul = ($r[0]['unseul'] == 'oui');
			$r="";
			if($table!="documents"){ //interface simplifiée poru els documents
				$r =  _T('info_retirer_mot')
			  	. "&nbsp;";
			}
			
			$r.=http_img_pack('croix-rouge.gif', "X", " class='puce' style='vertical-align: bottom;'");

			$retire = ajax_action_auteur('editer_mots', "$id_objet,$id_mot,$table,$table_id,$objet", $url_base, "$table_id=$id_objet", array($r,''),"&id_objet=$id_objet&objet=$objet");
		}
	}
	// Changer
	if ($unseul) {
		$mot = formulaire_mot_remplace($id_groupe, $id_mot, $url_base, $table, $table_id, $objet, $id_objet);
	} else {
		$mot = "<a href='$url'>".typo($titre_mot)."</a>";
	}

	$cle = $puce_statut($id_mot, 'publie', $id_groupe, 'mot');

	return array("<a href='$url'>$cle</a>", $mot, $groupe, $retire);
}

?>
