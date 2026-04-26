<?php

declare(strict_types=1);

namespace Clesson\Silverstripe\Graphing\Forms;

/**
 * Renders a Chart.js bar chart in the CMS.
 *
 * Usage:
 *   $chart = BarChartField::create('ExpenseChart', 'Monthly Expenses')
 *       ->setData([
 *           'labels' => ['Jan', 'Feb', 'Mar', 'Apr'],
 *           'datasets' => [
 *               ['label' => 'Expenses', 'data' => [800, 1200, 950, 1400]],
 *           ]
 *       ])
 *       ->setStacked(true)
 *       ->setBorderRadius(4);
 *
 * @package Clesson\Silverstripe\Graphing
 * @subpackage Forms
 */
class BarChartField extends ChartField
{
    /**
     * Whether to stack bars on top of each other.
     */
    private bool $stacked = false;

    /**
     * Whether bars are rendered horizontally (indexAxis 'y').
     */
    private bool $horizontal = false;

    /**
     * Border radius in pixels for rounded bar corners.
     */
    private ?int $borderRadius = null;

    /**
     * Bar thickness in pixels (null = auto).
     */
    private ?int $barThickness = null;

    /**
     * Returns the Chart.js chart type identifier.
     */
    public function getChartType(): string
    {
        return 'bar';
    }

    /**
     * Enable/disable stacked bars.
     */
    public function setStacked(bool $stacked): static
    {
        $this->stacked = $stacked;

        return $this;
    }

    /**
     * Enable/disable horizontal bar rendering.
     */
    public function setHorizontal(bool $horizontal): static
    {
        $this->horizontal = $horizontal;

        return $this;
    }

    /**
     * Set the border radius for rounded bar corners.
     */
    public function setBorderRadius(?int $borderRadius): static
    {
        $this->borderRadius = $borderRadius;

        return $this;
    }

    /**
     * Set a fixed bar thickness in pixels.
     */
    public function setBarThickness(?int $barThickness): static
    {
        $this->barThickness = $barThickness;

        return $this;
    }

    /**
     * Builds the Chart.js config with bar-specific options and dataset defaults.
     */
    public function getChartConfig(): array
    {
        $config = parent::getChartConfig();

        $this->applyBarOptions($config);

        foreach ($config['data']['datasets'] as &$dataset) {
            $this->applyDatasetDefaults($dataset);
        }

        return $config;
    }

    /**
     * Applies bar-specific options (stacking, orientation) to the config.
     */
    private function applyBarOptions(array &$config): void
    {
        if ($this->horizontal) {
            $config['options']['indexAxis'] = 'y';
        }

        if ($this->stacked) {
            $config['options']['scales']['x']['stacked'] = true;
            $config['options']['scales']['y']['stacked'] = true;
        }
    }

    /**
     * Applies bar-specific default values to a dataset.
     */
    private function applyDatasetDefaults(array &$dataset): void
    {
        if ($this->borderRadius !== null && !isset($dataset['borderRadius'])) {
            $dataset['borderRadius'] = $this->borderRadius;
        }

        if ($this->barThickness !== null && !isset($dataset['barThickness'])) {
            $dataset['barThickness'] = $this->barThickness;
        }
    }
}

