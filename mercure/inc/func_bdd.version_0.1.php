<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| diverses fonctions communes BDD...         |
|    -> fonctions de construction            |
|    -> fonctions globales privées           |
|    -> fonctions spécifiques privées        |
|    -> fonctions publiques                  |
+--------------------------------------------+
*/

define('_COULEUR_FOND_TR','#DDEEFF');

	# repertoire local MERCURE
	if (!defined('_DIR_LOCAL_MERCURE')) {
    $p = realpath(dirname(__FILE__));
    $p = substr($p,0,strrpos($p,'/')-1);
    $p = substr($p,0,strrpos($p,'/'));
    define('_DIR_LOCAL_MERCURE',$p.'/mercure/local/');
	}

	# repertoire smiley MERCURE
	if (!defined('_URL_SMILEY_MERCURE')) {
    $pageURL = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/')-1);
    $pageURL = substr($pageURL,0,strrpos($pageURL,'/'));
    define('_URL_SMILEY_MERCURE',$pageURL.'/img_pack/smiley/');
	}

include_once 'txt-db-api.php';

class PK_BDD{
    		private $timestamp;
				private $pseudo;
				private $message;				
				private $bdd;
				private $db;

    public function __construct($bdd){	 		    
        $init = false;
        $this->bdd = $bdd;
        if( $this->bdd == 'bdd'){ // MODE SQLITE
          if (!file_exists(_DIR_LOCAL_MERCURE . 'mercure_SQLITE.db')){
            $init = true;
          }
          $this->db = new SQLiteDatabase(_DIR_LOCAL_MERCURE . 'mercure_SQLITE.db', 600);
          if ($init){
            $this->initSQLiteDB();
          }    
        }else{              // MODE TEXTE
          if (!file_exists(DB_DIR . 'mercure')) {
	          $this->db = new Database(ROOT_DATABASE);
	          $this->db->executeQuery("CREATE DATABASE mercure");
	          $init = true;
          }
          if ($init){
            $this->initTextDB();
          }          
        }
    }

/* ================================================================================
                              FONCTIONS PRIVEES
================================================================================ */

###########################################
##           FONCTIONS GLOBALES           #
###########################################

    private function txtToUrl( $message ){
      $mots = explode(' ', $message);
      $nb = count( $mots );
      for($i=0; $i<$nb;$i++){
        if( substr( $mots[$i], 0, 7) == 'http://' ){
          $mots[$i] = '<a href="'.$mots[$i].'" target="_blank">'.$mots[$i].'</a>';
        }elseif( substr( $mots[$i], 0, 8) == 'https://' ){
          $mots[$i] = '<a href="'.$mots[$i].'" target="_blank">'.$mots[$i].'</a>';        
        }elseif( substr( $mots[$i], 0, 6) == 'ftp://' ){
          $mots[$i] = '<a href="'.$mots[$i].'" target="_blank">'.$mots[$i].'</a>';
        }elseif( substr( $mots[$i], 0, 4) == 'www.' ){
          $mots[$i] = '<a href="http://'.$mots[$i].'" target="_blank">'.$mots[$i].'</a>';
        }elseif( substr( $mots[$i], 0, 4) == 'ftp.' ){
          $mots[$i] = '<a href=ftp://"'.$mots[$i].'" target="_blank">'.$mots[$i].'</a>';
        }
      }
      return(implode(' ',$mots));
    }

    private function quoteToCode( $message ){
      return(str_replace("'",'&rsquo;',$message));
    }

    private function txtToImg( $message ){
      $translate = array(
                          ':-)' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_smile.gif">',
                          ':-D' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_grin.gif">',
                          ':lol' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_lol.gif">',
                          ':-p' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_razz.gif">',
                          ';-)' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_wink.gif">',
                          ':-b' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_yes.gif">',
                          ':-(' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_sad.gif">',
                          ':-s' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_unsure.gif">',
                          ':-=' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_confused.gif">',
                          ':-o' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_suprised.gif">',
                          ':-|' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_shocked.gif">',
                          ':B' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_cool.gif">',
                          ':-h' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_huh.gif">',
                          ':-x' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_mad.gif">',
                          ':o(' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_sick.gif">',
                          ':red' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_red.gif">',
                          ':love' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_wub.gif">',
                          ':kiss' => '<img align="top" src="'._URL_SMILEY_MERCURE.'sm_kiss.gif">'
                        );
      return(strtr($message,$translate));
    }
  
