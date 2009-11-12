<?php


if (!defined("_ECRIRE_INC_VERSION")) return;



function exec__spip_ajax_dist(){
	$msg = "Seul des fonctions ou classes appartenant à des fichiers inc peuvent être utilisées";
	if (function_exists($_POST["fct"]))	 die($msg);
	if (class_exists($_POST["class"]))die($msg);
	$a  =  new spip_ajax();
}



class spip_ajax{


	public function __construct(){

		global $auteur_session;
		
		
		// on test s'il y a une demande de code de hash
		if ($_POST['callback']=="spip_ajax.set_hash_env()"){
			echo $auteur_session["hash_env"];
			return;
		}
		
		
		
		// On valide le code de hash
		if ($auteur_session["hash_env"] != $_POST['hash_env'])die("tentative de hack ...");
		

		// redirection si on vient de l'admin
		 if (count($_POST)==0) {
	           include_spip('inc/headers');
	           redirige_par_entete("../ecrire/?exec=accueil");
	           return;
	      }

		// au cas d'un appel sur une classe et une fonction
		if (isset($_POST["fct"]) && isset($_POST["class"])){
			echo "Vous appellez une fonction et une  classe";
			return;
		}



		// Inclusion d'un fichier inc avec bridage
		if (isset($_POST["inc"])){
			// verification des droits sur le fichier dans le repertoire inc
			if (!autoriser('acces','sa',$_POST["inc"]))	die("pas les droits");
			
			// on verifie que la fonction n'appartient pas au core de spip
			if (file_exists("../"._DIR_RESTREINT_ABS."inc/".$_POST["inc"].".php"))	
				die("Vous ne pouvez appeler un fichier du core de spip");
				
			include_spip("inc/".$_POST["inc"]);
			
		}

		// appel a une fonction
		if (isset($_POST["fct"])) $this->ajax_fonction();

		// appel a une classe
		if (isset($_POST["class"])){
			$a = explode(":",$_POST["class"]);
			$taille = strlen((string)$a[1]);
			$class = $a[0];
			$method = $a[1];
			
			// on verifie que l'on appelle une classe et une methode
			if (count($a)==1 || $taille ==0) {
				echo "Vous avez appele une classe sans appele de methode ".$taille;
				return;
			}

			// on verifie que l'on appelle une classe et pas plus d'une methode
			if (count($a)>2 ) {
				echo "Vous avez appele plusieurs methodes... ";
				return;
			}

			if (class_exists($class)){
				
				// class et methode sans arguments
				if (!isset($_POST["args_class"])&& !isset($_POST["args_method"])) $this->ajax_class_sans_arguments($class,$method);
				
				// Cas d'une classe sans argument et d'une methode avec argument
				else if (!isset($_POST["args_class"])&& isset($_POST["args_method"])) $this->ajax_class_method_arguments($class,$method);

				// Cas d'une classe avec argument et d'une methode sans argument
				else if (isset($_POST["args_class"])&& !isset($_POST["args_method"])) $this->ajax_class_class_arguments($class,$method);

				// Cas d'une classe avec argument et d'une methode avac argument
				else if (isset($_POST["args_class"])&& isset($_POST["args_method"])) $this->ajax_class_arguments($class,$method);

			}else
			// La classe  appele n'existe pas
				{
				echo "Pas de class $class!!";
				return;
			}
		}

		// on  recupere si necessaire un fond sans argument
		if (isset($_POST["recup_fond"]) && !isset($_POST["args_fond"])){
			include_spip("public/assembler");
			echo recuperer_fond("fonds/".$_POST["recup_fond"]);
		}

		// on  recupere si necessaire un fond avec argument
		if (isset($_POST["recup_fond"]) && isset($_POST["args_fond"])){
			include_spip("public/assembler");
			$tab = explode(",",$_POST["args_fond"]);
			for ($i = 0; $i < count($tab); $i++) {$args [$tab[$i]]= $_POST[$tab[$i]];}
			echo recuperer_fond("fonds/".$_POST["recup_fond"],$args);
		}
	}


	private function ajax_fonction(){
		if (function_exists($_POST["fct"])){
			// y a t il des arguments passe a la fonction
			if (isset($_POST["args_fct"])){
				$tab = explode(",",$_POST["args_fct"]);
				for ($i = 0; $i < count($tab); $i++) {$args []= $_POST[$tab[$i]];}
				$reflection = new ReflectionFunction( $_POST["fct"] );
				$a  = $reflection->invokeArgs( $args );
				echo $a;
			}else{
				 echo $_POST["fct"]();
			}
		}else{
			echo "La fonction ".$_POST["fct"]." n'existe pas ou n'a pu être chargée";
		}

	}



	private function ajax_class_sans_arguments($class,$method){
		$a = new $class;
		if (method_exists($a,$method)){
			echo $a->$method();
		}else{
			echo "la methode $method de $class est absent";
		}
	}


	private function ajax_class_method_arguments($class,$method){
		$a = new $class;
		if (method_exists($a,$method)){
			$tab = explode(",",$_POST["args_method"]);
			for ($i = 0; $i < count($tab); $i++) {$args []= $_POST[$tab[$i]];}
			$reflection = new ReflectionMethod ($a , $method );
			echo $reflection->invokeArgs( $a, $args );
		}else{
			echo "la methode $method de $class est absent";
		}
	}



	private function ajax_class_class_arguments($class,$method){
		
		$tab = explode(",",$_POST["args_class"]);
		for ($i = 0; $i < count($tab); $i++) {$args []= $_POST[$tab[$i]];}
		$reflection = new ReflectionClass( $class );
		$a  = $reflection->newInstanceArgs( $args );

		if (method_exists($a,$method)){
			echo $a->$method();
		}else{
			echo "la methode $method de $class est absent";
		}
	}


	private function ajax_class_arguments($class,$method){
		$tab = explode(",",$_POST["args_class"]);
		for ($i = 0; $i < count($tab); $i++) {$args []= $_POST[$tab[$i]];}
		$reflection = new ReflectionClass( $class );
		$a  = $reflection->newInstanceArgs( $args );

		if (method_exists($a,$method)){
			$args = array();
			$tab = explode(",",$_POST["args_method"]);
			for ($i = 0; $i < count($tab); $i++) {$args []= $_POST[$tab[$i]];}

			$reflection = new ReflectionMethod ($a , $method );
			echo $reflection->invokeArgs( $a, $args );
		}else{
			echo "la methode $method de $class est absent";
		}
	}

}



?>