<?php

namespace App\Core;

class View
{
    protected static $layout = 'layouts/app';

    public static function render($view, $data = [])
    {
        // Extract data to make it available in the view
        extract($data);

        // Start output buffering for the view content
        ob_start();
        include dirname(__DIR__, 2) . "/views/{$view}.php";
        $content = ob_get_clean();

        // Start output buffering for the layout
        ob_start();
        include dirname(__DIR__, 2) . "/views/" . self::$layout . ".php";
        return ob_get_clean();
    }
}
