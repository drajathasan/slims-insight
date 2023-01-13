<?php
/**
 * Plugin Name: SLiMS Insight
 * Plugin URI: -
 * Description: -
 * Version: 1.0.0
 * Author: Drajat Hasan
 * Author URI: https://github.com/drajathasan/SLiMS-Insight
 */

use SLiMS\Plugins;

/**
 * Get plugin instance
 */
$plugin = Plugins::getInstance();

include_once __DIR__ . '/vendor/autoload.php';
$plugin->registerCommand(new \Drajat\SLiMSInsight\InsightsCommand);