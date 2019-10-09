/*
 * libphonenumber
 * formulaire de test pour la vérification des numéros internationaux
 * 
 * @plugin     libphonenumber for SPIP
 * @copyright  2019
 * @author     Anne-lise Martenot
 * @licence    GNU/GPL
 * (c) 2019 - Distribue sous licence GNU/GPL
 *
 */
 
Ce plugin installe la librairie libphonenumber for PHP
https://github.com/giggsey/libphonenumber-for-php
qui permet de créer ou valider des numéros de téléphone à l'international.

Il nécessite le plugin PAYS, donc un champs pays dans votre formulaire.

Pour appeler le fichier de vérification dans votre formulaire CVT

function formulaires_nomduform_verifier(){
	$erreurs = array();
	
	//vérifier valeur des champs
    $verifier = charger_fonction('verifier', 'inc');
    
    //pays par defaut à FR
    $pays = _request('pays');
    
	$erreur_telephone = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
	if ($erreur_telephone) {
		$erreurs['telephone'] = $verifier($telephone, 'phone', array('prefixes_pays' => $pays));
	}
	
    if (count($erreurs)) {
       $erreurs['message_erreur'] =  "Une erreur est présente dans votre saisie";
    }
	return $erreurs;
}

Une page de démo est accessible sous /?page=demo/libphonenumber_demo
Cette page intègre une vérification ajax à la volée pour aider à la rédaction du numéro.