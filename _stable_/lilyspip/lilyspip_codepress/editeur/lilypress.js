/***********TEMPLATES*******************/

	function single_template(){
		lilycode.setCode('melody = \\relative c\' { \n\t\\clef treble \n\t\\key c \\major \n\t\\time 4/4 \n\n\ta4 b c d \n\t} \n\n\\score { \n\t\\new Staff \\melody \n\t\\layout {} \n\t\\midi {} \n\t}','lilypond');
		//pour activer la coloration syntaxique
		lilycode.editor.syntaxHighlight('init'); 
	}
    
	function piano_template(){
		lilycode.setCode('voiceup = \\relative c\'\' { \n\t\\clef treble \n\t\\key c \\major \n\t\\time 4/4 \n\n\ta b c d \n\t} \n\nvoicelow = \\relative c { \n\t\\clef bass \n\t\\key c \\major \n\t\\time 4/4 \n\n\ta2 c \n\t} \n\n\\score { \n\t\\new PianoStaff << \n\t\t\\new Staff = "voiceup" \\voiceup \n\t\t\\new Staff = "voicelow" \\voicelow \n\t>> \n\t\\layout {} \n\t\\midi {} \n\t}','lilypond');
		lilycode.editor.syntaxHighlight('init'); 
	}

	function quartet_template(){
		lilycode.setCode('global = { \n\t\\time 4/4 \n\t\\key c \\major \n\t} \n\nviolinOne = \\new Voice { \\relative c\'\'{ \n\t\\set Staff.instrumentName = "Violin 1 " \n\n\tc2 d e1 \\bar "|." \n\t}} \n\nviolinTwo = \\new Voice { \\relative c\'\'{ \n\t\\set Staff.instrumentName = "Violin 2 " \n\n\tg2 f e1 \\bar "|." \n\t}} \n\nviola = \\new Voice { \\relative c\' { \\set Staff.instrumentName = "Viola " \n\t\\clef alto \n\n\te2 d c1 \\bar "|." \n\t}} \n\ncello = \\new Voice { \\relative c\' { \\set Staff.instrumentName = "Cello " \n\t\\clef bass \n\n\tc2 b a1 \\bar "|." \n\t}} \n\n\\score { \n\t\\new StaffGroup << \n\t\t\\new Staff << \\global \\violinOne >> \n\t\t\\new Staff << \\global \\violinTwo >> \n\t\t\\new Staff << \\global \\viola >> \n\t\t\\new Staff << \\global \\cello >> \n\t>> \n\t\\layout {} \n\t\\midi {}\n\t}','lilypond');
		lilycode.editor.syntaxHighlight('init'); 
	}

	function vocal_template(){
		lilycode.setCode('global = { \n\t\\key c \\major \n\t\\time 4/4 \n\t} \n\nsopMusic = \\relative c\'\' { \n\tc4 c c8[( b)] c4 \n\t} \n\nsopWords = \\lyricmode { \n\thi hi hi hi \n\t} \n\naltoMusic = \\relative c\' { \n\te4 f d e \n\t} \n\naltoWords = \\lyricmode { \n\tha ha ha ha \n\t} \n\ntenorMusic = \\relative c\' { \n\tg4 a f g \n\t} \n\ntenorWords = \\lyricmode { \n\thu hu hu hu \n\t} \n\nbassMusic = \\relative c { \n\tc4 c g c \n\t} \n\nbassWords = \\lyricmode { \n\tho ho ho ho \n\t} \n\n\\score { \n\t\\new ChoirStaff << \n\t\t\\new Lyrics = sopranos { s1 } \n\t\t\\new Staff = women << \n\t\t\t\\new Voice = "sopranos" { \\voiceOne << \\global \\sopMusic >> } \n\t\t\t\\new Voice = "altos" { \\voiceTwo << \\global \\altoMusic >> } \n\t\t\t>> \n\t\t\\new Lyrics = "altos" { s1 } \n\t\t\\new Lyrics = "tenors" { s1 } \n\t\t\\new Staff = men << \n\t\t\t\\clef bass \n\t\t\t\\new Voice = "tenors" { \\voiceOne <<\\global \\tenorMusic >> } \n\t\t\t\\new Voice = "basses" { \\voiceTwo <<\\global \\bassMusic >> } \n\t\t\t>> \n\t\t\\new Lyrics = basses { s1 } \n\t\t\\context Lyrics = sopranos \\lyricsto sopranos \\sopWords \n\t\t\\context Lyrics = altos \\lyricsto altos \\altoWords \n\t\t\\context Lyrics = tenors \\lyricsto tenors \\tenorWords \n\t\t\\context Lyrics = basses \\lyricsto basses \\bassWords \n\t\t>> \n\n\t\\layout { \n\t\t\\context { \n\t\t\\Staff \\override VerticalAxisGroup #\'minimum-Y-extent = #\'(-3 . 3)\n\t\t}\n\t}\n}','lilypond');
		lilycode.editor.syntaxHighlight('init'); 
	}
    
    
  
  
