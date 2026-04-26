<?php

declare(strict_types=1);

namespace Clesson\Silverstripe\Graphing\Forms;

use SilverStripe\Forms\FormField;
use SilverStripe\View\Requirements;

/**
 * Abstract base class for Chart.js based chart form fields.
 *
 * These fields are display-only and do not save data to the database.
 * Chart.js configuration is passed via a data attribute on the canvas element.
 *
 * @package Clesson\Silverstripe\Graphing
 * @subpackage Forms
 */
abstract class ChartField extends FormField
{
    /**
     * Creates a new ChartField.
     *
     * The title is used as the Chart.js title above the chart,
     * not as a form field label.
     *
     * @param string $name Field name
     * @param string|null $title Chart title (displayed inside the chart, not as form label)
     * @param mixed $value Unused
     */
    public function __construct(string $name, ?string $title = null, mixed $value = null)
    {
        parent::__construct($name, '', $value);
        $this->chartTitle = $title;
    }

    /**
     * Chart data in Chart.js format: ['labels' => [...], 'datasets' => [...]]
     *
     * For multi-series support, datasets can use string keys:
     * ['labels' => [...], 'datasets' => ['7 Days' => [...], '30 Days' => [...]]]
     */
    private array $chartData = ['labels' => [], 'datasets' => []];

    /**
     * When multi-series is used, stores all series keyed by label.
     * Each entry: ['labels' => [...], 'datasets' => [...]]
     *
     * @var array<string, array>|null
     */
    private ?array $seriesData = null;

    /**
     * The key of the initially active series (null = first).
     */
    private ?string $activeSeries = null;

    /**
     * Additional Chart.js options.
     */
    private array $chartOptions = [];

    /**
     * CSS width of the chart container.
     */
    private string $chartWidth = '100%';

    /**
     * CSS height of the chart container.
     */
    private string $chartHeight = '300px';

    /**
     * Optional chart title displayed via Chart.js title plugin.
     */
    private ?string $chartTitle = null;

    /**
     * Returns the Chart.js chart type identifier (e.g. 'line', 'bar', 'pie').
     */
    abstract public function getChartType(): string;

    /**
     * Sets the chart data.
     *
     * Simple format (single series):
     *   ['labels' => ['Jan', 'Feb'], 'datasets' => [['label' => 'Sales', 'data' => [100, 200]]]]
     *
     * Multi-series format (switchable):
     *   ['7 Days' => ['labels' => [...], 'datasets' => [...]], '30 Days' => ['labels' => [...], 'datasets' => [...]]]
     */
    public function setData(array $data): static
    {
        if ($this->isMultiSeries($data)) {
            $this->seriesData = $data;
            $activeKey = $this->activeSeries ?? array_key_first($data);
            $this->chartData = $data[$activeKey] ?? reset($data);
        } else {
            $this->chartData = $data;
            $this->seriesData = null;
        }

        return $this;
    }

    /**
     * Sets the initially active series key for multi-series charts.
     */
    public function setActiveSeries(?string $key): static
    {
        $this->activeSeries = $key;

        return $this;
    }

    /**
     * Sets additional Chart.js options.
     */
    public function setOptions(array $options): static
    {
        $this->chartOptions = $options;

        return $this;
    }

    /**
     * Sets the CSS width of the chart container.
     */
    public function setChartWidth(string $width): static
    {
        $this->chartWidth = $width;

        return $this;
    }

    /**
     * Sets the CSS height of the chart container.
     */
    public function setChartHeight(string $height): static
    {
        $this->chartHeight = $height;

        return $this;
    }

    /**
     * Sets the optional chart title.
     */
    public function setChartTitle(?string $title): static
    {
        $this->chartTitle = $title;

        return $this;
    }

    /**
     * Returns the CSS width of the chart container.
     */
    public function getChartWidth(): string
    {
        return $this->chartWidth;
    }

    /**
     * Returns the CSS height of the chart container.
     */
    public function getChartHeight(): string
    {
        return $this->chartHeight;
    }

    /**
     * Returns the optional chart title.
     */
    public function getChartTitle(): ?string
    {
        return $this->chartTitle;
    }

