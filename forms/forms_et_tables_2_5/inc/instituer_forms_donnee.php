<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *
 *	Loic LE MAO, Sylvain BLANC
 *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
if (!include_spip('inc/autoriser'))
	include_spip('inc/autoriser_compat');
include_spip('inc/minipres');
include_spip('inc/presentation');
	
function inc_instituer_forms_donnee_dist($id_form, $id_donnee, $statut, $rang=NULL)
{
	$type_form = "form";
	$pi18n = "form";

	$res = sql_select("type_form","spip_forms","id_form="._q($id_form));
	
	if ($row = spip_fetch_array($res)
		AND $row['type_form']!=''
		AND $rox['type_form']!='sondage')
			$type_form = $row['type_form'];
	$pi18n = forms_prefixi18n($type_form); 

	$res =
	"\n<div id='instituer_forms_donnee-$id_donnee'>" .
	"\n<center>" . 
	"<b>" .
	_T("$pi18n:texte_donnee_statut") .
	"</b>" .
	
	//--LLM 2012-12-17 TEST
	"<span class=\"show\">" .
	//-- FIN TEST
	
	"\n<select name='statut_nouv' size='1' class='fondl select statut'\n" . ">";
	//--LLM 2012-12-17 TEST
	
	// "onchange=\"this.nextSibling.nextSibling.src='" .
	// _DIR_PLUGIN_FORMS."img_pack/".
	// "' + puce_statut(options[selectedIndex].value);" .
	// " setvisibility('valider_statut', 'visible');\">\n";
	
	//-- FIN TEST

	$atts = array(
	"prepa"=>"style='background-color: white'",
	"prop"=>"style='background-color: #FFF1C6'",
	"publie"=>"style='background-color: #B4E8C5'",
	"poubelle"=>http_style_background('rayures-sup.gif'),
	"refuse"=>"style='background-color: #FFA4A4'"
	);
	foreach(array("prepa","prop","publie","poubelle","refuse") as $s) {
		$lib = _T("$pi18n:texte_statut_$s");
		if (
		$s==$statut
		OR autoriser('instituer','donnee',$id_donnee,NULL,array('id_form'=>$id_form,'statut'=>$statut,'nouveau_statut'=>$s)) 
		)
			$res .= "<option"  . 
				forms_mySel($s, $statut)  . " " .
				$atts[$s] . " >" .
				trim($lib) .
				"</option>\n";
	}
	
	

	if (version_compare($GLOBALS['spip_version_code'],'1.9250','>'))
		$puce = inserer_attribut(puce_statut($statut),'alt','');
	else 
		$puce = http_img_pack("puce-".puce_statut($statut).'.gif', "", "border='0'");
	$res .=	"</select>" .
	//--LLM 2012-12-17 TEST
	"";
	// " &nbsp; $puce &nbsp;\n";
	
	
	
		$res.="
		</span>
		<script type=\"text/javascript\">
			function update_select(statut_default){
				jQuery(this).attr('style',jQuery(this.options[this.selectedIndex]).attr('style')).closest('form').find('.boutons').css('visibility',this.options[this.selectedIndex].value==statut_default?'hidden':'visible');
			}
			jQuery(function(){
				jQuery(\".verifformok .show select\")
				.each(function(){update_select.apply(this,['".$statut."']);})
				.bind('change',function(){update_select.apply(this,['".$statut."']);})
				.bind('keyup',function(){update_select.apply(this,['".$statut."']);});
			});
		</script>";
	//-- FIN TEST
	
	
	if ($rang!==NULL){
		$res .= "<input name='rang_nouv' size='4' class='fondl' value='$rang' onchange=\"setvisibility('valider_statut', 'visible');\" />";
	}
	$res .= "<span class='visible_au_chargement' id='valider_statut'>" .
	"<input type='submit' value='"._T('bouton_valider')."' class='fondo boutons' />" .
	"</span>" .
	 "</center>"
	. '</div>';

	return ajax_action_auteur('instituer_forms_donnee',$id_donnee,'donnees_edit', "id_form=$id_form&id_donnee=$id_donnee", $res);
}

// http://doc.spip.org/@puce_statut_article
function puce_statut_donnee($id, $statut, $id_form, $ajax = false) {
	global $spip_lang_left, $dir_lang, $connect_statut, $options;
	static $script=NULL;
	static $pi18n = array();
	static $type_form = array();
	
	if (!$id) {
	  $id = $id_form;
	  $ajax_node ='';
	} else	$ajax_node = " id='imgstatutforms_donnee$id'";

	if (!isset($type_form[$id_form])){
		$type_form[$id_form] = "form";
		$res = spip_query("SELECT type_form FROM spip_forms WHERE id_form="._q($id_form));
		if ($row = spip_fetch_array($res)
			AND $row['type_form']!=''
			AND $rox['type_form']!='sondage')
				$type_form[$id_form] = $row['type_form'];
	}
	if (!isset($pi18n[$id_form]))
		$pi18n[$id_form] = forms_prefixi18n($type_form[$id_form]);

	$p = $pi18n[$id_form];
	$puce = array(
	  'prepa'=>'puce-blanche.gif',
	  'prop'=>'puce-orange.gif',
	  'publie'=>'puce-verte.gif',
	  'refuse'=>'puce-rouge.gif',
	  'poubelle'=>'puce-poubelle.gif');
	$lib = array();
	$clip = array();
	$c = 0;
	$statuts = array("prepa","prop","publie","poubelle","refuse");
	foreach($statuts as $s){
		$lib[$s] = _T("$p:texte_statut_$s");
		if (autoriser('instituer','donnee',$id_donnee,NULL,array('id_form'=>$id_form,'statut'=>$statut,'nouveau_statut'=>$s)))
			$clip[$s] = $c++;
		else 
			$clip[$s] = 0;
	}
	$width = 11*$c+1;
	$inser_puce = http_img_pack($puce[$statut], trim($lib[$statut]), " style='margin: 1px;'$ajax_node");

	if (!autoriser('publierdans', 'form', $id_form))
		return $inser_puce;

	if ($ajax){
		$action="";
		$res = "<div class=\"puce_article_content\">
		<span class='puce_forms_donnee_fixe'>"
		. $inser_puce
		. "</span>"
		. "<div class='puce_article_popup' id='statutdecalforms_donnee$id'>";
		foreach ($statuts as $s) {
			if (autoriser('instituer','donnee',$id_donnee,NULL,array('id_form'=>$id_form,'statut'=>$statut,'nouveau_statut'=>$s))){
				$res .= Forms_afficher_script_statut($id, 'forms_donnee', -((11*$clip[$s])+1), $puce[$s], $s, $lib[$s], $action,$id_form);
			}
		}
		$res .= "</div>"
		."</div>";
		return $res;
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX))
	  $over ='';
	else {
		$action = generer_url_ecrire('puce_statut_forms_donnee',"",true);
		$action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', 'forms_donnee', $id, '$action'); }";
		$over = " onmouseover=\"$action\"";
	}

	return 	"<span class='puce_article' id='{$nom}forms_donnee$id'$dir_lang$over>"
	. $inser_puce
	. '</span>';

}

function Forms_afficher_script_statut($id, $type, $n, $img, $statut, $titre, $act='',$id_form) {
	$h = generer_action_auteur("instituer_forms_donnee","$id");
	$t = supprimer_tags($titre);
	$action="forms_changement_statut('$id_form','$id','$statut','$h');";
	return "<a href=\"#\" onclick=\"".$action."\" title=\"$t\">".http_img_pack($img,$t)."</a>";
}


?>