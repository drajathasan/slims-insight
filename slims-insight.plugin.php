<?php
/**
 * Plugin Name: SLiMS Insight
 * Plugin URI: -
 * Description: Modification from laravel adapter, https://github.com/nunomaduro/phpinsights
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/SLiMS-Insight
 */

use SLiMS\Config;
use SLiMS\Plugins;

/**
 * Get plugin instance
 */
$plugin = Plugins::getInstance();

include_once __DIR__ . '/vendor/autoload.php';

if (null === config('insights')) 
    Config::create('insights', file_get_contents(__DIR__ . '/config/insights.php'));

$plugin->registerCommand(new \Drajat\SLiMSInsight\InsightsCommand);