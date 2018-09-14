<?php

namespace Core\UserBundle\Listener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Core\UserBundle\Event\CommandRegistrationSuccess;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;

class CommandRegistrationSuccessSubscriber implements EventSubscriberInterface{
	private $mailer;
	private $tokenGenerator;
	public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator){
		$this->mailer = $mailer;
		$this->tokenGenerator = $tokenGenerator;
	}
	public static function getSubscribedEvents(){
		return array(
            CommandRegistrationSuccess::NAME => 'onRegistrationSuccess'
        );
	}

	public function onRegistrationSuccess(CommandRegistrationSuccess $event){
		$user = $event->getUser();
		if (null === $user->getConfirmationToken()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
        }
        $this->mailer->sendConfirmationEmailMessage($user);
	}
}