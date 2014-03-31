// ---------------------------------------------
//  Plugin admin_lang pour spip 1.9
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  repris de simeray@tektonika.com
// ---------------------------------------------
# tester en allant sur 

#Accès interface privé de spip, configuration->gestion des langues->gestion des fichiers de langues
#Ce plugin permet de gérer les traductions par ecriture d'un fichier de langue
#et si ce fichier n'existe pas, de le créer dans un répertoire lang et uniquement dans des chemins proposés

/// par defaut on propose de modifier le fichier local_masterlang

# - argument d'URL   module = cequejeveux
# - argument d'URL   target_lang = les langues déclarées pour le site (ar,fr,en ...) et proposé alors en choix
# - master langue = fichier de référence avec la langue du site, si noexist, le fichier sert de master
# - via un find_in_path adapté, on cherche le fichier sinon on récupère array cheminpossibles
# - si non trouvé on propose de créer module_target_lang dans les cheminpossibles avec le dossier de lang  en supplément obligatoire
# - le dossier de lang est crée si il n'existe pas


#TODO
/// la langue de reference du site est $master_lang// il faudrait pouvoir choisir son master comme on veut

/// par souci de MAJ sans encombre il ne devrait pas y avoir ecriture des ecrire/lang/modules_xx de spip 

///actuellement le charset d'enregistrement est tout iso, que le site soit en utf8 ou en iso
#détecter la langue
//enregistrer les fichier de langues en utf8 si la langue est en UTF8
//enregistrer les fichier de langues en iso si la langue est en iso
 
#arranger les accès aux modules
//lister sur la gauche les modules de lang possibles ...squelettes et plugins installés (check des dossiers)
//dans le même style, arranger la liste des chemins possibles avec des dossiers ouverts/fermés

#faire un bak timé des fichiers modifiés si ils datent de plus de..x temps..

#remplacer les espaces par des underscore sur les nouvelles traductions au cas ou
#faire la traduction du plugin admin_lang ;)
