// ---------------------------------------------
//  Plugin admin_lang pour spip 1.9
//	 
//  spip addition to manage language files
//  alm@elastick.net
//  repris de simeray@tektonika.com
// ---------------------------------------------
# tester en allant sur 

#Acc�s interface priv� de spip, configuration->gestion des langues->gestion des fichiers de langues
#Ce plugin permet de g�rer les traductions par ecriture d'un fichier de langue
#et si ce fichier n'existe pas, de le cr�er dans un r�pertoire lang et uniquement dans des chemins propos�s

/// par defaut on propose de modifier le fichier local_masterlang

# - argument d'URL   module = cequejeveux
# - argument d'URL   target_lang = les langues d�clar�es pour le site (ar,fr,en ...) et propos� alors en choix
# - master langue = fichier de r�f�rence avec la langue du site, si noexist, le fichier sert de master
# - via un find_in_path adapt�, on cherche le fichier sinon on r�cup�re array cheminpossibles
# - si non trouv� on propose de cr�er module_target_lang dans les cheminpossibles avec le dossier de lang  en suppl�ment obligatoire
# - le dossier de lang est cr�e si il n'existe pas


#TODO
/// la langue de reference du site est $master_lang// il faudrait pouvoir choisir son master comme on veut

/// par souci de MAJ sans encombre il ne devrait pas y avoir ecriture des ecrire/lang/modules_xx de spip 

///actuellement le charset d'enregistrement est tout iso, que le site soit en utf8 ou en iso
#d�tecter la langue
//enregistrer les fichier de langues en utf8 si la langue est en UTF8
//enregistrer les fichier de langues en iso si la langue est en iso
 
#arranger les acc�s aux modules
//lister sur la gauche les modules de lang possibles ...squelettes et plugins install�s (check des dossiers)
//dans le m�me style, arranger la liste des chemins possibles avec des dossiers ouverts/ferm�s

#faire un bak tim� des fichiers modifi�s si ils datent de plus de..x temps..

#remplacer les espaces par des underscore sur les nouvelles traductions au cas ou
#faire la traduction du plugin admin_lang ;)
