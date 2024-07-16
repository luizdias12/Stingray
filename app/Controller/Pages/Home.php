<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class Home extends Page
{
    public static function getHome()
    {
        $objOrg = new Organization();

        $content = View::render('pages/home', [
            'name' => $objOrg->name,
            'aboutme' => $objOrg->description
        ]);

        return parent::getPage('Stingray - Home', $content);
    }
}