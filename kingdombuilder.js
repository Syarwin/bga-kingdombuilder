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

//# sourceURL=santorini.js
//@ sourceURL=santorini.js
var isDebug = true;
var debug = isDebug ? console.info.bind(window.console) : function () { };
define(["dojo", "dojo/_base/declare", "ebg/core/gamegui", "ebg/counter"], function (dojo, declare) {
  return declare("bgagame.kingdombuilder", ebg.core.gamegui, {

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
  var _this = this;
  debug('SETUP', gamedatas);

  // Setup the board
  var callback = function(x,y){
    return function(evt){
      evt.preventDefault(); evt.stopPropagation();
      _this.onClickCell(x,y);
    }
  };
  var quadrants = gamedatas.quadrants;
  for(var k = 0; k < 4; k++)
  for(var i = 0; i < 10; i++)
  for(var j = 0; j < 10; j++){
    var flipped = quadrants[k] >= 8;
    if(flipped)
      quadrants[k] -= 8;

    var _i = flipped? (9 - i) : i;
    var _j = flipped? (9 - j) : j;
    if(k == 1 || k == 3) _j += 10;
    if(k == 2 || k == 3) _i += 10;

    var cell = $('cell-background-' + _i + "-" + _j);
    var backgroundLeft = 10.53*j + (i % 2 == 1? 5.25 : 0);
    var backgroundTop = quadrants[k]*12.7 + 1.235*i;
    cell.style.backgroundPosition = backgroundLeft + "% " + backgroundTop + "%";
    if(flipped)
      cell.style.transform = "skewY(-30deg) rotate(240deg)";

    dojo.connect($('cell-' + _i + "-" + _j), 'onclick', callback(_i,_j));
  }

  // Setup player's board
  gamedatas.fplayers.forEach(function(player){
    dojo.place( _this.format_block( 'jstpl_player_panel', player) , 'overall_player_board_' + player.id );
    player.tiles.forEach(_this.addTile.bind(_this));
  });


  // Setup stuff on board
  this.setupBoard(gamedatas.board);

  // Handle for cancelled notification messages
  dojo.subscribe('addMoveToLog', this, 'kingdombuilder_addMoveToLog');

  // Setup game notifications
  this.setupNotifications();
},


/*
 * setupBoard : setup settlements and tiles on the board
 */
setupBoard: function(board){
  var _this = this;
  debug("Setting up the board", board);
  board.settlements.forEach(this.addSettlement.bind(this));

  board.locations.forEach(function(location){
    var cell = 'cell-' + location.x + '-' + location.y;
    $(cell).innerHTML = location.n;
  });
},


/*
 * notif_showTerrain:
 *   called whenever a player show its terrain card (during its turn)
 */
notif_showTerrain: function (n) {
  debug('Notif: update player terrain card', n.args);
  $('player-terrain-' + n.args.pId).className = "player-terrain terrain-" + n.args.terrain;
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
  if (["playerBuild"].includes(stateName)) {
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
//  this._connections.forEach(dojo.disconnect);
//  this._connections = [];
},


/*
 * onUpdateActionButtons:
 * 	called by BGA framework before onEnteringState
 *  in this method you can manage "action buttons" that are displayed in the action status bar (ie: the HTML links in the status bar).
 */
onUpdateActionButtons: function (stateName, args, suppressTimers) {
  debug('Update action buttons: ' + stateName, args); // Make sure it the player's turn

  if (!this.isCurrentPlayerActive())
    return;

  if (args && args.tiles && args.tiles.length > 0)
    this.addActionButton('buttonUsePower', _('Use a location tile'), 'onClickUseTile', null, false, 'blue');

  if (args && args.skippable)
    this.addActionButton('buttonSkip', _('Skip'), 'onClickSkip', null, false, 'gray');

  if ((stateName == "playerBuild" || stateName == "playerUsePower")) {
    if (args.cancelable)
      this.addActionButton('buttonCancel', _('Restart turn'), 'onClickCancel', null, false, 'gray');
  }

  if (stateName == "confirmTurn") {
    this.addActionButton('buttonConfirm', _('Confirm'), 'onClickConfirm', null, false, 'blue');
    this.addActionButton('buttonCancel', _('Restart turn'), 'onClickCancel', null, false, 'gray');
    if (!suppressTimers)
      this.startActionTimer('buttonConfirm');
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
  dojo.query(".hex-settlement").forEach(function(settlement){
    settlement.parentNode.className = "hex-grid-content";
    dojo.destroy(settlement);
  });

  // Reset settlements counter
  n.args.fplayers.forEach(function(player){
    $("player-settlements-" + player.id).firstChild.innerHTML = player.settlements;
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

/*
 * addMoveToLog: called by BGA framework when a new notification message is logged.
 * cancel it immediately if needed.
 */
kingdombuilder_addMoveToLog: function (logId, moveId) {
  if (this.gamedatas.cancelMoveIds && this.gamedatas.cancelMoveIds.includes(+moveId)) {
    debug('Cancel notification message for move ID ' + moveId + ', log ID ' + logId);
    dojo.addClass('log_' + logId, 'cancel');
  }
},


/*
 * Add a timer to an action and trigger action when timer is done
 */
startActionTimer: function (buttonId) {
  var _this = this;
  this.actionTimerLabel = $(buttonId).innerHTML;
  this.actionTimerSeconds = 10;
  this.actionTimerFunction = function () {
    var button = $(buttonId);
    if (button == null) {
      _this.stopActionTimer();
    } else if (_this.actionTimerSeconds-- > 1) {
      debug('Timer ' + buttonId + ' has ' + _this.actionTimerSeconds + ' seconds left');
      button.innerHTML = _this.actionTimerLabel + ' (' + _this.actionTimerSeconds + ')';
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
 * onClickCancel: is called when the active player decide to cancel previous works
 */
onClickCancel: function () {
  if (!this.checkAction('cancel')) {
    return;
  }
  this.takeAction("cancelPreviousWorks");
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
/////////    Settle    //////////
/////////////////////////////////
/////////////////////////////////

/*
 * playerSettle: TODO
 */
onEnteringStatePlayerBuild: function (args) {
  var _this = this;

  args.hexes.forEach(function(pos){
    dojo.addClass('cell-' + pos.x + "-" + pos.y, "selectable");
  });
},


/*
 * onClickCell: TODO
 */
onClickCell: function(x,y){
  if(!this.isCurrentPlayerActive() || !$("cell-"+x+"-"+y).classList.contains('selectable'))
    return;

  var state = this.gamedatas.gamestate.name;

  if(state == "playerBuild"){
    this.takeAction('build', { x:x, y:y });
  }
},


notif_build: function (n) {
  var _this = this;
  debug('Notif: building a settlement', n.args);

  var container = "player-settlements-" + n.args.player_id,
      target  = "cell-" + n.args.x + "-" + n.args.y,
      number = $(container).firstChild;

  this.slideTemporary('jstpl_settlement', { no:this.getPlayerNo(n.args.player_id) }, container, container, target, 1000, 0)
    .then(function(){ _this.addSettlement(n.args);  }),
  number.innerHTML = parseInt(number.innerHTML) - 1;
},



notif_obtainTile: function (n) {
  var _this = this;
  debug('Notif: obtaining a tile', n.args);

  var container = "player-tiles-" + n.args.player_id,
      cell  = "cell-" + n.args.x + "-" + n.args.y;

  this.slideTemporary('jstpl_tile', n.args, container, cell, container, 1000, 0)
    .then(function(){ _this.addTile(n.args);  }),
  $(cell).innerHTML = parseInt($(cell).innerHTML) - 1;

},



/////////////////////////////////
/////////////////////////////////
////////   Use power    /////////
/////////////////////////////////
/////////////////////////////////
onClickUseTile: function(){
  var _this = this;
  var dial = new ebg.popindialog();
  dial.create('chooseTile');
  dial.setTitle(_("Choose the location tile"));

  this.gamedatas.gamestate.args.tiles.forEach(function (tile) {
    var div = dojo.place(_this.format_block('jstpl_tilePrompt', _this.getLocation(tile)), $('popin_chooseTile_contents'));

    dojo.connect(div, 'onclick', function (e) {
      dial.destroy();
      _this.onClickSelectTile(tile);
    });
  });
  dial.show();
},

onClickSelectTile: function(tile){
  if(!this.isCurrentPlayerActive())
    return;

  this.takeAction('useTile', { tileId: tile.id });
},


notif_useTile: function(n){
  debug('Notif: using a tile', n.args);
  this.slideToObjectAndDestroy("tile-" + n.args.id, "topbar", 1000, 0);
},


onClickSkip: function(){
  if(!this.isCurrentPlayerActive())
    return;

  this.takeAction('skip', {});
},

////////////////////////////////
////////////////////////////////
/////////    Utils    //////////
////////////////////////////////
////////////////////////////////
addSettlement: function(settlement){
  var no = this.getPlayerNo(settlement.player_id),
      cell = 'cell-' + settlement.x + '-' + settlement.y;
  dojo.place( this.format_block( 'jstpl_settlement', { no: no}),  cell);
  dojo.addClass(cell, 'cell-player-'+no);
},

addTile: function(tile){
  dojo.place( this.format_block( 'jstpl_tile', tile), "player-tiles-" + tile.player_id);
},



getPlayerNo: function(playerId){
  return this.gamedatas.fplayers.reduce(function(carry, player){ return player.id == playerId? player.no : carry}, 0);
},


slideTemporary: function (template, data, container, sourceId, targetId, duration, delay) {
  var _this = this;
  return new Promise(function (resolve, reject) {
    var animation = _this.slideTemporaryObject(_this.format_block(template, data), container, sourceId, targetId, duration, delay);
    setTimeout(function(){
      resolve();
    }, duration + delay)
  });
},


getLocation: function(tile){
  var location = this.gamedatas.locations[tile.location];
  location.location = tile.location;
  location.description = location.desc.join("");
  return location;
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
    ['cancel', 200],
    ['obtainTile', 1000],
    ['useTile', 1000],
    ['showTerrain', 1],
  ];

  var _this = this;
  notifs.forEach(function (notif) {
    dojo.subscribe(notif[0], _this, "notif_" + notif[0]);
    _this.notifqueue.setSynchronous(notif[0], notif[1]);
  });
}

   });
});
