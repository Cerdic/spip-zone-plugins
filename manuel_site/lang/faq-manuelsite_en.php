<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/faq-manuelsite?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'The forums are enabled by default on your articles @complement@ and they are deactivated on an individual basis... Visitors can then react to your articles... You will be contacted by email each time a message is posted on one of your articles. Small flip side: spams which are not always easy to identify and that sometimes you need to manually manage. To process a forum message (remove it if it does not please you or report spam if it is one):
-* In the public site on the page of the article, if you are identified, there are two buttons "Delete this Message" or "SPAM"
-* In the private area, via the activity menu / Manage Forums',
	'forum_q' => 'How to manage the forums?',

	// I
	'img' => 'There is no "right" size to display an image in an article. In any case, no need to send an image of 3000 pixels wide, no screen can display it in its entirety! Unless the document is to be printed. 
-* If the picture has to be inserted into the text of an article,It depends on its content: if it’s a portrait, a height of 200px should be sufficient, and if it is a beautiful landscape, you can go up to @largeur_max@ pixels width;
-* If the picture is supposed to be placed in the portfolio of an article, avoid to exceed 1000 pixels wide and 600 pixels high;

{Please, note that the maximum weight should not exceed {{@poids_max@}}Mo otherwise the upload will be denied}.',
	'img_nombre' => 'It is possible to send a lot of photos in one click in an article:
-* Copy selected photos to a folder on your hard drive;
-* Resize them to a good size;
-* Insert them into a zip file;
-* Link this zip file to the article. At the end of the download, it asks you what you would like to do with the file, you can for example put all the pictures in the portfolio.',
	'img_nombre_q' => 'How easily fill a portfolio?',
	'img_ou_doc' => 'It mainly uses the tag <code><imgXX|center></code> to insert an image into a text article. But if you also want to display the title or description below the image, use <code><docXX|center></code>.',
	'img_ou_doc_q' => '<code><imgXX> or <docXX></code> ?',
	'img_q' => 'What size should my photo be?',

	// S
	'son' => 'Prepare your sound in mp3 format in mono with a frequency of 11 or 22 kHz and bitrate (compression) of 64kbps (or more if you would like a higher quality).

Link the mp3 file to your article as for an image and give it a title and maybe an optional description and a credit. Finally write in the body of your article where you want <code><docXX|center|player></code>. A flash player will appear in your public site to allow visitors to listen the sound.
_ {Warning, please note that the maximum size of a file is 150M, about more or less a length of 225 minutes}',
	'son_audacity' => 'To work an audio file, you can use the Audacity software (Mac, Windows, Linux) you can download here [->http://audacity.sourceforge.net/].

A few tips:
-* After installing the software, you will also need the lame library to encode to mp3 format [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* To change the file in mono: Menu {Tracks / Track stereo to mono}
-* To create the mp3 file: Menu {File / Export}
-* To set the bitrate: Menu {File / Export / Options / Quality}',
	'son_audacity_q' => 'How to prepare a sound?',
	'son_q' => 'How to add sound to an article?',

	// T
	'thumbsites' => 'Click "Reference a site" in the section {{@rubrique@}}. Fill in the url of the site, and validate, the system will try to get the title, description and a thumbnail of the site. Correct title and description if necessary. If the thumbnail is not generated automatically, make a screenshot and add it as the site logo with a size of 120x90 pixels.',
	'thumbsites_q' => 'How to reference a website in the links page?',
	'trier' => 'The numbers before the titles of articles / sections / documents, are used to manage their display order. The syntax is a number followed by a dot and a space',
	'trier_q' => 'How to manage the display order of articles / sections / documents?',

	// V
	'video_320x240' => 'Prepare your video in FLV format (streaming flash) with a size of 320x240 pixels, a bitrate (compression) of 400kbps and a sound mono/64kbps. To convert a video file, you can use avidemux software (Mac, Windows, Linux) that you can download here [->http://www.avidemux.org/].

Associate the created file with your article as an attachment, give it a title, a description, a credit, and a size (width 320, height 240). Finally put in the body of your article where you want <code><docXX|center|video></code>. A flash player will appear in your public site to allow visitors to play the video.
_ {Please note that the max size of a file is 150M, for an approximate length of 37.5 minutes}',
	'video_320x240_q' => 'How to add a video to an article?',
	'video_dist' => 'If your video is hosted on DailyMotion, YouTube or Vimeo, in a new tab of your browser, go to the page viewing of the video, and copy the url. In the edit page of your article click "Add Video" and paste the URL. Then insert into the text of the article <code><videoXX|center></code>',
	'video_dist_q' => 'How to add a video dailymotion (youtube,...) to an article?'
);

?>
