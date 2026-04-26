/**
 * Entwine integration for ChartField.
 *
 * Initializes Chart.js instances when chart canvas elements appear in the DOM.
 * Supports multi-series switching via buttons above the chart.
 * Works with AJAX-loaded content (GridField navigation, tab switching).
 */
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

(function($) {
    'use strict';

    $.entwine('ss', function($) {

        /**
         * Initialize chart canvases when they appear in the DOM.
         */
        $('.chart-field-canvas').entwine({

            onmatch: function() {
                this._initChart();
                this._super();
            },

            onunmatch: function() {
                this._destroyChart();
                this._super();
            },

            /**
             * Reads the chart config from the data attribute and creates a Chart.js instance.
             */
            _initChart: function() {
                var canvas = this[0];
                var configStr = canvas.dataset.chartConfig;

                if (!configStr) {
                    return;
                }

                try {
                    var config = JSON.parse(configStr);
                    var chart = new Chart(canvas, config);
                    canvas._chartInstance = chart;
                } catch (e) {
                    console.error('[ChartField] Failed to initialize chart:', e);
                }
            },

            /**
             * Destroys the Chart.js instance to prevent memory leaks.
             */
            _destroyChart: function() {
                var canvas = this[0];
                if (canvas._chartInstance) {
                    canvas._chartInstance.destroy();
                    canvas._chartInstance = null;
                }
            },

            /**
             * Replaces the chart data and options with a new series config.
             */
            _switchSeries: function(seriesConfig) {
                var canvas = this[0];
                var chart = canvas._chartInstance;

                if (!chart) {
                    return;
                }

                chart.data = seriesConfig.data;

                if (seriesConfig.options) {
                    chart.options = seriesConfig.options;
                }

                chart.update('none');
            }
        });

        /**
         * Series switcher button click handler.
         */
        $('.chart-field__series-btn').entwine({

            onclick: function(e) {
                e.preventDefault();

                var $btn = $(this);
                var key = $btn.data('series-key');
                var $switcher = $btn.closest('.chart-field__series-switcher');
                var seriesDataStr = $switcher.attr('data-series-data');

                if (!seriesDataStr) {
                    return;
                }

                try {
                    var allSeries = JSON.parse(seriesDataStr);
                    var seriesConfig = allSeries[key];

                    if (!seriesConfig) {
                        return;
                    }

                    // Update active button state
                    $switcher.find('.chart-field__series-btn').removeClass('chart-field__series-btn--active');
                    $btn.addClass('chart-field__series-btn--active');

                    // Find the canvas in the same chart-field container and switch series
                    var $canvas = $btn.closest('.chart-field').find('.chart-field-canvas');
                    $canvas._switchSeries(seriesConfig);
                } catch (err) {
                    console.error('[ChartField] Failed to switch series:', err);
                }
            }
        });

    });

})(jQuery);

