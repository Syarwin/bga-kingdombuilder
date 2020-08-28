{OVERALL_GAME_HEADER}

<div id="board">
  <div id="grid-container">
  <ul class="hex-grid-container">
    <li class="quadrant quadrant-{QUAD0}" id="quadrant-top-left"></li>
    <li class="quadrant quadrant-{QUAD1}" id="quadrant-top-right"></li>
    <li class="quadrant quadrant-{QUAD2}" id="quadrant-bottom-left"></li>
    <li class="quadrant quadrant-{QUAD3}" id="quadrant-bottom-right"></li>
    <!-- BEGIN cell -->
      <li class="hex-grid-item" id="cell-container-{I}-{J}">
        <div class="hex-grid-content" id="cell-{I}-{J}"></div>
      </li>
    <!-- END cell -->
  </ul>
  </div>
</div>

<div id="objectives"></div>
<script src="https://rawgit.com/FremyCompany/css-grid-polyfill/master/bin/css-polyfills.min.js"></script>
<script type="text/javascript">
var jstpl_objective = `
<div class="objective objective-\${id}">
  <div class="objective-background"></div>
  <div class="objective-mask" id="objective-mask-\${id}"></div>
  <div class="objective-name">\${name}</div>
  <div class="objective-desc"><p>\${desc}</p><p>\${text}</p></div>
  <div class="objective-picto"></div>
</div>`;


var jstpl_player_panel = `<div class="player-panel player-\${cno}">
  <div class="player-terrain terrain-\${terrain}" id="player-terrain-\${id}"></div>
  <div class='player-settlements' id='player-settlements-\${id}'><div class='player-settlements-counter'>\${settlements}</div></div>
  <div class='player-tiles' id='player-tiles-\${id}'></div>
</div>`;

var jstpl_tile_container = `<div class='tile-container' id ='tile-container-\${x}-\${y}'><div class="tile-counter"></div></div>`;
//var jstpl_location_counter = `<div class="hex-counter" id="hex-counter-\${x}-\${y}">\${n}</div>`;

var jstpl_settlement = `<div class="hex-settlement player-\${no}"></div>`;

var jstpl_tile = `<div id="tile-\${id}" class="tile location-\${location}"></div>`;

var jstpl_tilePrompt= `<div class='tile-prompt'><div class="tile location-\${location}"></div><p class="tile-desc"><span class='tile-name'>\${name}</span><br />\${description}</p><div class="tile-picto location-\${location}"></div></div>`;
</script>
{OVERALL_GAME_FOOTER}
