<?php
/*
+-------------------------------------------+
| filtre smileys.. , tableau_smileys (backoffice)
| + genere balise tableau_smileys (frontoffice)
| + fonctions balise affiche_avatar (back and front)
| + fonctions balise signature_post (back and front)
| + fonction generique
+-------------------------------------------+
*/

## h.
# Fonctions ici because appel back et front
# si 192, OK car appel spipbb_192 dans spipbb_fonctions ou fichier exec
##
if (!defined("_INC_SPIPBB_COMMON")) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);

/*
+----------------------------------+
| Lister un repert de smileys passer en arg
+----------------------------------+
| d'apres BoOz spipBB
+----------------------------------+
*/
function genere_list_smileys($repert) {
	$listimag=array();
	$listfich=opendir($repert);
	while ($fich=@readdir($listfich)) {
		if(($fich !='..') and ($fich !='.') and ($fich !='.test') and ($fich !='.svn')) {
			$nomfich=substr($fich,0,strrpos($fich, "."));
			$listimag[$nomfich]=$repert.$fich;
		}
	}
	ksort($listimag);
	reset($listimag);
	return $listimag;
}

/*
+----------------------------------+
| filtre smileys
| Sur base filtre smileys2 - Booz
| Recup les smileys du repert smileys/ ou mes_smileys/
| Exemple d'application : [(#TEXTE|smileys)]
+----------------------------------+
| Scoty GAF v.0.6 - 30/09/07
+----------------------------------+
*/
function smileys($chaine) 	{
	$dirbase = _DIR_PLUGIN_SPIPBB."smileys/";
	##$dirbase = _DIR_SMILEYS_SPIPBB;
	$listsmil = genere_list_smileys($dirbase);

	# h. indispensable !! pour gerer le changement de repertoire
	# en cours de route, donc tous les smileys dispo
	if(_DIR_SMILEYS_SPIPBB!=$dirbase) {
		$listperso=genere_list_smileys(_DIR_SMILEYS_SPIPBB);
		$listsmil = array_merge($listsmil, $listperso); // array verifies
	}

	while (list($nom,$chem) = each($listsmil)) {
		$smil_html = "<img src='".$chem."' style='border:0' title='".$nom."' alt='smil' align='baseline' />";
		$chaine = str_replace(":".$nom, $smil_html , $chaine);
	}
	return $chaine;
}

/*
+----------------------------------+
| Generer le tableau de smileys,
| pour formulaires de post (back/frontoffice).
| Voir balise ci-apres
+----------------------------------+
| Scoty GAF v.0.6 - 30/09/07
+----------------------------------+
*/
function tableau_smileys($cols='',$return=true) {
	$listimag = genere_list_smileys(_DIR_SMILEYS_SPIPBB);
	// nombre de colonnes (2 par défaut) (pas trop large pour GAF ! !!) c: 23/12/7 Il vaut mieux 3 ou reduire l'espace disponible ou le nombre d'emoticons
	if($cols=='') { $cols=3; }
	$compte=0;
	$champ='texte';

	$aff = "<table width='100%' cellspacing='0' cellpadding='1' border='0'><tr>\n";
	while (list($nom,$chem) = each($listimag)) {
		$aff.= "<td style='vertical-align:bottom' class='verdana1'><div style='text-align:center'>\n
			<a href=\"javascript:emoticon(':$nom',document.getElementById('$champ'))\">\n
			<img src='".$chem."' style='border:0' title='smiley - ".$nom."' alt='smil' />\n
			</a></div></td>\n";

		$compte++;
		if ($compte % $cols == 0) { $aff.= "</tr><tr>\n"; }
	}
	$aff.= "</tr></table>\n";

	if($return) { return $aff; } else { echo $aff; }
}





?>
