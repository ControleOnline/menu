<?php

namespace Menu\Controller;

use Zend\View\Model\ViewModel;
use Core\Controller\AbstractController;

class DefaultController extends AbstractController {

    public function indexAction() {

        return new ViewModel();
    }

}
