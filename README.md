# silverstripe-graphing

Chart form fields for Silverstripe CMS 6, rendered with [Chart.js](https://www.chartjs.org/). Display-only — no data storage.

## Features

- **Line charts** — display line charts with configurable fill, tension and point styles
- **Bar charts** — display bar charts with stacking, horizontal mode and rounded corners
- **Multi-series with time period switcher** — provide multiple data series (e.g. "7 days", "30 days", "1 year") and let the user switch between them via buttons above the chart
- **Stacked bars** — show individual items as coloured blocks stacked on top of each other
- **Display-only** — chart fields do not save data to the database
- **Chart.js powered** — uses Chart.js 4 for beautiful, responsive, animated charts
- **AJAX-safe** — works with GridField detail forms, tab switching and dynamically loaded content
- **Extensible** — create custom chart types by extending the abstract `ChartField` base class
- **Lazy-loaded assets** — Chart.js is only loaded when a chart field is actually rendered

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | `^8.1` |
| silverstripe/framework | `^6` |
| silverstripe/admin | `^3` |

---

## Installation

```bash
composer require clesson-de/silverstripe-graphing
```

Expose the frontend assets:

```bash
composer vendor-expose
```

Then run a database build:

```
/dev/build?flush=all
```

---

## Usage

### LineChartField

Add a line chart to any `getCMSFields()` method:

```php
use Clesson\Silverstripe\Graphing\Forms\LineChartField;

$chart = LineChartField::create('RevenueChart', 'Monthly Revenue')
    ->setData([
        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        'datasets' => [
            [
                'label' => 'Revenue 2025',
                'data' => [12000, 19000, 15000, 22000, 18000, 25000],
                'borderColor' => 'rgb(75, 192, 192)',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
            ],
        ],
    ])
    ->setTension(0.4)
    ->setFill(true)
    ->setChartHeight('400px')
    ->setChartTitle('Revenue Comparison');

$fields->addFieldToTab('Root.Charts', $chart);
```

### BarChartField

Add a bar chart:

```php
use Clesson\Silverstripe\Graphing\Forms\BarChartField;

$chart = BarChartField::create('ExpenseChart', 'Expenses by Category')
    ->setData([
        'labels' => ['Office', 'Travel', 'Software', 'Hardware'],
        'datasets' => [
            [
                'label' => 'Q1',
                'data' => [2400, 1800, 3200, 900],
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
            ],
            [
                'label' => 'Q2',
                'data' => [1900, 2200, 2800, 1500],
                'backgroundColor' => 'rgba(255, 159, 64, 0.7)',
            ],
        ],
    ])
    ->setBorderRadius(4)
    ->setChartHeight('400px');
```

### Stacked bars

Enable stacking to show individual items as coloured blocks on top of each other.
Each dataset becomes one block within the bar for its respective label:

```php
use Clesson\Silverstripe\Graphing\Forms\BarChartField;

$chart = BarChartField::create('StackedExpenses', 'Stacked Expenses')
    ->setData([
        'labels' => ['25 Mar', '26 Mar', '27 Mar'],
        'datasets' => [
            [
                'label' => 'EXP-2025-001',
                'data' => [120.50, 0, 0],
                'backgroundColor' => 'rgba(255, 99, 132, 0.7)',
            ],
            [
                'label' => 'EXP-2025-002',
                'data' => [89.00, 0, 0],
                'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
            ],
            [
                'label' => 'EXP-2025-003',
                'data' => [0, 250.00, 0],
                'backgroundColor' => 'rgba(255, 206, 86, 0.7)',
            ],
        ],
    ])
    ->setStacked(true)
    ->setBorderRadius(4)
    ->setChartHeight('350px');
```

This renders:
- **25 Mar**: Two blocks stacked (EXP-001 + EXP-002)
- **26 Mar**: One block (EXP-003)
- **27 Mar**: Empty

Hover over a block to see its label and amount in the tooltip.

### Multi-series (time period switcher)

Provide multiple data series as string-keyed top-level entries.
Buttons are rendered above the chart so the user can switch between series.

```php
use Clesson\Silverstripe\Graphing\Forms\BarChartField;

$chart = BarChartField::create('RevenueChart', 'Revenue')
    ->setData([
        '7 days' => [
            'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
            'datasets' => [
                ['label' => 'INV-001', 'data' => [500, 0, 0, 0, 0], 'backgroundColor' => 'rgba(75,192,192,0.7)'],
                ['label' => 'INV-002', 'data' => [0, 300, 0, 0, 0], 'backgroundColor' => 'rgba(54,162,235,0.7)'],
            ],
        ],
        '30 days' => [
            'labels' => ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            'datasets' => [
                ['label' => 'Revenue', 'data' => [4200, 3800, 5100, 4600], 'backgroundColor' => 'rgba(75,192,192,0.7)'],
            ],
        ],
        '1 year' => [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                ['label' => 'Revenue', 'data' => [18000, 22000, 19500, 25000, 21000, 28000], 'backgroundColor' => 'rgba(75,192,192,0.7)'],
            ],
        ],
    ])
    ->setStacked(true)
    ->setBorderRadius(4)
    ->setChartHeight('350px');
```

The module automatically detects multi-series data (string keys that are not `labels` or `datasets`) and renders a row of toggle buttons.
Each series can have its own labels and datasets — the chart is completely replaced when switching.

You can set the initially active series:

```php
$chart->setActiveSeries('30 days');
```

Multi-series works with all chart types (LineChartField, BarChartField, PieChartField, etc.).

### PieChartField

Add a pie chart:

```php
use Clesson\Silverstripe\Graphing\Forms\PieChartField;

$chart = PieChartField::create('CategoryShare', 'Revenue by Category')
    ->setData([
        'labels' => ['Products', 'Services', 'Consulting'],
        'datasets' => [
            [
                'data' => [45000, 28000, 17000],
                'backgroundColor' => [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                ],
            ],
        ],
    ])
    ->setBorderWidth(2)
    ->setHoverOffset(8)
    ->setChartHeight('300px');
```

Use `setCutout('50%')` to create a doughnut-style appearance with a hole in the centre.

---

## Configuration options

### Base options (all chart types)

| Method | Description | Default |
|---|---|---|
| `setData(array $data)` | Set chart data — single series or multi-series | `[]` |
| `setOptions(array $options)` | Set additional Chart.js options | `[]` |
| `setChartWidth(string $width)` | CSS width of the container | `'100%'` |
| `setChartHeight(string $height)` | CSS height of the container | `'300px'` |
| `setChartTitle(?string $title)` | Optional chart title | `null` |
| `setActiveSeries(?string $key)` | Initially active series key (multi-series only) | `null` (first) |

### LineChartField options

| Method | Description | Default |
|---|---|---|
| `setFill(bool $fill)` | Fill area below the line | `false` |
| `setTension(float $tension)` | Line curve tension (0 = straight, 0.4 = smooth) | `0.0` |
| `setPointStyle(?string $style)` | Point style (e.g. `'circle'`, `'triangle'`, `'rect'`) | `null` |

### BarChartField options

| Method | Description | Default |
|---|---|---|
| `setStacked(bool $stacked)` | Stack bars on top of each other | `false` |
| `setHorizontal(bool $horizontal)` | Render bars horizontally | `false` |
| `setBorderRadius(?int $radius)` | Rounded bar corners in pixels | `null` |
| `setBarThickness(?int $thickness)` | Fixed bar thickness in pixels | `null` (auto) |

### PieChartField options

| Method | Description | Default |
|---|---|---|
| `setCutout(?string $cutout)` | Cutout percentage (`'0%'` = full pie, `'50%'` = doughnut-style) | `null` |
| `setBorderWidth(?int $width)` | Border width between segments in pixels | `null` |
| `setHoverOffset(?int $offset)` | Offset distance for the hovered segment in pixels | `null` |

---

## Available chart types

| Class | Chart.js type | Description |
|---|---|---|
| `LineChartField` | `line` | Line chart with optional area fill |
| `BarChartField` | `bar` | Bar chart with stacking, horizontal mode and rounded corners |
| `PieChartField` | `pie` | Pie chart with optional cutout (doughnut-style) |

More chart types (radar, doughnut, polar area) can be added by extending the `ChartField` base class.

---

## Frontend assets

The module ships with compiled admin CSS and JS in `client/admin/dist/`.
If you want to modify the styles or scripts, you need to recompile them.

### Prerequisites

The required Node.js version is defined in `.nvmrc`. Switch to it with:

```bash
nvm use
```

### Install dependencies

```bash
npm install
```

### Build

```bash
npm run build
```

This compiles:
- `client/admin/src/js/chart-field.js` → `client/admin/dist/bundle.js` (includes Chart.js)
- `client/admin/src/scss/chart-field.scss` → `client/admin/dist/bundle.css`

### Watch mode (during development)

```bash
npm run watch
```

### Output files

The compiled files in `client/admin/dist/` are exposed via `composer vendor-expose`
and referenced in PHP via `Requirements::css()` / `Requirements::javascript()`.
Their filenames are **static** — they must not be renamed.

---

## Developer documentation

### Creating custom chart types

To add a new chart type, extend the abstract `ChartField` class and implement `getChartType()`:

```php
<?php

declare(strict_types=1);

namespace App\Forms;

use Clesson\Silverstripe\Graphing\Forms\ChartField;

class DoughnutChartField extends ChartField
{
    public function getChartType(): string
    {
        return 'doughnut';
    }
}
```

Override `getChartConfig()` to add type-specific dataset defaults:

```php
public function getChartConfig(): array
{
    $config = parent::getChartConfig();

    foreach ($config['data']['datasets'] as &$dataset) {
        if (!isset($dataset['borderWidth'])) {
            $dataset['borderWidth'] = 2;
        }
    }

    return $config;
}
```

### Architecture

- **`ChartField`** — abstract base class handling Chart.js configuration, multi-series detection, asset loading and template rendering
- **`LineChartField`** — concrete implementation for line charts with fill, tension and point style
- **`BarChartField`** — concrete implementation for bar charts with stacking, horizontal mode and border radius
- **Entwine integration** — jQuery Entwine ensures charts are initialized on AJAX-loaded content and destroyed on DOM removal
- **Series switcher** — when multi-series data is detected, toggle buttons are rendered above the chart; clicking a button swaps the chart data client-side without a page reload
- **Chart.js** — bundled via Rollup from `node_modules` into a single IIFE; jQuery is marked as external (provided by Silverstripe Admin)

---

## License

BSD-3-Clause — see [LICENSE](LICENSE).