    private function txtToCode( $message ){
      $traduction = array(
                          'É' => '&Eacute;',
                          'é' => '&eacute;',
                          'È' => '&Egrave;',
                          'è' => '&egrave;',
                          'Ë' => '&Euml;',
                          'ë' => '&euml;',
                          'Ê' => '&Ecirc;',
                          'ê' => '&ecirc;',
                          'À' => '&Agrave;',
                          'à' => '&agrave;',
                          'Â' => '&Acirc;',
                          'â' => '&acirc;',
                          'Á' => '&Aacute;',
                          'ä' => '&auml;',
                          'Ä' => '&Auml;',
                          'Î' => '&Icirc;',
                          'î' => '&icirc;',
                          'Ù' => '&Ugrave;',
                          'ù' => '&ugrave;',
                          'Û' => '&Ucirc;',
                          'û' => '&ucirc;',
                          'Ô' => '&Ocirc;',
                          'ô' => '&ocirc;',
                          'ö' => '&ouml;',
                          'Ö' => '&Ouml;',
                          'Ç' => '&Ccedil;',
                          'ç' => '&ccedil;',
                          'Ÿ' => '&Yuml;',
                          'ÿ' => '&yuml;',
                          'Š' => '&Scaron;',
                          'š' => '&scaron;',
                          'Œ' => '&OElig;',
                          'œ' => '&oelig;',
                          'Ž' => '%8E',
                          'ž' => '%9E',
                          'ß' => '&szlig;',
                          'ñ' => '%F1',
                          'Ñ' => '%D1',
                          '~' => '&tilde;',
                          '€' => '&euro;',
                          '<' => '&lt;',
                          '>' => '&gt;',
                          '[|{' => '<br>',
                          '|[{' => '+',
                          '[{|' => '%',
                          '{|[' => '&',
                          '{[|' => '#'                     
                        );
      return(strtr($message,$traduction));
    }

###########################################
##           FONCTIONS SQLITE             #
###########################################

