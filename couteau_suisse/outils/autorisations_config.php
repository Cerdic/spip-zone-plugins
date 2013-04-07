<?php

#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2013               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

# Fichier de configuration pris en compte par config_outils.php et specialement dedie a la configuration des autorisations SPIP
# -----------------------------------------------------------------------------------------------------------------------------

function outils_autorisations_config_dist() {

// Ajout de l'outil 'autorisations'
add_outil(array(
	'id' => 'autorisations',
	'autoriser' => "autoriser('webmestre')",
	'categorie' => 'securite',
	'version-min' => '1.9300',
	'code:spip_options' => '%%autorisations_debug%%',
	'code:options' => '%%autorisations_alias%%
function autoriser_redacteur($faire,$type,$id,$qui,$opt) {
	return in_array($qui["statut"], array("0minirezo", "1comite"));
}
function autoriser_administrateur($faire,$type,$id,$qui,$opt) {
	return $qui["statut"] == "0minirezo" && !$qui["restreint"];
}
@include_once "'.str_replace('\\','/',realpath(_DIR_CS_TMP.'mes_autorisations.php')).'";
',
	// TODO : Exploiter $GLOBALS['autoriser_exception']
	'pipelinecode:pre_description_outil' => 'if($id=="autorisations")
$texte=str_replace(array("@_CS_DIR_TMP@","@_CS_DIR_LOG@"), array(cs_root_canonicalize(_DIR_CS_TMP), cs_root_canonicalize(defined("_DIR_LOG")?_DIR_LOG:_DIR_TMP)), $texte);',
	
));

add_variables( array(
	'nom' => 'autorisations_debug',
	'check' => 'couteauprive:autorisations_debug',
	'defaut' => 0,
	'code:%s' => "define('_DEBUG_AUTORISER', true);",
	'label' => '',
	'commentaire' => '(!defined("_SPIP30000") || _LOG_FILTRE_GRAVITE>=_LOG_INFO)?""
			:_T("couteauprive:cs_comportement_ko", array("gr1"=>"{{"._LOG_FILTRE_GRAVITE."}}","gr2"=>"{{"._LOG_INFO." (INFO)}}"))',
) ,array(
	'nom' => 'autorisations_alias',
	'defaut' => "'// creer article = creer rubrique\n// 23 : modifier article 18 = ok\n// configurer cs = webmestre\n// auteur 7 = niet'",
	'format' => _format_CHAINE,
	'lignes' => 8,
	'code' => "function _autorisations_LISTE(){return %s;}",
	'commentaire' => 'couteauprive_T("autorisations_creees")." ".((include_spip(_DIR_CS_TMP."mes_autorisations") && function_exists("autorisations_liste") && $tmp=autorisations_liste())?join(", ",$tmp):couteauprive_T("variable_vide"))',
));

}

function autorisations_installe_dist() {
cs_log("autorisations_installe_dist()");
	if(!function_exists('_autorisations_LISTE')) return NULL;

	// on decode les alias entres dans la config
	$alias = preg_split("/[\r\n]+/", _autorisations_LISTE());
	$fct = array(); $erreurs = '';
	foreach($alias as $_a) {
		list($a,) = explode('//', $_a, 2);
		if (preg_match('/^\s*(?:(\d+)\s*:)?(.*?)=\s*(?:(\d+)\s*:)?(.*?)$/', $a, $regs)) {
			$qui = intval($regs[1]); list($faire, $type, $id) = autorisations_parse($regs[2]);
			$qui2 = intval($regs[3]); list($faire2, $type2, $id2) = autorisations_parse($regs[4]);
			if($faire===-1 || $faire2===-1 || ($faire==$faire2 && $type==$type2 && $id==$id2 && $qui=$qui2)) { 
				$erreurs .= "// #ERREUR : .$_a\n"; continue;
			}
			$if = $qui?"\$qui['id_auteur']==$qui":'';
			if($id) $if .= ($if?' && ':'') . '$id=='.$id;
			if($qui2) $alias = "'$faire2','$type2',$id2,$qui2";
			elseif($id2) $alias = "'$faire2','$type2',$id2,\$qui";
			elseif($type2) $alias = "'$faire2','$type2',\$id,\$qui";
			else $alias = "'$faire2',\$type,\$id,\$qui";
			$f = ($faire && $type)?"autoriser_{$type}_{$faire}":($faire?"autoriser_{$faire}":"autoriser_{$type}");
			$code[$f][] = "// $_a\n\t".($if?"if($if) ":'')."return autoriser($alias,\$opt);";
		}
	}
	foreach($code as $k=>$v) {
		$fct[] = $k;
		$v = join("\n\t", $v);
		$code[$k] = "if(!function_exists('$k')) {\n\tfunction $k(\$faire,\$type,\$id,\$qui,\$opt) {
	".$v.(strpos($v,') return')!==false?"\n\treturn autorisations_return(\$faire,\$type,\$id,\$qui['id_auteur'],\$opt);":'').' } }';
	}
	// fonction generique de retour
	$code[] = 'function autorisations_return($faire,$type,$id,$qui,$opt) {
	if($faire && $type && $id && intval($qui)) return autoriser($faire,$type,$id,NULL,$opt);
	if($faire && $type && $id) return autoriser($faire,$type,0,NULL,$opt);
	if($faire && $type) return autoriser($type,"",0,NULL,$opt);
	return autoriser("defaut");
}';
	// liste de autorisations "maison"
	$code[] = 'function autorisations_liste() { return '.var_export($fct,1).'; }';
	// en retour : le code PHP
	$alias = array($erreurs.join("\n", $code));
	$code = array('code_autorisations'=>$alias);
	ecrire_fichier_en_tmp($code, 'autorisations');
	return $alias;
}

