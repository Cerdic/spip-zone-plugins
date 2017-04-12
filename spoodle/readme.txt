SPOODLE (working title)

!qu'est-ce qu'on veut stocker dans la bdd:
- participant et leur pseudo (table auteur)
=> si inscrit (et logé) on met l'id_auteur, si non 
- nom (si id_auteur=0)
- e-mail (pour créer un nouveau rdv)
- sondage => table spip_spoodle_sondage
- choix des participants => table spip_spoodle_choix

? est-ce que n'importe qui peut utiliser spoodle? choix par cfg!

!les tables
spip_spoodle_sondage (table principale)
- id_sondage
- id_auteur=créateur de l'évennement
- nom de l'auteur (si pas de id_auteur)
- e-mail auteur
- titre (attention interprétation html spip)
- descriptif (attention interprétation html spip)  si non l'appeller commentaire ou autre
- status (publié, en construction, )
- date de création (date de l'insertion dans la bdd)
- date de mise à jour
- date deadline (pas obligatoire)
- oui ou non 'etre prevenu quand quelqu'un met son choix'
- sondage siple ou compliqué (oui-non ou oui-non-peutêtre)
- sondage caché (réponses visibles uniquement pour créateur)=> à coder plus tard
- limiter le choix à 1 par participants => voir si coder plus tard
- limiter les choix 'ok'(participants) sur une date, à un certain nombre (si nombre de participants limité pour un cour par exemple) !donner un exemple dans interface privé
- oui ou non préciser l'heure
- sondage privé ou publique (coix defaut fait par admin, voir cfg)

spip_spoodle_dates (table principale)
- id_date
- id_sondage
- date avec heure

spip_spoodle_participant (table principale)
- id_participant
- nom
- id_auteur
- date de l'enregistremant
- id_auteur si enregistré
- e-mail
- oui ou non 'etre prevenu en tant que participant'

spip_spoodle_participant_date (table auxiliaire)
- id_participant
- id_date
- oui ou peut-être

spip_spoodle_sondage_article (éventuellemnt)
- id_sondage
- id_article

!créer plugin.xml

svn cev:
fichier
REGLE_DE_COMMIT
écrire que c'est un projet de spip-be
ne pas committer svp

rdv 2 doodle : 4/6/15/17
http://www.doodle.com/f7k5dr97ypmgq39a



////////////     6 AVRIL 2009 ///////////////

http://code.spip.net/@Les-points-d-entree-pipelines


 http://trac.rezo.net/trac/spip/browser/spip/ecrire/inc_version.php -->


ici    <necessite id="nomplugin" version="[versionminimale;versionmax]" />
apl le prefixe déclaré au dessus

pour le contenu du fichier
http://code.spip.net/@plugin-xml


DECLARER UNE NOUVELLE TABLE
------------------------------------------------
dans 

* function spoodle_declarer_tables_principales($tables_principales){
tableau qui inclut les tables principales déjà déclarées.

puis on déclare les champs de la table											"id_sondage"=>"BIGINT NOT NULL",
etc...

* requetes en maj

ex : "PRIMARY KEY"=>"id_sondage",

* surcharge de $tables_principales 


$tables_principales["spip_spoodle_sondage"]=array(
"field"=>$spoodle_sondage,
key"=>$spoodle_sondage_key,
"join"=>$spoodle_sondage_join
)


* important car sinon incrémentation dans les tables mais pas de retour dans le flux de spip. = récupérer le pipe


return $tables_principales;

}

(....) Notes d'agnes ici :D

* on finit sur Formulaires cvt - début de formulaire. Crowfoot comite le tout
après. Exos: déclarer toutes les tables