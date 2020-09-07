/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * KingdomBuilder implementation : © <Your name here> <Your email address here>
 *
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * KingdomBuilder.js
 *
 * KingdomBuilder user interface script
 *
 * In this file, you are describing the logic of your user interface, in Javascript language.
 *
 */

//# sourceURL=kingdombuilder.js
//@ sourceURL=kingdombuilder.js
var isDebug = window.location.host == 'studio.boardgamearena.com';
var debug = isDebug ? console.info.bind(window.console) : function () { };
define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare) {
  function override_addMoveToLog(logId, moveId) {
    // [Undocumented] Called by BGA framework on new log notification message
    // Handle cancelled notifications
    this.inherited(override_addMoveToLog, arguments);
    if (this.gamedatas.cancelMoveIds && this.gamedatas.cancelMoveIds.includes(+moveId)) {
      debug('Cancel notification message for move ID ' + moveId + ', log ID ' + logId);
      dojo.addClass('log_' + logId, 'cancel');
    }
  }

  return declare("bgagame.kingdombuilder", ebg.core.gamegui, {
/*
 * [Undocumented] Override BGA framework functions
 */
addMoveToLog: override_addMoveToLog,

/*
 * Constructor
 */
constructor: function () { },

/*
 * Setup:
 *  This method set up the game user interface according to current game situation specified in parameters
 *  The method is called each time the game interface is displayed to a player, ie: when the game starts and when a player refreshes the game page (F5)
 *
 * Params :
 *  - mixed gamedatas : contains all datas retrieved by the getAllDatas PHP method.
 */
setup: function (gamedatas) {
  debug('SETUP', gamedatas);

  Object.values(gamedatas.locations).forEach(location => {
    location.name = _(location.name);
    location.description = location.text.map(text => _(text)).join("");
  });


  // Setup the board
  for(var i = 0; i < 20; i++){
  for(var j = 0; j < 20; j++){
    var cellC = $('cell-container-' + i + "-" + j);
    cellC.style.gridRow = (3*i + 1) + " / span 4";
    cellC.style.gridColumn = 2*j + (i % 2 == 0? 1 : 2) + " / span 2";

    let x = i, y = j;
    dojo.connect($('cell-' + i + "-" + j), 'onclick', evt => {
      evt.preventDefault(); evt.stopPropagation();
      this.onClickCell(x,y);
    });
  }}

  // Setup player's board
  gamedatas.fplayers.forEach(player => {
    dojo.place( this.format_block( 'jstpl_player_panel', player) , 'overall_player_board_' + player.id );
    player.tiles.forEach(tile => this.addTile(tile));
  });
  dojo.place("<div id='first-player'></div>", "player_name_" + gamedatas.firstPlayer);
  this.addTooltip('first-player', _('First player'));


  // Setup stuff on board
  this.setupBoard(gamedatas.board, true);

  // Setup objectives
  gamedatas.objectives.forEach(objective => {
    objective.name = _(objective.name);
    objective.desc = _(objective.desc);
    objective.text = objective.text.map(text => _(text)).join("<br />");
    var div = dojo.place( this.format_block( 'jstpl_objective', objective) , 'objectives' );
    div.id = "objective-" + objective.id;
    this.addTooltipHtml(div.id, this.format_block( 'jstpl_objective', objective));
  });


  this.setupPreference();

  // Setup game notifications
  this.setupNotifications();
},


/*
 * setupBoard : setup settlements and tiles on the board
 */
setupBoard: function(board, firstInit){
  debug("Setting up the board", board);
  board.settlements.forEach(this.addSettlement.bind(this));

  board.locations.forEach(location => {
    dojo.attr("cell-" + location.x + "-" + location.y, "data-location", location.location);
    if(firstInit){
      dojo.place(this.format_block('jstpl_tile_container', location) , 'cell-container-' + location.x + '-' + location.y);
    } else {
      dojo.query('#tile-container-' + location.x + '-' + location.y + ' .tile').forEach(dojo.destroy);
    }

    var l = this.getLocation(location);
    if(l)
      this.addTooltipHtml('cell-container-' + location.x + '-' + location.y, this.format_block( 'jstpl_tilePrompt', l));
  });

  board.tiles.forEach(tile => {
    dojo.place(this.format_block( 'jstpl_tile', tile), 'tile-container-' + tile.x + '-' + tile.y);
  });
},


setupPreference: function () {
  var preferenceSelect = $('preference_control_100');
  var updatePreference = () => {
    var value = preferenceSelect.options[preferenceSelect.selectedIndex].value;
    if(value == 2)
      dojo.addClass("board", "no-hex-border")
    else
      dojo.removeClass("board", "no-hex-border")
  };

  dojo.connect(preferenceSelect, 'onchange', updatePreference);
  updatePreference();
},


/*
 * notif_showTerrain:
 *   called whenever a player show its terrain card (during its turn)
 */
notif_showTerrain: function (n) {
  debug('Notif: update player terrain card', n.args);
  $('player-terrain-' + n.args.pId).className = "player-terrain terrain-" + n.args.terrain;
},



notif_enableTiles: function (n) {
  debug('Notif: update player tiles', n.args);
  dojo.query("#player-tiles-" + n.args.pId + " .tile").removeClass("pending");
},


/*
 * onEnteringState:
 * 	this method is called each time we are entering into a new game state.
 * params:
 *  - str stateName : name of the state we are entering
 *  - mixed args : additional information
 */
onEnteringState: function (stateName, args) {
  debug('Entering state: ' + stateName, args);

  if (args && args.args && args.args.tileName != "" && this.gamedatas.gamestate.descriptiontile) {
    this.gamedatas.gamestate.description = this.gamedatas.gamestate.descriptiontile;
    this.gamedatas.gamestate.descriptionmyturn = this.gamedatas.gamestate.descriptiontilemyturn;
    this.updatePageTitle();
  }

  // Stop here if it's not the current player's turn for some states
  if (["playerBuild", "playerMove", "confirmTurn"].includes(stateName)) {
    if (!this.isCurrentPlayerActive()) return;
  }

  // Call appropriate method
  var methodName = "onEnteringState" + stateName.charAt(0).toUpperCase() + stateName.slice(1);
  if (this[methodName] !== undefined)
    this[methodName](args.args);
},


/*
 * onLeavingState:
 * 	this method is called each time we are leaving a game state.
 *
 * params:
 *  - str stateName : name of the state we are leaving
 */
onLeavingState: function (stateName) {
  debug('Leaving state: ' + stateName);
  this.clearPossible();
},


/*
 * clearPossible:
 * 	clear every clickable space and any selected worker
 */
clearPossible: function clearPossible() {
  this.removeActionButtons();
  this.onUpdateActionButtons(this.gamedatas.gamestate.name, this.gamedatas.gamestate.args);

  dojo.query(".hex-grid-content").removeClass("selectable selected");
},


/*
 * onUpdateActionButtons:
 * 	called by BGA framework before onEnteringState
 *  in this method you can manage "action buttons" that are displayed in the action status bar (ie: the HTML links in the status bar).
 */
onUpdateActionButtons: function (stateName, args, suppressTimers) {
  debug('Update action buttons: ' + stateName, args); // Make sure it the player's turn
  this.stopActionTimer();

  if (!this.isCurrentPlayerActive())
    return;


  if (args && args.tiles && args.tiles.length > 0)
    this.addActionButton('buttonUsePower', _('Use a location tile'), 'onClickUseTile', null, false, 'blue');

  if (args && args.skippable)
    this.addActionButton('buttonSkip', _('Skip'), 'onClickSkip', null, false, 'gray');

  if (stateName == "confirmTurn") {
    this.addActionButton('buttonConfirm', _('Confirm'), 'onClickConfirm', null, false, 'blue');
    args = { cancelable : true };
  }

  if (args && args.cancelable){
    this.addActionButton('buttonRestart', _('Restart turn'), 'onClickRestart', null, false, 'gray');
    this.addActionButton('buttonUndo', _('Undo'), 'onClickUndo', null, false, 'gray');
  }
},


/*
 * TODO description
 */
takeAction: function (action, data, callback) {
  data = data || {};
  data.lock = true;
  callback = callback || function (res) { };
  this.ajaxcall("/kingdombuilder/kingdombuilder/" + action + ".html", data, this, callback);
},




/////////////////////////////////
/////////////////////////////////
//////    Cancel turn    ////////
/////////////////////////////////
/////////////////////////////////
/*
 * notif_cancel:
 *   called whenever a player restart their turn
 */
notif_cancel: function (n) {
  debug('Notif: cancel turn', n.args);

  // Clear existing settlements
  dojo.query(".hex-settlement").forEach(settlement => {
    settlement.parentNode.className = "hex-grid-content";
    dojo.destroy(settlement);
  });

  // Reset settlements counter
  n.args.fplayers.forEach(player => {
    $("player-settlements-" + player.id).firstChild.innerHTML = player.settlements;

    dojo.empty("player-tiles-"+ player.id);
    player.tiles.forEach(this.addTile.bind(this));
  });

  // Reset board
  this.setupBoard(n.args.board);

  this.cancelNotifications(n.args.moveIds);
},

/*
 * cancelNotifications: cancel past notification log messages the given move IDs
 */
cancelNotifications: function(moveIds) {
  for (var logId in this.log_to_move_id) {
    var moveId = +this.log_to_move_id[logId];
    if (moveIds.includes(moveId)) {
      debug('Cancel notification message for move ID ' + moveId + ', log ID ' + logId);
      dojo.addClass('log_' + logId, 'cancel');
    }
  }
},

onEnteringStateConfirmTurn: function(args){
  this.startActionTimer('buttonConfirm');
},


/*
 * Add a timer to an action and trigger action when timer is done
 */
startActionTimer: function (buttonId) {
  if(!$(buttonId))
    return;

  var isReadOnly = this.isReadOnly();
  if (isDebug || isReadOnly || !this.bRealtime) {
    debug('Ignoring startActionTimer(' + buttonId + ')', 'debug=' + isDebug, 'readOnly=' + isReadOnly, 'realtime=' + this.bRealtime);
    return;
  }

  this.actionTimerLabel = $(buttonId).innerHTML;
  this.actionTimerSeconds = 15;
  this.actionTimerFunction = () => {
    var button = $(buttonId);
    if (button == null) {
      this.stopActionTimer();
    } else if (this.actionTimerSeconds-- > 1) {
      debug('Timer ' + buttonId + ' has ' + this.actionTimerSeconds + ' seconds left');
      button.innerHTML = this.actionTimerLabel + ' (' + this.actionTimerSeconds + ')';
    } else {
      debug('Timer ' + buttonId + ' execute');
      button.click();
    }
  };
  this.actionTimerFunction();
  this.actionTimerId = window.setInterval(this.actionTimerFunction, 1000);
  debug('Timer #' + this.actionTimerId + ' ' + buttonId + ' start');
},

stopActionTimer: function () {
  if (this.actionTimerId != null) {
    debug('Timer #' + this.actionTimerId + ' stop');
    window.clearInterval(this.actionTimerId);
    delete this.actionTimerId;
  }
},


/*
 * onClickCancel: is called when the active player decide to undo last action
 */
onClickUndo: function () {
  if (!this.checkAction('undoAction')) {
    return;
  }
  this.takeAction("undoAction");
  this.clearPossible();
},



/*
 * onClickRestart: is called when the active player decide to cancel previous works
 */
onClickRestart: function () {
  if (!this.checkAction('restartTurn')) {
    return;
  }
  this.takeAction("restartTurn");
  this.clearPossible();
},


/*
 * onClickConfirm: is called when the active player decide to confirm their turn
 */
onClickConfirm: function () {
  if (!this.checkAction('confirm')) {
    return;
  }
  this.takeAction("confirmTurn");
},


/////////////////////////////////
/////////////////////////////////
/////////    Build     //////////
/////////////////////////////////
/////////////////////////////////

/*
 * playerBuild: TODO
 */
onEnteringStatePlayerBuild: function (args) {
  this.makeCellSelectable(args.hexes);
},


/*
 * onClickCell: TODO
 */
onClickCell: function(x,y){
  if(!this.isCurrentPlayerActive() || !$("cell-"+x+"-"+y).classList.contains('selectable'))
    return;

  var state = this.gamedatas.gamestate.name,
      data = { x:x, y:y };

  if(state == "playerBuild"){ this.takeAction('build', data);  }
  if(state == "playerMove"){ this.onClickCellPlayerMove(data); }
},


notif_build: function (n) {
  debug('Notif: building a settlement', n.args);

  var container = "player-settlements-" + n.args.player_id,
      target  = "cell-" + n.args.x + "-" + n.args.y,
      number = $(container).firstChild;

  this.slideTemporary('jstpl_settlement', { no:this.getPlayerNo(n.args.player_id) }, container, container, target, 1000, 0)
    .then(() => this.addSettlement(n.args) ),
  number.innerHTML = parseInt(number.innerHTML) - 1;
},



notif_obtainTile: function (n) {
  debug('Notif: obtaining a tile', n.args);

  var container = "player-tiles-" + n.args.player_id,
      tile  = dojo.query("#tile-container-" + n.args.x + "-" + n.args.y + " .tile")[0];

  this.slideDestroy(tile, container, 1000, 0)
    .then(() =>  this.addTile(n.args) );
},


notif_loseTile: function(n){
  debug('Notif: losing a tile', n.args);
  this.slideToObjectAndDestroy("tile-" + n.args.id, "topbar", 1000, 0);
},


/////////////////////////////////
/////////////////////////////////
////////   Use power    /////////
/////////////////////////////////
/////////////////////////////////
onClickUseTile: function(){
  var dial = new ebg.popindialog();
  dial.create('chooseTile');
  dial.setTitle(_("Choose the location tile"));

  this.gamedatas.gamestate.args.tiles.forEach(tile => {
    var div = dojo.place(this.format_block('jstpl_tilePrompt', this.getLocation(tile)), $('popin_chooseTile_contents'));

    dojo.connect(div, 'onclick', (e) => {
      dial.destroy();
      this.onClickSelectTile(tile);
    });
  });
  dial.show();
},

onClickSelectTile: function(tile){
  if(!this.isCurrentPlayerActive())
    return;

  var test = this.gamedatas.gamestate.args.tiles.find(function(t){ return t.id == tile.id });
  if(test)
    this.takeAction('useTile', { tileId: tile.id });
},


notif_useTile: function(n){
  debug('Notif: using a tile', n.args);
  dojo.addClass("tile-" + n.args.id, "pending");
},


onClickSkip: function(){
  if(!this.isCurrentPlayerActive())
    return;

  this.takeAction('skip', {});
},




////////////////////////////
////////////////////////////
////////   Move    /////////
////////////////////////////
////////////////////////////

/*
 * playerMove: TODO
 */
onEnteringStatePlayerMove: function (args) {
  this._selectedHex = null;
  this.makeCellSelectable(args.hexes);
},

onClickCellPlayerMove: function(pos){
  if(this._selectedHex == null){
    this._selectedHex = pos;
    this.clearPossible();
    dojo.addClass('cell-' + pos.x + "-" + pos.y, "selected");
    this.takeAction('moveSelect', pos);
  } else {
    var data = {
      fromX: this._selectedHex.x,
      fromY: this._selectedHex.y,
      toX: pos.x,
      toY: pos.y,
    };
    this.takeAction('move', data);
  }
},

notif_argPlayerMoveTarget: function(n){
  debug('Notif: displaying targets for move', n.args);

  this.makeCellSelectable(n.args.hexes);
  this.removeActionButtons();
  this.addActionButton('buttonCancelMoveSelect', _('Cancel'), 'onClickCancelSelectedHex', null, false, 'gray');
},

onClickCancelSelectedHex: function(){
  this.clearPossible();
  this._selectedHex = null;
  this.makeCellSelectable(this.gamedatas.gamestate.args.hexes);
},

notif_move: function (n) {
  debug('Notif: moving a settlement', n.args);

  var container = "player-settlements-" + n.args.player_id,
      source  = "cell-" + n.args.from.x + "-" + n.args.from.y,
      target  = "cell-" + n.args.x + "-" + n.args.y;

  this.slideTemporary('jstpl_settlement', { no:this.getPlayerNo(n.args.player_id) }, container, source, target, 1000, 0)
    .then(() => this.addSettlement(n.args) ),

  dojo.empty(source);
  $(source).className = "hex-grid-content";
},



////////////////////////////
////////////////////////////
///////   Scoring    ///////
////////////////////////////
////////////////////////////
notif_scoringEnd:function(n){
  debug("Notif: scoring end", n);

  dojo.query("#game_play_area .scorenumber").forEach(dojo.destroy);
  dojo.query('#objectives .objective').removeClass('active');
  dojo.query('#objectives .objective').addClass('inactive');
  if($("objective-" + n.args.objectiveId)){
    dojo.removeClass("objective-" + n.args.objectiveId, 'inactive');
    dojo.addClass("objective-" + n.args.objectiveId, 'active');
    this.displayScoring("objective-mask-" + n.args.objectiveId, this.gamedatas.players[n.args.playerId].color, n.args.total, 1900);
  }

  dojo.query("li.hex-grid-item").addClass("inactive");
  n.args.highlights.forEach(cell => {
    var id = "cell-container-" + cell.x + "-" + cell.y;
    if(!$(id)) return;
    dojo.removeClass(id, "inactive");
    setTimeout( () => dojo.addClass(id, "inactive"), 3000);
  });

  n.args.detail.forEach(detail => {
    var cell = detail.hexes[0];
    this.displayScoring("cell-" + cell.x + "-" + cell.y, this.gamedatas.players[n.args.playerId].color, detail.score, 1900);
  });
  this.scoreCtrl[n.args.playerId].incValue(n.args.total);
  setTimeout( () => dojo.query("#game_play_area .scorenumber").forEach(dojo.destroy), 2000);
},



////////////////////////////////
////////////////////////////////
/////////    Utils    //////////
////////////////////////////////
////////////////////////////////
makeCellSelectable: function(hexes){
  hexes.forEach(function(pos){
    dojo.addClass('cell-' + pos.x + "-" + pos.y, "selectable");
  });
},

addSettlement: function(settlement){
  var no = this.getPlayerNo(settlement.player_id),
      cell = 'cell-' + settlement.x + '-' + settlement.y;
  dojo.place( this.format_block( 'jstpl_settlement', { no: no}),  cell);
  dojo.addClass(cell, 'cell-player-'+no);
},

addTile: function(tile){
  var div = dojo.place( this.format_block( 'jstpl_tile', tile), "player-tiles-" + tile.player_id);
  if(tile.status == "pending"){
    dojo.addClass(div, "pending");
  }

  setTimeout( () => this.addTooltipHtml(div.id, this.format_block( 'jstpl_tilePrompt',  this.getLocation(tile))), 1);
  dojo.connect(div, 'onclick', () => this.onClickSelectTile(tile) );
},



getPlayerNo: function(playerId){
  return this.gamedatas.fplayers.reduce(function(carry, player){ return player.id == playerId? player.cno : carry}, 0);
},


slideDestroy: function (node, to, duration, delay) {
  return new Promise((resolve, reject) => {
    var animation = this.slideToObjectAndDestroy(node, to, duration, delay);
    setTimeout(function(){
      resolve();
    }, duration + delay + 2)
  });
},


slideTemporary: function (template, data, container, sourceId, targetId, duration, delay) {
  return new Promise((resolve, reject) => {
    var animation = this.slideTemporaryObject(this.format_block(template, data), container, sourceId, targetId, duration, delay);
    setTimeout(function(){
      resolve();
    }, duration + delay + 1)
  });
},


getLocation: function(tile){
  var location = this.gamedatas.locations[tile.location];
  if(typeof location == "undefined") return null;
  location.location = tile.location;
  return location;
},


isReadOnly: function () {
  return this.isSpectator || typeof g_replayFrom != "undefined" || g_archive_mode;
},


///////////////////////////////////////////////////
//////   Reaction to cometD notifications   ///////
///////////////////////////////////////////////////

/*
 * setupNotifications:
 *  In this method, you associate each of your game notifications with your local method to handle it.
 *	Note: game notification names correspond to "notifyAllPlayers" and "notifyPlayer" in the santorini.game.php file.
 */
setupNotifications: function () {
  var notifs = [
    ['build', 1000],
    ['move', 1000],
    ['cancel', 200],
    ['obtainTile', 1000],
    ['loseTile', 1000],
    ['useTile', 500],
    ['showTerrain', 1],
    ['enableTiles', 1],
    ['argPlayerMoveTarget', 1],
    ['scoringEnd', 4000],
  ];

  notifs.forEach(notif => {
    dojo.subscribe(notif[0], this, "notif_" + notif[0]);
    this.notifqueue.setSynchronous(notif[0], notif[1]);
  });
}

   });
});
