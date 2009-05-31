<?php
/***************************************************************************\
 * Pour tester la nouvelle fonction de champs homonymes                    *
 * Après avoir ajouté dans le fichier                                      *
 * la nouvelle fonction  extra_homonyme()                                  *
 * modifier la fonction  la fonction extra_homonyme()                      *
 * modifier le fichier mot_edit.php3                                       *
 * Il est possible de tester les champs homonymes.                         *
 *                                                                         *
 * Pour utiliser les fichier de test homonymes_plus.php3/html              *
 * et homonymes_test.php3/html                                             *
 * il faut ajouter dans mes_options.php3 les modifications proposées       *
 * ainsi que celles proposé à ajouter dans le fichier  mes_fonctions.php3  *
 *                                                                         *
 * Pour plus de détails voir:                                              *
 * http://www.spip-contrib.net/ecrire/articles.php3?id_article=1080        * 
 * ou communiquer avec                                                     *
 * francois.vachon@iago.ca                                                 *
\***************************************************************************/
$GLOBALS['param_perso'] = Array (
	'articles' => Array (
			'auteur_visiteur' => 'oui'
		),
	'documents' => Array (
			'vignette_infos' => 'oui'
		)	
);
$GLOBALS['param_perso']['texte']['lien_externe']='oui';
$GLOBALS['param_perso']['presentation']['statistique']='oui';
$GLOBALS['param_perso']['presentation']['bandeau_sec']='oui';


$GLOBALS['champs_extra'] = Array (
	'auteurs' => Array (
			"plus" => "ligne|typo|Plus"
		),
	'articles' => Array (
			"plus" => "ligne|typo|Plus"
		),	
	'rubriques' => Array (
			"plus" => "liste|brut|<multi>Estimation[en]Valuation
</multi>|<multi>Bon travail[en]Good job</multi>,<multi>Passable[en]Not so good
</multi>"
		),
	'breves' => Array (
			"plus" => "liste|brut|<multi>Estimation[en]Valuation
</multi>|<multi>Bon travail[en]Good job</multi>,<multi>Passable[en]Not so good
</multi>"
		),
	'sites' => Array (
			"plus" => "liste|brut|<multi>Estimation[en]Valuation
</multi>|<multi>Bon travail[en]Good job</multi>,<multi>Passable[en]Not so good
</multi>"
		),
	'mots' => Array (
			"plus" => "ligne|typo|Plus"
		)
	);
	

function extra_mise_a_jour($extra, $type, $id) {
$extra = unserialize ($extra);
if (!is_array($extra)) return;
        switch ($type) {
                case 'articles':
                        $id_table = 'id_article';
                        break;
                case 'breves':
                        $id_table = 'id_breve';
                        break;
                case 'rubriques':
                        $id_table = 'id_rubrique';
                        break;
                case 'auteurs':
                        $id_table = 'id_auteur';
                        break;
                case 'sites':
                        $id_table = 'id_syndic';
                        $type='syndic';
                        break;
                case 'mots':
                        $id_table = 'id_mot';
                        break;
                        
                default:
                        $id_table ='';
           break;
       }
$table = spip_fetch_array(spip_query("SELECT * FROM spip_$type WHERE $id_table=$id"));
                while (list($champ,$contenu) = each($extra)) {
                        // Pour chaque nom de champs extra 
                        // vérifier si la table comporte un champs du même nom (homonyme)
                        //if (isset($table[$champ])){
						if (array_key_exists($champ,$table)){
                                //Si oui, mettre à jour la valeur des champs de la table par la valeur du champs extra du même nom
                                $query = "UPDATE spip_$type SET 
                                $champ='".addslashes($extra[$champ])."'
                                WHERE $id_table=".$id;
                                $trace .= spip_query($query) OR die($query);
                        }
                 }
}
?>
