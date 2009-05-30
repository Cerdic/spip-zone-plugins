<?php

/*
+--------------------------------------------+
| DW2 2.14 (03/2007) - SPIP 1.9.2
+--------------------------------------------+
| H. AROUX . Scoty . koakidi.com
| Script certifié KOAK2.0 strict, mais si !
+--------------------------------------------+
| Le package : mes_fonctions
+--------------------------------------------+
| - Declarer table dw2_doc à SPIP
| - Déclarer BOUCLE et BALISES
| - Filtres
+--------------------------------------------+
*/

#
# Les tables de DW2
#
include_spip('base/dw2_tables');


#
# Boucle et Balises
#
include_spip('balise/dw2_boba');


/*
| h.03/03/07 - sur une idee orig. J. Larmarange
| redeclarer #URL_DOCUMENT
| Eviter modification de squelette (voir config)
*/
function balise_URL_DOCUMENT($p) {
	if($GLOBALS['forcer_url_dw2']=="oui") {
		return balise_URL_DOC_OUT($p);
	} else {
		return balise_URL_DOCUMENT_dist($p);
	}
}


#
# Filtres
#

/*
+--------------------------------------------+
| FILTRE dw_redir
+--------------------------------------------+
| Réécrit balise "<a href= ... > contenant 
| un chemin du type : IMG/nnn/monfichier.nnn,
| (pour les entrée <docxxx|nnn>, champs #TEXTE)
| par url action dw2_out.
+--------------------------------------------+
| appelle dans les squelettes : [(#TEXTE|dw_redir)]
+--------------------------------------------+
*/
function dw_redir($texte) {
	# doc local IMG
	$expres = "!(<a\ href=\")(IMG/([[:alnum:]]+)/[a-zA-Z0-9_-]+\.\\3)(\"\ (.*)>)!";
	if (preg_match_all($expres, $texte, $reg, PREG_SET_ORDER)) {
		
		foreach($reg as $inreg) {
			$sup_chaine=$inreg[5];
			# temporaire a peaufiner car : si on veux mettre compteur sur image en <docxxx|...> ?? hein ?
			#h.24/11 .. ne pas modifier si type=image/...
			if(ereg("type=\"image\/",$inreg[5])) {
				$retour = $inreg[0];
			}
			else {
				$chem=$inreg[2];
				$res=spip_query("SELECT id_document FROM spip_documents WHERE fichier='$chem'");
				while($row=spip_fetch_array($res)) {
					$id = $row['id_document'];
					$retour = $inreg[1]."?action=dw2_out&id=$id".$inreg[4];
				}
				$texte = str_replace($inreg[0], $retour, $texte);
			}
		}
	}


	// et distant ...
	$expres2 = "!(<a\ href=\")([http://|ftp://]([a-zA-Z0-9/_-]+))(\"\ (.*)>)!";
	if(preg_match_all($expres2, $texte, $reg2, PREG_SET_ORDER)) {
		foreach($reg2 as $in_reg) {
			$chemin=$inreg[2];
			$res2=spip_query("SELECT id_document FROM spip_documents WHERE fichier='$chemin'");
			while($row2 = spip_fetch_array($res2)) {
				$id = $row2['id_document'];
				$replace = $in_reg[1]."?action=dw2_out&id=$id".$in_reg[4];
			}
			$texte = str_replace($in_reg[0], $replace, $texte);
			
			# modif title et alt
			$expres3="!".$chemin."!";
			if(preg_match_all($expres3, $texte, $elems, PREG_SET_ORDER)) {
				foreach($elems as $elem) {
					$fichier=basename($elem[0]);
				}
				$texte = str_replace($elem[0], $fichier, $texte);
			}
		
		}
	}
	return $texte;
}
// fin dw_redir

/*
+--------------------------------------------+
| FILTRE longmot
+--------------------------------------------+
| inserer un 'espace' dans une chaine longue.
| par defaut insertion tous les 30 caracteres.
| dw2 - 2.13 - 01/2007
+--------------------------------------------+
| appelle dans les squelettes : [(#NOM_DOC|longmot{nnn})]
+--------------------------------------------+
*/
function longmot($texte,$longueur='') {
	if(!$longueur) { $longueur = '30'; }
	return wordwrap($texte,$longueur,' ',1);
}

?>
