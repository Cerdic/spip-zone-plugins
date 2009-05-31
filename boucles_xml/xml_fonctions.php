<?php
/*
 * Boucle xml
 * 
 *
 * Auteur :
 * Cedric Morin
 * © 2006 - Distribue sous licence GNU/GPL
 *
 */
include_spip('base/xml_temporaire');
function boucle_XML_dist($id_boucle, &$boucles) {
	$boucle = &$boucles[$id_boucle];
	$id_table = $boucle->id_table;
	$boucle->from[$id_table] =  "spip_xml";
	$boucle->select[] =  $boucle->id_table.".xpath";
	
	// on regarde dans les where si xml est specifie explicitement
	$_xmlcode = '';
	$_xml = '';
	foreach($boucle->where as &$w){
		if ($w[0]=="'='" && $w[1]=="'xml.xml'") {
			$_xml=$w[2];
			if ($_xml==''){
				$champ = new Champ;
				$champ->nom_champ = 'xml';
				$_xml = calculer_liste(array($champ),array(), $boucles, $boucle->$id_boucle);
			}
			break;
		} elseif ($w[0]=="'='" && $w[1]=="'xml.texte'") {
			// on recupere l'argument du critere
			$_xmlcode=$w[2];
			if(substr($_xmlcode, 0, 3)=='_q(') {
				$_xmlcode= substr($_xmlcode, 3, -1);
			}
			// puis on tripote ce critere pour qu'il trouve ce qu'on veut
			$w[1]="'xml.xml'";
			// le nom du fichier etant alors remplace par un hash du texte
			$w[2]="'\\''.md5($_xmlcode).'\\''";
			break;
		}
	}
	if ($_xml!='') {
		$boucle->hash = "
	// CREER la table temporaire xml et la peupler avec le resultat du parser
	if (is_string(\$x=$_xml))
		xml_fill_table_temporaire_boucle(\$x);
";
	} elseif ($_xmlcode!='') {
		$boucle->hash = "
	// CREER la table temporaire xml et la peupler avec le resultat du parser
	if (is_string(\$x=$_xmlcode))
		xml_fill_table_temporaire_boucle(\$x, true);
";
	}
	return calculer_boucle($id_boucle, $boucles); 
}

function extraire($attributs,$nom){
	return extraire_attribut("<fake $attributs>",$nom);
}

?>