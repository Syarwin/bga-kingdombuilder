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
  });


   // Setup game notifications
   this.setupNotifications();
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

  // Stop here if it's not the current player's turn for some states
  if (["playerSettle"].includes(stateName)) {
    //this.focusContainer('board');
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
onUpdateActionButtons: function (stateName, args) {
  debug('Update action buttons: ' + stateName, args); // Make sure it the player's turn

  if (!this.isCurrentPlayerActive())
    return;
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
  debug('Notif: building a settlement', n.args);
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
    ['build', 1000]
  ];
/*
    ['cancel', 1000],
    ['automatic', 1000],
    ['addOffer', 500],
    ['removeOffer', 500],
    ['powerAdded', 1200],
    ['workerPlaced', 1000],
    ['workerMoved', 1600],
  ];
*/

  var _this = this;
  notifs.forEach(function (notif) {
    dojo.subscribe(notif[0], _this, "notif_" + notif[0]);
    _this.notifqueue.setSynchronous(notif[0], notif[1]);
  });
}

   });
});
