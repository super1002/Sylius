<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandlerInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvents;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ResourceController extends ContainerAware
{
    /**
     * @var MetadataInterface
     */
    protected $metadata;

    /**
     * @var RequestConfigurationFactoryInterface
     */
    protected $requestConfigurationFactory;

    /**
     * @var ViewHandlerInterface
     */
    protected $viewHandler;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var NewResourceFactoryInterface
     */
    protected $newResourceFactory;

    /**
     * @var ObjectManager
     */
    protected $manager;

    /**
     * @var SingleResourceProviderInterface
     */
    protected $singleResourceProvider;

    /**
     * @var ResourcesCollectionProviderInterface
     */
    protected $resourcesCollectionProvider;

    /**
     * @var ResourceFormFactoryInterface
     */
    protected $resourceFormFactory;

    /**
     * @var RedirectHandlerInterface
     */
    protected $redirectHandler;

    /**
     * @var FlashHelperInterface
     */
    protected $flashHelper;

    /**
     * @var AuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @param MetadataInterface $metadata
     * @param RequestConfigurationFactoryInterface $requestConfigurationFactory
     * @param ViewHandlerInterface $viewHandler
     * @param RepositoryInterface $repository
     * @param FactoryInterface $factory
     * @param NewResourceFactoryInterface $newResourceFactory
     * @param ObjectManager $manager
     * @param SingleResourceProviderInterface $singleResourceProvider
     * @param ResourcesCollectionProviderInterface $resourcesFinder
     * @param ResourceFormFactoryInterface $resourceFormFactory
     * @param RedirectHandlerInterface $redirectHandler
     * @param FlashHelperInterface $flashHelper
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        MetadataInterface $metadata,
        RequestConfigurationFactoryInterface $requestConfigurationFactory,
        ViewHandlerInterface $viewHandler,
        RepositoryInterface $repository,
        FactoryInterface $factory,
        NewResourceFactoryInterface $newResourceFactory,
        ObjectManager $manager,
        SingleResourceProviderInterface $singleResourceProvider,
        ResourcesCollectionProviderInterface $resourcesFinder,
        ResourceFormFactoryInterface $resourceFormFactory,
        RedirectHandlerInterface $redirectHandler,
        FlashHelperInterface $flashHelper,
        AuthorizationCheckerInterface $authorizationChecker,
        EventDispatcherInterface $eventDispatcher
    )
    {
        $this->metadata = $metadata;
        $this->requestConfigurationFactory = $requestConfigurationFactory;
        $this->viewHandler = $viewHandler;
        $this->repository = $repository;
        $this->factory = $factory;
        $this->newResourceFactory = $newResourceFactory;
        $this->manager = $manager;
        $this->singleResourceProvider = $singleResourceProvider;
        $this->resourcesCollectionProvider = $resourcesFinder;
        $this->resourceFormFactory = $resourceFormFactory;
        $this->redirectHandler = $redirectHandler;
        $this->flashHelper = $flashHelper;
        $this->authorizationChecker = $authorizationChecker;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function showAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceControllerEvents::SHOW, $configuration, $resource);

        $view = View::create($resource);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::SHOW))
                ->setTemplateVar($this->metadata->getName())
                ->setData(array(
                    'metadata' => $this->metadata,
                    'resource' => $resource,
                    $this->metadata->getName() => $resource
                ))
            ;
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::INDEX);
        $resources = $this->resourcesCollectionProvider->get($configuration, $this->repository);

        $view = View::create($resources);

        if ($configuration->isHtmlRequest()) {
            $view
                ->setTemplate($configuration->getTemplate(ResourceActions::INDEX))
                ->setTemplateVar($this->metadata->getPluralName())
                ->setData(array(
                    'metadata' => $this->metadata,
                    'resources' => $resources,
                    $this->metadata->getPluralName() => $resources
                ))
            ;
        }

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::CREATE);
        $newResource = $this->newResourceFactory->create($configuration, $this->factory);

        $form = $this->resourceFormFactory->create($configuration, $newResource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->eventDispatcher->dispatch(ResourceControllerEvents::PRE_CREATE, $configuration, $newResource);
            $this->repository->add($newResource);
            $this->eventDispatcher->dispatch(ResourceControllerEvents::POST_CREATE, $configuration, $newResource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle(View::create($newResource, 201));
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::CREATE, $newResource);

            return $this->redirectHandler->redirectToResource($configuration, $newResource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create($form));
        }

        $view = View::create()
            ->setData(array(
                'metadata' => $this->metadata,
                'resource' => $newResource,
                $this->metadata->getName() => $newResource,
                'form' => $form->createView()
            ))
            ->setTemplate($configuration->getTemplate(ResourceActions::CREATE))
        ;

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function updateAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $resource = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $resource);

        if (in_array($request->getMethod(), array('POST', 'PUT', 'PATCH')) && $form->submit($request, !$request->isMethod('PATCH'))->isValid()) {
            $this->eventDispatcher->dispatch(ResourceControllerEvents::PRE_UPDATE, $configuration, $resource);
            $this->manager->flush();
            $this->eventDispatcher->dispatch(ResourceControllerEvents::POST_UPDATE, $configuration, $resource);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle(View::create(null, 204));
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $resource);

            return $this->redirectHandler->redirectToResource($configuration, $resource);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create($form));
        }

        $view = View::create()
            ->setData(array(
                'metadata' => $this->metadata,
                'resource' => $resource,
                $this->metadata->getName() => $resource,
                'form' => $form->createView()
            ))
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE))
        ;

        return $this->viewHandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::DELETE);
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceControllerEvents::PRE_DELETE, $configuration, $resource);
        $this->repository->remove($resource);
        $this->eventDispatcher->dispatch(ResourceControllerEvents::POST_DELETE, $configuration, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create(null, 204));
        }

        $this->flashHelper->addSuccessFlash($configuration, ResourceActions::DELETE, $resource);

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function enableAction(Request $request)
    {
        return $this->toggle($request, true);
    }
    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function disableAction(Request $request)
    {
        return $this->toggle($request, false);
    }

    /**
     * @param Request $request
     * @param $enabled
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    protected function toggle(Request $request, $enabled)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);

        $resource = $this->findOr404($configuration);
        $resource->setEnabled($enabled);

        $this->eventDispatcher->dispatch(ResourceControllerEvents::PRE_UPDATE, $configuration, $resource);
        $this->manager->flush();
        $this->eventDispatcher->dispatch(ResourceControllerEvents::POST_UPDATE, $configuration, $resource);

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create($resource, 204));
        }

        $this->flashHelper->addSuccessFlash($configuration, $enabled ? 'enable' : 'disable', $resource);

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function moveUpAction(Request $request)
    {
        return $this->move($request, 1);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function moveDownAction(Request $request)
    {
        return $this->move($request, -1);
    }

    /**
     * @param Request $request
     * @param integer $movement
     *
     * @return RedirectResponse
     */
    protected function move(Request $request, $movement)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $resource = $this->findOr404($configuration);

        $position = $configuration->getSortablePosition();
        $accessor = PropertyAccess::createPropertyAccessor();
        $accessor->setValue(
            $resource,
            $position,
            $accessor->getValue($resource, $position) + $movement
        );

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle(View::create($resource, 204));
        }

        $this->flashHelper->addSuccessFlash($configuration, 'move', $resource);

        return $this->redirectHandler->redirectToIndex($configuration, $resource);
    }

    /**
     * @param RequestConfiguration $configuration
     * @param string $permission
     *
     * @throws AccessDeniedException
     */
    protected function isGrantedOr403(RequestConfiguration $configuration, $permission)
    {
        $permission = $configuration->getPermission($permission);

        if (!$this->authorizationChecker->isGranted($configuration, $permission)) {
            throw new AccessDeniedException();
        }
    }

    /**
     * @param RequestConfiguration $configuration
     *
     * @return \Sylius\Component\Resource\Model\ResourceInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findOr404(RequestConfiguration $configuration)
    {
        if (null === $resource = $this->singleResourceProvider->get($configuration, $this->repository)) {
            throw new NotFoundHttpException();
        }

        return $resource;
    }
}