/********INITIALISATION**************/ 
  
	
	function selection(zone){
		this.s1 = "";
		this.s2 = "";
		this.s3 = "";
		
		this.recup_code = recup_code;
		this.existe = existe;
		
		if ((clientVer >= 4) && is_ie && is_win)
		{
			var theSelection = false;

 			theSelection = top.opener.document.selection.createRange().text; // Get text selection
			
			this.s1 = top.opener.document.getElementById("text_area").value;
			
			if (theSelection) {
				this.s2 = theSelection;
			} else {
				this.s1 = top.opener.document.getElementById("text_area").value;
			}
		}
		else {
			var selLength = zone.textLength;
			var selStart = zone.selectionStart;
			var selEnd = zone.selectionEnd;

			if (selEnd == 1 || selEnd == 2) selEnd = selLength;
	
			// Raccourcir la selection par double-clic si dernier caractere est espace	
			if (selEnd - selStart > 0 && (zone.value).substring(selEnd-1,selEnd) == ' ') selEnd = selEnd-1;
			this.s1 = (zone.value).substring(0,selStart);
			this.s2 = (zone.value).substring(selStart, selEnd)
			this.s3 = (zone.value).substring(selEnd, selLength);
		}

	

	
		function recup_code(){
			//pour supprimer les balises lilypond selectionnées
			code = (this.s2).replace(/<\/*lilypond>\s*/gi,'');
			return code;
		}
		
		function existe() {return (this.s2!="")} //indique si le code lilypond a été sélectionné
	}
	
	
	
	var ancien_code;
	
	function init(){
		   
		ancien_code = new selection(top.opener.zone_selection);
			
		if (ancien_code.existe()) {
			    lilycode.setCode(ancien_code.recup_code(),'lilypond') ;
			    lilycode.editor.syntaxHighlight('init');
		}
		
		
	}
	
	function d(s){debug.innerHTML+=s;}
 
    

/****GENERATION DU CODE LILYPOND ******/
    
// l'id du cadre contenant la previsualisation
	var outImage="previewField";

	function previsualise(adrserver,adrimagevide){

		var source=adrserver+'?code='+escape(lilycode.getCode())+'&format=png' ;
		var field=document.getElementById(outImage);
 
		// affichage de l'image vide en attendant le chargement de la partition
		field.src=adrimagevide;
 
		globalPic=new Image();
		globalPic.src=source; 
		field.src=globalPic.src;
	}
    
    
	function construit_code_lilypond(){
	      
		var le_code = lilycode.getCode();
		var texte="";
    	
		texte += "\n<lilypond> \n" + le_code +  "\n<\/lilypond> \n"
	
		return texte;	
	}
	
		
 

	function enregistre(){
		if (ancien_code.existe()) {
			if ((clientVer >= 4) && is_ie && is_win) {
				top.opener.document.selection.createRange().text = construit_code_lilypond();
			} else {
				top.opener.zone_selection.value = ancien_code.s1 + construit_code_lilypond() + ancien_code.s3;
			}
		} else { //insertion d'un nouveau tableau
			if (top.opener.zone_selection.createTextRange && top.opener.zone_selection.caretPos) { //IE
				var caretPos = top.opener.zone_selection.caretPos;
				caretPos.text = caretPos.text + construit_code_lilypond();
				top.opener.zone_selection.focus();
			} else {
				top.opener.zone_selection.value = ancien_code.s1 + construit_code_lilypond() + ancien_code.s3;
			}
		}
		window.close();		
	}
	
	

	
	


