<?php

$loader = require_once 'vendor/autoload.php';

use Colorz\Repository;

$colordb = new Repository(__DIR__);

$templateName = 'color.twig';

$view = isset($_GET['v']) ? $_GET['v'] : 'cotd';
$format = isset($_GET['format']) ? $_GET['format'] : 'grid';
$graphs = isset($_GET['g']) ? $_GET['g'] == '1' : false;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'hsl';

$context = [
    'params' => ['v' => $view, 'g' => $graphs, 'sort' => $sort],
    'show_menu' => false,
    'show_rgb_graphs' => $graphs,
];

switch ($view) {
case 'all':

    // Ensure in valid list of sort options
    if (!in_array($sort, ['hsl', 'hls', 'lhs', 'rgbc'])) {
        $sort = 'hsl';
    }

    $colorObjects = $colordb->getAll($sort);

    $templateName = $format . '.twig';
    $context['sort'] = $sort;
    $context['colors'] = $colorObjects;
    $context['show_menu'] = true;

    if ($format == "csv") {
        header('Content-Type: text/plain');
    }
    break;
case 'basic':
    $colors = [
        '#000',
        '#00F',
        '#0F0',
        '#0FF',
        '#F00',
        '#F0F',
        '#FF0',
        '#FFF',
    ];

    $colorObjects = [];
    foreach ($colors as $color) {
        $colorObjects[] = $colordb->findColor($color);
    }

    $templateName = 'grid.twig';
    $context['show_menu'] = true;
    $context['colortitle'] = 'Basic colors';
    $context['colors'] = $colorObjects;
    break;
case 'group':
    $colors = [
        '#a9a9a9',
        '#00F',
        '#0F0',
        '#0FF',
        '#F00',
        '#F0F',
        '#FF0',
        '#FFF',
        '#ffffff',
        '#ffffff',
        '#ffffff',
        '#ffffff',

        '#333333',
        '#000080',
        '#008000',
        '#008080',
        '#800000',
        '#800080',
        '#808000',
        '#808080',
        '#ffffff',
        '#ffffff',
        '#ffffff',
        '#ffffff',
        
        '#000000',
        '#00008B',
        '#006400',
        '#008B8B',
        '#8B0000',
        '#8B008B',
        '#8B8B00',
        '#808080',
    ];

    $colorObjects = [];
    foreach ($colors as $color) {
        $colorObjects[] = $colordb->findColor($color);
    }

    $templateName = 'grid.twig';
    $context['show_menu'] = true;
    $context['colortitle'] = 'Basic+ colors';
    $context['colors'] = $colorObjects;
    break;
case 'hues':
    $colors = [
        '#ff8080',
        '#ffc080',
        '#ffff80',
        '#c0ff80',
        '#80ff80',
        '#80ffc0',
        '#80ffff',
        '#80c0ff',
        '#8080ff',
        '#c080ff',
        '#ff80ff',
        '#ff80c0',

        '#ff0000',
        '#ff8000',
        '#ffff00',
        '#80ff00',
        '#00ff00',
        '#00ff80',
        '#00ffff',
        '#0080ff',
        '#0000ff',
        '#8000ff',
        '#ff00ff',
        '#ff0080',

        '#800000',
        '#804000',
        '#808000',
        '#408000',
        '#008000',
        '#008040',
        '#008080',
        '#004080',
        '#000080',
        '#400080',
        '#800080',
        '#800040',

        '#000000',
        '#161616',
        '#2c2c2c',
        '#404040',
        '#565656',
        '#6c6c6c',
        '#808080',
        '#969696',
        '#acacac',
        '#c0c0c0',
        '#d6d6d6',
        '#ececec',
    ];

    $colorObjects = [];
    foreach ($colors as $color) {
        $colorObjects[] = $colordb->findColor($color);
    }

    $templateName = 'grid.twig';
    $context['show_menu'] = true;
    $context['colortitle'] = 'Hues';
    $context['colors'] = $colorObjects;
    break;
case 'spread':
    $mask = isset($_GET['mask']) ? $_GET['mask'] : 'xx0000';

    $colorObjects = $colordb->getSpread($mask);

    $templateName = 'blocks.twig';
    $context['show_menu'] = true;
    $context['colortitle'] = 'Spread';
    $context['colors'] = $colorObjects;
    break;
case 'supergrid':
    $start = '000000';
    $end = 'ff00ff';

    $colorsets = [];
    for ($i = 0; $i <= 255; $i++) {
        $mask = 'xx' . Repository::formathex($i) . '00';
        $set = [
            'mask' => $mask,
            'colors' => $colordb->getSpread($mask)
        ];
        $colorsets[] = $set;
    }

    $templateName = 'gradient-blocks.twig';
    $context['show_menu'] = true;
    $context['colortitle'] = 'Spread';
    $context['colorsets'] = $colorsets;
    break;
case 'color':
    if (isset($_GET['c'])) {
        $input = $_GET['c'];

        $color = $colordb->findColor($input);

        if (!$color) {
            header('Location: /');
            exit();
        }
    } else {
        $color = $colordb->getRandomColor();
    }

    $templateName = 'color.twig';
    $context['colortitle'] = $color->getHex();
    $context['color'] = $color;
    $context['darkClass'] = $color->isDark() ? 'dark-gb' : '';
    break;
case 'cotd':
default:
    if (isset($_GET['generate'])) {
        // This section of the script is how the list was generated. In case a new
        // one needs to be generated.
        $colors = $colordb->getAll();
        shuffle($colors);

        $daycolors = array();
        $c = 0;
        for ($i = 0; $i < 366; $i++) {
            if ($c > count($colors) -1) {
                $c = 0;
            }
            $daycolors[$i] = $colors[$c]->getHex();
            $c++;
        }

        var_export($daycolors);exit();
    }

    $dayColors = include 'data/cotd.php';

    $day = date('z');
    $date = date('Y-m-d');

    if (isset($_GET['d'])) {
        // 1 should be Jan 1st, but it is the 0th index in the array above.
        $day = (int) $_GET['d'] - 1;
        $date = date('Y-m-d', strtotime(date('Y-01-01') . ' + ' . $day . ' days'));
        if (!isset($dayColors[$day])) {
            header("Location: ?");
            exit();
        }
    }

    if (isset($_GET['date'])) {
        $day = date('z', strtotime($_GET['date']));
        $date = date('Y-m-d', strtotime($_GET['date']));
    }

    $color = $colordb->findColorByHex($dayColors[$day]);

    $context['colortitle'] = 'Day ' . ((int) $day + 1) . ' | Color of the Day | ' . $date;
    $context['color'] = $color;
    $context['day'] = $day;
}

$loader = new Twig_Loader_Filesystem('templates');
$twig = new Twig_Environment($loader);

$template = $twig->loadTemplate($templateName);

echo $template->render($context);
exit();
