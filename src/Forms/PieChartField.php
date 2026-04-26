<?php

declare(strict_types=1);

namespace Clesson\Silverstripe\Graphing\Forms;

/**
 * Renders a Chart.js pie chart in the CMS.
 *
 * Usage:
 *   $chart = PieChartField::create('CategoryShare', 'Revenue by Category')
 *       ->setData([
 *           'labels' => ['Products', 'Services', 'Consulting'],
 *           'datasets' => [
 *               [
 *                   'data' => [45000, 28000, 17000],
 *                   'backgroundColor' => [
 *                       'rgba(255, 99, 132, 0.7)',
 *                       'rgba(54, 162, 235, 0.7)',
 *                       'rgba(255, 206, 86, 0.7)',
 *                   ],
 *               ],
 *           ],
 *       ])
 *       ->setCutout('0%');
 *
 * @package Clesson\Silverstripe\Graphing
 * @subpackage Forms
 */
class PieChartField extends ChartField
{
    /**
     * Cutout percentage (e.g. '0%' = full pie, '50%' = doughnut-style).
     */
    private ?string $cutout = null;

    /**
     * Border width between segments in pixels.
     */
    private ?int $borderWidth = null;

    /**
     * Offset distance for hovered segment in pixels.
     */
    private ?int $hoverOffset = null;

    /**
     * Returns the Chart.js chart type identifier.
     */
    public function getChartType(): string
    {
        return 'pie';
    }

    /**
     * Set the cutout percentage.
     *
     * Use '0%' for a full pie or e.g. '50%' for a doughnut-style appearance.
     */
    public function setCutout(?string $cutout): static
    {
        $this->cutout = $cutout;

        return $this;
    }

    /**
     * Set the border width between pie segments.
     */
    public function setBorderWidth(?int $borderWidth): static
    {
        $this->borderWidth = $borderWidth;

        return $this;
    }

    /**
     * Set the hover offset distance for the hovered segment.
     */
    public function setHoverOffset(?int $hoverOffset): static
    {
        $this->hoverOffset = $hoverOffset;

        return $this;
    }

    /**
     * Builds the Chart.js config with pie-specific dataset defaults.
     */
    public function getChartConfig(): array
    {
        $config = parent::getChartConfig();

        $this->applyCutout($config);

        foreach ($config['data']['datasets'] as &$dataset) {
            $this->applyDatasetDefaults($dataset);
        }

        return $config;
    }

    /**
     * Applies the cutout option to the config.
     */
    private function applyCutout(array &$config): void
    {
        if ($this->cutout !== null) {
            $config['options']['cutout'] = $this->cutout;
        }
    }

    /**
     * Applies pie-specific default values to a dataset.
     */
    private function applyDatasetDefaults(array &$dataset): void
    {
        if ($this->borderWidth !== null && !isset($dataset['borderWidth'])) {
            $dataset['borderWidth'] = $this->borderWidth;
        }

        if ($this->hoverOffset !== null && !isset($dataset['hoverOffset'])) {
            $dataset['hoverOffset'] = $this->hoverOffset;
        }
    }
}

