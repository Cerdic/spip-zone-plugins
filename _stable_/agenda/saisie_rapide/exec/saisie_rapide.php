<?php
/*
ajout d'une fonctionnalite de saisie rapide au plugin agenda.
c'est trop penible d'ajouter les evenements un par un qd yen a plus de trois...
en gros, pour moi, ca me change trop la vie !

j'attends l'avis des developpeurs pour l'inserer eventuellement plus tard au plugin s'ils le desirent.
Cette extension est testee sous spip 1.9.2 et fonctionne probablement sous spip 1.9.1.

fichiers à placer dans le repertoire plugins/agenda :
	exec/saisie_rapide.php : dialogue de saisie rapide
	inc/agenda_gestion.php : fichier d'origine modifie
	SAISIE.TXT : les details.

attention : aucune internationalisation pour l'instant !

Patrice VANNEUFVILLE - patrice.vanneufville(arob)laposte(pt)net

Syntaxe : 
	"jj/mm[/aaaa][-jj/mm[/aaaa]] [hh:mm[-hh:mm]] "Le titre" ["Le lieu" [ "La description"]] [REP=jj/mm/aaaa[,jj/mm/aaaa,etc]]

Les crochets indiquent les éléments facultatifs. 
Les répétitions de l'évènement sont indiquées par 'REP=' suivi d'une liste de dates séparées par des virgules. 
Bien respecter les espaces entre les éléments et ne pas mettre de guillemets dans les textes. 

Exemple 1 : 20/09/2006 19:30-22:00 "Répétition de rentrée" "Temple des Gobelins" "Reprise de contact, Duruflé, et mise au point des calendriers"
	(ajoute un évènement précis à une date précise, et d'une durée précise)
Exemple 2 : 17/08-23/08 "Stage d'été " "Les Salines" 
	(ajoute un évènement cette année, sans description et sur plusieurs jours)
Exemple 3 : 01/01/2007 "Bonne année à tous !" REP=01/01/2008,01/01/2009,01/01/2010
	(ajoute un évènement sans horaire, sans lieu, à une date précise et répété sur 3 autres dates)

*/

if (!defined("_ECRIRE_INC_VERSION")) return;

// essai d'intertionalisation...
$test = _T('onchargelalangue');
$GLOBALS[$GLOBALS['idx_lang']] += array(
 'saisie_rapide_entete' => "L'agenda pour les experts",
 'saisie_rapide_merci' => "Merci, vos &eacute;v&egrave;nements ont bien &eacute;t&eacute; enregistr&eacute;s :", 
 'saisie_rapide_compiler' => "Compiler et v&eacute;rifier la liste",
 'saisie_rapide_enregistrer' => "Enregistrer ces &eacute;v&egrave;nements",
 'saisie_rapide_votre_liste' => "VOTRE LISTE D'EVENEMENTS",
 'saisie_rapide_votre_liste_infos' => "Indiquer un seul &eacute;v&egrave;nement (&eacute;ventuellement ses r&eacute;p&eacute;titions) par ligne :",
 'saisie_rapide_article' => "Article propri&eacute;taire : ",
 'saisie_rapide_compilation' => "COMPILATION DE LA LISTE",
 'saisie_rapide_compilation_infos' => "Voici votre liste interpr&eacute;t&eacute;e par le compilateur.<br>En absence d'erreur, enregistrez d&eacute;finitvement les &eacute;v&egrave;nements suivants :",
 'saisie_rapide_evenement_de' => "EVENEMENTS DE : ",
 'saisie_rapide_heure_id' => "Id.",
 'saisie_rapide_heure_debut' => "Heure de d&eacute;but",
 'saisie_rapide_heure_fin' => "Heure de fin",
 'saisie_rapide_fermer' => "Fermer",
);
// fin de l'essai !!

global $spip_version_code;
if ($spip_version_code<1.92) { 
 include_spip('inc/presentation'); 
 function set_request($var, $val = NULL) {
	unset($_GET[$var]);
	unset($_POST[$var]);
	if ($val !== NULL) $_GET[$var] = $val;
 }
} else include_spip('inc/commencer_page');

include_spip('inc/agenda_filtres');
include_spip('inc/agenda_gestion');

function affiche_et_enregistre(&$t) {
 global $result;

 debut_cadre_enfonce("../plugins/agenda/img_pack/agenda-24.png", false, "", _T('saisie_rapide_merci')); 
 foreach($t as $e=>$v) if ($t[$e]=="") unset($t[$e]); 
 affiche_table_evenements($t); 
 fin_cadre_enfonce();
 echo "<div align='center'><button class='fondo' onClick='javascript:window.close()'>"._T('saisie_rapide_fermer').'</button></div>';
 set_request('evenement_insert', 1);
 foreach ($result as $r) {
  foreach ($r as $r2=>$v) set_request($r2, $v);
  Agenda_action_formulaire_article(_request('id_article'));
 } 
 affiche_evenements_article();
 echo "<script type=\"text/javascript\"><!--
 window.opener.location.reload();
 --></script>";
}

