<?php

namespace Core\UserBundle\Controller\Back;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FOS\UserBundle\Mailer\MailerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;

class AdminController extends BaseAdminController
{
    public function indexAction(Request $request)
    {
        $this->initialize($request);
        if ($this->request->query->get('type') == 'user') {
            $dql_filter = "entity.roles NOT LIKE '%%ROLE_ADMIN%%' AND  entity.roles NOT LIKE '%%ROLE_SUPER_ADMIN%%'";
            $this->entity['list']['dql_filter'] = $dql_filter;
        } 
        if (null === $request->query->get('entity')) {
            return $this->redirectToBackendHomepage();
        }

        $action = $request->query->get('action', 'list');
        if (!$this->isActionAllowed($action)) {
            throw new ForbiddenActionException(array('action' => $action, 'entity_name' => $this->entity['name']));
        }
        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

    public function profileAction(Request $request){
        $request->query->remove('type');
        $this->initialize($request);
        $action = 'show';
        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

    /**
     * The method that is executed when the user performs a 'new' action on an entity.
     *
     * @return Response|RedirectResponse
     */
    protected function newAction()
    {
        $this->dispatch(EasyAdminEvents::PRE_NEW);

        $entity = $this->executeDynamicMethod('createNew<EntityName>Entity');
        $easyadmin = $this->request->attributes->get('easyadmin');
        $easyadmin['item'] = $entity;
        $this->request->attributes->set('easyadmin', $easyadmin);

        $fields = $this->entity['new']['fields'];

        $newForm = $this->executeDynamicMethod('create<EntityName>NewForm', array($entity, $fields));

        $newForm->handleRequest($this->request);
        if ($newForm->isSubmitted() && $newForm->isValid()) {
            $type = $this->request->query->get('type');
            $role = $type == 'admin' ? 'ROLE_ADMIN' : 'ROLE_USER';
            $entity->addRole($role);
            $this->dispatch(EasyAdminEvents::PRE_PERSIST, array('entity' => $entity));

            $this->executeDynamicMethod('prePersist<EntityName>Entity', array($entity));
            $this->executeDynamicMethod('persist<EntityName>Entity', array($entity));

            $this->dispatch(EasyAdminEvents::POST_PERSIST, array('entity' => $entity));

            return $this->redirectToReferrer();
        }

        $this->dispatch(EasyAdminEvents::POST_NEW, array(
            'entity_fields' => $fields,
            'form' => $newForm,
            'entity' => $entity,
        ));

        $parameters = array(
            'form' => $newForm->createView(),
            'entity_fields' => $fields,
            'entity' => $entity,
        );
        return $this->executeDynamicMethod('render<EntityName>Template', array('new', $this->entity['templates']['new'], $parameters));
    }
}
