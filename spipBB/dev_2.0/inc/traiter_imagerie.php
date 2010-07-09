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



// ------------------------------------------------------------------------------
// scoty - 27/11/07
// ex spipbb_auteur_infos // ex gaf_auteur_infos
// infos recuperables pour auteur : $id (table)
// appel : ici, formulaire_spipbb_profil, spipbb_inscrits, spipbb_notifications
// ------------------------------------------------------------------------------
function spipbb_donnees_auteur($id) {
	$chps_sup=array();// c: 10/2/8 compat multibases
	$left_join='';
	$infos=array();
	$liste_chps = array();

	$type_support = lire_config('spipbb/support_auteurs');
	$table_support = lire_config('spipbb/table_support');

	foreach($GLOBALS['champs_sap_spipbb'] as $ch => $t) {
		$liste_chps[]=$ch;
	}

	if($type_support=="table") {
		foreach($liste_chps as $champ) {
			// c: 10/2/8 compat multibases
			//$chps_sup.= ", sap.".$champ;
			$chps_sup[]= "sap.".$champ;
		}
		$left_join = "LEFT JOIN spip_$table_support AS sap ON sa.id_auteur = sap.id_auteur ";
	}

	$result = sql_select(array_merge(array("sa.id_auteur","sa.statut","sa.nom","sa.login","sa.source","sa.extra"),$chps_sup),
						"spip_auteurs AS sa $left_join",
						"sa.id_auteur=$id");
	if($row = sql_fetch($result)) {
		foreach($row as $k => $v) {
			if($k=='extra') {
				# extraire extra
				# c: 4/2/8 il reste un bug ici parfois il est impossible de faire unserialize
				$v=preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.strlen('$2').':\"$2\";'", $v );
				$v=stripslashes($v);
				if (empty($v)) $chps_extra = array();
					else  if (!($chps_extra = unserialize($v))) {
						spipbb_log("ERREUR unserialize : v(".gettype($v)."):".$v.":",1,__FILE__);
						$chps_extra = array();
					}
				# on extrait champs gaf que si support : extra
				if($type_support=='extra') {
					foreach($liste_chps as $c) {
						if (isset($chps_extra[$c]))
							$infos[$c]=$chps_extra[$c];
					}
				}
				if (!is_array($chps_extra)) spipbb_log("ERREUR chps_extra no array : ".gettype($chps_extra).":v:".$v,1,__FILE__);
				# tous les autres extra
				foreach($chps_extra as $cle => $ve) {
					# recup tous extra sauf gaf
					if(!in_array($cle,$liste_chps)) {
						$infos[$cle]=$ve;
					}
				}
			}
			else {
				$infos[$k]=$v;
			}
		}
	}
	return $infos; # array: statut, nom .. avatar (et +)
} // spipbb_donnees_auteur


// ------------------------------------------------------------------------------
// spipbb_afficher_avatar (back ou front)
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function spipbb_afficher_avatar($id, $classe='')
{
	if(!$id) { return; }
	#recup de statut et extra/avatar
	$infos = spipbb_donnees_auteur($id);

	$insert = '';
	$retour = '';

	if ($classe!='') { $insert=" class=\"$classe\""; }

	if ($infos['statut']=="0minirezo" OR $infos['statut']=="1comite") {
		$chercher_logo = charger_fonction('chercher_logo', 'inc');
		if($logo = $chercher_logo($id, 'id_auteur', 'on')) {
			$retour="<img".$insert." src=\"$logo[0]\" alt=\"logo\" />";
		}
	}
	elseif($infos['statut']=="6forum" AND $infos['avatar']!='') {
			$retour="<img".$insert." src=\"".$infos['avatar']."\" alt=\"avatar\" />";
	}
	return $retour;
} // spipbb_afficher_avatar


// ------------------------------------------------------------------------------
// traitement balise affiche_avatar / + fonction back
// Fonction relais Affichage Avatar
// Soit appel fonction perso :
// [ inc_afficher_avatar() dans fichier inc/afficher_avatar.php ].
// Soit fonction GAF : spipbb_afficher_avatar()
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function afficher_avatar($id_auteur, $classe='')
{
	$f='';
	if(lire_config('spipbb/affiche_avatar')=="oui") {
		#si modeles/avatar.html present
		if($av = find_in_path("modeles/avatar.html")) {
			$f = recuperer_fond('modeles/avatar',
					array('id_auteur' => $id_auteur, 'classe' => $classe));
		}
		# si function inc_afficher_avatar(...) dans inc/afficher_avatar.php
		# sinon GAF fait son office
		else {
			$f = charger_fonction('afficher_avatar', 'inc', true);
			if(!$f) { $f='spipbb_afficher_avatar'; }
			return $f($id_auteur, $classe);
		}
	}
	return $f;
} // afficher_avatar



// ------------------------------------------------------------------------------
// traitement back / balise signature_post
// GAF v.0.6 - 12/10/07
// ------------------------------------------------------------------------------
function spipbb_afficher_signature_post($id_auteur)
{
	#recup de statut et extra/avatar
	$infos = spipbb_donnees_auteur($id_auteur);
	if(isset($infos['signature_post']) AND $infos['signature_post']!='') {
		return propre($infos['signature_post']);
	}
	return;
} // spipbb_afficher_signature_post


// ------------------------------------------------------------------------------
// relais
// ------------------------------------------------------------------------------
// GAF v.0.6 - 4/10/07
// ------------------------------------------------------------------------------
function afficher_signature_post($id_auteur)
{
	$f='';
	if(lire_config('spipbb/affiche_signature_post')=='oui' AND $id_auteur!=0) {
		if($av = find_in_path("modeles/signature_post.html")) {
			$f = recuperer_fond('modeles/signature_post',
					array('id_auteur' => ".$_id_auteur.",'classe' => '$_classe'));
		}
		# si function inc_afficher_signature(...) dans inc/afficher_signature.php
		# sinon GAF fait son office
		else {
			$f = charger_fonction('afficher_signature_post', 'inc', true);
			if(!$f) { $f='spipbb_afficher_signature_post'; }
			return $f($id_auteur);
		}
	}
	return $f;
} // afficher_signature_post


// ------------------------------------------------------------------------------
// generique : balise #SPIPBB{champ}, donnee brut (->formulaire)
// GAF v.0.6 - 12/10/07
// ------------------------------------------------------------------------------
function afficher_champ_spipbb($id_auteur,$champ)
{
	$infos = spipbb_donnees_auteur($id_auteur);
	if(isset($infos[$champ]) AND $ch=$infos[$champ]) {
		return $ch;
	}
	return;
} // afficher_champ_spipbb


?>
