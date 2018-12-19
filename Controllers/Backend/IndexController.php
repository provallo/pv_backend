<?php

namespace ProVallo\Controllers\Backend;

use ProVallo\Components\Controller;

class IndexController extends Controller
{
    
    public function indexAction ()
    {
        $filename = __DIR__ . '/../../Views/backend/dist/index.html';
        $html     = file_get_contents($filename);
        
        // Rewrite /static to correct path
        $html = strtr($html, [
            '/static' => '/ext/Backend/Views/backend/dist/static',
            'static' => '/ext/Backend/Views/backend/dist/static'
        ]);
        
        return $html;
    }
    
}