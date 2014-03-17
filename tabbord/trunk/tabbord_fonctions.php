<?php
/**
 * Fonctions utiles au plugin Tableau de bord
 *
 * @plugin     Tableau de bord
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Tabbord\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip('base/abstract_sql');

function etats_objets_publies($nom) {
	$qt = sql_fetsel("COUNT(id_$nom) as nb", "spip_" . $nom . "s");
	if($qt) {
		$tot = $qt['nb'];
		$qp = sql_fetsel("COUNT(id_$nom) as nb", "spip_" . $nom . "s", "statut='publie'");
		$pub = $qp['nb'];
		$npub = $tot - $pub;
		return array($pub,$npub,$tot);
	}
	else return array(0,0,0);
}


function etats_mots_clefs() {
	$qt = sql_fetsel("COUNT(id_mot) as nb", "spip_mots");
	
	if($qt) {
		// SELECT COUNT(DISTINCT spip_mots_liens.id_mot) FROM spip_mots, spip_mots_liens WHERE spip_mots_liens.id_mot = spip_mots.id_mot
		$qp = sql_fetsel("COUNT(DISTINCT spip_mots_liens.id_mot) as nb", "spip_mots,spip_mots_liens",'spip_mots_liens.id_mot = spip_mots.id_mot');
		
		return array($qp['nb'],$qt['nb']-$qp['nb'],$qt['nb']);
	}
	else return array(0,0,0);
}

function etats_objets($champ_id,$table = null) {
	$table = ($table) ? $table : $champ_id . "s";
	$qt = sql_fetsel("COUNT(id_$champ_id) as nb", "spip_$table");
	
	if($qt) {
		return array($qt['nb']);
	}
	else return array(0);
}

function etats_forums() {
	$q = spip_query("SELECT id_forum, statut FROM spip_forum");
	if($tt=spip_num_rows($q)) {
		while($a=spip_fetch_array($q)) {
			$f=$a['statut'];
			if($f=='prop') { $prop[]=$a['id_forum']; }
			elseif($f=='prive' || $f=='privrac') { $priv[]=$a['id_forum']; }
			elseif($f=='privadm') { $privadm[]=$a['id_forum']; }
			elseif($f=='publie') { $pub[]=$a['id_forum']; }
			elseif($f=='off') {$off[]=$a['id_forum']; }
		}
		$fprop = (isset($prop)? count($prop):'0');
		$fpriv = (isset($priv)? count($priv):'0');
		$fprivadm = (isset($privadm)? count($privadm):'0');
		$fpub = (isset($pub)? count($pub):'0');
		$tt = $tt-(isset($off)? count($off):'0');
	}
	else $tt='0';
	
	return array($fpub,$fpriv,$fprivadm,$fprop,$tt);
}

function etats_documents() {
	$qt = sql_fetsel("COUNT(id_document) as nb", "spip_documents");
	if($qt) {
		return array($qt['nb']);
	}
	else return array(0);
}

function etats_petitions() {
	$qt = sql_fetsel("COUNT(id_petition) as nb", "spip_petitions");
	if($qt) {
		$pet = $qt['nb'];
		$qs = sql_fetsel("COUNT(id_signature) as sign", "spip_signatures", "statut='publie'");
		if($qs) {
			$sign=$qs['sign'];
		}
		else { $sign = '0'; }
	}
	else { $pet='0'; }
	
	return array($pet,$sign);	
}


function etats_auteurs() {
	$q=spip_query("SELECT id_auteur, statut FROM spip_auteurs");
	$tt=spip_num_rows($q);
	$admres=0;
	while ($a=spip_fetch_array($q)) {
		$f=$a['statut'];
		if($f=='0minirezo') {
			$adm[]=$a['id_auteur'];
			$ar = spip_query("SELECT id_rubrique FROM spip_auteurs_rubriques WHERE id_auteur=".$a['id_auteur']);
			if($res= spip_num_rows($ar)) { $admres++; }
		}
		elseif($f=='1comite') { $red[]=$a['id_auteur']; }
		elseif($f=='6forum') { $vis[]=$a['id_auteur']; }
		elseif($f=='5poubelle') { $poub[]=$a['id_auteur']; }
		else {$otr[]=$a['id_auteur']; }
	}
	$fadm = (isset($adm)? count($adm):'0');
	$fred = (isset($red)? count($red):'0');
	$fvis = (isset($vis)? count($vis):'0');
	$fpoub = (isset($poub)? count($poub):'0');
	$fotr = (isset($otr)? count($otr):'0');
	
	return array($fadm,$admres,$fred,$fvis,$fpoub,$fotr,$tt);
}


function etats_sites() {
	#sites ref dans art
	$qa=spip_query("SELECT COUNT(id_article) as nb FROM spip_articles WHERE url_site!=''");
	if(spip_num_rows($qa)) {
		$ra=spip_fetch_array($qa);
		$sa = $ra['nb'];
	}
	else { $sa = 0; }

	#sites ref dans auteur
	$qat=spip_query("SELECT COUNT(id_auteur) as nb FROM spip_auteurs WHERE url_site!=''");
	if(spip_num_rows($qat)) {
		$rat=spip_fetch_array($qat);
		$sat = $rat['nb'];
	}
	else { $sat = 0; }

	#sites ref dans breve
	$qb=spip_query("SELECT COUNT(id_breve) as nb FROM spip_breves WHERE lien_url!=''");
	if(spip_num_rows($qb)) {
		$rb=spip_fetch_array($qb);
		$sb = $rb['nb'];
	}
	else { $sb = 0; }

	#sites ref syndic (rub)
	$qs=spip_query("SELECT COUNT(id_syndic) as nb FROM spip_syndic");
	if(spip_num_rows($qs)) {
		$rs=spip_fetch_array($qs);
		$ss = $rs['nb'];
		}
	else { $ss = 0; }

	$tt=$sa+$sat+$sb+$ss;
	// art, auteur, breve, syndic
	return array($sa,$sat,$sb,$ss,$tt);
}
?>