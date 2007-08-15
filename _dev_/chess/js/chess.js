/************************************************************************************************************
@fileoverview
Chess Widget
Copyright (C) 2007  DTHMLGoodies.com, Alf Magne Kalleland

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA

Dhtmlgoodies.com., hereby disclaims all copyright interest in this script
written by Alf Magne Kalleland.

Alf Magne Kalleland, 2007
Owner of DHTMLgoodies.com


************************************************************************************************************/	

/**
 * 
 * @package DHTML Chess
 * @copyright Copyright &copy; 2007, www.dhtmlgoodies.com
 * @author Alf Magne Kalleland <post@dhtmlgoodies.com>
 */
var DHTMLGoodies = new Object();
if(!String.trim)String.prototype.trim = function() { return this.replace(/^\s+|\s+$/, ''); };
var D_chessObjects = new Array();
var ChessWidgetEventFuncs = new Object();

/**
* @constructor
* @class Store chess information
* @version				1.0
* @version 1.0
* @author	Alf Magne Kalleland(www.dhtmlgoodies.com)
**/
DHTMLGoodies.ChessPgn = function(props)
{
	var pgnFile;
	var gameList;
	var ajaxObjects;
	var serverFile;
	var gameDetails;
	var cachedGameList;
	var numberOfGames;
	var pgnTimeStamps;
	
	this.pgnTimeStamps = new Object();
	this.gameDetails = new Array();	// JS cache - JS object describing a game. array
	this.cachedGameList = new Object();	// JS cache - Game list.
	this.numberOfGames = new Object();	// JS cache - Number of games.
	
	this.serverFile = 'serverFile';
	this.ajaxObjects = new Array();
	if(props)this.__setInitialProperties(props);
	this.objectIndex = D_chessObjects.length;
	D_chessObjects[this.objectIndex] = this;	
	
}
DHTMLGoodies.ChessPgn.prototype = {
	
	// {{{ __setInitialProperties()
    /**
     *
     * @private
     */ 	
	__setInitialProperties : function(props)
	{
		if(props.pgnFile)this.pgnFile = props.pgnFile;
		if(props.serverFile)this.serverFile = props.serverFile;
	}
	// }}}
	,
	// {{{ __getGameDetails()
    /**
     *
     * @private
     */ 	
	__getGameDetails : function(gameIndex,jsOnComplete,liveUpdateMode)
	{
		if(this.gameDetails[gameIndex] && !liveUpdateMode){
			this.__returnGameDetailsFromCache(gameIndex,jsOnComplete);
			return;	
		}
		var objIndex = this.objectIndex;
		var index = this.ajaxObjects.length;
		this.ajaxObjects[index] = new sack();
		this.ajaxObjects[index].method = 'GET';
		this.ajaxObjects[index].requestFile = this.serverFile;	
		this.ajaxObjects[index].setVar('getGameDetails','1');
		this.ajaxObjects[index].setVar('pgnFile',this.pgnFile);
		this.ajaxObjects[index].setVar('gameIndex',gameIndex);
	
		if(!this.pgnTimeStamps[this.pgnFile + '_' + gameIndex] || !this.gameDetails[gameIndex])this.pgnTimeStamps[this.pgnFile + '_' + gameIndex]=0;
		this.ajaxObjects[index].setVar('timestamp',this.pgnTimeStamps[this.pgnFile + '_' + gameIndex]);
		
		this.ajaxObjects[index].onCompletion = function(){ D_chessObjects[objIndex].__returnGameDetails(gameIndex,index,jsOnComplete); }		
		this.ajaxObjects[index].onError = function(){ this.__ajaxError(); }		
		this.ajaxObjects[index].runAJAX();		
		
	}
	// }}}
	,
	// {{{ __isNumberOfGamesSet()
    /**
     *
     * @private
     */ 
	__isNumberOfGamesSet : function()
	{
		return (this.numberOfGames[this.pgnFile]?true:false);	
	}
	// }}}
	,
	// {{{ __getNumberOfGames()
    /**
     *
     * @private
     */ 
	__getNumberOfGames : function()
	{
		return this.numberOfGames[this.pgnFile];
	}
	// }}}
	,
	// {{{ __getNumberOfGamesFromServer()
    /**
     *
     * @private
     */ 
	__getNumberOfGamesFromServer : function(jsOnComplete)
	{
		if(this.numberOfGames[this.pgnFile]){
			this.__returnNumberOfGamesFromCache(jsOnComplete);
			return;	
		}		
		var objIndex = this.objectIndex;
		var index = this.ajaxObjects.length;
		this.ajaxObjects[index] = new sack();
		this.ajaxObjects[index].method = 'GET';
		this.ajaxObjects[index].requestFile = this.serverFile;	
		this.ajaxObjects[index].setVar('getNumberOfGames','1');
		this.ajaxObjects[index].setVar('pgnFile',this.pgnFile);
		this.ajaxObjects[index].onCompletion = function(){ D_chessObjects[objIndex].__returnNumberOfGames(index,jsOnComplete); }		
		this.ajaxObjects[index].onError = function(){ this.__ajaxError(); }		
		this.ajaxObjects[index].runAJAX();			
	}
	// }}}
	,
	// {{{ __returnNumberOfGames()
    /**
     *
     * @private
     */ 
	__returnNumberOfGames : function(ajaxIndex,jsOnComplete)
	{
		this.numberOfGames[this.pgnFile] = this.ajaxObjects[ajaxIndex].response;
		eval(jsOnComplete + '(' + this.ajaxObjects[ajaxIndex].response + ')');		
	}
	// }}}
	,
	// {{{ __returnNumberOfGamesFromCache()
    /**
     *
     * @private
     */ 
	__returnNumberOfGamesFromCache : function(jsOnComplete)
	{
		eval(jsOnComplete + '(' + this.numberOfGames[this.pgnFile] + ')');
	}	
	// }}}
	,
	// {{{ __returnGameDetails()
    /**
     *  Return game details in json format.
     * 
     * @private
     */	
	__returnGameDetails : function(gameIndex,index,jsOnComplete)
	{
		try{
			var obj = eval('(' + this.ajaxObjects[index].response + ')');
			if(!obj.result)obj.result='*';
		}catch(e){
			self.status = 'Error in returned JSON string';
			//alert('Error in returned JSON string:\n' + this.ajaxObjects[index].response);			
			return;
		}
		if(!obj){
			eval(jsOnComplete + '(false)');
			return;
		}
		this.gameDetails[gameIndex] = obj;
		
		this.__setPgnTimestamp(gameIndex);

		if(jsOnComplete.indexOf('(')>=0){
			jsOnComplete = jsOnComplete.replace(')','');
			eval(jsOnComplete + ',this.gameDetails[' + gameIndex + '])');	
		}else{
			eval(jsOnComplete + '(this.gameDetails[' + gameIndex + '])');	
		}		
	}
	// }}}
	,
	// {{{ __setPgnTimestamp()
    /**
     *  Set timestamp for pgn
     * 
     * @private
     */	
	__setPgnTimestamp : function(gameIndex)
	{
		var d = new Date();
		this.pgnTimeStamps[this.pgnFile + '_' + gameIndex] = Math.floor(d.getTime()/1000);		
	}
	// }}}
	,
	// {{{ __returnGameDetailsFromCache()
    /**
     *  Return game details in json format(from cache)
     * 
     * @private
     */	
	__returnGameDetailsFromCache : function(gameIndex,jsOnComplete)
	{
		if(jsOnComplete.indexOf('(')>=0){
			jsOnComplete = jsOnComplete.replace(')','');
			eval(jsOnComplete + ',this.gameDetails[' + gameIndex + '])');	
		}else{
			eval(jsOnComplete + '(this.gameDetails[' + gameIndex + '])');	
		}			
		
	}
	// }}}
	,
	// {{{ __getGameList()
    /**
     * 
     * @private
     */	
	__getGameList : function(jsOnComplete)
	{
		if(this.cachedGameList[this.pgnFile]){			
			this.__returnGameListFromCache(jsOnComplete);
			return;	
		}
		var objIndex = this.objectIndex;
		var index = this.ajaxObjects.length;
		this.ajaxObjects[index] = new sack();
		this.ajaxObjects[index].method = 'GET';
		this.ajaxObjects[index].requestFile = this.serverFile;	
		this.ajaxObjects[index].setVar('getGameList','1');
		this.ajaxObjects[index].setVar('pgnFile',this.pgnFile);
		this.ajaxObjects[index].onCompletion = function(){ D_chessObjects[objIndex].__returnGameList(index,jsOnComplete); }		
		this.ajaxObjects[index].onError = function(){ this.__ajaxError(); }		
		this.ajaxObjects[index].runAJAX();
	}
	// }}}
	,
	// {{{ __returnGameList()
    /**
     *  Return game list in json format.
     * 
     * @private
     */	
	__returnGameList : function(index,jsOnComplete)
	{
		this.cachedGameList[this.pgnFile] = this.ajaxObjects[index].response;
		
		if(jsOnComplete.indexOf('(')>=0){
			jsOnComplete = jsOnComplete.replace(')','');
			eval(jsOnComplete + ',this.ajaxObjects[' + index + '].response)');	
		}else{
			eval(jsOnComplete + '(this.ajaxObjects[' + index + '].response)');	
		}	
	}
	// }}}
	,
	// {{{ __returnGameListFromCache()
    /**
     * 
     * @private
     */	
	__returnGameListFromCache : function(jsOnComplete)
	{
		if(jsOnComplete.indexOf('(')>=0){
			jsOnComplete = jsOnComplete.replace(')','');
			eval(jsOnComplete + ',this.cachedGameList[this.pgnFile])');	
		}else{
			eval(jsOnComplete + '(this.cachedGameList[this.pgnFile])');	
		}		
	}
	// }}}
	,
	// {{{ __ajaxError()
    /**
     * 
     * @private
     */	
	__ajaxError : function()
	{
		alert('Could not complete ajax request for ' + this.pgnFile);
	}
	// }}}
	,
	// {{{ __setPgnFile()
    /**
     * 
     * @private
     */	
	__setPgnFile : function(pgnFile)
	{
		this.gameList = new Array();
		this.gameDetails = new Array();
		this.pgnFile = pgnFile;		
	}
		
}


/**
* @constructor
* @class Store chess information
* @version				1.0
* @version 1.0
* @author	Alf Magne Kalleland(www.dhtmlgoodies.com)
**/
DHTMLGoodies.Chess = function(props)
{
	var divBoard;
	var divBoardOuter;
	
	var cssPath;
	var parentRef;
	var squareSize;
	var imageFolder;
	var isOldMSIE;
	var boardLabels;
	var flipBoardWhenBlackToStart;	
	var flipBoardWhenBlackWins;
	var flipBoard;
	var divIndicators;
	var indicateLastMove;
	var coordLastMove;
	var animate;
	var animationSpeed;
	var autoplayDelayBeforeComments;
	var stopAutoplayBeforeComments;

	var languageCode;
	
	var chessSet;
	var pgnObject;
	var colorLightSquares;
	var colorDarkSquares;
	var pieces;
	var objectIndex;
	var currentGameIndex;
	var currentMove;
	var currentColor;
	var currentMoveNumber;
	
	var currentVariationMove;
	var currentVariationColor;
	var currentVariationMoveNumber;
	
	var dragProperties;	// Properties for currently dragged piece
	
	var animateNextMove;
	var isBusy;	// Variable indicating if the script is busy animating
	var currentZIndex;
	var lastMoveEnPassant;
	var currentHighlightInlineMove;
	var currentHighlightTableMove;
	var currentHihglightInlineVariationMove;
	
	var currentHighlightedGame;
	var previousClassHighlighedGame;
	
	var elPlayerNames;
	var elMovesInline;
	var elMovesInTable;
	var elMovesInTableMaxMovesPerTable;
	var elActiveMove;
	var elActiveComment;
	var elGameAttributes;
	var insideVariation;
	var displayPrefaceCommentWithInlineMoves;
	var slideCoordinates;
	var eventElements;
	var dragAndDropColor;
	var officers;	

	this.dragAndDropColor = false;
	this.eventElements = new Array();
	this.slideCoordinates = new Array();
	this.dragProperties = new Object();
	this.animationSpeed = 1;

	this.elMovesInTable = new Array();
	this.languageCode = chess_languageCode;
	this.officers = new Object();
	
	this.officers.en = ['B','R','Q','N','K'];
	this.officers.no = ['L','T','D','S','K'];
	this.officers.fr = ['F','T','D','C','R'];
	this.officers.it = ['A','T','D','C','R'];
	this.officers.es = ['A','T','D','C','R'];
	this.officers.de = ['L','T','D','S','K'];
	
	this.currentMoveNumber = 1;
	this.divIndicators = new Object();
	this.chessSet = 'alpha';
	this.squareSize = 60;
	this.elMovesInTableMaxMovesPerTable = 900;
	this.isOldMSIE = (navigator.userAgent.toLowerCase().match(/msie\s[0-6]/gi))?true:false;
	this.isMSIE = (navigator.userAgent.toLowerCase().indexOf('msie')>=0)?true:false;
	this.isOpera = (navigator.userAgent.toLowerCase().indexOf('opera')>=0)?true:false;
	this.isFirefox = (navigator.userAgent.toLowerCase().indexOf('firefox')>=0)?true:false;
	this.elGameAttributes = new Object();
	this.stopAutoplayBeforeComments = false;
	this.displayPrefaceCommentWithInlineMoves = true;
	this.currentGameIndex = false;
	
	this.animationSetTimeout = 13;
	this.insideVariation = false;
	this.indicateLastMove = true;
	this.coordLastMove = new Object();
	this.coordLastMove.from = new Object();
	this.coordLastMove.to = new Object();
	this.animate = true;
	this.cssPath = 'plugins/chess/css/chess.css';
	this.parentRef = document.body;
	this.imageFolder = 'plugins/chess/images/';
	this.boardLabels = true;
	this.flipBoardWhenBlackToStart = true;
	this.flipBoardWhenBlackWins = true;
	this.flipBoard = false;
	this.animateNextMove = false;
	this.isBusy = false;
	this.currentZIndex = 20000;
	this.lastMoveEnPassant = false;
	this.autoPlayDelayBetweenMoves = 0.5;
	this.autoPlayActive = false;
	this.gameListProperties = new Object();
	this.objectIndex = D_chessObjects.length;
	D_chessObjects[this.objectIndex] = this;
	
	if(props)this.__setInitProps(props);
	this.pgnObject = new DHTMLGoodies.ChessPgn(props);
	this.__init();

	
}


