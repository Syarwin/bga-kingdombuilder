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

<div id="kb-cards">
  <!-- BEGIN kbcard -->
  <div class="kb-card kb-card-{ID}">
    <div class="kb-card-background"></div>
    <div class="kb-card-mask"></div>
    <div class="kb-card-name">{NAME}</div>
    <div class="kb-card-desc"><p>{SHORT}</p><p>{POINTS}</p></div>
  </div>
  <!-- END kbcard -->
</div>

<script type="text/javascript">
var jstpl_player_panel = `<div class="player-panel player-\${no}">
  <div class='tokens-container' id='tokens-container-\${id}'>
    <div class='token token-settlements'>\${settlements}</div>
  </div>
</div>`;

var jstpl_settlement = `<div class="hex-settlement player-\${no}"></div>`;

</script>
{OVERALL_GAME_FOOTER}
