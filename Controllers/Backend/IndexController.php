<?php

namespace ProVallo\Controllers\Backend;

use ProVallo\Components\Controller;
use ProVallo\Plugins\Backend\Models\Menu\Menu;

class IndexController extends Controller
{
    
    public function indexAction ()
    {
        $filename = __DIR__ . '/../../Views/backend/dist/index.html';
        
        if (!is_file($filename))
        {
            echo sprintf('The backend is not yet compiled. Please run "php bin/console backend:build" first.');
            die;
        }
        
        $html = file_get_contents($filename);
        
        // Rewrite /static to correct path
        $html = strtr($html, [
            '/static' => '/ext/Backend/Views/backend/dist/static',
            'static'  => '/ext/Backend/Views/backend/dist/static'
        ]);
        
        return $html;
    }
    
    public function menuAction ()
    {
        return self::json()->success([
            'data' => $this->getMenu(-1)
        ]);
    }
    
    protected function getMenu ($parentID)
    {
        $items  = Menu::repository()->findBy(['parentID' => $parentID], 'position ASC');
        $result = [];
        
        foreach ($items as $item)
        {
            $row             = $item->toArray();
            $row['children'] = $this->getMenu($item->id);
            $result[]        = $row;
        }
        
        return $result;
    }
    
}