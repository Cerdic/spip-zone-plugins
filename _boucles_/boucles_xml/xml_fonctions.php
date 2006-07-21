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
	$_xml = '';
	foreach($boucle->where as $w){
		if ($w[0]=="'='" && $w[1]=="'xml.xml'")
		{
			$_xml=$w[2];
			break;
		}
	}
	if ($_xml==''){
		$champ = new Champ;
		$champ->nom_champ = 'xml';
		$_xml = calculer_liste(array($champ),array(), $boucles, $boucle->$id_boucle);
	}
	if ($_xml!='')
	$boucle->hash = "
	// CREER la table temporaire xml et la peupler avec le resultat du parser
	if (is_string(\$x=$_xml))
		xml_fill_table_temporaire_boucle(\$x);
";
	return calculer_boucle($id_boucle, $boucles); 
}

function extraire($attributs,$nom){
	return extraire_attribut("<fake $attributs>",$nom);
}
?>