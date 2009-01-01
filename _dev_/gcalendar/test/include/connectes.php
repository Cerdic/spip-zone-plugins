<? 
//include("../../../../ecrire/inc_connect.php");	
include("config.pass.php");	

// Connexion à MySQL 
mysql_connect($adresse_db, $login_db, $pass_db); 
mysql_select_db($bases); 

//$dbtable= "gb_recordconnect";

// ------- 
// ETAPE 1 : on vérifie si l'IP se trouve déjà dans la table 
// Pour faire ça, on n'a qu'à compter le nombre d'entrées dont le champ "ip" est l'adresse ip du visiteur 
$retour = mysql_query('SELECT COUNT(ip) AS nbre_entrees FROM gb_recordconnect WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\''); 
$donnees = mysql_fetch_array($retour); 

if ($donnees['nbre_entrees'] == 0) // L'ip ne se trouve pas dans la table, on va l'ajouter 
{ 
    mysql_query('INSERT INTO gb_recordconnect VALUES(\'' . $_SERVER['REMOTE_ADDR'] . '\', ' . time() . ')'); 
} 
else // L'ip se trouve déjà dans la table, on met juste à jour le timestamp 
{ 
    mysql_query('UPDATE gb_recordconnect SET timestamp=' . time() . ' WHERE ip=\'' . $_SERVER['REMOTE_ADDR'] . '\''); 
} 

// ------- 
// ETAPE 2 : on supprime toutes les entrées dont le timestamp est plus vieux que 5 minutes 

// On stocke dans une variable le timestamp qu'il était il y a 5 minutes : 
$timestamp_5min = time() - (60 * 3); // 60 * 5 = nombre de secondes écoulées en 5 minutes 
mysql_query('DELETE FROM gb_recordconnect WHERE timestamp < ' . $timestamp_5min); 

// ------- 
// ETAPE 3 : on compte le nombre d'ip stockées dans la table. C'est le nombre de visiteurs connectés 
$retour = mysql_query('SELECT COUNT(ip) AS nbre_entrees FROM gb_recordconnect'); 
$donnees = mysql_fetch_array($retour); 


// Ouf ! On n'a plus qu'à afficher le nombre de connectés ! 
//echo '<p>Il y a actuellement ' . $donnees['nbre_entrees'] . ' visiteurs connectés sur mon site !</p>'; 

// Recherche du nombre de gb_recordconnect 
//ajuste les données de la requete id et table 

$query="SELECT COUNT(ip) FROM gb_recordconnect"; 
$sql_result = mysql_query($query); 
$nb_actuel=mysql_result($sql_result, 0,0); 

//traitement du fichier -> lecture du record 
$fichier_text="record.txt"; 
$f=fopen($fichier_text,"r+"); 
$record = fread ($f, filesize ($fichier_text));



if($record<$nb_actuel)  //si la valeur lue dans le fichier est inferieure alors on écrase le fichier avec la nouvelle valeur 
  { 
       rewind ($f); 
       fwrite($f, $nb_actuel); 
       $record=$nb_actuel; 
        
   } 
fclose($f);


echo '' . $donnees['nbre_entrees'] . ' connecté(s) '; 

?> 