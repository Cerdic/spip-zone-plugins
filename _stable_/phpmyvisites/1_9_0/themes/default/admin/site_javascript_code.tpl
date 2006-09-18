<h1>{'install_display_javascript_code'|translate}</h1>
{'install_js_code_text'|translate}
<code>
{$js_code}
</code>

<h2>Tips for advanced users (these tips will be integrated in a future documentation)</h2>
<h3>Classify pages into groups</h3>
<p>This is VERY useful for huge WebSites with content separated into categories. As you can see on phpMyVisites online demonstration, you can classify your pages into groups. You browse the groups and pages very easily in the phpMyVisites interface in the "Pages view" part.
To do this, it is very simple (thanks to phpMyVisites supreme power)</p>
<p>You have 3 working modes : </p>
<p>- <u>automatic</u> : if your website is physically structured with directories, phpmyvisites will detect them and class your pages into groups (named as your site directories)</p>
<p>- <u>manual</u> : if you don't have directories, for example if all your pages call "index.php" with different variables, you can set up a virtual page name and assign its value to the Javascript variable "pagename", separating the groups with the character slash "/"</p>

<code>pagename = 'group1/subgroup1/infinitegroup/hello_kitty';</code>  
<p>You can also set up a <u>semi-automatic</u> pagename using the HTML value of the TITLE markup (but there won't be group hierarchy)</p>
<code>pagename = document.title;</code>

<h3>Count files download and/or URL clicks</h3>
<p>If you want to count file downloads, or URL clicks, it is very simple. You have to change your URLs with the pattern : <br>
<code>
http://PATH_TO_YOUR_PHPMYVISITES/phpmyvisites.php?url=URL_WHERE_TO_REDIRECT&id=ID_SITE&pagename=FILE:NAME_OF_FILE
</code>
<p>Note that the string "FILE:" is mandatory! If you don't put this string, it won't work</p>
<p>For example, if you want to set up a Google url click count, instead of linking to "http://www.google.com", you will link to </p>
<code>phpmyvisites.php?url=http://www.google.com&id=1&pagename=FILE:google click count</code>
<p>Or if you want to count the files download (this link will redirect to "http://www.download.com/phpmyvisites.zip")</p>
<code>phpmyvisites.php?url=http://www.download.com/phpmyvisites.zip&id=1&pagename=FILE:phpmyvisites_last_version</code>

<h3>A super tip</h3>
<p>You can classify Files download, URL count, into groups! As you can for the classic pages. It allows to have a very precise report, well organized</p>
<p>For example this will word</p>
<code>phpmyvisites.php?url=http://download.com/phpmv.zip&id=1&pagename=FILE:files download/phpmyvisites/last release</code>
<p>The "File" will be classified into the groups files download/phpmyvisites/</p>

<h3>Good luck with phpmyvisites!</h3>
<p>If you're happy with it, don't hesitate to make a little donation, it helps us a lot :-) See on the official website for more information</p>

