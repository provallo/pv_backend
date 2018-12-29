<?php

namespace ProVallo\Controllers\Backend;

use ProVallo\Components\Controller;
use ProVallo\Plugins\Backend\Models\Menu\Menu;

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
    
    public function menuAction ()
    {
        $items  = Menu::repository()->findBy(['parentID => -1'], 'position ASC');
        $result = [];
        
        foreach ($items as $item)
        {
            $result[] = $item->toArray();
        }
        
        return self::json()->success([
            'data' => $result
        ]);
    }
    
}