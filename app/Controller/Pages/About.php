<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Organization;

class About extends Page
{
    public static function getAbout()
    {
        $objOrg = new Organization();

        $content = View::render('pages/about', [
            'name' => $objOrg->name,
            'myself' => $objOrg->myself
        ]);

        return parent::getPage('Stingray - About', $content);
    }
}