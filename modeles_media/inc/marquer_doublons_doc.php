<?php
/*
 * Surcharge pour identifier également les modèles <media> en attendant une solution plus élégante (pipeline adéquat par exemple)
 *
 */

// On liste tous les champs susceptibles de contenir des documents ou images si on veut que ces derniers soient lies a l objet lorsqu on y fait reference par imgXX docXX ou embXX ou mediaXX
$GLOBALS['gestdoc_liste_champs'][] = 'texte';
$GLOBALS['gestdoc_liste_champs'][] = 'chapo';
 
// http://doc.spip.org/@marquer_doublons_documents
function inc_marquer_doublons_doc($champs,$id,$type,$id_table_objet,$table_objet,$spip_table_objet, $desc=array(), $serveur=''){
	$champs_selection=array();
		foreach ($GLOBALS['gestdoc_liste_champs'] as $champs_choisis) {
			if ( isset($champs[$champs_choisis]) )
			array_push($champs_selection,$champs_choisis);
		}
	if (count($champs_selection) == 0)
		return;
	if (!$desc){
		$trouver_table = charger_fonction('trouver_table', 'base');
		$desc = $trouver_table($table_objet, $serveur);
	}
	$load = "";
	// charger le champ manquant en cas de modif partielle de l	'objet
	// seulement si le champ existe dans la table demande
	
		foreach ($champs_selection as $champs_a_parcourir) {
			if (isset($desc['field'][$champs_a_parcourir])) {
			$load = $champs_a_parcourir;
			$champs_a_traiter .= $champs[$champs_a_parcourir];
			}
		}	

	if ($load){
		$champs[$load] = "";
		$row = sql_fetsel($load, $spip_table_objet, "$id_table_objet=".sql_quote($id));
		if ($row AND isset($row[$load]))
			$champs[$load] = $row[$load];
	}
	include_spip('inc/texte');
	include_spip('base/abstract_sql');
	$GLOBALS['doublons_documents_inclus'] = array();
	media_traiter_modeles($champs_a_traiter,true); // detecter les doublons avec une version modifiée de traiter_modeles
	sql_updateq("spip_documents_liens", array("vu" => 'non'), "id_objet=$id AND objet=".sql_quote($type));
	if (count($GLOBALS['doublons_documents_inclus'])){
		// on repasse par une requete sur spip_documents pour verifier que les documents existent bien !
		$in_liste = sql_in('id_document',
			$GLOBALS['doublons_documents_inclus']);
		$res = sql_select("id_document", "spip_documents", $in_liste);
		while ($row = sql_fetch($res)) {
			// Creer le lien s'il n'existe pas deja
			sql_insertq("spip_documents_liens", array('id_objet'=>$id, 'objet'=>$type, 'id_document' => $row['id_document'], 'vu' => 'oui'));
			sql_updateq("spip_documents_liens", array("vu" => 'oui'), "id_objet=$id AND objet=".sql_quote($type)." AND id_document=" . $row['id_document']);
		}
	}
}

// Hack pour identifier aussi les modèles <media>

function media_traiter_modeles($texte, $doublons=false, $echap='', $connect='', $liens = null) {
	// preserver la compatibilite : true = recherche des documents
	if ($doublons===true)
		$doublons = array('documents'=>array('doc','emb','img','image','audio','application','video','text','media'));
	// detecter les modeles (rapide)
	if (strpos($texte,"<")!==false AND
	  preg_match_all('/<[a-z_-]{3,}\s*[0-9|]+/iS', $texte, $matches, PREG_SET_ORDER)) {
		include_spip('public/assembler');
		foreach ($matches as $match) {
			// Recuperer l'appel complet (y compris un eventuel lien)

			$a = strpos($texte,$match[0]);
			preg_match(_RACCOURCI_MODELE_DEBUT,
			substr($texte, $a), $regs);
			$regs[]=""; // s'assurer qu'il y a toujours un 5e arg, eventuellement vide
			list(,$mod, $type, $id, $params, $fin) = $regs;
			if ($fin AND
			preg_match('/<a\s[^<>]*>\s*$/i',
					substr($texte, 0, $a), $r)) {
				$lien = array(
					'href' => extraire_attribut($r[0],'href'),
					'class' => extraire_attribut($r[0],'class'),
					'mime' => extraire_attribut($r[0],'type')
				);
				$n = strlen($r[0]);
				$a -= $n;
				$cherche = $n + strlen($regs[0]);
			} else {
				$lien = false;
				$cherche = strlen($mod);
			}

			// calculer le modele
			# hack articles_edit, breves_edit, indexation
			if ($doublons)
				$texte .= preg_replace(',[|][^|=]*,s',' ',$params);
			# version normale
			else {
				// si un tableau de liens a ete passe, reinjecter le contenu d'origine
				// dans les parametres, plutot que les liens echappes
				if (!is_null($liens))
					$params = str_replace($liens[0], $liens[1], $params);
			  $modele = inclure_modele($type, $id, $params, $lien, $connect);
				// en cas d'echec, 
				// si l'objet demande a une url, 
				// creer un petit encadre vers elle
				if ($modele === false) {
					if (!$lien)
						$lien = traiter_lien_implicite("$type$id", '', 'tout', $connect);
					if ($lien)
						$modele = '<a href="'
						  .$lien['url']
						  .'" class="spip_modele'
						  . '">'
						  .sinon($lien['titre'], _T('ecrire:info_sans_titre'))
						  ."</a>";
					else {
						$modele = "";
						if (test_espace_prive()) {
							$modele = entites_html(substr($texte,$a,$cherche));
							if (!is_null($liens))
								$modele = "<pre>".str_replace($liens[0], $liens[1], $modele)."</pre>";
						}
					}
				}
				// le remplacer dans le texte
				if ($modele !== false) {
					$modele = protege_js_modeles($modele);
					$rempl = code_echappement($modele, $echap);
					$texte = substr($texte, 0, $a)
						. $rempl
						. substr($texte, $a+$cherche);
				}
			}

			// hack pour tout l'espace prive
			if (((!_DIR_RESTREINT) OR ($doublons)) AND ($id)){
				foreach($doublons?$doublons:array('documents'=>array('doc','emb','img','image','audio','application','video','text','media')) as $quoi=>$modeles)
					if (in_array($type,$modeles))
						$GLOBALS["doublons_{$quoi}_inclus"][] = $id;
			}
		}
	}

	return $texte;
}


?>