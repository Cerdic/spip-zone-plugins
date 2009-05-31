<?php
/*
+--------------------------------------------+
| Tableau de bord 2.6 (06/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Requetes de base du recap d'accueil
+--------------------------------------------+
*/

function etats_rubriques() {
	$qt = spip_query("SELECT COUNT(id_rubrique) as nb FROM spip_rubriques");
	
	if(spip_num_rows($qt)) {
		$a = spip_fetch_array($qt);
		$tot = $a['nb'];
		$qp = spip_query("SELECT COUNT(id_rubrique) as nb FROM spip_rubriques WHERE statut='publie'");
		$b = spip_fetch_array($qp);
		$pub = $b['nb'];
		$npub = $tot - $pub;
		return array($pub,$npub,$tot);
	}
	else return array(0,0,0);
}

function etats_articles() {
	$qt = spip_query("SELECT COUNT(id_article) as nb FROM spip_articles");
	
	if(spip_num_rows($qt)) {
		$a = spip_fetch_array($qt);
		$tot = $a['nb'];
		$qp = spip_query("SELECT COUNT(id_article) as nb FROM spip_articles WHERE statut='publie'");
		$b = spip_fetch_array($qp);
		$pub = $b['nb'];
		$npub = $tot - $pub;
		return array($pub,$npub,$tot);
	}
	else return array(0,0,0);
}

function etats_breves() {
	$qt = spip_query("SELECT COUNT(id_breve) as nb FROM spip_breves");
	
	if(spip_num_rows($qt)) {
		$a = spip_fetch_array($qt);
		$tot = $a['nb'];
		$qp = spip_query("SELECT COUNT(id_breve) as nb FROM spip_breves WHERE statut='publie'");
		$b = spip_fetch_array($qp);
		$pub = $b['nb'];
		$npub = $tot - $pub;
		return array($pub,$npub,$tot);
	}
	else return array(0,0,0);
}

function etats_mots_clefs() {
	$qt = spip_query("SELECT COUNT(id_mot) as nb FROM spip_mots");
	
	if(spip_num_rows($qt)) {
		$a = spip_fetch_array($qt);
		
		$qg = spip_query("SELECT COUNT(id_groupe) as nbg FROM spip_groupes_mots");
		$g = spip_fetch_array($qg);
		
		return array($a['nb'],$g['nbg']);
	}
	else return array(0,0);
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
	$qt = spip_query("SELECT COUNT(id_document) as nb FROM spip_documents");
	if(spip_num_rows($qt)) {
		$a = spip_fetch_array($qt);
		return $a['nb'];
	}
	else return 0;
}

function etats_petitions() {
	$q = spip_query("SELECT COUNT(id_article) as pet FROM spip_petitions");
	if($pet = spip_num_rows($q)) {
		$a = spip_fetch_array($q);
		$nbpet = $a['pet'];
		$qs=spip_query("SELECT COUNT(id_signature) as sign FROM spip_signatures WHERE statut='publie'");
		if(spip_num_rows($qs)) {
			$as=spip_fetch_array($qs);
			$sign=$as['sign'];
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
