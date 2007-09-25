<?php
/*
*   Plugin HoneyPot
*   Copyright (C) 2007 Pierre Andrews
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

$GLOBALS[$GLOBALS['idx_lang']] = array(
									   'cfg_titre' => 'Project Honey Pot',
									   'cfg_descriptif' => "The honeypot project provides a trap for spammer bots and havesters that might visit your web site.

This plugin provides a simple way to integrate trap links to your installed honeypot in your SPIP template. You will need to have previously installed a honeypot provided by [->http://projecthoneypot.org]",
									   'documentation' => "<p>To start using a honeypot on your website, you first have to create an account with <a href=\"http://projecthoneypot.org\">project honeypot</a>. Follow their instruction to download the honeypot script.
</p><p>
Once you have the archive on your computer, decompress it in some folder and look for a <em>.php</em> file. You will have to send this file to the root folder of your SPIP installation on your server.
</p><p>
Visit the honeypot page on your website and follow the activation instructions.
</p>
<hr/>
<p>
Once the honeypot is activated, you will have to put trap links on the main pages of your website. This plugin provides a <code>#HONEYPOT</code> tag that will generate hidden links to your visitors that will take the bots to the honeypot. Just put it somewhere in your templates.
</p><p>
First of all, you have to configure the plugin with the name of your honeypot file (without the <em>.php</em> extension). Then put the <code>#HONEYPOT</code> in your templates. If you use the default templates provided by SPIP, this plugin already provides a replacement for the footer template and you won't have to do anything.</p>",
'cfg_hpfile' => 'Honeypot file name (without the <em>.php</em> extension): '

);

?>