    /**
     * Builds the complete Chart.js configuration object.
     */
    public function getChartConfig(): array
    {
        $options = array_merge_recursive(
            $this->getDefaultOptions(),
            $this->chartOptions,
        );

        return [
            'type' => $this->getChartType(),
            'data' => $this->chartData,
            'options' => $options,
        ];
    }

    /**
     * Returns the JSON-encoded chart configuration for the data attribute.
     */
    public function getChartConfigJson(): string
    {
        return json_encode($this->getChartConfig(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Renders the chart without the default label/holder wrapper.
     *
     * This ensures the chart takes the full width of the form,
     * similar to how LiteralField renders its content.
     *
     * @param array $properties
     * @return string
     */
    public function FieldHolder($properties = []): string
    {
        return $this->Field($properties);
    }

    /**
     * Renders the chart field. Loads Chart.js requirements only when actually rendered.
     *
     * @param array $properties
     * @return string
     */
    public function Field($properties = []): string
    {
        $this->addChartAssets();
        $properties = array_merge($properties, $this->getTemplateData());

        return parent::Field($properties)->getValue();
    }

    /**
     * No-op: chart fields do not save data.
     *
     * @param mixed $record
     * @return void
     */
    public function saveInto($record): void
    {
        // Intentionally empty — display-only field
    }

    /**
     * Chart fields are always readonly by nature.
     */
    public function performReadonlyTransformation(): FormField
    {
        return clone $this;
    }

    /**
     * Chart fields are always disabled-safe.
     */
    public function performDisabledTransformation(): FormField
    {
        return clone $this;
    }

    /**
     * Returns the default Chart.js options.
     */
    protected function getDefaultOptions(): array
    {
        $options = [
            'responsive' => true,
            'maintainAspectRatio' => false,
        ];

        if ($this->chartTitle !== null) {
            $options['plugins']['title'] = [
                'display' => true,
                'text' => $this->chartTitle,
            ];
        }

        return $options;
    }

    /**
     * Loads the required JavaScript and CSS resources.
     */
    protected function addChartAssets(): void
    {
        Requirements::javascript('clesson-de/silverstripe-graphing: client/admin/dist/bundle.js');
        Requirements::css('clesson-de/silverstripe-graphing: client/admin/dist/bundle.css');
    }

    /**
     * Returns template variables for rendering the chart.
     */
    protected function getTemplateData(): array
    {
        $data = [
            'ChartConfig' => json_encode($this->getChartConfig(), JSON_UNESCAPED_UNICODE),
            'ChartWidth' => $this->chartWidth,
            'ChartHeight' => $this->chartHeight,
            'ChartTitle' => $this->chartTitle,
            'ChartType' => $this->getChartType(),
            'HasSeries' => $this->hasMultipleSeries(),
        ];

        if ($this->hasMultipleSeries()) {
            $data['SeriesLabels'] = $this->getSeriesLabels();
            $data['SeriesData'] = $this->getSeriesDataJson();
            $data['ActiveSeries'] = $this->activeSeries ?? array_key_first($this->seriesData);
        }

        return $data;
    }

    /**
     * Checks whether the given data uses the multi-series format (string-keyed top-level).
     */
    private function isMultiSeries(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $firstKey = array_key_first($data);

        return is_string($firstKey) && !in_array($firstKey, ['labels', 'datasets'], true);
    }

    /**
     * Returns whether this chart has multiple switchable series.
     */
    public function hasMultipleSeries(): bool
    {
        return $this->seriesData !== null && count($this->seriesData) > 1;
    }

    /**
     * Returns the series labels as an array of strings.
     *
     * @return string[]
     */
    public function getSeriesLabels(): array
    {
        if ($this->seriesData === null) {
            return [];
        }

        return array_keys($this->seriesData);
    }

    /**
     * Returns all series data as JSON for the JS series switcher.
     *
     * Each series is wrapped in a full Chart.js config so the JS can
     * swap type + data + options in one go.
     */
    public function getSeriesDataJson(): string
    {
        if ($this->seriesData === null) {
            return '{}';
        }

        $series = [];

        foreach ($this->seriesData as $key => $seriesChartData) {
            $originalData = $this->chartData;
            $this->chartData = $seriesChartData;
            $series[$key] = $this->getChartConfig();
            $this->chartData = $originalData;
        }

        return json_encode($series, JSON_UNESCAPED_UNICODE);
    }
}

