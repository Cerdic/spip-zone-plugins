<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
# il s'agit ici de proposer des elements de formulaire
# n'ayant pas besoin de correction.

#---------------------------------------------------#
#  Plugin  : jeux                                   #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#--------------------------------------------------------------------------#
#  Documentation : https://contrib.spip.net/Des-jeux-dans-vos-articles     #
#--------------------------------------------------------------------------#
/*

Insere élément de formulaire dans vos articles !
------------------------------------------------

separateurs obligatoires : [texte], [saisie]
separateurs optionnels   : [titre], [config]
parametres de configuration par defaut :
	voir la fonction jeux_trous_init() ci-dessous

Exemple de syntaxe dans l'article :
-----------------------------------

<jeux>
	[label]
	prenom Quel est ton prenom ?
	[saisie]
	prenom text size=48
	[texte]
	Je suis :
	[saisie]
	genre checkbox
	M = Un garcon
	F = Une fille
	[label]
	preference Tu preferes :
	[saisie]
	preference select
	bonbons = Les bonbons
	gateaux = Les gateaux
	[texte]
	Ton message :
	[saisie]
	message textarea cols=40 rows=10
	value = Ligne 1
Ligne 2
</jeux>

Syntaxe a utiliser apres [label] (tous les parametres sont optionnels) :
	(nom) (votre texte)
	- nom par defaut : saisie
Syntaxe a utiliser apres [saisie] (tous les parametres sont optionnels) :
	(nom) (type) (autres parametres de la balise)
	(valeur 1 = libelle 1, cas d'un type 'select', 'radio' ou 'checkbox')
	(valeur 2 = libelle 2, cas d'un type 'select', 'radio' ou 'checkbox')
	...
	(value = Votre texte par defaut, pour certains types de saisie)
	- nom par defaut : saisie
Le nom :
	un mot sans espace compose de lettres/chiffres/:._-
Les types reconnus sont les type HTML :
	button, checkbox, color, date, datetime, datetime-local
	email, file, hidden, image, month, number, password
	radio, range, search, select, tel, text, textarea, time, url, week
	- types non reconnus : submit reset
	- type par defaut : text
Les autres parametres :
	permet de completer la balise HTML <input> (ou <textarea> le cas echeant)
	exemple 1 : cols=40 rows=10
	exemple 2 : size=48

*/

// configuration par defaut : jeu_{mon_jeu}_init()
function jeux_saisies_init() {
	return "
		bouton_corriger=valider // fond utilise pour le bouton 'Valider'
		bouton_refaire=reinitialiser // fond utilise pour le bouton 'Reset'
	";
}

function jeux_saisies_trouve_texte($type, $texte) {
	if(!in_array($type, array('button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local',
		'email', /*'file', */'hidden', 'image', 'month', 'number', 'password', 'radio', 'range',
		/*'select', */'search', 'tel', 'text', 'textarea', 'time', 'url', 'week'))) return '';
	$texte = preg_split(',\bvalue *=,', $texte, 2);
	return isset($texte[1])?trim($texte[1]):'';
}

function jeux_saisies_trouve_nom($nom, $defaut='saisie') {
	$nom = trim($nom);
	return preg_match(',[a-z][a-z0-9:_\.\-]*,i', $nom)?$nom:$defaut;
}

function jeux_saisies_trouve_type($type, $defaut='text') {
	$type = trim($type);
	return in_array($type, array('button', 'checkbox', 'color', 'date', 'datetime', 'datetime-local',
		'email', 'file', 'hidden', 'image', 'month', 'number', 'password', 'radio', 'range',
		'select', 'search', 'tel', 'text', 'textarea', 'time', 'url', 'week'))?$type:$defaut;
}

function jeux_saisies_label($texte) {
 $texte = explode(' ', trim($texte), 2);
 $for = jeux_saisies_trouve_nom($texte[0]);
 return isset($texte[1])?'<div class="jeux_saisies_label"><label for="'.$for.'">'.$texte[1].'</label></div>':'';
}

