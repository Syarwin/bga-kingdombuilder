{OVERALL_GAME_HEADER}

<div id="board">
  <ul class="hex-grid-container">
    <!-- BEGIN cell -->
      <li class="hex-grid-item">
        <div class="hex-grid-content" id="cell-{I}-{J}"></div>
        <div class="hex-grid-content-background" id="cell-background-{I}-{J}"></div>
      </li>
    <!-- END cell -->
  </ul>
</div>

<div id="objectives">
  <!-- BEGIN objective -->
  <div class="objective objective-{ID}">
    <div class="objective-background"></div>
    <div class="objective-mask"></div>
    <div class="objective-name">{NAME}</div>
    <div class="objective-desc"><p>{DESC}</p><p>{TEXT}</p></div>
  </div>
  <!-- END objective -->
</div>

<script type="text/javascript">
var jstpl_player_panel = `<div class="player-panel player-\${no}">
  <div class="player-terrain terrain-\${terrain}" id="player-terrain-\${id}"></div>
  <div class='player-settlements' id='player-settlements-\${id}'><div class='player-settlements-counter'>\${settlements}</div></div>
  <div class='player-tiles' id='player-tiles-\${id}'></div>
</div>`;

var jstpl_settlement = `<div class="hex-settlement player-\${no}"></div>`;

var jstpl_tile = `<div id="tile-\${id}" class="tile location-\${location}"></div>`;


var jstpl_tilePrompt= `<div class='tile-prompt'><div class="tile location-\${location}"></div><p class="tile-desc"><span class='tile-name'>\${name}</span><br />\${description}</p><div class="tile-picto location-\${location}"></div></div>`;
</script>
{OVERALL_GAME_FOOTER}
