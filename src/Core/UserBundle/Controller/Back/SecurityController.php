<?php

namespace Core\UserBundle\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\UserBundle\Controller\SecurityController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseController
{

    protected function renderLogin(array $data)
    {
    	return $this->render('@User/back/Security/login.html.twig', $data);
    }

}

