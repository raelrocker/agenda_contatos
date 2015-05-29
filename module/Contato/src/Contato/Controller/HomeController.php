<?php

namespace Contato\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class HomeController extends AbstractActionController
{

    public function indexAction()
    {
        return new ViewModel();
    }
    
    public function sobreAction()
    {
        return new ViewModel();
    }


}

