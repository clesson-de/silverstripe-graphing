<div class="chart-field chart-field--{$ChartType}"
     style="width: {$ChartWidth}; min-height: {$ChartHeight};">
    <% if $ChartTitle %>
    <div class="chart-field__title">{$ChartTitle}</div>
    <% end_if %>
    <% if $HasSeries %>
    <div class="chart-field__series-switcher"
         data-series-data="{$SeriesData.ATT}">
        <% loop $SeriesLabels %>
        <button type="button"
                class="chart-field__series-btn<% if $Up.ActiveSeries == $Me %> chart-field__series-btn--active<% end_if %>"
                data-series-key="{$Me}">{$Me}</button>
        <% end_loop %>
    </div>
    <% end_if %>
    <div class="chart-field__canvas-wrapper" style="height: {$ChartHeight};">
        <canvas class="chart-field-canvas"
                id="{$ID}_canvas"
                data-chart-config="{$ChartConfig.ATT}">
        </canvas>
    </div>
</div>

