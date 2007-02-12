<?php

  /** il doit y avoir des cas ou cette fonction peut être appelée d'une page
   * en cache => la fonction doit être dans le <options> et pas dans
   * le <fonctions>
   */
function compteur_plus($donnee, $id, $categ, $montant) {
	$row= spip_abstract_fetsel('1', 'compteurs',
					array(array('=', 'type', "'$donnee'"),
						  array('=', 'id', "'$id'"),
						  array('=', 'categ', "'$categ'")));
	if($row!==false) {
		spip_query("update compteurs set total=total+$montant, nb=nb+1 where type='$donnee' and id='$id' and categ='$categ'");
	} else {
		spip_query("insert into compteurs values ('$donnee','$id','$categ',$montant,1)");
	}
	return "";
}

?>
