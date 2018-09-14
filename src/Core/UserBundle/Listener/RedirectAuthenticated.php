<?php
/**
 * Created by PhpStorm.
 * User: develope_alg
 * Date: 4/22/18
 * Time: 11:34 PM
 */

namespace Core\UserBundle\Listener;


use FOS\UserBundle\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class RedirectAuthenticated {

    private $router;
    private $tokenStorage;

    public function __construct(UrlGeneratorInterface $router, TokenStorage $tokenStorage){
        $this->router = $router;
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelRequest(GetResponseEvent $event){
        if (!$event->isMasterRequest()) return;
        if ($this->isLogged()){
            $current_route = $event->getRequest()->attributes->get('_route');
            if ($this->checkRoutesForAdmin($current_route)){
                // $event->setResponse(new RedirectResponse($this->router->generate('admin_index')));
                $event->setResponse(new RedirectResponse('/admin'));
            }elseif ($this->checkRoutesForStorekeeper($current_route)){
                $event->setResponse(new RedirectResponse($this->router->generate('admin_storekeeper')));
            }
        }

    }

    private function isLogged(){
        // check token value before getting user object
        $token = $this->tokenStorage->getToken();
        $user = $token ? $token->getUser() : NULL;
        return $user instanceof User;
    }

    private function checkRoutesForAdmin($_route){
        return in_array($_route, ['admin_login']);
    }

    private function checkRoutesForStorekeeper($_route){
        return in_array(
            $_route,
            ['fos_user_registration_register', 'fos_user_resetting_request',
                'fos_user_resetting_request', 'fos_user_security_login']
        );
    }
}