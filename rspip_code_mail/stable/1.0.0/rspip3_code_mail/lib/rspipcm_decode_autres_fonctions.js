// code..js
// decrypt mail address
// By robert-A-T-attac-D-O-t-be 27/05/02
// Licence GNU GPL

function empoisonne() {
var poison="",i=0,u=0,d=0;
   tld = new Array(".be",".com",".net",".org",".eu",".fr",".it");
   u=Math.round(Math.random()*6)+6;
   d=Math.round(Math.random()*5)+4;
   for (i=1;i<=u;i++) {poison = poison.concat(String.fromCharCode(Math.round(Math.random()*25)+97));}
   poison = poison.concat("@");
   i=0;
   for (i=1;i<=d;i++) {poison = poison.concat(String.fromCharCode(Math.round(Math.random()*25)+97));}
   poison = poison.concat(tld[Math.round(Math.random()*(tld.length-1))]);
   return poison;
}


function decode(adr) {
// used by the browser
var email="",i=0,poison=300;
   
   // Si le robot a passé le captcha, on l'empoisonne.
   // En réserve ...
   // for (i=1;i<=poison;i++) {email=empoisonne();email="";}

   i=0
	for (i=adr.length-1;i>-1;i--) {
//		ch=adr[i];
		ch=adr.substring(i,i+1);
		if (ch==":") {ch="@"}
		if (ch=="!") {ch="?"}
		if (ch=="#") {ch="&"}
		email=email.concat(ch);
		}
   return email;
}
