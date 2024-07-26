<?php

namespace App\Controller\Pages;

use App\Utils\View;
use App\Model\Entity\Helpdesk as EntityHelpdesk;


class Helpdesk extends Page
{
    public static function getHelpdesks()
    {

        $results = EntityHelpdesk::getHelpdesks('status in ("A")', 'id DESC', '5', 'id, dt_abertura');

        while ($object = $results->fetchObject(EntityHelpdesk::class)){
            $content .= View::render('pages/helpdesk', [
           'id' => $object->id,
           'dt_abertura' => $object->dt_abertura
        ]);
        }
        
        return parent::getPage('Stingray - Heldpesk', $content);
    }
}