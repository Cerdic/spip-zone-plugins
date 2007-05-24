<?php
function confirmation()
	$id = $_GET['id'];
	$email = $_GET['email'];
	$mode = $_GET['mode'];
	$cle = _GET['cle'];
	$q = spip_query("SELECT email, statut, alea_actuel FROM spip_auteurs WHERE id_auteur = '$id'");
	$q = spip_fetch_array($q);
	if($email == $q['email'] and $q['statut'] == 'aconfirmer' and $mod == 'conf' /**and $cle ==  qqchose**/){
		echo "<html><head><title>Page de confirmation de l\'inscription</title></head>
		  <body><h1>Choissisez votre mot de passe</h1>
		  <form method='post' action='#SELF'>
		  Inserez votre mot de passe&nbsp;:
		  <input type='text' name='pass' id='pass'>
		  Reinserez votre mot de passe&nbsp;:
		  <input type='text' name='pass2' id='pass2'>
		  <input type='submit' value='<:bouton_valider:>'/>
		  </body></html>";
		$pass = $_POST['pass'];
		$pass2 = $_POST['pass2'];
		$mdpass = md5($pass);
		$mdpass2 = md5($pass2);
		if($mdpass == $mdpass2){
			$htpass = generer_htpass($pass);
			spip_query("UPDATE spip_auteurs SET statut = 'visiteur', pass='$mdpass', htpass='$htpass' WHERE id_auteur = ".intval($id_auteur));
		}else{
			echo "probleme";
		}
	}elseif($email == $q['email'] and $q['statut'] == 'aconfirmer' and $mod == 'sup' /**and $cle ==  qqchose**/){
		spip_query("DELETE FROM spip_auteurs WHERE id_auteur = '$id'");
		spip_query("DELETE FROM spip_auteurs_elargis WHERE id_auteur = '$id'");
		/**affichage du message de confirmation de suppression*//
	}



	
?>