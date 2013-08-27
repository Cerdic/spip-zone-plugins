<?php
/**
 * Déclaration des tâches du génie
 *
 * @plugin     Mots de passe expirables
 * @copyright  2013
 * @author     erational
 * @licence    GNU/GPL
 * @package    SPIP\Motpasseexpirable\Genie
 */
if (!defined("_ECRIRE_INC_VERSION")) return;


function genie_motpasseexpirable_dist($t){

  // etape 1: mettre à jour la date expiration des comptes nouvellement crées
  $date = date("Y-m-d H:i:s");
  sql_updateq('spip_auteurs', array('pass_maj' => $date), "pass_maj='0000-00-00 00:00:00'");

  // etape 2: recherche les comptes périmées
  include_spip('inc/config');
  $delai_expiration = lire_config('motpasseexpirable/delai',30);
  $delai_expiration = intval($delai_expiration);
  if ($delai_expiration<1)
                        $delai_expiration = 1;  
  $date_expiration = date("Y-m-d H:i:s", time() - $delai_expiration * 24 * 3600);                     
  
  $result = sql_select('id_auteur,email', "spip_auteurs", "pass_maj<'$date_expiration'");
  while ($row = sql_fetch($result)){
       $id_auteur = $row['id_auteur'];
       $email = $row['email'];
       sql_updateq('spip_auteurs', array('pass' => '*','pass_maj'=>$date), "id_auteur=$id_auteur");
       
       // notifications
       // inspi:  squelettes-idst/formulaires/oubli.php
       include_spip('inc/filtres'); # pour email_valide()
	     if (email_valide($email)) {       
            include_spip('inc/texte'); # pour corriger_typo
      
        		include_spip('action/inscrire_auteur');
        		$cookie = auteur_attribuer_jeton($id_auteur);
        
        		$msg = recuperer_fond(
        			"prive/modeles/mail_motpasseexpirable",
        			array(
        				'url_reset'=>generer_url_public('spip_pass',"p=$cookie", true, false)
        			)
        		);
        		include_spip("inc/notifications");
        		notifications_envoyer_mails($email, $msg);
            
       }

  }
  return 1;
}

?>