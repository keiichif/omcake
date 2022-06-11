<?php

namespace App\Controller;

use App\Controller\AppController;


class ForbiddenController extends AppController
{


    public function initialize(): void
    {
        parent::initialize();
        
        $this->viewBuilder()->setLayout('mr-common');
    }


    public function index()
    {
    }


}
