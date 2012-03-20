<?php
	//Charger SPIP
	if (!defined('_ECRIRE_INC_VERSION')) {
		$currentdir = getcwd();
		// recherche du loader SPIP.
		$deep = 2;
		$lanceur ='ecrire/inc_version.php';
		$include = '../../'.$lanceur;
		while (!defined('_ECRIRE_INC_VERSION') && $deep++ < 6) { 
			// attention a pas descendre trop loin tout de meme ! 
			// plugins/zone/stable/nom/version/tests/ maximum cherche
			$include = '../' . $include;
			if (file_exists($include)) {
				chdir(dirname(dirname($include)));
				require $lanceur;
			}
		}	
	}
	if (!defined('_ECRIRE_INC_VERSION')) {
		die("<strong>Echec :</strong> SPIP ne peut pas etre demarre.<br />
			Vous utilisez certainement un lien symbolique dans votre repertoire plugins.");
	}	

	// N° de Marchand
	$merchant_id = "xxxxxxxxxxxxxxx";
	
	session_start();
	
	$total = $_SESSION['total'];
		
	$total *= 100;
	
	
	print ("<HTML><HEAD><TITLE>ATOS - Paiement Securise sur Internet</TITLE></HEAD>");
	print ("<BODY bgcolor=#ffffff>");
	print ("<Font color=#000000>");
	print ("<center><H1>PAIEMENT SECURISE ATOS </H1></center><br><br>");
	print ("<center><H1>" . $GLOBALS['meta']['nom_site'] . "</H1></center><br><br>");

	//		Affectation des paramètres obligatoires

	$parm="merchant_id=$merchant_id";
	$parm="$parm merchant_country=fr";
	$parm="$parm amount=$total";
	$parm="$parm currency_code=978";


	// Initialisation du chemin du fichier pathfile (à modifier)
    //   ex :
    //    -> Windows : $parm="$parm pathfile=c:\\repertoire\\pathfile";
    //    -> Unix    : $parm="$parm pathfile=/home/repertoire/pathfile";
    //
    // Cette variable est facultative. Si elle n'est pas renseignée,
    // l'API positionne la valeur à "./pathfile".

		$parm="$parm pathfile=conf/pathfile";

	//		Si aucun transaction_id n'est affecté, request en génère
	//		un automatiquement à partir de heure/minutes/secondes
	//		Référez vous au Guide du Programmeur pour
	//		les réserves émises sur cette fonctionnalité
	//
	
	#$parm="$parm transaction_id=" . urlencode($_SESSION['ref']);
	
	$path_bin = "bin/request";


	//	Appel du binaire request
	chdir($currentdir); // Il faut revenir dans le dossier du script de paiement pour trouver les binaires !!!!
	$result=exec("$path_bin $parm");

	//	sortie de la fonction : $result=!code!error!buffer!
	//	    - code=0	: la fonction génère une page html contenue dans la variable buffer
	//	    - code=-1 	: La fonction retourne un message d'erreur dans la variable error

	//On separe les differents champs et on les met dans une variable tableau

	$tableau = explode ("!", "$result");

	//	récupération des paramètres

	$code = $tableau[1];
	$error = $tableau[2];
	$message = $tableau[3];

	//  analyse du code retour

  if (( $code == "" ) && ( $error == "" ) )
 	{
  	print ("<BR><CENTER>erreur appel request</CENTER><BR>");
  	print ("executable request non trouve $path_bin");
 	}

	//	Erreur, affiche le message d'erreur

	else if ($code != 0){
		print ("<center><b><h2>Erreur appel API de paiement.</h2></center></b>");
		print ("<br><br><br>");
		print (" message erreur : $error <br>");
	}

	//	OK, affiche le formulaire HTML
	else {
		print ("<br><br>");
		print ("  $message <br>");
	}

print ("</BODY></HTML>");

?>
