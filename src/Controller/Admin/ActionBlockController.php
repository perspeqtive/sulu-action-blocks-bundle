<?php

declare(strict_types=1);

namespace PERSPEQTIVE\SuluActionBlockBundle\Controller\Admin;

use Doctrine\ORM\EntityManagerInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use PERSPEQTIVE\SuluActionBlockBundle\Entity\ActionBlock;
use PERSPEQTIVE\SuluActionBlockBundle\Repository\ActionBlockRepository;
use Sulu\Component\Rest\ListBuilder\ListRestHelperInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\ListBuilder\Doctrine\DoctrineListBuilderFactoryInterface;
use Sulu\Component\Rest\ListBuilder\Metadata\FieldDescriptorFactoryInterface;
use Sulu\Component\Rest\RestHelperInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @RouteResource("action-block")
 */
class ActionBlockController extends AbstractRestController
{
    public function __construct(
        ViewHandlerInterface $viewHandler,
        private readonly ListRestHelperInterface $listRestHelper,
        private readonly DoctrineListBuilderFactoryInterface $listBuilderFactory,
        private readonly FieldDescriptorFactoryInterface $fieldDescriptorFactory,
        private readonly RestHelperInterface $restHelper,
        private readonly ActionBlockRepository $actionBlockRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($viewHandler);
    }

    public function getAction(int $id): Response
    {
        $entity = $this->actionBlockRepository->find($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($entity));
    }

    public function putAction(int $id, Request $request): Response
    {
        $entity = $this->actionBlockRepository->findById($id);
        if (!$entity) {
            throw new NotFoundHttpException();
        }

        $this->mapDataToEntity($request->toArray(), $entity);
        $this->entityManager->flush();

        return $this->handleView($this->view($entity));
    }

    public function postAction(Request $request): Response
    {
        $entity = new ActionBlock();
        $this->mapDataToEntity($request->toArray(), $entity);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $this->handleView($this->view($entity, 201));
    }

    public function deleteAction(int $id): Response
    {
        $entity = $this->actionBlockRepository->findById($id);
        if ($entity) {
            $this->entityManager->remove($entity);
            $this->entityManager->flush();
        }

        return $this->handleView($this->view(null, 204));
    }

    public function cgetAction(Request $request): Response
    {
        $fieldDescriptors = $this->fieldDescriptorFactory->getFieldDescriptors(ActionBlock::RESOURCE_KEY);
        $listBuilder = $this->listBuilderFactory->create(ActionBlock::class);
        $this->restHelper->initializeListBuilder($listBuilder, $fieldDescriptors);

        $list = $listBuilder->execute();

        return $this->handleView($this->view(
            [
                '_embedded' => [
                    ActionBlock::RESOURCE_KEY => $list,
                ],
                'total' => $listBuilder->count(),
            ],
        ));
    }

    protected function mapDataToEntity(array $data, ActionBlock $entity): void
    {
        $entity->setTitle($data['title'] ?? null);
        $entity->setConfiguration($data['configuration'] ?? []);
    }
}
