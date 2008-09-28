<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2008                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_guestbook_charger() {
return true;
}
function formulaires_guestbook_verifier(){
return true;
}


function formulaires_guestbook_traiter() {

	include_spip('base/abstract_sql');
$maj    = date('Y-m-d H:i:s');
$nom	= $_POST['nom'];
$ville	= $_POST['ville'];
$email	= $_POST['email'];
$note	= $_POST['note'];
$texte	= $_POST['texte'];
$texte=nl2br($texte); 
sql_insertq("spip_livre", array('email' => $email, 'nom' => $nom, 'ville' => $ville, 'maj' => $maj, 'note' => $note, 'texte' => $texte));
echo"Merci de votre participation! Le message apparaitra dès que le webmaster aura répondu.";
}


?>