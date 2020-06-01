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

{OVERALL_GAME_FOOTER}