DHTMLGoodies.Chess.prototype = {
	// {{{ setAutoPlayDelay()
    /**
     * Specify auto play speed, i.e. seconds between each move.
     *
     * @param Integer autoPlayDelay - seconds between each move when "AutoPlay" is active. (default = 0.5 seconds)
     * 
     * @public
     */		
	setAutoPlayDelayBetweenMoves : function(autoPlayDelayBetweenMoves){
		if(autoPlayDelayBetweenMoves<0.1)autoPlayDelayBetweenMoves = 0.1;
		this.autoPlayDelayBetweenMoves = autoPlayDelayBetweenMoves;
	}
	// }}}
	,
	// {{{ setPgn()
    /**
     * setDragAndDropColor
     *
     * @param String dragAndDropColor - Specify which color it should be possible to drag. possible values: 'white' and 'black'
     * 
     * @public
     */		
	setDragAndDropColor : function(dragAndDropColor){
		this.dragAndDropColor = dragAndDropColor;
	}
	// }}}
	,
	// {{{ setPgn()
    /**
     * Specify new pgn file
     *
     * @param String pgnFile - Path to new pgn file (relative path from the html file)
     * 
     * @public
     */		
	setPgn : function(pgnFile)
	{		
		if(this.isBusy || this.autoPlayActive)return;
		this.currentGameIndex = false;
		this.__clearGameDetails();
		this.__clearCurrentVariationVariables();
		this.pgnObject.__setPgnFile(pgnFile);	
		this.__handleCallback('switchPgn');
	}
	// }}}
	,
	// {{{ setSquareSize()
    /**
     *  Set size of squares on the board(default = 60). The board will be updated instantly. However, this method
     *	can not be invoked when auto play is active or the script is busy animating a move.
     *
     * @param Integer squareSize - Size of squares
     * 
     * @public
     */		
	setSquareSize : function(squareSize)
	{
		if(this.isBusy || this.autoPlayActive)return;
		var toMove = this.currentMove;
		var toColor = this.currentColor;			
		this.squareSize = squareSize;
		this.__clearBoard();
		if(this.currentGameIndex===false){
			this.__createDefaultPieces();
			return;
		}		
			
		
		if(this.pgnObject.gameDetails[this.currentGameIndex].fen){
			this.displayBoardByFen(this.pgnObject.gameDetails[this.currentGameIndex].fen,this.parentRef);
		}else{
			this.__createDefaultPieces();
		}	
		if(this.insideVariation){
			this.goToVariationMove(this.currentVariationMove,this.currentVariationColor,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);			
		}else{
			if(toMove==0)this.currentMove=0;			
			if(toMove>0)this.goToMove(toMove,toColor);
		}	
		this.__createIndicators();
		this.__indicateLastMove();	
		
	}
	// }}}
	,
	// {{{ setChessSet()
    /**
     * Specify new chess set. The board will be updated instantly. However, this method
     *	can not be invoked when auto play is active or the script is busy animating a move.
     *
     * @param String chessSet - Name of chess set(possible values: "smart","alpha","merida","leipzig",traveler","motif","cases")
     * 
     * @public
     */	
	setChessSet : function(chessSet)
	{
		if(this.isBusy || this.autoPlayActive)return;
		this.chessSet = chessSet;
		var toMove = this.currentMove;
		var toColor = this.currentColor;				
		this.__clearBoard();
		if(this.currentGameIndex===false){
			this.__createDefaultPieces();
			return;
		}
		if(this.pgnObject.gameDetails[this.currentGameIndex].fen){
			this.displayBoardByFen(this.pgnObject.gameDetails[this.currentGameIndex].fen,this.parentRef);
		}else{
			this.__createDefaultPieces();
		}
		if(this.insideVariation){
			this.goToVariationMove(this.currentVariationMove,this.currentVariationColor,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);			
		}else{
			if(toMove==0)this.currentMove=0;			
			if(toMove>0)this.goToMove(toMove,toColor);
		}		
	}
	// }}}
	,
	// {{{ flip()
    /**
     *  Flips the board. The board will be updated instantly. However, this method
     *	can not be invoked when auto play is active or the script is busy animating a move.
     *
     * 
     * @public
     */		
	flip : function()
	{
		if(this.isBusy || this.autoPlayActive)return;
		if(this.flipBoard){
			this.flipBoard = false;
		}else{
			this.flipBoard = true;	
		}
		var toMove = this.currentMove;
		var toColor = this.currentColor;		
		this.__clearBoard();
		if(this.currentGameIndex===false){
			this.__createDefaultPieces();
			return;
		}
		if(this.pgnObject.gameDetails[this.currentGameIndex].fen){
			this.displayBoardByFen(this.pgnObject.gameDetails[this.currentGameIndex].fen,this.parentRef);
		}else{
			this.__createDefaultPieces();
		}	
		if(this.insideVariation){
			this.goToVariationMove(this.currentVariationMove,this.currentVariationColor,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);			
		}else{
			if(toMove==0)this.currentMove=0;			
			if(toMove>0)this.goToMove(toMove,toColor);
		}		
	}
	// }}}
	,
	// {{{ getNextMove()
    /**
     *  Return next move to be played.
     *
     * @public
     */	
	getNextMove : function()
	{
		var move = this.__getNextMove();	
		return move.notation;
	}
	// }}}
	,
	// {{{ getResult()
    /**
     *  Return result of game
     *
     * @public
     */		
	getResult : function()
	{
		if(this.currentGameIndex===false)return false;
		if(!this.pgnObject.gameDetails[this.currentGameIndex].result)return false;
		return this.pgnObject.gameDetails[this.currentGameIndex].result.trim();	
	}
	// }}}
	,
	// {{{ getPgnFile()
    /**
     *  Return name/path of pgn file
     *
     * @public
     */		
	getPgnFile : function()
	{
		return this.pgnObject.pgnFile;
	}
	// }}}
	,		
	// {{{ getStartColor()
    /**
     *  Return color of the player who starts the game.
     *
     * @public
     */	
	getStartColor : function()
	{
		return this.whoToStartMove=='w'?'white':'black';	
	}
	// }}}
	,
	// {{{ __init()
    /**
     *  Script init
     *
     * @private
     */		
	__init : function()
	{
		this.__loadCss(this.cssPath);
		this.__addGeneralEvents();

		
	}
	// }}}

	,
	// {{{ getCurrentGameIndex()
    /**
     *  Returns index of currently displayed game.(0 = first game)
     *
     * 
     * @public
     */	
	getCurrentGameIndex : function()
	{
		return this.currentGameIndex;
	}
	// }}}

	,
	// {{{ showGame()
    /**
     *  Display a game in the selected pgn
     *	@param Integer gameIndex - index of game, first game = index 0
     *
     * 
     * @public
     */		
	showGame : function(gameIndex)
	{		
		this.__handleCallback('beforeGameLoad');
		this.currentGameIndex = gameIndex;
		this.__clearCurrentVariationVariables();
		this.__clearDragProperties();
		var ind = this.objectIndex;		

		this.pgnObject.__getGameDetails(gameIndex,'D_chessObjects[' + ind + '].__showGame',false);
		return gameIndex;				
	}
	// }}}
	,
	// {{{ __getNewGameData()
    /**
     * 
     * @private
     */	
	__getNewGameData : function()
	{
		if(this.isBusy || this.autoPlayActive  )return;
		if(this.currentGameIndex===false)return;

		var lastMove = this.__getLastMove();

		var ind = this.objectIndex;	

	}
	// }}}
	,
	// {{{ displayGameListInSelect()
    /**
     *	Display list of games in selected pgn file in a select box, format: "white name vs. black name"
     *	@param Object selectRef - reference to select box.
     * @public
     */	
	displayGameListInSelect : function(selectRef)
	{
		if(this.isBusy || this.autoPlayActive)return;
		var ind = this.objectIndex;		
		this.pgnObject.__getGameList('D_chessObjects[' + ind + '].__displayGameListInSelect("' + selectRef + '")');	
		
	}
	// }}}
	,
	// {{{ __displayGameListInSelect()
    /**
     *
     * @private
     */	
	__displayGameListInSelect : function(selectRef,json)
	{
		selectRef = this.__getEl(selectRef);
		var ind = this.objectIndex;
		selectRef.setAttribute('objectIndex',ind);
		selectRef.onchange = this.__showGameFromSelect;
		this.__addEventEl(selectRef);
		selectRef.options.length=0;
		var ind = this.objectIndex;
		try{
			var gameList = eval('(' + json + ')');
		}catch(e){
			alert(json);
		}
		
		for(var no in gameList){			
			selectRef.options[selectRef.options.length] = new Option(gameList[no].white + ' vs. ' + gameList[no].black,no);

		}					
		
	}
	// }}}
	,
	// {{{ __showGameFromSelect()
    /**
     *
     * @private
     */		
	__showGameFromSelect : function()
	{
		var objectIndex = this.getAttribute('objectIndex');
		D_chessObjects[objectIndex].showGame(this.options[this.selectedIndex].value);
		
	}
	// }}}
	,
	// {{{ displayGameListInTable()
    /**
     *  Display a list of games from specified pgn in a TABLE tag. Remember that this tag needs a thead tag where you have your heading and an empty tbody tag where the games will e listed dynamically by
     *	this method.
     *
     *	@param Object tableRef - Reference to table, either id or a direct reference(i.e. document.getElementById('myTable') or similar)
     *  @param Array props - which properties to show, i.e. columns in the table. example ['view','white','black','result','event'], all properties except "view" are properties in the pgn file.
     *	@param Object viewProperties - view properties, this is an associative array and the only property so far is "viewGameLink" which is the label of the link which displays the game.
     *				example of this argument:  { viewGameLink:'View game' } 
     *
     * @public
     */		
	displayGameListInTable : function(tableRef,props,viewProperties)
	{
		if(this.isBusy || this.autoPlayActive)return;
		if(props && !this.__isArray(props)){	// Properties sent in as commadelimited string
			props = props.split(/,/g);
		}
		tableRef = this.__getEl(tableRef);
		this.__clearTBodyRows(tableRef);		
		var ind = this.objectIndex;
		var string = '';
		if(!props){
			props = ['view','white','black','result'];
		}
		for(var no=0;no<props.length;no++){
			if(no==0)string = '['; else string = string + ',';
			string = string + '"' + props[no] + '"';
		}
		if(string)string = string + ']';
		
		if(viewProperties)this.gameListProperties = viewProperties;			
		this.pgnObject.__getGameList('D_chessObjects[' + ind + '].__displayGameListInTable("' + tableRef.id + '",' + string + ')');		
	}
	,
	// {{{ __displayGameListInTable()
    /**
     *  Display game list in predefined table.
     * 
     * @private
     */
	__displayGameListInTable : function(tableRef,props,json)
	{		
		tableRef = this.__getEl(tableRef);
		var ind = this.objectIndex;
		try{
			var gameList = eval('(' + json + ')');
		}catch(e){
			alert(json);
		}
		this.__clearTBodyRows(tableRef);
		var tbody = tableRef.getElementsByTagName('tbody')[0];
		if(!tbody){
			alert('Your game list table is missing a <tbody> element. Please insert it');
			return;
		}		
		
		var counter=0;		
		var d = new Date();
		var start = d.getTime();
		
		var currentRowClassName = 'GameListOddRow';
		var tableContent = '';
		
		var rowTemplate = '<tr id="ChessGameList<ID>" class="<CLASSNAME>">';
		for(var no2=0;no2<props.length;no2++){
			for(var no2=0;no2<props.length;no2++){
				if(props[no2]=='view'){
					rowTemplate = rowTemplate + '<td><a href="#" id="game<ID>" onclick="D_chessObjects[' + ind + '].showGame(<ID>);return false"><PROPERTY_view></a></td>';
				}else{
					rowTemplate = rowTemplate + '<td><PROPERTY_' + props[no2] + '></td>';
				}				
				
			}		
		}
		rowTemplate+='</TR>';
		
		for(var no in gameList){	
			currentRowClassName = (currentRowClassName=='GameListOddRow'?'GameListEvenRow':'GameListOddRow');
			var thisRow = rowTemplate;
			thisRow = rowTemplate.replace(/<ID>/g,no);
			thisRow = thisRow.replace(/<CLASSNAME>/g,currentRowClassName);
			thisRow = thisRow.replace('<PROPERTY_view>',this.gameListProperties.viewGameLink);
			for(var no2=0;no2<props.length;no2++){
				thisRow = thisRow.replace('<PROPERTY_' + props[no2] + '>',gameList[no][props[no2]]);			
				
			}
			tableContent = tableContent + thisRow;
		}		
		this.__replaceTbody(tableContent,tableRef);
		
		var d2 = new Date();
		var end = d2.getTime();
		// alert(end-start);	
		return;
						
	}
	// }}}
	,	
	// {{{ __showGame()
    /**
     *  Called by the pgn class at the top when game details has been loaded via ajax. 
     * 
     * @private
     */		
	__showGame : function(details)
	{		

		this.flipBoard = false;	
		this.stopAutoPlay();
		if(details.fen){
			this.__setWhoToMoveFromFen(details.fen); 
			if(this.whoToStartMove=='b' && this.flipBoardWhenBlackToStart)this.flipBoard=true; 
			this.__clearBoard();
			this.displayBoardByFen(details.fen,this.parentRef);
		}else{

			this.__clearBoard();
			this.__createDefaultPieces();
		}		
		this.currentMove = 0;
		this.currentColor = this.whoToStartMove=='w';
		this.__clearDisplayedActiveMove();
		this.__highlightActiveGame();
		this.__displayGameDetails();		
		this.__handleCallback('startGame');
		
	}
	// }}}
	,
	// {{{ __clearGameDetails()
    /**
     *  Clear displayed game attributes
     * 
     * @private
     */	
	__clearGameDetails : function()
	{
		this.__clearMoveDetails();
		for(var prop in this.elGameAttributes){
			try{
				this.__getEl(this.elGameAttributes[prop]).innerHTML = '';	
			}catch(e){
				
			}		
		}	
		if(this.elPlayerNames)this.elPlayerNames.innerHTML='';			
	}
	// }}}
	,
	// {{{ __displayGameDetails()
    /**
     *  Display game details when uses selects a game.
     * 
     * @private
     */	
	__displayGameDetails : function()
	{
		this.__clearGameDetails();
		
		this.__displayMoveDetails();		
		var gameDetails = this.pgnObject.gameDetails[this.currentGameIndex];
		for(var prop in this.elGameAttributes){
			if(gameDetails[prop.toLowerCase()]){
				try{
					var el = this.__getEl(this.elGameAttributes[prop]);
					el.innerHTML = gameDetails[prop.toLowerCase()];
				}catch(e){
					
				}
			}			
		} 
		if(this.elPlayerNames){
			this.elPlayerNames.innerHTML = this.pgnObject.gameDetails[this.currentGameIndex].white + ' vs. ' + this.pgnObject.gameDetails[this.currentGameIndex].black;
			
		}		
	}
	// }}}
	,
	// {{{ __highlightActiveGame()
    /**
     *  Highlight active game in game list.
     * 
     * @private
     */	
	__highlightActiveGame : function()
	{
		if(this.currentHighlightedGame){
			this.currentHighlightedGame.className = this.previousClassHighlighedGame;
		}
		if(document.getElementById('ChessGameList' + this.currentGameIndex)){
			this.currentHighlightedGame = document.getElementById('ChessGameList' + this.currentGameIndex);
			if(this.currentHighlightedGame.className!='ActiveGameInTable')this.previousClassHighlighedGame = this.currentHighlightedGame.className;
			this.currentHighlightedGame.className = 'ActiveGameInTable';
		}			
	}
	// }}}
	,
	// {{{ __clearMoveDetails()
    /**
     *  Clear move details, i.e. remove inline moves. This method is called by the __clearGameDetails method.
     * 
     * @private
     */	
	__clearMoveDetails : function()
	{
		
		if(this.elMovesInline)this.elMovesInline.innerHTML = '';
		if(this.elMovesInTable){
			for(var no=0;no<this.elMovesInTable.length;no++){
				this.__clearTBodyRows(this.elMovesInTable[no]);					
			}
		}
		
	}
	,
	// }}}
	__clearCurrentVariationVariables : function()
	{
		this.insideVariation = false;
		this.currentVariationMove = 0;
		this.currentVariationColor = 'white';		
		this.currentVariationMoveNumber = false;		
	}
	,
	__setStartVariationVariables : function(moveRoot,moveRootColor,variationIndex)
	{
		this.insideVariation = { move:moveRoot,color:moveRootColor,variationIndex:variationIndex };
		var variations = this.pgnObject.gameDetails[this.currentGameIndex].moves[moveRoot][moveRootColor].variation[variationIndex];
		for(var prop in variations){
			this.currentVariationMoveNumber = prop*2;
			this.currentVariationMove = prop/1;
			this.currentVariationColor = 'white';
			if(!variations[prop].white){
				this.currentVariationMoveNumber++;
				this.currentVariationColor = 'black';
			}
			break;	
		}
		
		if(this.currentVariationColor=='black'){	// Set current variation move prior to the start of the variation
			this.currentVariationColor = 'white';
		}else{
			this.currentVariationColor = 'black';
			this.currentVariationMove--;
		}
	}
	// }}}
	,
	goToVariationMove : function(move,color,moveRoot,moveRootColor,variationIndex)
	{
		var goToMove = moveRoot;
		var goToColor = moveRootColor;
		var goToMoveNumber = (move*2) + (color=='black'?1:0);
		if(goToColor=='black'){
			goToColor='white';
		}else{
			goToColor='black';
			goToMove--;
		}
		var animateNext = this.animateNextMove;
		this.animateNextMove = false;
		if(this.insideVariation && (this.insideVariation.move!=moveRoot || this.insideVariation.color!=moveRootColor || this.insideVariation.variationIndex!=variationIndex)){
			this.goToMove(goToMove,goToColor);
			this.__clearCurrentVariationVariables();
			this.__setStartVariationVariables(moveRoot,moveRootColor,variationIndex);
		}
		if(!this.insideVariation){
			this.goToMove(goToMove,goToColor);
			this.__setStartVariationVariables(moveRoot,moveRootColor,variationIndex);
		}
		if(this.insideVariation && goToMoveNumber<this.currentVariationMoveNumber){
			this.goToMove(goToMove,goToColor);
			this.__setStartVariationVariables(moveRoot,moveRootColor,variationIndex);				
		}
		this.animateNextMove = animateNext;
		var variations = this.pgnObject.gameDetails[this.currentGameIndex].moves[moveRoot][moveRootColor].variation[variationIndex];
		var pieceMoved = false;
		for(var no=this.currentVariationMoveNumber;no<=goToMoveNumber;no++){
			pieceMoved = true;
			var moveNumber = Math.floor(no/2);
			var moveToColor = no%2==1?'black':'white';	
			if(variations[moveNumber][moveToColor]){
				this.__parseAMove(variations[moveNumber][moveToColor],moveToColor);	
				this.currentVariationMove = moveNumber;
				this.currentVariationColor = moveToColor;	
				this.currentVariationMoveNumber=no;			
			}
		}
		this.currentVariationMoveNumber++;
		this.__indicateLastMove();		
		this.__highlightActiveVariationMove(move,color,moveRoot,moveRootColor);
	}
	// }}}
	,	
	// {{{ __getMoveDetailsForVariation()
    /**
     *  Return inline move string for a variation branch
     * 
     * @private
     */	
	__getMoveDetailsForVariation : function(variationObj,move,color)
	{
		var ind = this.objectIndex;
		var retValue = '';
		for(var variationIndex in variationObj){			
			var aVariation = variationObj[variationIndex]
			
			var ret = '';
			for(var prop in aVariation){
				if(ret)ret = ret + ' '; else ret = '[';
				if(aVariation[prop].white){
					ret = ret + prop + '. <a class="InlineVariationMove" href="#" id="InlineVariationMove_' + move + '_' + color + '_' + variationIndex + '_' + prop + '_' + 'white" onclick="D_chessObjects[' + ind + '].goToVariationMove(\'' + prop + '\',\'white\',\'' + move + '\',\'' + color + '\',\'' + variationIndex + '\');return false">' + this.__getAMoveInLanguage(aVariation[prop].white) + '</a>';
					if(aVariation[prop].black){
						ret = ret + ' <a class="InlineVariationMove" href="#" id="InlineVariationMove_' + move + '_' + color + '_' + variationIndex + '_' + prop + '_' + 'black" onclick="D_chessObjects[' + ind + '].goToVariationMove(\'' + prop + '\',\'black\',\'' + move + '\',\'' + color + '\',\'' + variationIndex + '\');return false">' + this.__getAMoveInLanguage(aVariation[prop].black) + '</a>' ;
					}
				}else{
					ret = ret + prop + '... <a class="InlineVariationMove" href="#" id="InlineVariationMove_' + move + '_' + color + '_' + variationIndex + '_' + prop + '_' + 'black" onclick="D_chessObjects[' + ind + '].goToVariationMove(\'' + prop + '\',\'black\',\'' + move + '\',\'' + color + '\',\'' + variationIndex + '\');return false">' + this.__getAMoveInLanguage(aVariation[prop].black) + '</a>' ;				
				}							
			}			
			ret = ret + '] ';
			ret = ' <span class="InlineChessVariationBlock">' + ret + '</span>';
			retValue = retValue + ret;
		}	
		return retValue;		
	}
	// }}}
	,	
	// {{{ __getClassNameOfInlineMove()
    /**
     * 
     * @private
     */
	__getClassNameOfInlineMove : function(move)
	{
		var ret = 'InlineChessMove_plainMove';
		if(move.indexOf('!')>=0)ret = 'InlineChessMove_goodMove';
		if(move.indexOf('!!')>=0)ret = 'InlineChessMove_badMove';					
		if(move.indexOf('?')>=0)ret = 'InlineChessMove_veryGoodMove';
		if(move.indexOf('??')>=0)ret = 'InlineChessMove_veryBadMove';
		if(move.indexOf('!?')>=0)ret = 'InlineChessMove_supriseMove';
		if(move.indexOf('?!')>=0)ret = 'InlineChessMove_questionableMove';	
		return ret;
	}
	// }}}
	,
	// {{{ __getAMoveInLanguage()
    /**
     *  Return language specific version of a move.
     * 
     * @private
     */		
	__getAMoveInLanguage : function(move)
	{
		if(this.languageCode=='en')return move;
		for(var no=0;no<this.officers.en.length;no++){
			move = move.replace(this.officers.en[no],this.officers[this.languageCode][no]);			
		}		
		return move;
	}	
	,	
	// {{{ __displayMoveDetails()
    /**
     *  Display move details
     * 
     * @private
     */	
	__displayMoveDetails : function()
	{
		
		this.__clearGameDetails();
		var moves = this.pgnObject.gameDetails[this.currentGameIndex].moves;
		var ind = this.objectIndex;
		
		if(this.elMovesInline){	// Display move inline as a string
			var string = '';
			if(this.pgnObject.gameDetails[this.currentGameIndex].prefaceComment && this.displayPrefaceCommentWithInlineMoves){
				string = string + '<span class="InlineChessComment">' + this.pgnObject.gameDetails[this.currentGameIndex].prefaceComment + '</span> ';				
			}
			for(var prop in moves){
				if(string)string = string + ' ';
				if(moves[prop].white){
					var className=this.__getClassNameOfInlineMove(moves[prop].white.move);

					string = string + prop + '.' + '<a class="' + className + '" id="InlineChessMove' + prop + 'white" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'white\');return false">' + this.__getAMoveInLanguage(moves[prop].white.move) + '</a>';
					if(moves[prop].white.comment){
						string = string + ' <span class="InlineChessComment">' + moves[prop].white.comment + '</span> ';
					}
					if(moves[prop].white.variation){
						string = string + this.__getMoveDetailsForVariation(moves[prop].white.variation,prop,'white');						
					}
					if(moves[prop].black){
						var className=this.__getClassNameOfInlineMove(moves[prop].black.move);
						string = string + ' ' + '<a class="' + className + '" id="InlineChessMove' + prop + 'black" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'black\');return false">' + this.__getAMoveInLanguage(moves[prop].black.move) + '</a>';
						if(moves[prop].black.comment){
							string = string + ' <span class="InlineChessComment">' + moves[prop].black.comment + '</span> ';
						}
						if(moves[prop].black.variation){
							string = string + this.__getMoveDetailsForVariation(moves[prop].black.variation,prop,'black');						
						}						
					}
				}else{
					var className=this.__getClassNameOfInlineMove(moves[prop].black.move);
					string = string + prop + ' ...' + '<a class="' + className + '" id="InlineChessMove' + prop + 'black" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'black\');return false">' + this.__getAMoveInLanguage(moves[prop].black.move) + '</a>';					
					if(moves[prop].black.comment){
						string = string + ' <span class="InlineChessComment">' + moves[prop].black.comment + '</span> ';
					}
					if(moves[prop].black.variation){
						string = string + this.__getMoveDetailsForVariation(moves[prop].black.variation,prop,'black');						
					}					
				}				
			}
			string = string + ' ' + this.pgnObject.gameDetails[this.currentGameIndex].result;
			this.elMovesInline.innerHTML = string;
		}
		if(this.elMovesInTable[0]){
			var tbody = this.elMovesInTable[0].getElementsByTagName('TBODY')[0];
			if(!tbody){
				alert('Cannot display moves inside a table because it is missing a <tbody> element');
				return;
			}
			var tableCounter = 0;
			var moveCounter = 1;
			for(var prop in moves){
				if(moveCounter>this.elMovesInTableMaxMovesPerTable){
					tableCounter++;
					moveCounter=1;
					var tbody = this.elMovesInTable[tableCounter].getElementsByTagName('TBODY')[0];
				}
				var row = document.createElement('TR');
				tbody.appendChild(row);
				
				var cell = document.createElement('TD');
				cell.innerHTML = prop;
				row.appendChild(cell);
			
				if(moves[prop].white){
					var cell = document.createElement('TD');
					cell.innerHTML = '<a id="TableChessMove' + prop + 'white" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'white\');return false">' + this.__getAMoveInLanguage(moves[prop].white.move) + '</a>';
					row.appendChild(cell);					
					
					if(moves[prop].black){
						var cell = document.createElement('TD');
						cell.innerHTML = '<a id="TableChessMove' + prop + 'black" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'black\');return false">' + this.__getAMoveInLanguage(moves[prop].black.move) + '</a>';
						row.appendChild(cell);							
					}
				}else{
					// No white move - create empty cell
					var cell = document.createElement('TD');
					cell.innerHTML = '...';
					row.appendChild(cell);
					
					if(moves[prop].black){
						var cell = document.createElement('TD');
						cell.innerHTML = '<a id="TableChessMove' + prop + 'black" href="#" onclick="D_chessObjects[' + ind + '].goToMove(\'' + prop + '\',\'black\');return false">' + this.__getAMoveInLanguage(moves[prop].black.move) + '</a>';
						row.appendChild(cell);							
					}				
				}		
					
				moveCounter++;		
			}			
		}	
	}
	// }}}
	,
	// {{{ __showGameLink()
    /**
     *  User have clicked on "View" in the game list, call showGame from this method.
     * 
     * @private
     */	
	__showGameLink : function()
	{
		var ind = this.getAttribute('objectIndex');
		D_chessObjects[ind].showGame(this.id.replace(/[^0-9]/g,''));
		
	}
	// }}}
	,

	// {{{ moveToStart()
    /**
     *  Move to the start of the game. This method won't do anything when the script is busy animating or if no game has been selected.
     * 
     * @public
     */
	moveToStart : function()
	{		
		if(this.currentGameIndex===false || this.isBusy)return;
		if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])return;
		if(this.pgnObject.gameDetails[this.currentGameIndex].fen){
			this.displayBoardByFen(this.pgnObject.gameDetails[this.currentGameIndex].fen,this.parentRef);
		}else{
			this.__createDefaultPieces();
		}
		if(this.insideVariation){
			var firstMove = this.__getFirstVariationMove(this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);
			this.goToVariationMove(firstMove.move,firstMove.color,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex)			
		}else{
			this.currentMove=0;
			this.currentColor=0;
			this.currentMoveNumber=1;			
			var tmpColor='white';
			if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1].white)tmpColor='black';
			this.__highlightActiveMove(0,tmpColor);	// 0 since no highlight		
			this.__displayActiveMove(1,tmpColor);			

		}	
						
	}
	// }}}
	,
	// {{{ moveToEnd()
    /**
     *  Move to the end of the game. This method won't do anything when the script is busy animating or if no game has been selected.
     * 
     * @public
     */
	moveToEnd : function()
	{
		if(this.currentGameIndex===false || this.isBusy)return;
		if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])return;
		var moveObj =  this.pgnObject.gameDetails[this.currentGameIndex].moves;
		if(this.insideVariation){
			var lastMove = this.__getLastVariationMove(this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);
			this.goToVariationMove(lastMove.move,lastMove.color,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);
		}else{
			var lastMove = this.__getLastMove();
			this.goToMove(lastMove.move,lastMove.color);
		}
	}
	// }}}
	,
	// {{{ __getLastMove()
    /**
     *  Return an associative array with keys "move" and "color" for the last move in the game.
     * 
     * @private
     */
	__getLastMove : function()
	{
		var moveObj =  this.pgnObject.gameDetails[this.currentGameIndex].moves;
		var lastMove = false;
		for(var prop in moveObj){
			lastMove=prop;
		}		
		if(lastMove===false)return { move:0,color:'white' }
		var ret = new Object();
		ret.move = lastMove;
		if(moveObj[lastMove].black)ret.color='black'; else ret.color='white';		
		return ret;
	}
	// }}}
	,
	// {{{ __getFirstVariationMove()
    /**
     *  Return an associative array with keys "move" and "color" for the first move in a variation.
     * 
     * @private
     */	
	__getFirstVariationMove : function(moveRoot,moveRootColor,variationIndex)
	{
		var moveObj =  this.pgnObject.gameDetails[this.currentGameIndex].moves[moveRoot][moveRootColor].variation[variationIndex];
		for(var prop in moveObj){
			lastMove=prop;
			break;
		}			
		var ret = new Object();
		ret.move = lastMove;
		if(!lastMove)return 'white';
		if(moveObj[lastMove].black)ret.color='black'; else ret.color='white';	
		return ret;				
	}
	// }}}
	,
	// {{{ __getLastMove()
    /**
     *  Return an associative array with keys "move" and "color" for the last move in a variation.
     * 
     * @private
     */	
	__getLastVariationMove : function(moveRoot,moveRootColor,variationIndex)
	{
		
		var moveObj =  this.pgnObject.gameDetails[this.currentGameIndex].moves[moveRoot][moveRootColor].variation[variationIndex];
		for(var prop in moveObj){
			lastMove=prop;
		}			
		var ret = new Object();
		ret.move = lastMove;
		if(!lastMove)return 'white';
		if(moveObj[lastMove].black)ret.color='black'; else ret.color='white';	
		return ret;		
	}
	// }}}
	,
	// {{{ move()
    /**
     *  Move on the board
     *	@param Integer - number of moves, for example 1 for one step forward or -1 for one step back.
     * 
     * @public
     */
	move : function(moves)
	{
		if(this.currentGameIndex===false || this.isBusy)return;
		if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])return;
		
		var fullMoves = Math.floor(Math.abs(moves)/2);
		var halfMoves = Math.abs(moves)%2;		
		
		
		if(this.insideVariation){	// Inside variation - get current variation move and color
			var color = this.currentVariationColor;
			var move = this.currentVariationMove;			
		}else{	// Following main line
			var color = this.currentColor;
			var move = this.currentMove;			
		}
		
		if(moves>0){
			if(halfMoves==1){
				if(color=='white'){
					color='black';
				}else{
					color='white';
					move++;	
				}
			}
		}
		if(moves<0){
			fullMoves*=-1;
			if(halfMoves==1){
				if(color=='black'){
					color='white';
				}else{
					color='black';
					move--;	
				}
			}				
		}
		move+=fullMoves;
			
		if(this.insideVariation){	// We are inside a variation
			if(move<=this.insideVariation.move){
				if(color=='white' && this.insideVariation.color=='black')return;	// We're at the start of the variation
				if(move<this.insideVariation.move)return;// We're at the start of the variation				
			}		
			if(moves>0){
				var lastMove = this.__getLastVariationMove(this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);				
			}
		}else{
			if(move<=0){
				this.moveToStart();
				this.__hideIndicators();	
				return;		
			}
			if(moves>0){
				var lastMove = this.__getLastMove();
			}			
		}
		
		this.animateNextMove = false;
		if(this.animate && moves==1)this.animateNextMove = true;			
		if(moves>0){
			if(move>=lastMove.move){
				if(move>lastMove.move){
					move = lastMove.move/1;
					color = lastMove.color;
					this.animateNextMove = false;
					if(moves==1)return;// already at last move
				}else{
					move = lastMove.move/1;
					if(color=='black' && lastMove.color=='white'){
						color='white';
						this.animateNextMove = false;
					}
				}
			} 
		}
						
		if(this.insideVariation){	// We are inside a variation
			if(moves!=0){
				this.goToVariationMove(move,color,this.insideVariation.move,this.insideVariation.color,this.insideVariation.variationIndex);
			}				
		}else{	// Inside main line
			if(moves!=0){
				if(moves>0 && move==1 && color=='white' && !this.pgnObject.gameDetails[this.currentGameIndex].moves[1]['white'])color='black';
				this.goToMove(move,color,moves);
			}

		}
	}
	// }}}
	,
	// {{{ autoPlay()
    /**
     *  Start autoplay mode. this method won't do anything if no game has been selected.
     * 
     * @public
     */
	autoPlay : function()
	{
		if(this.autoPlayActive || this.currentGameIndex===false)return;	// Already in auto play mode or no games loaded			
		if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])return;
		this.autoPlayActive = true;
		var moveDetails = this.__getNextMove();
		var move = moveDetails.move;
		var color = moveDetails.color;
	
		this.__autoPlayStep(move,color);
	}
	// }}}
	,
	// {{{ __autoPlayStep()
    /**
     *  This method takes care of the step by step moves in autoplay mode.
     * 
     * @private
     */
	__autoPlayStep : function(move,color)
	{		
		if(this.insideVariation){
			var moveObj =  this.pgnObject.gameDetails[this.currentGameIndex].moves[this.insideVariation.move][this.insideVariation.color].variation[this.insideVariation.variationIndex];			
			if(moveObj[move] && moveObj[move][color]){// Move exists
				this.move(1);					
			}else{
				this.stopAutoPlay();
				this.__handleCallback('stopAutoPlay');
			}	
		}else{
			if(this.pgnObject.gameDetails[this.currentGameIndex] && this.pgnObject.gameDetails[this.currentGameIndex].moves[move] && this.pgnObject.gameDetails[this.currentGameIndex].moves[move][color]){// Move exists	
				this.move(1);		
			}else{
				this.stopAutoPlay();
				this.__handleCallback('stopAutoPlay');
			}
		}	
	}
	// }}}
	,
	// {{{ stopAutoPlay()
    /**
     *  Exit autoplay mode.
     * 
     * @public
     */
	stopAutoPlay : function()
	{
		this.autoPlayActive = false;
	}
	// }}}
	,
	// {{{ goToMove()
    /**
     *  Go to a specific move in the game
     *	@param Integer moveNumber - example: 2
     *	@param String color, example "black"
     * 
     * @public
     */
	goToMove : function(moveNumber,color,direction)
	{

		if(this.currentGameIndex===false || this.isBusy || !moveNumber){
			this.__hideIndicators();
			return;
		}	
		
		if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])return;	
		
		var tmpNo = (moveNumber*2) + (color=='black'?1:0);		
		if(tmpNo<this.currentMoveNumber || this.insideVariation){
			this.__clearBoard();
			if(this.pgnObject.gameDetails[this.currentGameIndex].fen){
				this.displayBoardByFen(this.pgnObject.gameDetails[this.currentGameIndex].fen,this.parentRef);
			}else{
				this.__createDefaultPieces();
			}			
			this.currentColor = this.whoToStartMove;		
		}
		if(tmpNo==this.currentMoveNumber-1)return;
		
		var tmpColor;
		if(this.currentColor=='w')tmpColor='white'; else tmpColor='black';
		for(var no=this.currentMoveNumber;no<tmpNo;no++){	/* Loop through moves */
			var tmpColor = (no%2==1?'white':'black');		
			var moveIndex = Math.ceil(no/2);	
			
			if(no==tmpNo-1){ // Only highlight the last move in the loop, i.e. the actual move we're moving to.
				this.__highlightActiveMove(moveIndex,tmpColor);			
				this.__displayActiveMove(moveIndex,tmpColor);			
			}
			if(this.pgnObject.gameDetails[this.currentGameIndex].moves[moveIndex] && this.pgnObject.gameDetails[this.currentGameIndex].moves[moveIndex][tmpColor])this.__parseAMove(this.pgnObject.gameDetails[this.currentGameIndex].moves[moveIndex][tmpColor].move,tmpColor);
		}		
		var indicate = true;
		if(direction<0 && moveNumber==1){
			if(color=='white')indicate=false;
			if(color=='black' && !this.pgnObject.gameDetails[this.currentGameIndex].moves[1].white)indicate=false;
		}
		
		if(indicate)this.__indicateLastMove();
		this.currentMoveNumber = moveNumber*2 + (color=='black'?1:0);
		this.currentMove = moveNumber/1;
		this.currentColor = color;
		
		this.__clearCurrentVariationVariables();
	}
	// }}}
	,
	// {{{ __highlightActiveMove()
    /**
     *  Highlight active move in table or inline view
     * 
     * @private
     */
	__highlightActiveMove : function(moveIndex,color)
	{
		if(this.currentHighlightInlineMove){
			try{
				var className = this.currentHighlightInlineMove.className.replace(' ActiveInlineChessMove','');
				var className = className.replace(' ActiveInlineVariationChessMove','');
				className = className.trim();
				this.currentHighlightInlineMove.className=className;
			}catch(e){				
			}
		}
		if(this.currentHighlightTableMove){
			try{
				this.currentHighlightTableMove.className='';
			}catch(e){				
			}
		}
		if(document.getElementById('InlineChessMove' + moveIndex + color)){
			this.currentHighlightInlineMove = document.getElementById('InlineChessMove' + moveIndex + color);
			var newClass = this.currentHighlightInlineMove.className + ' ActiveInlineChessMove';
			this.currentHighlightInlineMove.className = newClass;
			this.__autoScrollContainerInlineMove(this.currentHighlightInlineMove);
		}
		if(document.getElementById('TableChessMove' + moveIndex + color)){
			this.currentHighlightTableMove = document.getElementById('TableChessMove' + moveIndex + color);
			this.currentHighlightTableMove.className = 'ActiveTableChessMove';
			this.__autoScrollContainerTableMove(this.currentHighlightTableMove);
		}		
	}
	// }}}
	,
	// {{{ __highlightActiveVariationMove()
    /**
     *  Highlight active move in table or inline view
     * 
     * @private
     */
	__highlightActiveVariationMove : function(moveIndex,color,moveRoot,colorRoot)
	{
		this.__highlightActiveMove(0,'white'); // remove standard highlight	
		var idOfEl = 'InlineVariationMove_' + this.insideVariation.move + '_' + this.insideVariation.color + '_' + this.insideVariation.variationIndex + '_' + moveIndex + '_' + color;
		if(document.getElementById(idOfEl)){
			this.currentHighlightInlineMove = document.getElementById(idOfEl);
			this.currentHighlightInlineMove.className = this.currentHighlightInlineMove.className + ' ActiveInlineVariationChessMove';
			this.__autoScrollContainerInlineMove(this.currentHighlightInlineMove);
		}		

		
	}
	// }}}
	,
	// {{{ __autoScrollContainerTableMove()
    /**
     *  Auto scroll games moves in table
     * 
     * @private
     */
	__autoScrollContainerTableMove : function(el)
	{
		try{
			var parent = el.parentNode.parentNode;
			el = el.parentNode;
			while(parent!=this.elMovesInTable[0] && parent.tagName.toLowerCase()!='body')parent = parent.parentNode;	// Find parent element
			var overflow = this.__getStyle(parent,'overflow');
			if(overflow && (overflow!='auto' || overflow!='scroll')){
				parent = parent.parentNode;
				var overflow = this.__getStyle(parent,'overflow');
			}		
	
			if(el.offsetTop > (parent.clientHeight + parent.scrollTop - el.offsetHeight - 2)){
				parent.scrollTop = (el.offsetTop - parent.clientHeight + el.offsetHeight +2);
			}		
		}catch(e){
			
		}
	}
	// }}}
	,
	// {{{ __autoScrollContainerInlineMove()
    /**
     *  Automatically scroll highlighted inline move into view
     *	@param Object el - reference to inline move link
     * 
     * @private
     */
	__autoScrollContainerInlineMove : function(el)
	{
		var parent = el.parentNode;
		while(parent!=this.elMovesInline)parent = parent.parentNode;	// Find parent element
		if(el.offsetTop > (parent.clientHeight + parent.scrollTop - el.offsetHeight - 2)){
			parent.scrollTop = (el.offsetTop - parent.clientHeight + el.offsetHeight +2);
		}	
		
	}
	// }}}
	,
	// {{{ __clearDisplayedActiveMove()
    /**
     *  Clear the element where active move is eventually beeing displayed.
     * 
     * @private
     */
	__clearDisplayedActiveMove : function()
	{
		if(this.elActiveMove){
			this.elActiveMove.innerHTML = '';
		}
		if(this.elActiveComment){
			this.elActiveComment.innerHTML = '';
		}
	}
	// }}}
	,
	// {{{ __displayActiveMove()
    /**
     *  Display active move inside eventual predefined element, i.e. the displayActiveMove property sent to the constructor.
     * 
     * @private
     */
	__displayActiveMove : function(moveIndex,color)
	{
		if(this.elActiveMove){		
			var move = this.__getAMoveInLanguage(this.pgnObject.gameDetails[this.currentGameIndex].moves[moveIndex][color].move);
			if(color=='black')move = '... ' + move;else move = '. ' + move;
			move = moveIndex + move;
			this.elActiveMove.innerHTML = move;
		}	
		if(this.elActiveComment){

			var move = this.pgnObject.gameDetails[this.currentGameIndex].moves[moveIndex][color];
			if(move.comment){
				this.elActiveComment.innerHTML = move.comment;
			}else{
				this.elActiveComment.innerHTML = '';
			}	
		}	
	}
	// }}}
	,
	// {{{ __getInfoByMoveString()
    /**
     *  This method returns info regarding a piece from a move, i.e. from which file, rank, which piece moved etc.
     *	
     *	@param String move, Move, example: Nxf6
     * 
     * @private
     */	
	__getInfoByMoveString : function(move,color)
	{
		move = move.replace(/[\#\+]/gi,'');
		
		if(move.length==3 && move.match(/^[a-h][18][BRNQ]$/)){	// If notation like g8Q
			move = move.substr(0,2) + '=' + move.substr(2,1);
		}
		var capture = (move.indexOf('x')>=0)?true:false;
		var castle = (move.indexOf('0')>=0)?true:false;
		if(!castle){
			castle = (move.indexOf('O')>=0)?true:false;
		}
		var officer = move.match(/[BKQNR]/g);
		var promote = move.match(/=/);
		var pawnMove = move.match(/[abcdefgh]/);		


				
		var fromRank = false;	// It has been specified where to move from. 
		var rankMatches = move.match(/[0-9]/g);
		if(rankMatches && rankMatches.length>1){
			fromRank = rankMatches[0];			
		}
		if(promote){
			pawnMove=true;
			officer=false;
		}		
		if(officer)pawnMove=false;
		var fromFile = false;	// It has been specified where to move from. 
		var fileMatches = move.match(/[a-h]/g);
		if(fileMatches && fileMatches.length>1){
			fromFile = fileMatches[0];
		}
		if(pawnMove){
			if(move.indexOf('x')>=0){
				fromFile = move.replace(/^([a-h]).*$/,'$1');
			}else{
				fromFile = move.replace(/[^a-h]/g,'');
			}
			fromRank = move.replace(/[^0-9]/g,'');
			
			if(color=='black')fromRank = fromRank/1+1;else fromRank = fromRank/1-1;
		}
		var ret = new Object();
		ret.fromFile = fromFile;
		ret.fromRank = fromRank;
		ret.castle = castle;
		ret.officer = officer;
		ret.pawnMove = pawnMove;	
		ret.capture = capture;	
		ret.promote = promote;	

		return ret;
	}	
	// }}}
	,
	// {{{ __parseAMove()
    /**
     *  Parse a specific move and display it on the board.
     *	@param Integer move, example 2
     *	@param String color, example "black"
     * 
     * @private
     */
	__parseAMove : function(move,color)
	{
		this.lastMoveEnPassant = false;
		this.lastMovePawnPromote = false;
		this.currentlyParsedMove = move;
		
		var opositeColor = (color=='white')?'black':'white';
		
		var moveInfo = this.__getInfoByMoveString(move);
		
		move = move.replace(/[\#\+!\?]/gi,'');
		
		if(!moveInfo.castle){
			var toSquare = move.replace(/=[BKQNR][+\#]?/g,'');
			toSquare = toSquare.replace(/[\#+]/g,'');
			toSquare = toSquare.substr(toSquare.length-2,2);
		}
		
		// Remember: exf6 could mean that pawn takes pawn on f5 (en passant)
		if(moveInfo.pawnMove){
			var numericPos = move.replace(/[^0-9]/g,'');
			if(!moveInfo.capture){
				var pieceIndex = this.__movePawnForward(move,color);
				if(pieceIndex||pieceIndex==0)this.__movePieceToLocation(color,pieceIndex,move);	
			}else{
				var pieceIndex = this.__movePawnCapture(move,moveInfo.fromFile,color,true);
			}			
			if(moveInfo.promote){				
				var obj = this.pieces[color][pieceIndex];
				var promoteTo = move.replace(/[^NBRQ]/g,'').toLowerCase();	
				this.__removePieceFromBoard(pieceIndex,color);
				var ind = this.__createPieceByPromotion(obj.file,obj.rank,color,promoteTo);
				this.lastMovePawnPromote = true;				
			}	
		}	
		if(moveInfo.officer){
			var pieceType = move.substr(0,1).toLowerCase();
			if(moveInfo.capture){
				var el = this.__getPieceOnSquare(toSquare,opositeColor);
				this.__removePieceFromBoard(el,opositeColor);
			}
			this.__moveOfficer(toSquare,color,moveInfo.fromFile,moveInfo.fromRank,pieceType);				
		}
		if(moveInfo.castle){
			var matches = move.match(/\-/g);
			var rank = (color=='white')?1:8;
			if(matches.length>1){	/* Long castle */
				this.__moveOfficer('d' + rank,color,'a',false,'r');				
				this.__moveOfficer('c' + rank,color,false,false,'k');				
			}else{		
				this.__moveOfficer('f' + rank,color,'h',false,'r');				
				this.__moveOfficer('g' + rank,color,false,false,'k');				
			}			
		}		
	}
	// }}}
	,
	// {{{ __createPieceByPromotion()
    /**
     *  Piece has been promoted, create a new piece and display it on the board. The pawn promoted is technically being removed from the board.
     *	@param String file, example: "e"
     *	@param Integer rank, example 8
     *	@param String color, example "white"
     *	@param String promoteTo - What to promote the piece to, example "n" for knight.
     * 
     * @private
     */
	__createPieceByPromotion : function(file,rank,color,promoteTo)
	{		
		if(color=='white')color='w'; else color='b';
		col = this.__getColFromFile(file) + ((8-rank)*8);
		col--;
		var el = document.createElement('div');
		el.style.width = this.squareSize + 'px';
		el.style.height = this.squareSize + 'px';
		el.style.position = 'absolute';
		this.divBoard.appendChild(el);
		var img = document.createElement('img');
		img.src = this.imageFolder + this.chessSet + this.squareSize  + color + promoteTo.toLowerCase() + '.png';			
		
		el.appendChild(img);			
		var pos = this.__getBoardPosByCol(col);
		if(this.isOldMSIE && !this.isOpera)this.correctPng(img);	
		el.style.left = pos.x + 'px';
		el.style.top = pos.y + 'px';
		if(this.animateNextMove)el.style.display='none';// Hide the piece while animation is in progress, show it afterwards
		var index = this.__addPieceToArray(color,promoteTo,col,el);
		this.__addEventToChessPiece(el,color,index);
		return index;
		
	}
	// }}}
	,
	// {{{ __moveOfficer()
    /**
     *  Move officer
     *	@param String toSquare, example: "e4"
     *	@param String color, example "white"
     *	@param String fromFile, example "e"
     *	@param Integer fromRank, example 8
     *	@param String pieceType, example "b" for bishop
     * 
     * @private
     */
	__moveOfficer : function(toSquare,color,fromFile,fromRank,pieceType)
	{
		for(counter=0;counter<this.pieces[color].length;counter++){
			var obj = this.pieces[color][counter];
			if(fromFile && obj.file!=fromFile)continue;
			if(fromRank && obj.rank!=fromRank)continue;
			if(obj.pieceType==pieceType){	// Matching piece found
				if(this.__canPieceMoveToSquare(color,counter/1,toSquare)){	/* Correct piece found */
					this.__movePieceToLocation(color,counter,toSquare);	
					return;
				}
			}			
		}		
	}
	// }}}
	,
	// {{{ __canPieceMoveToSquare()
    /**
     *  Return trure if a specific piece can move to a given square.
     *	@param color color of piece
     *	@param Integer index, Index of piece in the piece array - color and index is used to get a reference to the piece.
     *	@param String toSquare, whcih square to move to, example "h4"
     * 
     * @private
     */
	__canPieceMoveToSquare : function(color,index,toSquare,recursive)
	{
		var opositeColor = color=='white'?'black':'white';
		var obj = this.pieces[color][index];
		if(!obj.onBoard)return false;
		var toPos = this.__getColAndRowFromSquare(toSquare);
		var currCol = this.__getColFromFile(obj.file);
		var currRow = obj.rank;	
		
		var diffTo = Math.max(toPos.col,currCol) - Math.min(toPos.col,currCol);
		var diffFrom = Math.max(toPos.row,currRow) - Math.min(toPos.row,currRow);
					
	
		if(obj.pieceType=='b' || obj.pieceType=='q'){	// Bishop - piecetypes are all in lower case			
			if(diffTo == diffFrom){
				if(!this.__occupiedSquaresBetween({ 'col':toPos.col,'row':toPos.row },{ 'col':currCol,'row':currRow } )){
					if(recursive)return true;
					if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
				}
			}				
		}		
		if(obj.pieceType=='r' || obj.pieceType=='q'){	/* rook */			
			if(diffTo==0 || diffFrom==0){
				if(!this.__occupiedSquaresBetween({ 'col':toPos.col,'row':toPos.row },{ 'col':currCol,'row':currRow } )){
					if(recursive)return true;
					if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
				}			
			}			
		}
		if(obj.pieceType=='k'){	/* King */
			return true;		
		}
		if(obj.pieceType=='n'){
			if(diffFrom==2 && diffTo==1){
				if(recursive)return true;
				if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
			}	
			if(diffTo==2 && diffFrom==1){
				if(recursive)return true;
				if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;	
			
			}
		}
		return false;
	}
	// }}}
	,
	// {{{ __isMovingIntoCheckByMovingThisPiece()
    /**
     *  Return true if a specific move results in a check position, i.e. invalid move
     *	@param Object pieceObj - Reference to piece to move
     *	@param String color - color of king to check
     *	@param String toSquare - To which square
     * 
     * @private
     */
	__isMovingIntoCheckByMovingThisPiece : function(pieceObj,color,toSquare)
	{
		
		var checkBefore = this.__isKingInCheck(color);		
		if(checkBefore)return false;		
		
		pieceObj.onBoard=false;	// Temporary "removing" the piece from the board		
		var opositeColor = color=='white'?'black':'white';
		var elOnDestSquare = this.__getPieceOnSquare(toSquare,opositeColor);	// If there's a piece on the destination square, we need to temporary remove that piece too.
		if(elOnDestSquare || elOnDestSquare===0){
			this.pieces[opositeColor][elOnDestSquare].onBoard=false;
		}
		var checkAfter = this.__isKingInCheck(color);
		
		
		pieceObj.onBoard = true;		
		if(checkAfter){	/* Is in check after ? */
			/*
			if(this.currentlyParsedMove.indexOf('+')>=0 || this.currentlyParsedMove.indexOf('#')>=0){
				if(elOnDestSquare || elOnDestSquare===0){
					this.pieces[opositeColor][elOnDestSquare].onBoard=true;
				}				
				return true;
			}
			*/
			var savedFile = pieceObj.file;
			var savedRank = pieceObj.rank;
			
			pieceObj.file = toSquare.substr(0,1);
			pieceObj.rank = toSquare.substr(1,1);
			
			var checkAfter = this.__isKingInCheck(color);				
			
			pieceObj.file = savedFile;
			pieceObj.rank = savedRank;
		}
		
		if(elOnDestSquare || elOnDestSquare===0){
			this.pieces[opositeColor][elOnDestSquare].onBoard=true;
		}
				
		return checkAfter;		
	}
	// }}}
	
	,
	
	// {{{ __movingIntoCheckIfMoving()
    /**
     *  Return true if move of a piece results in check, i.e. invalid move
     *	@param Integer index, Index of piece in the piece array - color and index is used to get a reference to the piece.
     * 
     * @private
     */
	__isKingInCheck : function(color)
	{
		var kingObj;
		for(var no=0;no<this.pieces[color].length;no++){
			if(this.pieces[color][no].pieceType=='k'){
				kingObj = this.pieces[color][no];
				break;
			}	
		}			
		
		var kingRow = kingObj.rank;
		var kingCol = this.__getColFromFile(kingObj.file);
		
		var opositeColor = color=='white'?'black':'white';
		for(var no=0;no<this.pieces[opositeColor].length;no++){ 	/* Find bishop,rooks and queens and check if they put the king in check if the piece sent to this method moves */
			var piece = this.pieces[opositeColor][no];
			if(!piece.onBoard)continue;
			if(piece.pieceType=='b' || piece.pieceType=='q' || piece.pieceType=='r'){
				var ret = this.__canPieceMoveToSquare(opositeColor,no,kingObj.file+kingObj.rank,true);	
				if(ret){					
					return true;
				}			
			}	
		}	
		return false;	
	}
	// }}}	
  ,
	// {{{ __canPieceMoveToSquare()
    /**
     *  Return trure if a specific piece can move to a given square.
     *	@param color color of piece
     *	@param Integer index, Index of piece in the piece array - color and index is used to get a reference to the piece.
     *	@param String toSquare, whcih square to move to, example "h4"
     * 
     * @private
     */
	__canPieceMoveToSquare : function(color,index,toSquare,recursive)
	{
		var opositeColor = color=='white'?'black':'white';
		var obj = this.pieces[color][index];
		if(!obj.onBoard)return false;
		var toPos = this.__getColAndRowFromSquare(toSquare);
		var currCol = this.__getColFromFile(obj.file);
		var currRow = obj.rank;	
		
		var diffTo = Math.max(toPos.col,currCol) - Math.min(toPos.col,currCol);
		var diffFrom = Math.max(toPos.row,currRow) - Math.min(toPos.row,currRow);
					
	
		if(obj.pieceType=='b' || obj.pieceType=='q'){	// Bishop - piecetypes are all in lower case			
			if(diffTo == diffFrom){
				if(!this.__occupiedSquaresBetween({ 'col':toPos.col,'row':toPos.row },{ 'col':currCol,'row':currRow } )){
					if(recursive)return true;
					if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
				}
			}				
		}		
		if(obj.pieceType=='r' || obj.pieceType=='q'){	/* rook */			
			if(diffTo==0 || diffFrom==0){
				if(!this.__occupiedSquaresBetween({ 'col':toPos.col,'row':toPos.row },{ 'col':currCol,'row':currRow } )){
					if(recursive)return true;
					if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
				}			
			}			
		}
		if(obj.pieceType=='k'){	/* King */
			return true;		
		}
		if(obj.pieceType=='n'){
			if(diffFrom==2 && diffTo==1){
				if(recursive)return true;
				if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;
			}	
			if(diffTo==2 && diffFrom==1){
				if(recursive)return true;
				if(!this.__isMovingIntoCheckByMovingThisPiece(obj,color,toSquare))return true;	
			
			}
		}
		return false;
	}
	// }}}
	,
	// {{{ __occupiedSquaresBetween()
    /**
	 * Return true if there are occupied squares on the line between fromSquare and toSquare 
	 * from and to are associative arrays of rows and cols (numeric 
     *	@param Object from, associative array of row and col, i.e. numeric file and rank.
     *	@param Object to, associative array of row and col, i.e. numeric file and rank.
     * 
     * @private
     */
	__occupiedSquaresBetween : function(from,to)
	{
		var squares = this.__getSquaresBetween(from,to);
		for(var squareCounter=0;squareCounter<squares.length;squareCounter++){	
			var el = this.__getPieceOnSquare(squares[squareCounter],'white');
			if(el || el===0)return true;
			var el = this.__getPieceOnSquare(squares[squareCounter],'black');
			if(el || el===0)return true;	
		}
		return false;
		
	}
	// }}}
	,
	// {{{ __getSquaresBetween()
    /**
	 * Return array of squares between a and b.
     *	@param Object from, associative array of row and col, i.e. numeric file and rank.
     *	@param Object to, associative array of row and col, i.e. numeric file and rank.
     * 
     * @private
     */
	__getSquaresBetween : function(from,to)
	{
		var retArray = new Array();
		if(from.row==to.row){	/* Same rank */			
			var min = Math.min(from.col,to.col)+1;
			var max = Math.max(from.col,to.col)-1;
			if(max<min==1)return false;
			for(var counter=min;counter<=max;counter++){
				retArray[retArray.length] = this.__getFileByCol(counter) + '' + to.row;
			}
			return retArray;				
		}else if(from.col==to.col){	/* Same file */
			var file = this.__getFileByCol(from.col);
			var min = Math.min(from.row,to.row)+1;
			var max = Math.max(from.row,to.row)-1;	
			if(max<min==1)return false;
			for(var counter=min;counter<=max;counter++){
				retArray[retArray.length] = file + '' + counter;
				
			}	
			return retArray;					
		}else{	/* Diagonals */
			var moveX = 1;
			var moveY = 1;
			if(from.col>to.col)moveX = -1;
			if(from.row>to.row)moveY = -1;
			
			var min = Math.min(from.col,to.col);
			var max = Math.max(from.col,to.col);
			var diff = max-min;
			for(var counter=1;counter<diff;counter++){
				var file = 	from.col + (counter*moveX);
				var rank = from.row + (counter*moveY);				
				file = this.__getFileByCol(file);
				retArray[retArray.length] = file + '' + rank;
				
			}
			return retArray;
		}
	}
	// }}}
	,
	// {{{ __movePawnForward()
    /**
	 *  Move pawn forward on the board(i.e. straight forward, not capture).
     *	@param String move, which move.
     *	@param color, color of piece to move.
     * 
     * @private
     */
	__movePawnForward : function(move,color)
	{
		move = move.replace(/[^0-9a-h]/g,'');
		var file = move.replace(/[0-9]/g,'');
		var rank = move.replace(/[^0-9]/g,'');
	
		for(var no=0;no<this.pieces[color].length;no++){
			var obj = this.pieces[color][no];
			if(!obj.onBoard)continue;
			var rankMatch = false;
			if(color=='white' && obj.rank<rank)rankMatch=true;
			if(color=='black' && obj.rank>rank)rankMatch=true;
			var maxRankDiff = 1;
			
			if(color=='white'){
				if(rank==4)maxRankDiff=2;
				if(rank-obj.rank>maxRankDiff)rankMatch=false;					
			}
			if(color=='black'){
				if(rank==5)maxRankDiff=2;
				if(obj.rank - rank>maxRankDiff)rankMatch=false;				
			}
			if(rankMatch){	/* Check for occupied squares between this pawn and the destination. if found, this is not the right pawn to move */
				var colFrom = this.__getColFromFile(file);
				var colTo = this.__getColFromFile(obj.file);			
				if(this.__occupiedSquaresBetween({ 'col':colFrom,'row':rank },{ 'col':colTo,'row':obj.rank } )){
					rankMatch = false;
				}
			}	
			if(obj.pieceType=='p' && obj.file==file && rankMatch){	
				return no;
			}	
		}	
		return false;
	}
	// }}}
	,
	// {{{ __movePawnCapture()
    /**
	 *  Move pawn forward by capturing another piece.
     *	@param String move, which move.
     *	@param String fromFile, from which file, example: "e".
     *	@param color, color of piece to move.
     * 
     * @private
     */
	__movePawnCapture : function(move,fromFile,color,removePiece)
	{
		var opositeColor = (color=='white')?'black':'white';
		var capturedSquare = move.replace(/.*?x([a-z][1-8]).*/g,'$1');	
		var el = this.__getPieceOnSquare(capturedSquare,opositeColor);
		var retVal = this.__movePawn(fromFile,capturedSquare,color,removePiece);
		if(el || el===0){			
			if(removePiece)this.__removePieceFromBoard(el,opositeColor);
		}else{	/* En passant */
			this.lastMoveEnPassant = true;
			var file = capturedSquare.substr(0,1);
			var rank = capturedSquare.substr(1,1);
			if(color=='white')rank--; else rank++;
			capturedSquare  = file + '' + rank;
			var el = this.__getPieceOnSquare(capturedSquare,opositeColor);
			if(el && removePiece)this.__removePieceFromBoard(el,opositeColor);
		}	
		return retVal;
	}
	// }}}
	,
	// {{{ __movePawn()
    /**
	 *  Move pawn one step forward - this method is called by the __movePawnCapture method.
     *	@param String fromFile, from which file, example: "e".
     *	@param String toSquare - to which square, example: "d5"
     *	@param color, color of piece to move.
     * 
     * @private
     */
	__movePawn : function(fromFile,toSquare,color,removePiece)
	{
		var rank = toSquare.substr(1,1)/1;
		if(color=='white')rank--; else rank++;
		var index = this.__getPieceOnSquare(fromFile+rank,color);	
		if(removePiece)this.__movePieceToLocation(color,index,toSquare);
		return index;
	}
	// }}}
	,
	// {{{ __movePawn()
    /**
	 *  return index of piece on a specific square
     *	@param String square - which square, example: "d5"
     *	@param String color, piece of which color, example "white"
     * 
     * @private
     */
	__getPieceOnSquare : function(square,color)
	{
		var file = square.substr(0,1);
		var rank = square.substr(1,1);
		for(no=0;no<this.pieces[color].length;no++){
			var obj = this.pieces[color][no];
			if(!obj.onBoard)continue;
			if(obj.file==file && obj.rank==rank && obj.onBoard)return no;			
		}		
		return false;
	}
	// }}}
	,
	__getSlideCoordinates : function(from,to)
	{
		var diffX = Math.max(from.x,to.x) - Math.min(from.x,to.x);
		var diffY = Math.max(from.y,to.y) - Math.min(from.y,to.y);
		var aniSpeed = 22.5 * this.animationSpeed;
		
		if(diffX > diffY){
			if(diffX==0)diffX=1;
			moveX = diffX / aniSpeed;	
			moveY = moveX * (diffY/diffX);		
		}else{
			if(diffY==0)diffY=1;
			moveY = Math.ceil(diffY/aniSpeed);
			moveX = moveY * (diffX/diffY);			
		}
		var directionX = 1;
		if(to.x<from.x)directionX = -1;
		var directionY = 1;
		if(to.y<from.y)directionY = -1;		
		
		var retArray = new Array();
		moveX = moveX * directionX;
		moveY = moveY * directionY;
		
		var counter=0;
		while(Math.abs(moveX)<1 && Math.abs(moveY)<1 && counter<10){
			moveX*=2;
			moveY*=2;	
			counter++;
		}
		
		retArray[retArray.length] = { x: from.x,y:from.y };		
		
		var finished = false;
		var finishedX = false;
		var finishedY = false;
		if(directionY==0)finishedY = true;
		if(directionX==0)finishedX = true;
		
		var counter=0;
		while(!finished){
			if(moveX==0 && moveY==0)finished=true;
			counter++;
			if(counter>400){
				alert('Infinite loop (' + moveX + ',' + moveY + ')');
				return;
			}
			from.x = from.x + moveX;
			from.y = from.y + moveY;
			if(directionY>0 && from.y>=to.y && moveY!=0){
				finishedY = true;
				from.y = to.y;	
				from.x = to.x;
			}else if(directionY<0 && from.y<=to.y && moveY!=0){
				finishedY = true;
				from.y = to.y;	
				from.x = to.x;
			}else if(directionX>0 && from.x>=to.x && moveX!=0){
				finishedX = true;
				from.x = to.x;	
				from.y = to.y;
			}else if(directionX<0 && from.x<=to.x && moveX!=0){
				finishedX = true;
				from.x = to.x;
				from.y = to.y;
			}	
			retArray[retArray.length] = { x: Math.round(from.x),y:Math.round(from.y) };
			if(finishedX || finishedY)finished=true;
		}
		if(retArray.length<=2)retArray[retArray.length] = { x: Math.round(from.x),y:Math.round(from.y) };
		if(retArray.length<=2)alert('Length of array: ' + retArray.length);
		return retArray;
		
	}
	// }}}
	,
	// {{{ __onAnimatedMoveComplete()
    /**
	 *  Callback executed on move complete - This method is only executed after an animated move. 
     * 
     * @private
     */
	__onAnimatedMoveComplete : function()
	{		
		this.__handleCallback('moveComplete');	
		
		var lastMove = this.__getLastMove();
		var lastPlayedMove = this.__getLastPlayedMove();

		if(lastMove.color==lastPlayedMove.color && lastMove.move==lastPlayedMove.move)this.__handleCallback('afterLastMove');
	}
	,
	// {{{ __movePawn()
    /**
	 *  Move a piece to a new location on the board and update array(i.e. where the position of each piece is stored)     
     *	@param String color, piece of which color, example "white"
     *	@param Integer index - piece with which index in the array
     *	@param String toSquare - Move to which square, example: "d5"
     * 
     * @private
     */
	__movePieceToLocation : function(color,index,toSquare)
	{
		
		var pos = this.__getBoardPosByNotation(toSquare);
		var el = this.pieces[color][index].el;
		this.currentZIndex++;
		el.style.zIndex = this.currentZIndex;
					
		
		var fromPos = this.__getBoardPosByNotation(this.pieces[color][index].file + this.pieces[color][index].rank);
		this.coordLastMove.from.x = fromPos.x;
		this.coordLastMove.from.y = fromPos.y;

		this.coordLastMove.to.x = pos.x;
		this.coordLastMove.to.y = pos.y;

		if(this.animateNextMove){/* Animate move */
			this.isBusy = true;
			var currLeft = el.style.left.replace('px','')/1;
			var currTop = el.style.top.replace('px','')/1;
			
			var coordIndex = this.slideCoordinates.length;
			this.slideCoordinates[coordIndex] = this.__getSlideCoordinates({ x:currLeft,y:currTop },{x:pos.x,y:pos.y });
		
			this.__slideElement(
				{ 'coordIndex':coordIndex,'coordStep':1 },
				{ 'x':pos.x,'y':pos.y }
				,
				index,
				color
			)			
		}else{	
			el.style.left = pos.x + 'px';
			el.style.top = pos.y + 'px';	
		}				
		this.__updatePieceRankFileByPosition(index,color,{'x':pos.x,'y':pos.y});
	}
	// }}}
	,
	// {{{ __slideElement()
    /**
	 *  Move element to new location by sliding. this method is called a number of times and it moves the piece step by step from a to b
	 *	@param Object from - associative array with keys "x" and "y" (pixels)
	 *	@param Object to - associative array with keys "x" and "y" (pixels)
	 *	@param Integer index - index of piece in the array
	 *	@param String color - color of piece, example "white"
     * 
     * @private
     */
	__slideElement : function(coordinateRef,to,index,color,dragDropVar)
	{
		var ind = this.objectIndex;
		var repeat = true;
		if(coordinateRef.coordStep==this.slideCoordinates[coordinateRef.coordIndex].length-1)repeat=false;

		this.pieces[color][index].el.style.left = this.slideCoordinates[coordinateRef.coordIndex][coordinateRef.coordStep].x+'px';
		this.pieces[color][index].el.style.top = this.slideCoordinates[coordinateRef.coordIndex][coordinateRef.coordStep].y+'px';	
		
		if(repeat){				
			var string = "{ coordIndex:" + coordinateRef.coordIndex + ",coordStep:" + (coordinateRef.coordStep+1) + " },"
					+ "{ 'x':" + to.x + ",'y':" + to.y+" },"
					+ "'" + index + "','" + color + "'";
			if(dragDropVar)string = string + ",'" + dragDropVar + "'";
			setTimeout('D_chessObjects[' + ind + '].__slideElement(' + string + ')',this.animationSetTimeout);
			return;
		}else{
			if(!dragDropVar){
				this.slideCoordinates[coordinateRef.coordIndex] = null;
				this.animateNextMove = false;
				this.__indicateLastMove();
				this.isBusy = false;
				var opositeColor = (color=='white')?'black':'white';
				var toSquare = this.__getNotationByBoardPos(to.x,to.y);
				var pieceIndex = this.__getPieceOnSquare(toSquare,opositeColor);			
				if(pieceIndex || pieceIndex===0){
					this.__removePieceFromBoard(pieceIndex,opositeColor);
				}else{
					if(this.lastMoveEnPassant){
						if(opositeColor=='black'){
							toSquare = toSquare.substr(0,1) + (toSquare.substr(1,1)/1-1);						
						}else{
							toSquare = toSquare.substr(0,1) + (toSquare.substr(1,1)/1+1);
						}					
						var pieceIndex = this.__getPieceOnSquare(toSquare,opositeColor);
						this.__removePieceFromBoard(pieceIndex,opositeColor);
					}
				}
				if(this.lastMovePawnPromote){
					var pieceIndex = this.__getPieceOnSquare(toSquare,color);	
					this.__removePieceFromBoard(pieceIndex,color);
					var pieceIndex = this.__getPieceOnSquare(toSquare,color);
					this.pieces[color][pieceIndex].el.style.display='block';					
				}
				
				var moveComplete=true;
				if(this.pieces[color][index].pieceType=='k'){
					var lastMove = this.__getLastPlayedMove();
					if(lastMove.moveString=='O-O' || lastMove.moveString=='O-O-O' || lastMove.moveString=='0-0' || lastMove.moveString=='0-0-0')moveComplete = false;					
				}
				if(moveComplete)this.__onAnimatedMoveComplete();
								
				if(this.autoPlayActive){
					var moveDetails = this.__getNextMove();
					if(this.autoplayDelayBeforeComments && this.pgnObject.gameDetails[this.currentGameIndex].moves[this.currentMove][this.currentColor].comment){
						var autoplayDelay = (this.autoplayDelayBeforeComments*1000);
						
					}else{
						var autoplayDelay = (this.autoPlayDelayBetweenMoves*1000);
					}				
					if(this.stopAutoplayBeforeComments && this.pgnObject.gameDetails[this.currentGameIndex].moves[this.currentMove][this.currentColor].comment){
						this.stopAutoPlay();
						return;
					}
					
					setTimeout('D_chessObjects[' + ind + '].__autoPlayStep(' + moveDetails.move + ',"' + moveDetails.color + '")',autoplayDelay);
				}
				

			}else{
				
				if(dragDropVar!='sameSquare')this.__handleCallback('wrongMove');else this.__playSound('move');	
			}
		}
	}
	// }}}
	,
	// {{{ __removePieceFromBoard()
    /**
	 *  Removing a piece from the board by hiding it and by setting the "onBoard" attribute to false.
	 *	@param Integer pieceIndex - index of piece in the array
	 *	@param String color - color of piece, example "white"
     * 
     * @private
     */
	__removePieceFromBoard : function(pieceIndex,color)
	{
		if(this.animateNextMove)return;
		try{
			this.pieces[color][pieceIndex].onBoard=false;
			this.pieces[color][pieceIndex].el.style.display='none';	
		}catch(e){
		
		}
	}
	// }}}
	,
	// {{{ __updatePieceRankFileByPosition()
    /**
	 *  Update rank and file of position based on x and y coordinates (pixel)
     * 
     * @private
     */
	__updatePieceRankFileByPosition : function(pieceIndex,color,position)
	{
		var files = 'abcdefgh';
		var file = position.x / this.squareSize;
		var rank = position.y / this.squareSize;
		rank = 8-rank;
		if(this.flipBoard){
			file = 7-file;
			rank = 9-rank;			
		}
		
		file = files.substr(file,1);
		this.pieces[color][pieceIndex].file = file;
		this.pieces[color][pieceIndex].rank = rank;
		
	}
	// }}}
	,
	// {{{ __indicateLastMove()
    /**
	 *  Indicate last move with a rectangle around the two squares
     * 
     * @private
     */
	__indicateLastMove : function()
	{

		if(!this.divIndicators.from.parentNode)this.__createIndicators();	
		var borderWidth = this.__getStyle(this.divIndicators.to,'borderLeftWidth').replace('px','')/1;
		var size = this.squareSize - (borderWidth*2);
		
		var fromStyle = this.divIndicators.from.style;
		var toStyle = this.divIndicators.to.style;
		

		
		fromStyle.left = this.coordLastMove.from.x + 'px';
		fromStyle.top = this.coordLastMove.from.y + 'px';
		fromStyle.width = size + 'px';
		fromStyle.height = size + 'px';	
	

		toStyle.left = this.coordLastMove.to.x +'px';
		toStyle.top = this.coordLastMove.to.y + 'px';
		toStyle.width = size + 'px';
		toStyle.height =size + 'px';
	
		
		if(this.indicateLastMove && !this.animateNextMove){
			fromStyle.display='block';	
			toStyle.display='block';	
		}else{
			this.__hideIndicators();
		}
	}
	// }}}
	,
	// {{{ __hideIndicators()
    /**
	 *  Hide rectangle indicators
     * 
     * @private
     */
	__hideIndicators : function()
	{
		this.divIndicators.from.style.display='none';
		this.divIndicators.to.style.display='none';		
	}
	// }}}
	,
	// {{{ setBoardLabels()
    /**
	 *  Specify if board labels(A-H,1-8) should be displayed or not
     * 
     *	@param Boolean boardLabels
     * @private
     */
	setBoardLabels : function(boardLabels)
	{
		this.boardLabels = boardLabels;
	}
	// }}}
	,
	// {{{ displayBoardByFen()
    /**
	 *  Display board pieces based on fen string.
     * 
     *	@param Boolean boardLabels
     * @public
     */
	displayBoardByFen : function(fenString,element)
	{			
		element = this.__getEl(element);
		element.innerHTML = '';
		this.__createBoardDiv(element);
		this.__loadFen(fenString,this.divBoard );
		this.__hideIndicators();	
	}
	// }}}
	,


	// {{{ __clearBoard()
    /**
	 *  Clear pieces from the board
     * 
     * @private
     */
	__clearBoard : function()
	{
		this.parentRef.innerHTML = '';
		this.__createBoardDiv();
		this.divBoard.innerHTML = '';
		this.__createSquares(this.divBoard);
		if(!this.whoToStartMove)this.whoToStartMove='w';
		this.currentMove = 1;
		
		this.currentColor = this.whoToStartMove;	
		this.currentMoveNumber = 1;	
		
	}
	// }}}
	,
	// {{{ __createDefaultPieces()
    /**
	 *  Create pieces and put them in the startup position
     * 
     * @private
     */
	__createDefaultPieces : function()
	{
		this.__clearBoard();
		this.pieces = new Array();
		this.pieces['white'] = new Array();
		this.pieces['black'] = new Array();
		
		var string = 'rnbqkbnrpppppppp';
		var color = 'b';
		for(var no=0;no<string.length;no++){
			var character = string.substr(no,1);
			var el = document.createElement('div');			
			el.style.width = this.squareSize + 'px';
			el.style.height = this.squareSize + 'px';
			el.style.position = 'absolute';
			this.divBoard.appendChild(el);
			if(this.isMSIE){
				var img = document.createElement('img');
				img.src = this.imageFolder + this.chessSet + this.squareSize  + color + character.toLowerCase() + '.png';	
				el.appendChild(img);	
				if(this.isOldMSIE && !this.isOpera)this.correctPng(img);
			}else{
				el.style.backgroundImage = 'url("' + this.imageFolder + this.chessSet + this.squareSize  + color + character.toLowerCase() + '.png")' 		
			}						
			var pos = this.__getBoardPosByCol(no);
			el.style.left = pos.x + 'px';
			el.style.top = pos.y + 'px';
			var pieceIndex = this.__addPieceToArray('b',character.toLowerCase(),no,el);
			this.__addEventToChessPiece(el,color,pieceIndex);
		}	

		var string = 'pppppppprnbqkbnr';
		var color = 'w';
		for(var no=0;no<string.length;no++){
			var character = string.substr(no,1);
			var el = document.createElement('div');
			
			el.style.width = this.squareSize + 'px';
			el.style.height = this.squareSize + 'px';
			el.style.position = 'absolute';
			this.divBoard.appendChild(el);
			if(this.isMSIE){
				var img = document.createElement('img');
				img.src = this.imageFolder + this.chessSet + this.squareSize  + color + character.toLowerCase() + '.png';	
				el.appendChild(img);	
				if(this.isOldMSIE && !this.isOpera)this.correctPng(img);
			}else{
				el.style.backgroundImage = 'url("' + this.imageFolder + this.chessSet + this.squareSize  + color + character.toLowerCase() + '.png")' 		
			}			
			var pos = this.__getBoardPosByCol(no+48);
			el.style.left = pos.x + 'px';
			el.style.top = pos.y + 'px';	
			var pieceIndex = this.__addPieceToArray('w',character.toLowerCase(),(no+48),el);
			this.__addEventToChessPiece(el,color,pieceIndex);
		
		}	
	}
	// }}}
	,
	// {{{ __addPieceToArray()
    /**
	 *  Add a piece to the array of pieces
     * 
     * @private
     */
	__addPieceToArray : function(color,type,col,el)
	{
		if(color=='w')color='white';
		if(color=='b')color='black';
		var ind = this.pieces[color].length;	
		this.pieces[color][ind] = new Object();
		this.pieces[color][ind]['pieceType'] = type;
		var pos = this.__getRankFileByCol(63-col);
		this.pieces[color][ind]['file'] = pos.file;
		this.pieces[color][ind]['rank'] = pos.rank;
		this.pieces[color][ind]['el'] = el;				
		this.pieces[color][ind]['onBoard'] = true;	
		return ind;	
	}
	// }}}
	,
	// {{{ __createBoardDiv()
    /**
	 *  Create main div element(s) for the chess board
	 *	@param Object element - where to insert the board.
     * 
     * @private
     */
	__createBoardDiv : function(element)
	{
		if(!element)element = this.parentRef;
		var boardOuter = document.createElement('DIV');	
		
		boardOuter.className = 'ChessBoard' + this.squareSize;
		boardOuter.style.position = 'relative';
		
		
		var board = document.createElement('DIV');		
		board.className = 'ChessBoardInner' + this.squareSize;			
		board.style.width = (this.squareSize*8) + 'px';
		board.style.height = (this.squareSize*8) + 'px';
		this.divBoard = board;		
		this.divBoard.onselectstart = function(){ return false; }
		this.__addEventEl(this.divBoard);
		
		this.boardFrame = document.createElement('DIV');
		this.boardFrame.className = 'ChessBoardFrame' + this.squareSize;
		this.boardFrame.appendChild(board);
		
		if(this.boardLabels){			
			boardOuter.appendChild(this.boardFrame);
			this.boardFrame.style.position = 'absolute';
			this.boardFrame.style.top='0px';
			this.boardFrame.style.right='0px';
			board.style.position = 'relative';
			board.style.left='0px';
			board.style.top='0px';
			element.appendChild(boardOuter);
			this.__addBoardLabels(boardOuter);
		}else{
			board.style.position = 'relative';	
			element.appendChild(this.boardFrame);
		}
		
		this.__createSquares(this.divBoard);
		this.__createIndicators();	
		return board;	
	}
	// }}}
	,
	// {{{ __createSquares()
    /**
	 *  Create div elements for the squares on the board
	 *	@param Object board - Reference to the board div.
     * 
     * @private
     */
	__createSquares : function(board)
	{	
		currentBgColor = this.colorLightSquares;
		currentBgImg = this.bgImageLightSquares;
		
		var lightIsArray = false;
		var darkIsArray = false;
		if(this.__isArray(this.bgImageLightSquares))lightIsArray = true;
		if(this.__isArray(this.bgImageDarkSquares))darkIsArray = true;
		
		for(no=1;no<=64;no++){
			var square = document.createElement('DIV');
			
			square.style.cssText = 'float:left;width:' + this.squareSize + 'px;height:' + this.squareSize + 'px';	
			square.style.styleFloat='left';
			square.style.width = this.squareSize + 'px';	
			square.style.height = this.squareSize + 'px';	
			if(currentBgColor)square.style.backgroundColor = currentBgColor;
			if(currentBgImg)square.style.backgroundImage = 'url(\'' + currentBgImg + '\')';
			board.appendChild(square);
			val = (no + Math.floor(no/8))%2;
			if(val==0){
				currentBgColor = this.colorLightSquares; 
				currentBgImg = this.bgImageLightSquares; 
				if(lightIsArray)currentBgImg = this.bgImageLightSquares[Math.floor(Math.random()*this.bgImageLightSquares.length)];
				else currentBgImg = this.bgImageLightSquares; 				
			}else{
				currentBgColor=this.colorDarkSquares;
				if(darkIsArray)currentBgImg = this.bgImageDarkSquares[Math.floor(Math.random()*this.bgImageDarkSquares.length)];
				else currentBgImg = this.bgImageDarkSquares; 
			}
		}			
				
	}
	// }}}
	,
	// {{{ __addBoardLabels()
    /**
	 *  Create board labels(A-H, 1-8) around the table.
	 *	@param Object boardOuter - Reference to the board div(the outer div).
     * 
     * @private
     */
	__addBoardLabels : function(boardOuter)
	{
		var letters = 'ABCDEFGH';
		
		var borderWidth = this.__getStyle(this.divBoard,'borderLeftWidth').replace('px','')/1;
		
		var posDiff = borderWidth;
		try{
			var left = this.__getLeftPos(this.divBoard);
			var leftOuter = this.__getLeftPos(this.boardFrame);
			posDiff += left - leftOuter;
		}catch(e){

		}

		for(var no=1;no<=8;no++){
			var file = document.createElement('DIV');
			file.style.position = 'absolute';
			file.style.right = (((8-no) * this.squareSize) + posDiff) + 'px';	
			file.style.bottom = '0px';
			file.innerHTML = letters.substr((no-1),1);
			file.style.textAlign = 'center';
			file.style.width = this.squareSize + 'px';
			boardOuter.appendChild(file);
			file.className = 'ChessBoardLabel ChessBoardLabel'+this.squareSize;
			file.id = 'ChessBoardLabel_' + (file.innerHTML);
			var rank = document.createElement('DIV');
			rank.style.position = 'absolute';
			rank.style.left = '0px';	
			rank.style.top = (((8-no) * this.squareSize) + posDiff)+ 'px';	;
			rank.innerHTML = no;
			rank.style.height = this.squareSize + 'px';
			rank.style.lineHeight = this.squareSize + 'px';
			boardOuter.appendChild(rank);
			rank.className = 'ChessBoardLabel ChessBoardLabel'+this.squareSize;
			rank.id = 'ChessBoardLabel_' + (rank.innerHTML);
			if(this.flipBoard){				
				rank.innerHTML = 9-no;	
				file.innerHTML = letters.substr((8-no),1);
			}
		}		
	}
	// }}}
	,
	// {{{ __loadFen()
    /**
	 *  Load Forsyth-Edwards Notation (FEN)
     * 
     * @private
     */
	__loadFen : function(fenString,boardEl)
	{
		this.__setWhoToMoveFromFen(fenString);	
		
		var items = fenString.split(/\s/g);
		var pieces = items[0];
		
		this.pieces = new Array();
		this.pieces['white'] = new Array();
		this.pieces['black'] = new Array();

			
		var currentCol = 0;
		for(var no=0;no<pieces.length;no++){
			var character = pieces.substr(no,1);
			
			if(character.match(/[A-Z]/i)){	
				var boardPos = this.__getBoardPosByCol(currentCol);
				var piece = document.createElement('DIV');
				
				piece.style.position = 'absolute';
				
				piece.style.left = boardPos.x + 'px';
				piece.style.top = boardPos.y + 'px';			
				if(character.match(/[A-Z]/)){	/* White pieces */						
					var color = 'w';
				}
				if(character.match(/[a-z]/)){	/* Black pieces */
					var color = 'b';
				}
				
				var img = document.createElement('IMG');
				img.src = this.imageFolder + this.chessSet + this.squareSize  + color + character.toLowerCase() + '.png';				
				piece.appendChild(img);
				piece.className = 'ChessPiece' + this.squareSize;
				boardEl.appendChild(piece);
				var pieceIndex = this.__addPieceToArray(color,character.toLowerCase(),currentCol,piece);
				this.__addEventToChessPiece(piece,color,pieceIndex);
				currentCol++;
				if(this.isOldMSIE && !this.isOpera)this.correctPng(img);						
			}
			if(character.match(/[0-8]/))currentCol+=character/1;
		}		
	}
	,
	__addEventToChessPiece : function(el,color,pieceIndex)
	{
		var ind = this.objectIndex;
		el.onmousedown = this.__moveDownOnChessPiece;	
		el.onselectstart = this.__cancelEvent;
		el.ondragstart = this.__cancelEvent;
		this.__addEventEl(el);		
		el.setAttribute('objectIndex',ind);
		el.setAttribute('color',color);
		el.setAttribute('pieceIndex',pieceIndex);
	}
	// }}}
	,
	// {{{ __getLastPlayedMove()
    /**
	 *  Return last played move
	 *	Internal note: This method will return wrong values when you click on a move, i.e. uses the goToMove 
     * 
     * @private
     */	
	__getLastPlayedMove : function()
	{
		var ret = new Object();
		if(this.insideVariation){
			ret.color = this.currentVariationColor;
			ret.move = this.currentVariationMove;
			try{
				ret.moveString = this.pgnObject.gameDetails[this.currentGameIndex].moves[this.insideVariation.move][this.insideVariation.color].variation[this.insideVariation.variationIndex][this.currentVariationMove][this.currentVariationColor];
			}catch(e){

			}
		}else{
			ret.color = this.currentColor;
			ret.move = this.currentMove;	
			try{		
				ret.moveString = this.pgnObject.gameDetails[this.currentGameIndex].moves[this.currentMove][this.currentColor].move;
			}catch(e){
				// Move does not exists
			}
		}
		
		return ret;
	}
	,
	// {{{ __getNextMove()
    /**
	 *  Return move and color for the next move.
     * 
     * @private
     */
	__getNextMove : function()
	{
		if(this.currentGameIndex===false)return { move:0,color:'white' };
		var ret = new Object();
		if(this.insideVariation){
			var move = this.currentVariationMove;
			var color = this.currentVariationColor;	
			
		}else{
			var move = this.currentMove;
			var color = this.currentColor;	
		}
		if(move==0){	// At the start of a game 
			move = 1;
			color='white';
			if(!this.pgnObject.gameDetails[this.currentGameIndex].moves[1])color='white';// No moves at all
			else if(!this.insideVariation && !this.pgnObject.gameDetails[this.currentGameIndex].moves[1]['white'])color='black';
		}else{
			if(color=='black'){
				color='white';
				move++;
			}else{
				color='black';				
			}			
		}
		ret.move = move;
		ret.color = color;		
		var notationNextMove;
		try{			
			if(this.insideVariation){
				notationNextMove = this.pgnObject.gameDetails[this.currentGameIndex].moves[this.insideVariation.move][this.insideVariation.color].variation[this.insideVariation.variationIndex][move][color];
				
			}else{
				notationNextMove = this.pgnObject.gameDetails[this.currentGameIndex].moves[move][color].move;		
			}
		}catch(e){
			return ret; // Move does not exists, we're at the end of the game.
		}
		var squareInfo = this.__getSquareAndPieceByNotation(notationNextMove,color);
		var moveInfo = this.__getInfoByMoveString(notationNextMove,color);
		
		ret = moveInfo;
		
		ret.notation = notationNextMove;
		ret.toSquare = squareInfo.square;
		ret.pieceType = squareInfo.pieceType;
		if(moveInfo.pawnMove)ret.pieceType='p';
		ret.move = move;
		ret.color = color;			
		return ret;			
		
	}	
	// }}}
	,
	// {{{ __getSquareAndPieceByNotation()
    /**
	 *  Return an object of square and piece by a notation, example Nxf6+ returns pieceType 'n' and toSquare '
     * 
     * @private
     */
	__getSquareAndPieceByNotation : function(notation,color)
	{		
		var ret = new Object();
		notation = notation.trim();
		notation = notation.replace(/[\+#]/g,'');
		if(notation=='0-0' || notation=='O-O'){
			ret.pieceType='k';
			if(color=='white')ret.square= 'g1'; else ret.square='g8';
			return ret;
		}
		if(notation == '0-0-0' || notation == 'O-O-O'){
			ret.pieceType='k';
			if(color=='white')ret.square= 'c1'; else ret.square='c8';
			return ret;			
		}
		var piece = notation.replace(/[^BKQNR]/g,'');
		if(!piece)piece='p';
		piece = piece.toLowerCase();
		var toSquare = notation.replace(/=[BKQNR][+\#]?/g,'');
		toSquare = toSquare.replace(/[\#+]/g,'');
		ret.square = toSquare.substr(toSquare.length-2,2);
		ret.pieceType = piece;
		return ret;		
	}
	// }}}
	,
	// {{{ __getFileByCol()
    /**
	 *  Return a-h from column number
     * 
     * @private
     */
	__getFileByCol : function(col)
	{
		var files = 'abcdefgh';	
		return files.substr(col-1,1);
	}
	// }}}
	,
	// {{{ __getColFromFile()
    /**
	 *  Return column(1-8) from file(a-h)
	 *	@param String file - (a-h)
     * 
     * @private
     */
	__getColFromFile : function(file)
	{
		var files = 'abcdefgh';
		return files.indexOf(file)+1;		
	}
	// }}}
	,
	// {{{ __getColAndRowFromSquare()
    /**
	 *  Return column(1-8) and row(1-8) from square(example: e4)
	 *	@param String square - example: "e4"
     * 
     * @private
     */
	__getColAndRowFromSquare : function(square)
	{
		var file = square.substr(0,1);
		var rank = square.substr(1,1)/1;
		file = this.__getColFromFile(file);
		var retArray = new Object();
		retArray.col = file;
		retArray.row = rank;
		return retArray;		
	}		
	// }}}
	,
	// {{{ __getRankFileByCol()
    /**
	 *  Return rank(1-8) and file(a-h) from column(1-64)
	 *	@param Integer col - example: 5 for e1(col starts at the bottom left corner)
     * 
     * @private
     */
	__getRankFileByCol : function(col)
	{
		var files = 'hgfedcba';
		var rank = 1;
		while(col>=8){
			rank++;
			col-=8;
		}
		
		var ret = new Object();
		ret.file = files.substr(col,1);
		
		ret.rank = rank;
		return ret;		
	}	
	// }}}
	,
	// {{{ __getNotationByBoardPos()
    /**
	 *  Return notation(example e4) from board position(x and y in pixels)
	 *	@param Integer x in pixels
	 *	@param integer y in pixels.
     * 
     * @private
     */
	__getNotationByBoardPos : function(x,y)
	{
		var files = 'abcdefgh';
		var file = x / this.squareSize;
		var rank = 8 - (y / this.squareSize);
		if(this.flipBoard){
			file = 7-file;
			rank = 9-rank;				
			
		}
		file = files.charAt(file);		
		return file+rank;		
	}
	// }}}
	,
	// {{{ __getBoardPosByNotation()
    /**
	 *  Return square position(x and y) from notation.
	 *	@param String notation, example: "e4"
     * 
     * @private
     */
	__getBoardPosByNotation : function(notation)
	{
		var files = 'abcdefgh';
		notation = notation.replace(/[^0-9a-h]/g,'');
		var y = notation.replace(/[^0-9]/gi,'')/1;
		var file = notation.replace(/[0-9]/gi,'');	
		var x = files.indexOf(file)+1;
		
		x--;
		y = 8-y;
		
		if(this.flipBoard){
			x = 7-x;
			y = 7-y;			
		}
		
		var retArray = new Object();
		retArray.x = x * this.squareSize;
		retArray.y = y * this.squareSize;
		return retArray;		
				
	}
	// }}}
	,
	// {{{ __getBoardPosByCol()
    /**
     *  Starting from the top - 1-64
	 *  
	 *	@param Integer col
     * 
     * @private
     */
	__getBoardPosByCol : function(col)
	{
		var rank = 0;
		while(col>=8){
			rank++;
			col-=8;
		}
		var retArray = new Object();
		
		if(this.flipBoard){
			col = 7-col;
			rank = 7-rank;			
		}
		
		retArray.x = col* this.squareSize;
		retArray.y = rank * this.squareSize;
		return retArray;
		
		
	}
	// }}}
	,
	// {{{ __loadCss()
    /**
     *  Load css file dynamically
	 *  
	 *	@param String cssFile
     * 
     * @private
     */
	__loadCss : function(cssFile)
	{
		var lt = document.createElement('LINK');
		lt.href = cssFile + '?rand=' + Math.random();			
		lt.rel = 'stylesheet';
		lt.media = 'screen';
		lt.type = 'text/css';
		document.getElementsByTagName('HEAD')[0].appendChild(lt);			
	}		
	// }}}
	,
	// {{{ __createIndicators()
    /**
     *  Create div indicators(rectangle).
	 *  
     * @private
     */
	__createIndicators : function()
	{
		this.divIndicators.from = document.createElement('DIV');
		this.divIndicators.from.style.position = 'absolute';
		this.divIndicators.from.className = 'ChessMoveIndicator';	
		this.divBoard.appendChild(this.divIndicators.from);	
		this.divIndicators.from.zIndex = 9000000;
		
		this.divIndicators.to = document.createElement('DIV');
		this.divIndicators.to.className = 'ChessMoveIndicator';	
		this.divIndicators.to.style.position = 'absolute';
		this.divBoard.appendChild(this.divIndicators.to);			
		this.divIndicators.to.zIndex = 9000000;

		this.divIndicators.to.style.width = '100px';
		this.divIndicators.to.style.height = '100px';	
	}
	// }}}
	,
	// {{{ __handleCallback()
    /**
     *  Handle callback actions.
     *	@param String action - which callback action.
	 *  
     * @private
     */
	__handleCallback : function(action)
	{
		var callbackString = '';
		switch(action){
			case 'beforeGameLoad':
				callbackString = this.callbackOnBeforeGameLoaded;
				break;					
			case 'startGame':
				callbackString = this.callbackOnGameLoaded;
				break;	
			case 'switchPgn':
				callbackString = this.callbackOnSwitchPgn;
				break;	
			case 'stopAutoPlay':
				callbackString = this.callbackOnStopAutoPlay;
				break;	
			case 'moveComplete':
				callbackString = this.callbackOnMoveComplete;
				break;
			case 'wrongMove':
				callbackString = this.callbackOnWrongMove;	
				break;
			case 'correctMove':
				callbackString = this.callbackOnCorrectMove;
				break;	
			case 'afterLastMove':
				callbackString = this.callbackAfterLastMove;
				break;
		}	
		if(callbackString){
			if(callbackString.indexOf('(')==-1){				
				callbackString = callbackString + '(this.pgnObject.gameDetails[' + this.currentGameIndex + '])';
			}
			eval(callbackString);		
		}	
	}
	// }}}
	,
	// {{{ __getEl()
    /**
     *  Return reference to DOM element from name or id. If direct reference is sent to this method, it will be returned unchanged.
     *	@param Object elRef - Reference to HTML element.
	 *  
     * @private
     */
	__getEl : function(elRef)
	{
		if(typeof elRef=='string'){
			if(document.getElementById(elRef))return document.getElementById(elRef);
			if(document.forms[elRef])return document.forms[elRef];
			if(document[elRef])return document[elRef];
			if(window[elRef])return window[elRef];
		}
		return elRef;	// Return original ref.		
	}
	// }}}
	,
	// {{{ __setInitProps()
    /**
     *  Set initial properties sent to the constructor
     *	@param Object props - Associative array of properties
	 *  
     * @private
     */
	__setInitProps : function(props)
	{
		if(props.cssPath)this.cssPath = props.cssPath;	
		if(props.imageFolder)this.imageFolder = props.imageFolder;	
		if(props.squareSize)this.squareSize = props.squareSize;	
		if(props.callbackOnGameLoaded)this.callbackOnGameLoaded = props.callbackOnGameLoaded;	
		if(props.callbackBeforeComments)this.callbackBeforeComments = props.callbackBeforeComments;	
		if(props.callbackOnBeforeGameLoaded)this.callbackOnBeforeGameLoaded = props.callbackOnBeforeGameLoaded;	
		if(props.callbackOnSwitchPgn)this.callbackOnSwitchPgn = props.callbackOnSwitchPgn;	
		if(props.callbackOnStopAutoPlay)this.callbackOnStopAutoPlay = props.callbackOnStopAutoPlay;	
		if(props.callbackOnWrongMove)this.callbackOnWrongMove = props.callbackOnWrongMove;	
		if(props.callbackOnCorrectMove)this.callbackOnCorrectMove = props.callbackOnCorrectMove;	
		if(props.callbackAfterLastMove)this.callbackAfterLastMove = props.callbackAfterLastMove;	
		if(props.autoPlayDelayBetweenMoves)this.autoPlayDelayBetweenMoves = props.autoPlayDelayBetweenMoves;	
		if(props.animationSpeed)this.animationSpeed = props.animationSpeed;	
		if(props.autoplayDelayBeforeComments)this.autoplayDelayBeforeComments = props.autoplayDelayBeforeComments;	
		if(props.stopAutoplayBeforeComments || props.stopAutoplayBeforeComments===false)this.stopAutoplayBeforeComments = props.stopAutoplayBeforeComments;	
		if(props.parentRef)this.parentRef = this.__getEl(props.parentRef);	
		if(props.boardLabels || props.boardLabels===false)this.boardLabels = props.boardLabels;	
		if(props.flipBoardWhenBlackToStart || props.flipBoardWhenBlackToStart===false)this.flipBoardWhenBlackToStart = props.flipBoardWhenBlackToStart;	
		if(props.chessSet)this.chessSet = props.chessSet;		
		if(props.elMovesInline)this.elMovesInline = this.__getEl(props.elMovesInline);		
		if(props.elMovesInTable){
			if(this.__isArray(props.elMovesInTable)){
				for(var no=0;no<props.elMovesInTable.length;no++){
					this.elMovesInTable[no] = this.__getEl(props.elMovesInTable[no]);
				}				
			}else{
				this.elMovesInTable[0] = this.__getEl(props.elMovesInTable);	
			}
		}	
		if(props.elPlayerNames)this.elPlayerNames = this.__getEl(props.elPlayerNames);		
		if(props.elActiveMove)this.elActiveMove = this.__getEl(props.elActiveMove);		
		if(props.elActiveComment)this.elActiveComment = this.__getEl(props.elActiveComment);		
		if(props.colorLightSquares || props.colorLightSquares=='')this.colorLightSquares = props.colorLightSquares;		
		if(props.colorDarkSquares || props.colorDarkSquares=='')this.colorDarkSquares = props.colorDarkSquares;		
		if(props.bgImageDarkSquares || props.bgImageDarkSquares=='')this.bgImageDarkSquares = props.bgImageDarkSquares;		
		if(props.bgImageLightSquares || props.bgImageLightSquares=='')this.bgImageLightSquares = props.bgImageLightSquares;		
		if(props.animate || props.animate===false)this.animate=props.animate;

		if(props.indicateLastMove || props.indicateLastMove===false)this.indicateLastMove=props.indicateLastMove;
		if(props.displayPrefaceCommentWithInlineMoves || props.displayPrefaceCommentWithInlineMoves===false)this.displayPrefaceCommentWithInlineMoves=props.displayPrefaceCommentWithInlineMoves;
		if(props.elGameAttributes)this.elGameAttributes = props.elGameAttributes;
		if(props.dragAndDropColor)this.dragAndDropColor = props.dragAndDropColor;

		if(props.elMovesInTableMaxMovesPerTable)this.elMovesInTableMaxMovesPerTable = props.elMovesInTableMaxMovesPerTable;
		if(props.languageCode)this.languageCode = props.languageCode;

	}
	// }}}
	,
	// {{{ __isDragOk()
    /**
     *  Return true if it's ok to start dragging this piece
     *	@param String color - color of piece
     *	@param Integer pieceIndex - Index of piece in piece array.
	 *  
     * @private
     */	
	__isDragOk : function(color,pieceIndex)
	{
		if(this.currentGameIndex===false || this.isBusy || this.autoPlayActive)return false;
		
		var nextMove = this.__getNextMove();
		if(color!=nextMove.color)return false;	// Color of piece is not the same as the one to move
		if(color!=this.dragAndDropColor)return false;
		return true;
			
	}
	// }}}
	,
	// {{{ __moveDownOnChessPiece()
    /**
     *  Mouse down event on check piece.
     *	@param Event e
	 *  
     * @private
     */	
	__clearDragProperties : function()
	{
		this.dragProperties = new Object();
	}
	// }}}
	,
	// {{{ __moveDownOnChessPiece()
    /**
     *  Mouse down event on check piece.
     *	@param Event e
	 *  
     * @private
     */	
	__moveDownOnChessPiece : function(e)
	{
		if(document.all)e = event;
		var color = this.getAttribute('color');
		var pieceIndex = this.getAttribute('pieceIndex');
		var objectIndex = this.getAttribute('objectIndex');
		var chessObj = D_chessObjects[objectIndex];
		var tmpColor = 'white';
		if(color=='b')tmpColor='black';
			
		if(chessObj.__isDragOk(tmpColor,pieceIndex)){
			var pieceX = chessObj.pieces[tmpColor][pieceIndex].el.style.left.replace('px','')/1;
			var pieceY = chessObj.pieces[tmpColor][pieceIndex].el.style.top.replace('px','')/1;
			chessObj.currentZIndex++;
			chessObj.pieces[tmpColor][pieceIndex].el.style.zIndex = chessObj.currentZIndex;					
			chessObj.dragProperties = { color:tmpColor,pieceIndex:pieceIndex,mouseX:e.clientX,mouseY:e.clientY,pieceX:pieceX,pieceY:pieceY,toFile:false,toRank:false };
			chessObj.dragCountDownVar = 2;
			document.body.style.cursor = 'move';
			chessObj.__countDownToDragStart();
		}
		return false;
	}
	// }}}
	,
	// {{{ __countDownToDragStart()
    /**
     *  A small delay before drag starts
     *	@param Event e
	 *  
     * @private
     */	
	__countDownToDragStart : function()
	{
		if(this.dragCountDownVar>=0 && this.dragCountDownVar<5){
			this.dragCountDownVar++;
			var ind = this.objectIndex;
			setTimeout('D_chessObjects[' + ind + '].__countDownToDragStart()',5);
		}		
	}
	// }}}
	,
	// {{{ __moveDraggedPiece()
    /**
     *  Move dragged piece according to the mouse position
     *	@param Event e
	 *  
     * @private
     */		
	__moveDraggedPiece : function(e)
	{
		if(document.all)e = event;
		if(this.dragCountDownVar==5){	// Drag is in progress
			this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex].el.style.left = (e.clientX - this.dragProperties.mouseX + this.dragProperties.pieceX) + 'px';
			this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex].el.style.top = (e.clientY - this.dragProperties.mouseY + this.dragProperties.pieceY) + 'px';			
		}	
	}
	// }}}
	,
	// {{{ __getSquareFromDragXY()
    /**
     *  Get square, example "e4" from x and y in pixels
     *	@param Integer x - x position in pixels
     *	@param Integer y - y position in pixels
	 *  
     * @private
     */		
	__getSquareFromDragXY : function(x,y)
	{
		x = Math.ceil(x/this.squareSize);
		y = Math.ceil(8-y/this.squareSize);	
		if(this.flipBoard){
			x = 9-x;
			y = 9-y;			
		}	
		return this.__getFileByCol(x) + y;	
	}
	// }}}
	,
	// {{{ __isCorrectPieceDraggedToCorrectSquare()
    /**
     *  Returns true if correct piece has been dragged to correct square
     *	@param String toSquare - To which square has it been dragged
	 *  
     * @private
     */		
	__isCorrectPieceDraggedToCorrectSquare : function(toSquare)
	{
		if(this.currentGameIndex===false)return false;	
		
		var nextMove = this.__getNextMove();	

		if(nextMove.pawnMove){
			if(nextMove.capture){
				var index = this.__movePawnCapture(nextMove.notation,nextMove.fromFile,nextMove.color,false);
			}else{
				var index = this.__movePawnForward(nextMove.notation,nextMove.color);	
			}
			if(index==this.dragProperties.pieceIndex && toSquare==nextMove.toSquare)return true;
			return false;		
		}
		
		var pieceObj = this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex];		
		var moveOk=true;
		
		if(!this.__canPieceMoveToSquare(this.dragProperties.color,this.dragProperties.pieceIndex/1,toSquare))moveOk=false;		
		
		if(pieceObj.pieceType!=nextMove.pieceType)moveOk=false;
		if(nextMove.fromFile && nextMove.fromFile!=pieceObj.file)moveOk=false;
		if(nextMove.toSquare!=toSquare)moveOk=false;
		if(nextMove.fromRank && nextMove.fromRank!=pieceObj.rank)moveOk=false;
		if(moveOk){
			return true;
		}
			
		// Move doesn't match main line, check variations.
		if(!this.insideVariation){	// We shouldn't be inside a variation already.
			var variations = this.__getVariationIndexes(nextMove.move,nextMove.color);
			if(variations){
				for(var prop in variations){
					var aVariation = variations[prop];
					this.__setStartVariationVariables(nextMove.move,nextMove.color,prop);
					if(this.__isCorrectPieceDraggedToCorrectSquare(toSquare))return true;
					
				}
				this.insideVariation = false;			
			}			
		}		
		var opositeColor = this.dragProperties.color=='white'?'black':'white';
		var el = this.__getPieceOnSquare(toSquare,opositeColor);
		if(el===false)this.__playSound('move');else this.__playSound('capture');
		return false;
	}
	// }}}
	,
	// {{{ __getVariationIndexes()
    /**
     *  Return array indexes for variations from a specific move.
	 *  
     * @private
     */
	__getVariationIndexes : function(move,color)
	{
		try{
			return this.pgnObject.gameDetails[this.currentGameIndex].moves[move][color].variation;
		}catch(e){
			return false;
		}
	}
	// }}}
	,
	// {{{ __dragAndDropAfterMoveCallback()
    /**
     *  This method is used to control how long auto play is allowed to proceed between dragged moves. 
	 *  
     * @private
     */	
	__dragAndDropAfterMoveCallback : function()
	{
		var lastMove = this.__getLastPlayedMove();
		if(lastMove.color==this.dragProperties.color){
			var ind = this.objectIndex;
			setTimeout('D_chessObjects[' + ind + '].move(1)',100);
			this.__handleCallback('correctMove');
		}else{
			this.callbackOnMoveComplete = false;
		}
		
	}
	// }}}
	,
	// {{{ __releaseDraggedPiece()
    /**
     *  Mouse up - check if drag is in progress and if piece has been moved to correct square
	 *  
     * @private
     */	
	__releaseDraggedPiece : function(e)
	{
		if(this.dragCountDownVar==5){
			document.body.style.cursor = '';
			var ind = this.objectIndex;
			this.dragCountDownVar = -1;
			if(document.all)e = event;	
			var x = e.clientX - this.__getLeftPos(this.divBoard) + Math.max(document.documentElement.scrollLeft,document.body.scrollLeft);
			var y = e.clientY - this.__getTopPos(this.divBoard) + Math.max(document.documentElement.scrollTop,document.body.scrollTop);		
			var toSquare = this.__getSquareFromDragXY(x,y);	
			
			if(this.__isCorrectPieceDraggedToCorrectSquare(toSquare)){
				this.callbackOnMoveComplete = 'D_chessObjects[' + ind + '].__dragAndDropAfterMoveCallback';
				this.move(1);				
			}else{				
				var fromPos = this.__getPieceXY(this.dragProperties.color,this.dragProperties.pieceIndex);
				
				var pieceObj = this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex];				
				var toPos = this.__getBoardPosByNotation(pieceObj.file+pieceObj.rank);
				
				var coordIndex = this.slideCoordinates.length;
				this.slideCoordinates[coordIndex] = this.__getSlideCoordinates({ x:fromPos.x,y:fromPos.y },{x:toPos.x,y:toPos.y });
			
				var dragDropVar = 'differentSquare';
				if(toSquare==this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex].file + this.pieces[this.dragProperties.color][this.dragProperties.pieceIndex].rank){
					dragDropVar = 'sameSquare';	
				}
				this.__slideElement(
					{ 'coordIndex':coordIndex,'coordStep':1 },
					{ 'x':fromPos.x,'y':fromPos.y }
					,
					this.dragProperties.pieceIndex,
					this.dragProperties.color,
					dragDropVar
				)			
			}		
		}	
	}
	// }}}
	,
	// {{{ __getPieceXY()
    /**
     *  Returns x and y position of a specific chess piece
     *	@param String color - color of piece
     *	@param Integer pieceIndex - Index of piece in the array of pieces.
	 *  
     * @private
     */	
	__getPieceXY : function(color,pieceIndex)
	{
		var ret = new Object();
		ret.x = this.pieces[color][pieceIndex].el.style.left.replace('px','')/1;
		ret.y = this.pieces[color][pieceIndex].el.style.top.replace('px','')/1;
		return ret;		
	}
	// }}}
	,
	// {{{ __cancelEvent()
    /**
     *  Just to cancel ondragstart and onselectstart events.
	 *  
     * @private
     */	
	__cancelEvent : function()
	{
		return false;
	}
	// }}}
	,
	// {{{ __addGeneralEvents()
    /**
     *  Add general events for the widget
	 *  
     * @private
     */	
	__addGeneralEvents : function()
	{
		var ind = this.objectIndex;
		this.addEvent(window,'unload',function(){ D_chessObjects[ind].__clearMemoryGarbage(); });		
		this.addEvent(document.documentElement,'mousemove',function(e){ D_chessObjects[ind].__moveDraggedPiece(e); });		
		this.addEvent(document.documentElement,'mouseup',function(e){ D_chessObjects[ind].__releaseDraggedPiece(e); });		
	}
	// }}}
	,

	// {{{ correctPng()
    /**
     *  Add transparency to pgn files dynamically - for old IE browsers.
     *	@param Object el - reference to DOM img element.
	 *  
     * @private
     */	
	correctPng : function(el)
	{		
		el = this.__getEl(el);		
		el.style.filter = "progid:DXImageTransform.Microsoft.AlphaImageLoader(src='" + el.src + "', sizingMethod='scale')";
		el.src = this.imageFolder + 'spacer.gif';
		el.width = this.squareSize;
		el.height = this.squareSize;
	}
	// }}}
	,
	// {{{ getStyle()
    /**
     * Return specific style attribute for an element
     *
     * @param Object el = Reference to HTML element
     * @param String property = Css property
     * @private
     */		
	__getStyle : function(el,property)
	{		
		el = this.__getEl(el);
	    if (document.defaultView && document.defaultView.getComputedStyle) {
            var retVal = null;	            
            var comp = document.defaultView.getComputedStyle(el, '');
            if (comp){
                retVal = comp[property];
            }	            
            return el.style[property] || retVal;
	    }
		if (document.documentElement.currentStyle && this.isMSIE){	
            var value = el.currentStyle ? el.currentStyle[property] : null;
            return ( el.style[property] || value );
                    	                            		
		}
		return el.style[property];				
	}	
	// }}}
	,
	// {{{ __clearTBodyRows()
    /**
     * Clear rows in a data table.
     *
     * @param Object tbodyRef = Reference to HTML element(tbody or table)
     * @private
     */	
	__clearTBodyRows : function(tbodyRef)
	{
		if(!tbodyRef)return;
		if(tbodyRef.tagName.toLowerCase()=='table'){
			var table = tbodyRef;
			var tbodies = tbodyRef.getElementsByTagName('TBODY');
			var tbodyRef = tbodies[0];
		}else{
			var table = tbodyRef.parentNode;
		}
		var className = tbodyRef.className;
		var css = tbodyRef.style.cssText;
		this.__discardElement(tbodyRef);
		var tbody = document.createElement('tbody');
		if(className)tbody.className = className;
		if(css)tbody.style.cssText = css;
		table.appendChild(tbody);
	}
	// }}}
	,
	// {{{ __replaceTbody()
    /**
     * Replace old <tbody> with new content. The rows are sent to this method as a string.
     *
     * @param String content = HTML Content - table rows.
     * @param Object tableRef = Reference to HTML element
     * @private
     */	
	__replaceTbody : function(content,tableRef)
	{
		var className;
		var css;			
		var tbodies = tableRef.getElementsByTagName('TBODY');
		if(tbodies.length>0){
			className = tbodies[0].className;
			css = tbodies[0].style.cssText;
			this.__discardElement(tbodies[0]);			
		}		
		content = '<tbody class="' + className + '" style="' + css + '">' + content + '</tbody>';		
		try{
			tableRef.innerHTML = tableRef.innerHTML + content;
		}catch(e){	// IE
			var outerHTML = tableRef.outerHTML;
			tokens = outerHTML.split('</TABLE>');
			newHTML = tokens[0] + content + '</table>';
			tableRef.outerHTML = newHTML;
		}		
	}
	// }}}
	,
	// {{{ __discardElement()
    /**
     * Delete DOM element
     *
     * @param Object element = Reference to HTML element
     * @private
     */	
	__discardElement : function(element){
		element = this.__getEl(element);
	    var gBin = document.getElementById('IELeakGBin'); 
	    if (!gBin) { 
	        gBin = document.createElement('DIV'); 
	        gBin.id = 'IELeakGBin'; 
	        gBin.style.display = 'none'; 
	        var head = document.getElementsByTagName('HEAD')[0];
	        head.appendChild(gBin); 
	    } 
	    // move the element to the garbage bin 
	    gBin.appendChild(element); 
	    gBin.innerHTML = ''; 
	}
	,
	// {{{ __isArray()
    /**
     * Return true if element is an array
     *
     * @param Object el = Reference to HTML element
     * @private
     */		
	__isArray : function(el)
	{
		if(!el)return false;
		if(el.constructor.toString().indexOf("Array") != -1)return true;
		return false;
	}
    ,
	// {{{ __addEventEl()
    /**
     *
     *  Add element to garbage collection array. The script will loop through this array and remove event handlers onload in ie.
     *
     * 
     * @private
     */	    
    __addEventEl : function(el)
    {
    	this.eventElements[this.eventElements] = el;    
    }
	,
	// {{{ __clearMemoryGarbage()
    /**
     *
     *  This function is used for Internet Explorer in order to clear memory when the page unloads.
     *
     * 
     * @private
     */	
    __clearMemoryGarbage : function()
    {
   		/* Example of event which causes memory leakage in IE 
   		DHTMLSuite.commonObj.addEvent(expandRef,"click",function(){ window.refToMyMenuBar[index].__changeMenuBarState(this); })
   		We got a circular reference.
   		*/
    	if(!this.isMSIE)return;
   	
    	for(var no=0;no<this.eventElements.length;no++){
    		try{
    			var el = this.eventElements[no];
	    		el.onclick = null;
	    		el.onmousedown = null;
	    		el.onmousemove = null;
	    		el.onmouseout = null;
	    		el.onmouseover = null;
	    		el.onmouseup = null;
	    		el.onfocus = null;
	    		el.onblur = null;
	    		el.onkeydown = null;
	    		el.onkeypress = null;
	    		el.onkeyup = null;
	    		el.onselectstart = null;
	    		el.ondragstart = null;
	    		el.oncontextmenu = null;
	    		el.onscroll = null;   
	    		el = null; 		
    		}catch(e){
    		}
    	}    	
    	window.onbeforeunload = null;
    	window.onunload = null;
    }   
	// }}}	
	,
	// {{{ addEvent()
    /**
     *
     *  This function adds an event listener to an element on the page.
     *
     *	@param Object whichObject = Reference to HTML element(Which object to assigne the event)
     *	@param String eventType = Which type of event, example "mousemove" or "mouseup" (NOT "onmousemove")
     *	@param functionName = Name of function to execute. 
     * 
     * @public
     */	
	addEvent : function( obj, type, fn,suffix ) {
		if(!suffix)suffix = '';
		if ( obj.attachEvent ) {
			if ( typeof ChessWidgetEventFuncs[type+fn+suffix] != 'function') {
				ChessWidgetEventFuncs[type+fn+suffix] = function() {
					fn.apply(window.event.srcElement);
				};
				obj.attachEvent('on'+type, ChessWidgetEventFuncs[type+fn+suffix] );
			}
			obj = null;
		} else {
			obj.addEventListener( type, fn, false );
		}
		this.__addEventEl(obj);
	}
	// }}}	
	,	
	// {{{ removeEvent()
    /**
     *
     *  This function removes an event listener from an element on the page.
     *
     *	@param Object whichObject = Reference to HTML element(Which object to assigne the event)
     *	@param String eventType = Which type of event, example "mousemove" or "mouseup"
     *	@param functionName = Name of function to execute. 
     * 
     * @public
     */		
	removeEvent : function(obj,type,fn,suffix)
	{ 
		if ( obj.detachEvent ) {
		obj.detachEvent( 'on'+type, ChessWidgetEventFuncs[type+fn+suffix] );
			ChessWidgetEventFuncs[type+fn+suffix] = null;
			obj = null;
		} else {
			obj.removeEventListener( type, fn, false );
		}
	}     	
	,
	// {{{ getLeftPos()
    /**
     * This method will return the left coordinate(pixel) of an HTML element
     *
     * @param Object el = Reference to HTML element
     * @private
     */	
	__getLeftPos : function(el)
	{	 
		if(document.getBoxObjectFor){
			return document.getBoxObjectFor(el).x
		}		 
		var returnValue = el.offsetLeft;
		while((el = el.offsetParent) != null){
			if(el.tagName!='HTML'){
				returnValue += el.offsetLeft;
				if(document.all)returnValue+=el.clientLeft;
			}
		}
		return returnValue;
	}
	// }}}
	,
	// {{{ getTopPos()
    /**
     * This method will return the top coordinate(pixel) of an HTML element/tag
     *
     * @param Object el = Reference to HTML element
     * @private
     */	
	__getTopPos : function(el)
	{	
		if(document.getBoxObjectFor){
			return document.getBoxObjectFor(el).y
		}
		
		var returnValue = el.offsetTop;
		while((el = el.offsetParent) != null){
			if(el.tagName!='HTML'){
				returnValue += (el.offsetTop - el.scrollTop);
				if(document.all)returnValue+=el.clientTop;
			}
		} 
		return returnValue;
	}			
}
