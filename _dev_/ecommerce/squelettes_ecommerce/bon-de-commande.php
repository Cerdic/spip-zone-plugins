<?php
// Indispensable pour utiliser tout ce qui est relatif aux sessions
session_start();
//_____________________________________________
//
// --> SI ON VIDE LE DEVIS, QUE FAIRE ? ON VIDE LA VARIABLE DE SESSION
//_____________________________________________
//Si on a cliqué sur "Vider" pour vider la variable de session
if ($_POST['detruire2']!=''){
		session_unset();
		session_unregister('bon-de-commande');
		
}
//*****************************************
//
// ----> suppression d'un item dans le devis
//
//*****************************************
if(($_GET['action']=='delete')&&(is_numeric($_GET['id_item']))){
// on s'assure de la validité de la variable en fixant son type à integer
$id = $_GET['id_item'];
settype($id,'integer');
//suppression de l'item
unset($_SESSION['bon-de-commande'][$id]);
}

		//_________________________________________________________________________
		//
		// --->> AJOUT DE LA DERNIERE BOUTEILLE ET DE SA QUANTITE SI EXISTE !
		//_________________________________________________________________________
		if(($_POST['id_dernier']!='')&&($_POST['qte_dernier']!='')){
				$dernier = $_POST['id_dernier'];
				$_SESSION['bon-de-commande'][$dernier] = $_POST['qte_dernier'];
		}
		//_________________________________________________________________________
		//
		// --->> SI mise à jour demandée ALORS
		// 		On parcourt le tablau : 
		//				SI il l'id de la case en cours existe et correspond au nom de du champ qui a été posté ALORS
		//					on met à  jour la quantité pour cette case
		// L'astuce utilisée ici est en fait que chaque champ texte où est affiché la quantité a comme nom l'id de l'article auquel il correspond
		// Ainsi si à un champ [id] correspond une quantité, et donc on se sert de l'id pour mettre à jour la quantié correspondante
		// J'utilise donc le tableau des ids des articles de la session, et pour chacun de ces id, je met à jour sa quantité, la quantité étant 
		// stockée dans une variable postée dont le nom est en fait l'id de l'article
		// Je met donc à jour pour tous les ids de la session
		//_________________________________________________________________________
		if($_POST['update']!=''){
			$keys = array_keys($_SESSION['bon-de-commande']);
			for($i=0; $i <= count($keys)-1; $i ++) {
					$key = $keys[$i];
					$_SESSION['bon-de-commande'][$key] = $_POST[$key];
			}
		}
		//_________________________________________________________________________
		//
		// --->> FIN MISE A JOUR 
		//_________________________________________________________________________
				// On crée un tableau ne contenant que les ids des articles sélectionnés
				$array_articles = array_keys($_SESSION['bon-de-commande']);
				//On convertit le tableau en chaine, les éléments étant séparés par un trait |
				$str = implode('|', $array_articles);
				// on récupère le dernier caractere de la chaine, et s'il s'agit d'unt trait et bien on le retire 
				// si on ne le retire pas,  cela fait planter SPIP qui cherche un truc vide
				$last = $str{strlen($str)-1};
				if ($last=='|'){
					$str = substr($str, 0,-1);
				}
				//la variable qui va être utilisée dans la boucle 
				$_GET['mes_ids'] ='^('. $str.')$';
//-------------------------------------
// PROPRE A SPIP
//-------------------------------------
$fond = "bdc";
$delais = 0;
include ("inc-public.php3");
?>
