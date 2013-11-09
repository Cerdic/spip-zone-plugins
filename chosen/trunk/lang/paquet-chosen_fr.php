<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/chosen/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'chosen_description' => '[Chosen->http://harvesthq.github.com/chosen/] est une librairie JavaScript qui améliore l’expérience utilisateur des sélecteurs dans les formulaires HTML.

La classe CSS <code>chosen</code> sur un <code><select></code> chargera automatiquement Chosen dessus.
Cette branche du plugin Chosen est basée sur le fork de koenpunt - version 1.0.0 - voir https://github.com/koenpunt/chosen/releases/.

Avec ce fork, Chosen permet de <a href=\'https://github.com/harvesthq/chosen/pull/166\'>créer de nouvelles options</a> dans un &lt;select&gt; existant, à condition qu’il ait la classe ’chosen-create-option’.
Lorsque chosen crée une nouvelle &lt;option&gt; (le mot ’nouveau’ par ex.) dans un &lt;select&gt;, celle-ci prend la forme suivante : <code>&lt;option selected=\'selected\' value=\'chosen_nouveau\'&gt;nouveau&lt;/option&gt;</code>.
Bien noter le préfixe ’chosen_’ ajouté dans le paramètre ’value’ pour permettre de différencier les &lt;option&gt; créées par Chosen.',
	'chosen_nom' => 'Chosen (fork de koenpunt)',
	'chosen_slogan' => 'Intégrer la librairie Chosen dans SPIP (fork de koenpunt)'
);

?>