function affiche_evenements_article() {
 global $titre_defaut;
 //echo Agenda_formulaire_article(_request('id_article'), false);
 echo "<br>";
 debut_cadre_enfonce("../plugins/agenda/img_pack/agenda-24.png", false, "", _T('saisie_rapide_evenement_de').$titre_defaut); 
  list($s, $les_evenements) = Agenda_formulaire_article_afficher_evenements(_request('id_article'), false);
  echo $s;
 fin_cadre_enfonce();
}

function compile_t(&$t) {
 foreach($t as $e=>$v) {
  $t[$e]=trim(str_replace("\t", " ", $t[$e]));
  if (ereg ("([0-9]{1,2})/([0-9]{1,2})/?([0-9]{4})?-?([0-9]{1,2})?/?([0-9]{1,2})?/?([0-9]{4})? +".
  			"([0-9]{1,2})?:?([0-9]{1,2})?-?([0-9]{1,2})?:?([0-9]{1,2})? *".
			'" *([^ ^"][^"]*) *" *("([^"]*)")? *("([^"]*)")? *'.
			'(REP *=)?([0-9 /,]*)?', $t[$e]=trim($t[$e]), $regs)) {
   // annee_debut omise
   if($regs[3]=='') $regs[3]=date('Y', time());
   // annee_fin omise
   if($regs[6]=='') $regs[6]=$regs[3]; ;
   // heure_fin omise
   if($regs[9].$regs[10]=='') { $regs[9]=$regs[7]; $regs[10]=$regs[8]; }   
   // date_fin omise
   if($regs[4].$regs[5]=='') { $regs[4]=$regs[1]; $regs[5]=$regs[2]; }   
   // format complet
   for ($i=0;$i<=10;$i++) $regs[$i]=sprintf("%02d", intval($regs[$i]));
   $t[$e]="$regs[1]/$regs[2]/$regs[3]-$regs[4]/$regs[5]/$regs[6] $regs[7]:$regs[8]-$regs[9]:$regs[10]".
   		  " \"$regs[11]\" \"$regs[13]\" \"$regs[15]\" REP=".str_replace(' ','',trim($regs[17]));
  } else { if ($t[$e]!="") $t[$e]=""; else unset($t[$e]); }
 }
}

function affiche_table_evenements(&$t) { 
 global $result; unset($result); global $result; ?>
   <div class="liste liste-evenements"><table background="" border="0" cellpadding="2" cellspacing="2" width="100%">
   <tbody><tr class="tr_liste">
   <th><?=_T('saisie_rapide_heure_id')?></th>
   <th><?=_T('agenda:evenement_date_debut')?></th>
   <th><?=_T('agenda:evenement_date_fin')?></th>
   <th><?=_T('saisie_rapide_heure_debut')?></th>
   <th><?=_T('saisie_rapide_heure_fin')?></th>
   <th><?=_T('agenda:evenement_titre')?></th>
   <th><?=_T('agenda:evenement_lieu')?></th>
   <th><?=_T('agenda:evenement_descriptif')?></th>
 </tr><?php $n=0;
 foreach($t as $e=>$v) {
  echo "<tr ><th>".++$n."</th>";
  if (ereg ("([0-9]{1,2})/([0-9]{1,2})/([0-9]{4})-([0-9]{1,2})/([0-9]{1,2})/([0-9]{4}) ".
  			"([0-9]{1,2}):([0-9]{1,2})-([0-9]{1,2}):([0-9]{1,2}) ".
			'"([^"]*)" ("([^"]*)") ("([^"]*)") *'.
			'(REP=)([0-9 /,]*)?', $t[$e], $regs)) {
   echo "<td>$regs[1]/$regs[2]/$regs[3]</td><td>$regs[4]/$regs[5]/$regs[6]</td>";
   echo "<td><div align=center>$regs[7]:$regs[8]</div></td><td><div align=center>$regs[9]:$regs[10]</div></td>";
   echo "<td>$regs[11]</td><td>".($regs[13]?$regs[13]:"&nbsp;")."</td><td>".($regs[15]?$regs[15]:"&nbsp;")."</td></tr>\n";
   if ($regs[17]!="") echo "<tr ><th>&nbsp;</th><td colspan=7>Autres occurences : ".str_replace(',',', ',$regs[17])."</td></tr>";
   $result[$n]['evenement_titre']=$regs[11];
   $result[$n]['evenement_lieu']=$regs[13];
   $result[$n]['evenement_descriptif']=$regs[15];
   $result[$n]['jour_evenement_debut']=$regs[1];
   $result[$n]['mois_evenement_debut']=$regs[2];
   $result[$n]['annee_evenement_debut']=$regs[3];
   $result[$n]['evenement_horaire']="$regs[7]:$regs[8]-$regs[9]:$regs[10]"=="00:00-00:00"?'non':'oui';
   $result[$n]['heure_evenement_debut']=$regs[7];
   $result[$n]['minute_evenement_debut']=$regs[8];
   $result[$n]['jour_evenement_fin']=$regs[4];
   $result[$n]['mois_evenement_fin']=$regs[5];
   $result[$n]['annee_evenement_fin']=$regs[6];
   $result[$n]['heure_evenement_fin']=$regs[9];
   $result[$n]['minute_evenement_fin']=$regs[10];
   $result[$n]['selected_date_repetitions']=$regs[17];
  } else echo "<td colspan=7>Format invalide !</td></tr>";
 }
 ?></tbody></table></div><?php
 }