function jeux_saisies_saisie(&$jeux_saisies, $indexJeux, $texte) {
 static $indexSaisie = 0;
 $lignes = preg_split("/[\r\n]+/", trim($texte));
 $texte = explode(' ', trim($lignes[0]), 3); unset($lignes[0]);
 $type = isset($texte[1])?jeux_saisies_trouve_type($texte[1],''):'';
 if(!$type) {
 	if($type = jeux_saisies_trouve_type($texte[0], '')) {
		$texte[0] = ''; $nom = 'saisie';
	} else {
		$type = 'text'; $nom = jeux_saisies_trouve_nom($texte[0], '');
		if($nom) $texte[0] = ''; else $nom = 'saisie';
	}
 } else {
	$nom = jeux_saisies_trouve_nom($texte[0]);
	$texte[0] = $texte[1] = '';
 }
 if(isset($jeux_saisies[$nom])) { $nom = "$type / $nom"; $type = 'erreur'; }
 $texte = trim(join(' ', $texte)); if($texte) $texte = ' '.$texte;
 if($correction = jeux_form_correction($indexJeux)) {
 	$value = jeux_form_reponse($indexJeux, $indexSaisie, $nom);
	$jeux_saisies[$nom] = $nom.':'.(is_array($value)?join(' / ', $value):$value);
	$disabled = ' disabled="disabled"';
 } else {
 	$value = jeux_saisies_trouve_texte($type, join("\n", $lignes));
	$jeux_saisies[$nom] = $disabled = '';
 }
 list($idInput, $nameInput) = jeux_idname($indexJeux, $indexSaisie++, $nom);

 switch ($type) {
    case 'erreur':
		$res = '<div><b>#'._T('jeux:erreur_doublon')." ($nom)</b></div>";
		break;
    case 'textarea':
		$res = "<textarea id=\"$idInput\" name=\"$nameInput\"$disabled$texte>$value</textarea>";
		break;
    case 'select':
		$res = "<select id=\"$idInput\" name=\"$nameInput\"$disabled$texte>";
		foreach($lignes as $l) {
 			$l = explode('=', $l, 2); list($a, $b) = array(trim($l[0]), trim($l[1]));
			$checked = ($correction && ( (is_array($value) && in_array($a, $value)) || $a==$value))
				?' selected="selected"':'';
			$res .= isset($l[1])?'<option value="'.attribut_html($a)."\"$checked>".$b.'</option>'
				:("<option$checked>".trim($l[0]).'</option>');
		}
		$res .= '</select>';
        break;
    case 'radio':
    case 'checkbox':
		$tmp = '<input type="'.$type.'" name="'.$nameInput.($type=='checkbox'?'[]':'')."\"$disabled value=\"";
		$res = array(); $indexCB = 0;
		foreach($lignes as $l) {
 			$l = explode('=', $l, 2); $id = $idInput.'-'.$indexCB++;
			if(!isset($l[1])) $l[1] = $l[0]; list($a, $b) = array(trim($l[0]), trim($l[1]));
			$checked = ($correction && ( (is_array($value) && in_array($a, $value)) || $a==$value))
				?' checked="checked"':'';
			$res[] = $tmp.attribut_html($a).'" id="'.$id.'"'.$checked.'><label style="display:inline;" for="'.$id.'">'.$b.'</label>';
		}
		$res = join('<br/>', $res);
        break;
	default:
		if($value) $value = ' value="' . attribut_html($value).'"';
		$res = "<input type=\"$type\" id=\"$idInput\" name=\"$nameInput\"$disabled$value$texte>";
  }
 return "<div class='jeux_saisies_saisie'>$res</div>";
}

// traitement du jeu : jeu_{mon_jeu}()
function jeux_saisies($texte, $indexJeux, $form=true) {
	// initialisation
	$jeux_saisies_[$indexJeux] = array();
	$jeux_saisies = &$jeux_saisies_[$indexJeux];
	$titre = $html = false;
	$id_jeu = _request('id_jeu');

	// parcours des [separateurs]
	$tableau = jeux_split_texte('saisies', $texte);
	foreach($tableau as $i => $valeur) if ($i & 1) {
	 if ($valeur==_JEUX_TITRE) $titre = $tableau[$i+1];
	  elseif ($valeur==_JEUX_TEXTE && strlen($tableau[$i+1])) $html .= '<div class="jeux_saisies_texte">'.$tableau[$i+1].'</div>';
	  elseif ($valeur==_JEUX_LABEL) $html .= jeux_saisies_label($tableau[$i+1]);
	  elseif ($valeur==_JEUX_SAISIE) $html .= jeux_saisies_saisie($jeux_saisies, $indexJeux, $tableau[$i+1]);
	}

	// calcul des extremes
	$tete = '<div class="jeux_cadre jeux_saisies">'.($titre?'<div class="jeux_titre jeux_saisies_titre">'.$titre.'<hr /></div>':'');
	$pied = '';
	if(jeux_form_correction($indexJeux)) {
		// mode correction
		if($form) $pied .= jeux_bouton(jeux_config('bouton_refaire'), $id_jeu, $indexJeux);
		// stockage des resultats, mais sans les afficher
		jeux_afficher_score(0, 0, $id_jeu, join(" | ", $jeux_saisies));
	} else {
		// mode formulaire
		if($form) {
			$pied = '<br />' . jeux_bouton(jeux_config('bouton_corriger'), $id_jeu) . jeux_form_fin();
			$tete .= jeux_form_debut('qcm', $indexJeux, '', 'post', self());
		}
	}

	return $tete.$html.$pied.'</div>';
}
