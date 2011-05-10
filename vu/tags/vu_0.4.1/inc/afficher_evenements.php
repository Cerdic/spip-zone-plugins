<?php

/* 	On trouve ici la fonction 'afficher_objets' appelees par 	*/
/* 	'veille_tous' : permet de lister tous les objets, avec leur	*/
/* 	titre, leur statut, leur date, leur id et le lien vers la  	*/
/*	 page 'veille_voir'						*/

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS['my_sites']=array();

//
// Prechargement : quelques fonctions secondaires d'affichage
///

// libelle du titre de l'objet
function vu_afficher_titre_evenement($type,$row){
	$titre = $row['titre'];
	return array(typo(supprime_img($titre,'')),'');
}

// affichage de la puce
function vu_puce_statut_evenement($id, $statut, $id_rubrique, $type, $ajax='') {

	global $lang_objet;
	static $coord = array('publie' => 1,
			      'prop' => 0,
			      'refuse' => 2,
			      'poubelle' => 3);

	$lang_dir = lang_dir($lang_objet);
	$puces = array(
		       0 => 'puce-orange-breve.gif',
		       1 => 'puce-verte-breve.gif',
		       2 => 'puce-rouge-breve.gif',
		       3 => 'puce-blanche-breve.gif');

	switch ($statut) {
		case 'prop':
			$clip = 0;
			$puce = $puces[0];
			$title = _T('titre_breve_proposee');
			break;
		case 'publie':
			$clip = 1;
			$puce = $puces[1];
			$title = _T('titre_breve_publiee');
			break;
		case 'refuse':
			$clip = 2;
			$puce = $puces[2];
			$title = _T('titre_breve_refusee');
			break;
		default:
			$clip = 0;
			$puce = $puces[3];
			$title = '';
	}

	$type1 = "statut$type$id"; 
	$inser_puce = http_img_pack($puce, $title, "id='img$type1' style='margin: 1px;'");

	// Test d'autorisation
	if (!autoriser('publierdans','rubrique',$id_rubrique)
		// Si on ne possede pas les autorisations requise,
	OR !_ACTIVER_PUCE_RAPIDE)
		// ou si les puces rapides sont desactivees
		return $inser_puce;
		// alors on se contente d'afficher la puce

	$titles = array(
			  "blanche" => _T('texte_statut_en_cours_redaction'),
			  "orange" => _T('texte_statut_propose_evaluation'),
			  "verte" => _T('texte_statut_publie'),
			  "rouge" => _T('texte_statut_refuse'),
			  "poubelle" => _T('texte_statut_poubelle'));
			  
	$clip = 1+ (11*$coord[$statut]);

// Attention  : Dans ce bloc on trouve une erreur sur l'affichage de la popup de changement de statut
/*	if ($ajax){
		// Si Ajax est active 
		return "<span class='puce_breve_fixe'>"
		. $inser_puce
		. "</span>"
		. "<span class='puce_breve_popup' id='statutdecal$type$id' style='margin-left: -$clip"."px;'>"
		. afficher_script_statut($id, $type, -1, $puces[0], 'prop', $titles['orange'])
		. afficher_script_statut($id, $type, -10, $puces[1], 'publie', $titles['verte'])
	  	. afficher_script_statut($id, $type, -19, $puces[2], 'refuse', $titles['rouge'])
		. "</span>";
	} 

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX) AND $type != 'evenement') 	
		// Si Ajax n'est pas active ET que l'objet n'est pas une evenement
		$over ='';
	else {
		// Dans tous les autres cas (la grande majorite)
		$action = generer_url_ecrire('puce_statut',"",true);
		$action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
		$over = "\nonmouseover=\"$action\"";
		echo "<br />nom:".$nom." - type:".$type." - id:".$id." - action:".$action." - over:".$over."<br />";
	}
*/
// Fin du bloc qui poserait le probleme

	return 	"<span class='puce_$type' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. "</span>";

}

//
// Fonction principale d'affichage
//

// Est executee en boucle autant de fois que d'objets listes
// Indispensable pour l'affichage de l'ID et de la puce dans cette liste.
// En cas de dysfonctionnement la fonction generique 'afficher_objet_boucle'
// du core prends le relais, de façon simplifiee toutefoispuc.

function afficher_evenements_boucle($row, $own)
{
	global $connect_statut, $spip_lang_right;
	static $chercher_logo = true;

	list($afficher_langue, $affrub, $langue_defaut, $formater,$type,$primary) = $own;
	$vals = array();

	$primary = "id_evenement";		// On force $primary. Semble poser probleme sinon : elle n'est pas definie.

	$id_objet = $row[$primary]; 		
	if (autoriser('voir',$type,$id_objet)){
		
		// Le contenu de chaque objet est mis en variable (date, statut, logo, titre...)
		$date_heure = isset($row['date'])?$row['date']:(isset($row['date_heure'])?$row['date_heure']:"");

		$statut = isset($row['statut'])?$row['statut']:"";
		if (isset($row['lang']))
		  changer_typo($lang = $row['lang']);
		else $lang = $langue_defaut;
		$lang_dir = lang_dir($lang);
		$id_rubrique = isset($row['id_rubrique'])?$row['id_rubrique']:0;


		// Affichage du statut (fonction definie au-dessus)
		//$puce_statut = charger_fonction('puce_statut', 'inc');
		//$vals[] = $puce_statut($id_objet, $statut, $id_rubrique, $type);
		$vals[] = vu_puce_statut_evenement($id_objet, $statut, $id_rubrique, $type);

		// Affichage du titre (fonction definie au-dessus)
		list($titre,$suite) = vu_afficher_titre_evenement($type,$row);
		$flogo = '';
		
		// Afficher le lien et le titre de l'objet (fonction 'lien' definie au-dessus)
		if ($titre) {
			$titre = "<a href='"
			. generer_url_ecrire('veille_voir',"id_$type=$id_objet")
			.  "'>"
			. $titre
			. "</a>";
		}
		$vals[] = "\n<div>$flogo$titre$suite</div>";

		// Sens d'affichage selon langue
		$s = "";
		if ($afficher_langue){
			if (isset($row['langue_choisie'])){
				$s .= " <span class='spip_xx-small' style='color: #666666' dir='$lang_dir'>";
				if ($row['langue_choisie'] == "oui") $s .= "<b>".traduire_nom_langue($lang)."</b>";
				else $s .= "(".traduire_nom_langue($lang).")";
				$s .= "</span>";
			}
			elseif ($lang != $langue_defaut)
				$s .= " <span class='spip_xx-small' style='color: #666666' dir='$lang_dir'>".
					($lang
						? "(".traduire_nom_langue($lang).")"
						: ''
					)
				."</span>";
		}
		$vals[] = $s;

		// Affichage de la date
		if ($statut){
			if ($statut != "prop")
					$s = affdate_jourcourt($date_heure);
				else
					$s .= _T('info_a_valider');
		}
		$vals[] = $s;

		$vals[] = afficher_numero_edit($id_objet, $primary, $type, $row);
	}
	return $vals;

}

?>