function affiche_compilation(&$t) {
 debut_cadre_enfonce("../plugins/agenda/img_pack/agenda-24.png", false, "", _T('saisie_rapide_compilation')); 
 echo _T('saisie_rapide_compilation_infos');
 ?>
  <form method="POST">
  <input name='exec' type='hidden' value='saisie_rapide' />
  <input name='action' type='hidden' value='enregistre' />
  <input name='id_article' type='hidden' value='<?=_request('id_article')?>' />
  <input name='liste_evenements' type='hidden' value="<?=htmlentities(_request('liste_evenements'))?>" />
  <?php affiche_table_evenements($t); ?>
 <div align='right'><input class='fondo' type='submit' value='<?=_T('saisie_rapide_enregistrer')?>'></div>
 </form>
 <?php
 fin_cadre_enfonce(); 
}

function affiche_formulaire() {
  debut_cadre_enfonce("../plugins/agenda/img_pack/agenda-24.png", false, "", _T('saisie_rapide_votre_liste')); 
  echo _T('saisie_rapide_votre_liste_infos');
  ?>
  
  <form method="POST">
  <input name='exec' type='hidden' value='saisie_rapide' />
  <input name='action' type='hidden' value='compile' />
  <input name='id_article' type='hidden' value='<?=_request('id_article')?>' />
  <textarea name="liste_evenements" style="width: 99%;" rows="10" class="forml" ><?=_request('liste_evenements')?></textarea>
  <div align='right'><input class='fondo' type='submit' value='<?=_T('saisie_rapide_compiler')?>'></div></form>
  <?php fin_cadre_enfonce(); 
    debut_cadre_formulaire(); ?>
    <strong>Syntaxe</strong> : &quot;jj/mm[/aaaa][-jj/mm[/aaaa]] [hh:mm[-hh:mm]]
    &quot;Le titre&quot;
[&quot;Le lieu&quot; [ &quot;La description&quot;]] [REP=jj/mm/aaaa[,jj/mm/aaaa,etc]]<br>
 Les crochets indiquent les &eacute;l&eacute;ments facultatifs. <br>
 Les r&eacute;p&eacute;titions de l'&eacute;v&egrave;nement sont indiqu&eacute;es par  'REP=' suivi d'une liste de dates s&eacute;par&eacute;es par des virgules. <br>
 Bien respecter les espaces (ou tabulations) entre les &eacute;l&eacute;ments et ne pas mettre de guillemets dans les textes. <br>
    <br>

    <em>Exemple 1</em> : 
20/09/2006 19:30-22:00 &quot;R&eacute;p&eacute;tition de rentr&eacute;e&quot; &quot;Temple des Gobelins&quot; &quot;Reprise de contact, Durufl&eacute;, et mise au point des calendriers&quot;<br />
<em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement pr&eacute;cis &agrave; une date pr&eacute;cise, et d'une dur&eacute;e  pr&eacute;cise)</em><br>
    <em>Exemple 2</em> : 
  17/08-23/08 &quot;Stage d'&eacute;t&eacute; <?=date('Y', time())?> &quot; &quot;Les Salines&quot; <br>
  <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement cette ann&eacute;e, sans description et sur plusieurs jours)<br />
  Exemple 3</em> : 
  01/01/2007 &quot;Bonne ann&eacute;e &agrave; tous !&quot; REP=01/01/2008,01/01/2009,01/01/2010<br />
  <em>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(ajoute un &eacute;v&egrave;nement sans horaire, sans lieu, &agrave; une date pr&eacute;cise et r&eacute;p&eacute;t&eacute; sur 3 autres dates)</em><br />
  <em></em>
  <?php fin_cadre_formulaire(); 
}

function exec_saisie_rapide_dist()
{ global $titre_defaut;
	//header("Content-Type: text/html; charset=utf-8");
	//echo _DOCTYPE_ECRIRE, html_lang_attributes();
	//echo "<head><title>", "L'agenda pour les experts",	"</title></head>\n";

	// s'assurer que les tables sont crees
	Agenda_install();

	include_spip('inc/headers');
	http_no_cache();
	echo init_entete(_T('saisie_rapide_entete'), 0);

	echo "<body>";
	$titre_defaut = "";
	$res = spip_query("SELECT titre FROM spip_articles where id_article=".spip_abstract_quote(_request('id_article')));
	if ($row = spip_fetch_array($res)) $titre_defaut = $row['titre'];
	echo '<h3>'._T('saisie_rapide_article')._request('id_article').". $titre_defaut</h3>";
    $t=split("\n",_request('liste_evenements'));
	compile_t($t);
	if  (_request('action')=='enregistre') affiche_et_enregistre($t);
	else {
		if (_request('liste_evenements')) affiche_compilation($t);
		affiche_formulaire();
		affiche_evenements_article();
	}	

	echo "</body></html>";
}
?>