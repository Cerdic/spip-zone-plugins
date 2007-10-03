/* 
FCKCommands.RegisterCommand(commandName, command)
       commandName - Command name, referenced by the Toolbar, etc...
       command - Command object (must provide an Execute() function).
*/

var HighlightCodeCommand=function(){
        //create our own command, we dont want to use the FCKDialogCommand because it uses the default fck layout and not our own
};

HighlightCodeCommand.prototype.Execute=function(){
}

HighlightCodeCommand.GetState=function() {
        return FCK_TRISTATE_OFF; //we dont want the button to be toggled
}

HighlightCodeCommand.Execute=function() {
        //open a popup window when the button is clicked
        window.open(FCKPlugins.Items['geshighlighter'].Path + 'highlight.html', 'GeshiHighlighter', 'width=500,height=400,scrollbars=no,scrolling=no,location=no,toolbar=no');
}


// Register the related commands.
FCKCommands.RegisterCommand(
   'coloration',
    new FCKDialogCommand(
        FCKLang['GeshighlighterHighlightTitle'],
        FCKLang['GeshighlighterHighlightTitle'],
        FCKPlugins.Items['geshighlighter'].Path + 'highlight.html', 600, 420));
        
var btnHighlight = new FCKToolbarButton('coloration', FCKLang['GeshighlighterHighlightTitle'])
btnHighlight.IconPath = FCKPlugins.Items['geshighlighter'].Path + 'highlighter.gif' ;

// 'My_Find' is the name used in the Toolbar config.
FCKToolbarItems.RegisterItem( 'coloration', btnHighlight ) ;