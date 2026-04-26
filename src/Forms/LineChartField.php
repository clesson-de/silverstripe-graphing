<?php

declare(strict_types=1);

namespace Clesson\Silverstripe\Graphing\Forms;

/**
 * Renders a Chart.js line chart in the CMS.
 *
 * Usage:
 *   $chart = LineChartField::create('SalesChart', 'Monthly Sales')
 *       ->setData([
 *           'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
 *           'datasets' => [
 *               ['label' => 'Revenue', 'data' => [1200, 1900, 3000, 2500]],
 *           ]
 *       ])
 *       ->setFill(true)
 *       ->setTension(0.3);
 *
 * @package Clesson\Silverstripe\Graphing
 * @subpackage Forms
 */
class LineChartField extends ChartField
{
    /**
     * Whether to fill the area below the line.
     */
    private bool $fill = false;

    /**
     * Line tension (0 = straight lines, 0.4 = smooth curves).
     */
    private float $tension = 0.0;

    /**
     * Point style (e.g. 'circle', 'triangle', 'rect', 'star').
     */
    private ?string $pointStyle = null;

    /**
     * Returns the Chart.js chart type identifier.
     */
    public function getChartType(): string
    {
        return 'line';
    }

    /**
     * Enable/disable area fill below the line.
     */
    public function setFill(bool $fill): static
    {
        $this->fill = $fill;

        return $this;
    }

    /**
     * Set the line tension (0 = straight lines, 0.4 = smooth curves).
     */
    public function setTension(float $tension): static
    {
        $this->tension = $tension;

        return $this;
    }

    /**
     * Set the point style (e.g. 'circle', 'triangle', 'rect', 'star').
     */
    public function setPointStyle(?string $pointStyle): static
    {
        $this->pointStyle = $pointStyle;

        return $this;
    }

    /**
     * Builds the Chart.js config with line-specific dataset defaults.
     */
    public function getChartConfig(): array
    {
        $config = parent::getChartConfig();

        foreach ($config['data']['datasets'] as &$dataset) {
            $this->applyDatasetDefaults($dataset);
        }

        return $config;
    }

    /**
     * Applies line-specific default values to a dataset.
     */
    private function applyDatasetDefaults(array &$dataset): void
    {
        if (!isset($dataset['fill'])) {
            $dataset['fill'] = $this->fill;
        }

        if (!isset($dataset['tension'])) {
            $dataset['tension'] = $this->tension;
        }

        if ($this->pointStyle !== null && !isset($dataset['pointStyle'])) {
            $dataset['pointStyle'] = $this->pointStyle;
        }
    }
}

