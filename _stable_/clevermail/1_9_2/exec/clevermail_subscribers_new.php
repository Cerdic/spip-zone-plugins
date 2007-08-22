<?php
	/**
	 *
	 * CleverMail : plugin de gestion de lettres d'information basé sur CleverMail
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 *
	 **/

include_spip('inc/presentation');

function exec_clevermail_subscribers_new() {

	debut_page("CleverMail Administration", 'configuration', 'cm_index');

	echo debut_gauche('', true);
		include_spip("inc/clevermail_menu");
		echo '<br />';
		debut_cadre_relief();
			echo '<strong>CSV Format :</strong><br />';
			echo 'user@example.com<br />user2@example.com';
		fin_cadre_relief();
	echo debut_droite('', true);

	debut_cadre_relief();
		echo gros_titre('CleverMail Administration', '', '');
	fin_cadre_relief();

	debut_cadre_relief('../'._DIR_PLUGIN_CLEVERMAIL.'/img_pack/abonne.png');

		echo '<h3>'._T('clevermail:ajouter_abonne').' :</h3>';

		$data = array();
		if (isset($_FILES['cm_file']) && is_uploaded_file($_FILES['cm_file']['tmp_name'])) {
			$data = array_merge($data, file($_FILES['cm_file']['tmp_name']));
		}

		if (isset($_POST['cm_subs']) && strlen($_POST['cm_subs']) > 0) {
			$subString = ereg_replace("(\n|\r)+", "#", $_POST['cm_subs']);
			$subArray = explode('#', $subString);
			$data = array_merge($data, $subArray);
		}

		if (count($data) > 0) {
			if (isset($_POST['cm_lists']) && is_array($_POST['cm_lists'])) {
				$lists = $_POST['cm_lists'];
			}
			$nbSub = 0;
			$nbMaj = 0;
			if (isset($lists)) {
				reset($lists);
				foreach($data as $subscriber) {
					if (ereg("^([^@ ]+@[^@ ]+\.[^@.; ]+)(;[^;]*)?(;[^;]*)?$", $subscriber, $regs)) {
						// CSV format: user@example.com;
						list($address, $firstName, $lastName) = explode(';', $subscriber);
						$address = trim($regs[1]);

						$result = spip_fetch_array(spip_query("SELECT sub_id FROM cm_subscribers WHERE sub_email='".$address."'"));
					    if (!$recId = $result['sub_id']) {
					        // New e-mail address
					        spip_query("INSERT INTO cm_subscribers (sub_id, sub_email, sub_profile) VALUES ('', '".$address."', '')");
					        $recId = spip_insert_id();
					        spip_query("UPDATE cm_subscribers SET sub_profile = '".md5($recId.'#'.$address.'#'.time())."' WHERE sub_id='".$recId."'");
							$nbSub++;
				        }

						foreach($lists as $listId) {
							$list = spip_fetch_array(spip_query("SELECT COUNT(*) AS nb FROM cm_lists_subscribers WHERE lst_id = ".$listId." AND sub_id = ".$recId));
					        if ($list['nb'] == 0) {
					            // New subscription
					            $actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
								spip_query("INSERT INTO cm_lists_subscribers (lst_id, sub_id, lsr_mode, lsr_id) VALUES (".$listId.", ".$recId.", ".$_POST['cm_mode'].", '".$actionId."')");
					        } else if($list['nb'] == 1) {
					        	// Update subscription
					        	$actionId = md5('subscribe#'.$listId.'#'.$recId.'#'.time());
					        	spip_query("UPDATE cm_lists_subscribers SET lsr_mode = '".$_POST['cm_mode']."', lsr_id = '".$actionId."' WHERE lst_id = ".$listId." AND sub_id = ".$recId);
					        	$nbMaj++;
					        }
						}
				    } elseif (trim($subscriber) != '') {
						echo '<span class="error">'._T('clevermail:erreur').' : '.$subscriber.'</span><br />';
					}
				}
			} else {
				echo '<p class="error">'._T('aucune_liste').'</p>';
			}
			echo '<p class="noerror">';
			if($nbSub == 0) {
				echo _T('clevermail:aucun_abonne_ajoute').'<br />';
			} else {
				echo $nbSub.' ';
				if($nbSub > 1) {
					echo _T('clevermail:abonnes_ajoutes').'<br />';
				} else {
					echo _T('clevermail:abonne_ajoute').'<br />';
				}
			}
			if($nbMaj == 1) {
				echo $nbMaj.' '. _T('clevermail:maj_inscription');
			} else if($nbMaj > 1) {
				echo $nbMaj.' '. _T('clevermail:maj_inscriptions');
			}
			echo '</p>';
		}
?>
		<form enctype="multipart/form-data" action="<?php echo generer_url_ecrire('clevermail_subscribers_new',''); ?>" method="post">
		<?php echo debut_cadre_formulaire('', true) ?>
			<label><?php echo _T('clevermail:a_partir_csv') ?></label>
			<input type="file" name="cm_file" class="formo" /><br />

			<label><?php echo _T('clevermail:emails') ?> :</label>
			<textarea name="cm_subs" cols="50" rows="5" wrap="virtual" class="formo" ></textarea><br />

			<label><?php echo _T('clevermail:abonne_lettres') ?> :</label>
			<select name="cm_lists[]" multiple="multiple" class="formo" >
				<?php
				$lists = spip_query("SELECT lst_id, lst_name FROM cm_lists ORDER BY lst_name");
				while ($list = spip_fetch_array($lists)) {
					echo '<option value="'.$list['lst_id'].'">'.$list['lst_name'].'</option>';
				}
				?>
			</select><br />
			<?php echo _T('clevermail:mode') ?> :
	        <input type="radio" name="cm_mode" value="1" checked="checked" id="html" />
	        <label for="html" class="verdana1">HTML</label>
	        <input type="radio" name="cm_mode" value="0" id="txt" />
	        <label for="txt" class="verdana1">Text</label>
		<?php echo fin_cadre_formulaire(true) ?>
			<br />
			<div style="text-align: right">
				<input type="submit" value="<?php echo _T('clevermail:importer') ?>" class="fondo"  />
			</div>
		</form>
<?php

	fin_cadre_relief();

	fin_page();
}
?>
