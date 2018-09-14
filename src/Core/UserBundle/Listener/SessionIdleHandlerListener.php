<?php

namespace Core\UserBundle\Listener;


use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RequestContext;

/**
 * Description of SessionIdleHandler
 *
 */
class SessionIdleHandlerListener {
    private $session;
    private $tokenStorage;
    private $router;
    private $maxIdleTime;
    private $requestStack;


    public function __construct(SessionInterface $session, TokenStorage $tokenStorage, UrlGeneratorInterface $router, $maxIdleTime = 0, RequestStack $requestStack){
        $this->session = $session;
        $this->tokenStorage = $tokenStorage;
        $this->router = $router;
        $this->maxIdleTime = $maxIdleTime; 
        $this->requestStack = $requestStack;
    }

    public function onKernelRequest(GetResponseEvent $event){                
        if (!$event->isMasterRequest()) return;

        // Setting context for router, otherwise : it will generate relative URL(not absolute URL) 
        $context = new RequestContext();
        $context->fromRequest($this->requestStack->getCurrentRequest());
        $this->router->setContext($context);

        if ($this->maxIdleTime > 0) {
            $this->session->start();
            $lapse = time() - $this->session->getMetadataBag()->getLastUsed();

            if ($lapse > $this->maxIdleTime) {
                $this->tokenStorage->setToken(null);
                $this->session->getFlashBag()->set('info', 'You have been logged out due to inactivity.');
                $this->session->invalidate();

                // Redirect URL to logout
                // print_r($this->router->generate('admin_logout', array(), UrlGeneratorInterface::ABSOLUTE_URL));exit();
                $event->setResponse(new RedirectResponse($this->router->generate('admin_logout')));
            }
        }
    }
}