    private function initSQLiteDB(){
        $this->db->queryExec("
				CREATE TABLE messages (
                                timestamp VARCHAR(18) PRIMARY KEY NOT NULL,
                                pseudo VARCHAR(32) NOT NULL,
						                    message TEXT				 
				                      );
                             ");
		    $output = 	  "'".(time()-10)."',"
		    		  			.	"'Mercure',"
		    			   		.	"'Bienvenue...'";		    					
        $this->db->unbufferedQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES (".$output.");");
    }
    
		private function storeSQLiteMessage($time,$pseudo,$message){
		   $output = 	  "'".$time."',"
		    					.	"'".$pseudo."',"
		    					.	"'".$this->txtToImg($this->quoteToCode($this->txtToUrl($this->txtToCode($message))))."'";		    					
			 $this->db->unbufferedQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES (".$output.");");
		}

		private function getSQLiteMessages($nb_messages){
       if( $nb_messages != 0){
          // on ne retourne que les "nb_messages" derniers messages
          $result = $this->db->arrayQuery('SELECT * FROM messages ORDER BY timestamp ASC LIMIT '.$nb_messages.';', SQLITE_ASSOC); 
       }else{
          // on les prends tous => on veut la liste de tous les messages
				  $result = $this->db->arrayQuery('SELECT * FROM messages ORDER BY timestamp DESC', SQLITE_ASSOC);
       }       
       if( $result ){
            // il y a des messages
            $output = '<table border="0" width="100%">';
				    foreach( $result as $value){
 			        $ifond = $ifond ^ 1;
			        $couleur = ($ifond) ? '#FFFFFF' : _COULEUR_FOND_TR;			    
				      $output .=	'<tr bgcolor="'.$couleur.'"><td width="20%"><small><strong>'.$value['pseudo'].'</strong><br>'.date('d/m/Y',$value['timestamp']).'<br>'.date('H:i:s',$value['timestamp']).'</small></td>' .
				  					 	    '<td width="80%"><small>'.str_replace("\'","'",str_replace("\’","’",str_replace('\"','"',$value['message']))).'</small></td></tr>';
				    }
				    $output .= '</table>';
       }else{
            // il n'y a pas ou plus de messages...
            $output = '';
       }           
       return($output);		
		}

		private function deleteSQLiteOldMessages($time_limit){		
		   if($time_limit>0){
        $date_limite = time() - $time_limit * 60*60*24;
        $this->db->queryExec("DELETE FROM messages WHERE timestamp < '".$date_limite."';");
      }
		}

		private function limitSQLiteMessages($item_limit){		
		   $timestamp = array();
		   $pseudo = array();
		   $message = array();
       // // on les prends tous classés du plus récent au plus vieux
			 $result = $this->db->arrayQuery('SELECT * FROM messages ORDER BY timestamp DESC;', SQLITE_ASSOC);
       $nb_enregistrements = 20; // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
       if( $nb_enregistrements > $item_limit ){
        // il y a des messages et en plus grand nombre que ITEM_LIMIT : on les prends tous
				foreach( $result as $value){
				      $pseudo[] = $value['pseudo'];
              $timestamp[] = $value['timestamp'];
              $message[] = $value['message'];
				}
			  // on détruit les enregistrements de la BDD
			  // cela fait aussi un beau "VACUUM" !
        $this->db->queryExec('DROP TABLE messages;');
        $this->db->queryExec("
				CREATE TABLE messages (
                                timestamp VARCHAR(18) PRIMARY KEY NOT NULL,
                                pseudo VARCHAR(32) NOT NULL
						                    message TEXT				 
				                      );
                             ");
			  // on insert alors les ITEM_LIMIT premiers enregistrements des tableaux []
			  for($i=0; $i< $item_limit; $i++){
  			 $this->db->unbufferedQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES ('".$timestamp[$i]."','".$pseudo[$i]."','".$message[$i]."');");            
        }
       }else{
        // le nombre de messages est inférieur à ITEM_LIMIT
        // ou
        // il n'y a pas de messages
        // => on fait rien !
       }
		}
    
		private function optimizeSQLite(){		
       $this->db->queryExec("VACUUM;");
		}

###########################################
##           FONCTIONS TEXTE              #
###########################################

		private function initTextDB(){
       $this->db = new Database('mercure');	
	     $this->db->executeQuery("
				CREATE TABLE messages (
                                timestamp inc,
                                pseudo str,
						                    message str				 
				                      );
				                       ");
		   $output = 	  "'".(time()-10)."',"
		    		  			.	"'Mercure',"
		    			   		.	"'Bienvenue...'";		    					
       $this->db = new Database('mercure');	
       $this->db->executeQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES (".$output.");");				                       
		}



		private function storeTXTMessage($time,$pseudo,$message){
		   $output = 	  "'".$time."',"
		    					.	"'".$pseudo."',"
		    					.	"'".$this->txtToImg($this->quoteToCode($this->txtToUrl( $this->txtToCode($message))))."'";		    					
       $this->db = new Database('mercure');	
			 $this->db->executeQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES (".$output.");");            
		}

		private function getTXTMessages($nb_messages){
       $timestamp = array();
       $pseudo = array();
       $message = array();			    
       $this->db = new Database('mercure');	
       if( $nb_messages != 0){
          // on ne retourne que les "nb_messages" derniers messages
          $result = $this->db->executeQuery('SELECT * FROM messages ORDER BY timestamp DESC LIMIT '.$nb_messages.';');
          $pre_html ='';
          $post_html= '';
        }else{
          // on les prends tous => on veut la liste de tous les messages
				  $result = $this->db->executeQuery('SELECT * FROM messages');
          $pre_html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
                      <html lang="fr" dir="ltr"> 
                      <head>
                        <title>Mercure Redactor\'s Chat</title> 
                        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
                      </head>
                      <body>';
          $post_html= '</body>
                       </html>';
        }
        if( 1 > 0 ){
            // il y a des messages
				    while( $result->next() ){
              list($timestamp[],$pseudo[],$message[]) = $result->getCurrentValues();			    
            }
            $nb = count($pseudo);            
            $output = '<table border="0" width="100%">';
				    for($i=$nb-1; $i>=0; $i--){
 			        $ifond = $ifond ^ 1;
			        $couleur = ($ifond) ? '#FFFFFF' : _COULEUR_FOND_TR;
				      $output .=	'<tr bgcolor="'.$couleur.'"><td width="20%"><small><strong>'.$pseudo[$i].'</strong><br>'.date('d/m/Y',$timestamp[$i]).'<br>'.date('H:i:s',$timestamp[$i]).'</small></td>' .
				  					 	    '<td width="80%"><small>'.$message[$i].'</small></td></tr>';
				    }
				    $output .= '</table>';
          }else{
            // il n'y a pas ou plus de messages...
            $output = '';
       }
       return($pre_html.$output.$post_html);		
		}

		private function deleteTXTOldMessages($time_limit){		
		   if($time_limit>0){
		    $date_limite = time() - $time_limit * 60*60*24;
        $this->db = new Database('mercure');	
        $this->db->executeQuery("DELETE FROM messages WHERE timestamp < '".$date_limite."';");
      }
		}

		private function limitTXTMessages($item_limit){		
		   $timestamp = array();
		   $pseudo = array();
		   $message = array();
       $this->db = new Database('mercure');	
       // on les prends tous classés du plus récent au plus vieux
			 $result = $this->db->executeQuery('SELECT * FROM messages ORDER BY timestamp DESC;');
				while( $result->next()){
          list($timestamp[],$pseudo[],$message[]) = $result->getCurrentValues();			    
			  }
			 $nb_enregistrements = count($pseudo);
       if( $nb_enregistrements > $item_limit ){
        // il y a des messages et en plus grand nombre que ITEM_LIMIT : on les prends tous
			  // on détruit les enregistrements de la BDD
        $this->db = new Database('mercure');
        $this->db->executeQuery('DROP TABLE messages;');
	      $this->db->executeQuery("
				  CREATE TABLE messages (
                                timestamp inc,
                                pseudo str,
						                    message str				 
				                        );
				                       ");                	
			  // on insert alors les ITEM_LIMIT premiers enregistrements des tableaux []
			  for($i=0; $i< $item_limit; $i++){
  			 $this->db->executeQuery("INSERT INTO messages (timestamp,pseudo,message) VALUES ('".$timestamp[$i]."','".$pseudo[$i]."','".$message[$i]."');");            
        }
      }else{
        // le nombre de messages est inférieur à ITEM_LIMIT
        // ou
        // il n'y a pas de messages
        // => on fait rien !
      }           
		}
    
		private function optimizeTXT(){		
      // pas d'optimisation en mode TEXTE...
		}


/* ================================================================================
                              FONCTIONS PUBLIQUES
================================================================================ */

    # ecrire un nouvel enregistrement 
		public function bdd_ecrire_message($timestamp, $pseudo, $message){
		  if( $this->bdd == 'bdd'){
		    $this->storeSQLiteMessage($timestamp,$pseudo,$message);
      }else{
		    $this->storeTXTMessage($timestamp,$pseudo,$message);
      }
    }

    # lire nb enregistrements
		public function bdd_lire_messages($nb_enregistrements_a_lire){
		  if( $this->bdd == 'bdd'){
		    return($this->getSQLiteMessages($nb_enregistrements_a_lire));
      }else{
		    return($this->getTXTMessages($nb_enregistrements_a_lire));
      }
    }

    # limiter les enregistrements
		public function bdd_limiter_messages($item_limit){
		  if( $this->bdd == 'bdd'){
		    $this->limitSQLiteMessages($item_limit);
      }else{
		    $this->limitTXTMessages($item_limit);
      }
    }

    # purger les vieux enregistrements
		public function bdd_effacer_vieux_messages($time_limit){
		  if( $this->bdd == 'bdd'){
		    $this->deleteSQLiteOldMessages($time_limit);
      }else{
		    $this->deleteTXTOldMessages($time_limit);
      }
    }

    # optimiser la bdd
		public function bdd_optimiser(){
		  if( $this->bdd == 'bdd'){
		    $this->optimizeSQLite();
      }else{
		    $this->optimizeTXT();
      }
    }
}
?>
