<?php

/**
 * Plugin Name: SeraphConsulting monitor
 * Plugin URI: https://seraphconsulting.net/
 * Description: Simple plugin to show wp and installed plugins info'
 * Text Domain: seraph-monitor
 * Author: Alex Pershin (SeraphConsulting)
 * Author URI: https://seraphconsulting.net/
 * Version: 1.0.4
 */

require_once __DIR__ . '/inc/endpoints.class.php';


class seraphMonitor
{
    public function __construct()
    {

        $this->registerEndpoints();

    }

    public function registerEndpoints()
    {
        new seraphMonitorEndpoints();
    }

}

$gogogo = new seraphMonitor();

?>