// renvoie array($faire, $type, $id)
function autorisations_parse($a) {
	$a = explode(' ', trim(preg_replace(',\s+,',' ',preg_replace(',[^a-z0-9]+,i',' ',$a))), 3);
	if(!$a[0] || is_integer($a[0])) return array(-1);
	if(intval($a[2])) return array($a[0], $a[1], intval($a[2]));
	if(intval($a[1])) return array('', $a[0], intval($a[1]));
	return array($a[0], $a[1], 0);
}

function autorisations_action_rapide() {
	$res = $obj = $faire = array();
	// les objets existants (SPIP>=3)
	$objets = function_exists('lister_tables_objets_sql')?lister_tables_objets_sql():array();
	// les fonctions disponibles
	$arr = get_defined_functions(); $user = &$arr['user'];
	unset($arr['user']['autoriser_dist']);
	// les alias maison
	$alias = function_exists('autorisations_liste')?autorisations_liste():array();
	$nb_fonctions = $nb_surcharges = 0;
	foreach ($user as $v) if(strncmp($v, 'autoriser_', 10)===0 && preg_match(',^(autoriser_(.*?))(_dist)?$,', $v, $reg)) {
		$nb_fonctions++;
		if(!$reg[3] && function_exists($reg[1].'_dist')) { $nb_surcharges++; continue; }
		$sup = ($reg[3] && function_exists($reg[1]))?'<sup>(*)</sup>':'';
		if(in_array($reg[1], $alias)) { $sp1='<span class="vert">'; $sp2="</span>"; } else $sp1 = $sp2 = '';
		$tmp = explode('_', $reg[2], 2);
		$table = table_objet_sql($tmp[0]);
		if(in_array($tmp[1], array('menu','onglet','bouton'))) 
			$res['['.$tmp[1].']'][] = $sp1.$tmp[0].$sup.$sp2; 
		elseif(strncmp($table, 'spip_', 5)===0)
			$faire[$tmp[1]?$tmp[1]:couteauprive_T('variable_vide')][] = $obj[$table][] = $tmp[1]?$sp1.$tmp[1].$sup.' '.$tmp[0].$sp2:"<span class='bleu'>$sp1$tmp[0]$sp2</span>"; 
		elseif($tmp[1])
			$res[$tmp[0]][] = $sp1.$tmp[1].$sup.' '.$tmp[0].$sp2; 
		else
			$res['[unique]'][] = $sp1.$tmp[0].$sup.$sp2; 
	}
	foreach($obj as $k=>$r) {
		sort($r);
		$t = $objets[$k]['texte_objets'];
		$obj[$k] = '<li><b>'.(!$t?"[$k]":_T($t)).' : </b>'.join(', ', $r).'</li>';
	}
	foreach($res as $k=>$r) {
		sort($r);
		$res[$k] = "<li><b>$k : </b>".join(', ', $r).'</li>';
	}
	foreach($faire as $k=>$r) {
		sort($r);
		$faire[$k] = "<li><b>$k : </b>".join(', ', $r).'</li>';
	}
	sort($obj); sort($res); sort($faire);
	return '<style>#cs_auth h3{cursor:pointer; margin:0.5em 0;} #cs_auth{margin:0 2em;} #cs_auth ul{margin:0 1em;} .vert{color:#2B2;} .bleu{color:#22B;}</style>
<div id="cs_auth"><p>'
		. couteauprive_T('autorisations_bilan', array('nb1'=>$nb_fonctions,'nb2'=>$nb_surcharges))
		. '</p><h3>1. '.couteauprive_T('autorisations_titre1', array('nb'=>count($obj))).'<span id="auth_1" class="cs_hidden"> (...)</span></h3><ul>'
		. join('', $obj)
		. '</ul><h3>2. '.couteauprive_T('autorisations_titre2', array('nb'=>count($faire))).'<span id="auth_2" class="cs_hidden"> (...)</span></h3><ul>'
		. join(' ', $faire)
		. '</ul><h3>3. '.couteauprive_T('autorisations_titre3', array('nb'=>count($res))).'<span id="auth_3" class="cs_hidden"> (...)</span></h3><ul>'
		. join('', $res)
		. '</ul>'.($nb_surcharges?'<p><sup>(*)</sup> '.couteauprive_T('autorisations_surcharge').'</p>':'') 
		. '</div>' . http_script("
jQuery(document).ready(function() {
	jQuery('#cs_auth h3').click( function() {
		var span = jQuery('span', this);
		if(!span.length) return true;
		jQuery(this).next().toggleClass('cs_hidden');
		cs_EcrireCookie(span[0].id, '+'+span[0].className, dixans);
		span.toggleClass('cs_hidden');
		return false; // annulation du clic
	}).each(autorisations_lire_cookie);

function autorisations_lire_cookie(i,e){
	var span = jQuery('span', this);
	if(!span.length) return;
	var c = cs_LireCookie(span[0].id);
	if(c==null || c.match('cs_hidden')) {
		jQuery(this).next().addClass('cs_hidden');
		span.removeClass('cs_hidden');
	}
}
});");
}

?>