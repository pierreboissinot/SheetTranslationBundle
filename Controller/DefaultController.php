<?php

namespace PierreBoissinot\SheetTranslationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('PierreBoissinotSheetTranslationBundle:Default:index.html.twig');
    }
}
