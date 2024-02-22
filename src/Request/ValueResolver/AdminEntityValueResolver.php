<?php

namespace Sidus\AdminBundle\Request\ValueResolver;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Sidus\AdminBundle\Admin\Admin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminEntityValueResolver implements ValueResolverInterface
{
    public function __construct(private ManagerRegistry $doctrine)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$request->attributes->has('_admin')) {
            throw new \UnexpectedValueException('Missing _admin request attribute');
        }
        $admin = $request->attributes->get('_admin');

        if (!$admin instanceof Admin) {
            throw new \UnexpectedValueException('_admin request attribute is not an Admin object');
        }
        $entityManager = $this->doctrine->getManagerForClass($admin->getEntity());

        if (!$entityManager instanceof EntityManagerInterface) {
            throw new \UnexpectedValueException("Unable to find an EntityManager for class {$admin->getEntity()}");
        }
        $id = $request->attributes->get($argument->getAttributes()['attribute'] ?? 'id');

        if (null === $id) {
            $m = "Unable to resolve request attribute for identifier, either use 'id' as a request parameter or set it";
            $m .= " manually in the 'attribute' option of your param converter configuration";
            throw new \UnexpectedValueException($m);
        }
        $repository = $entityManager->getRepository($admin->getEntity());
        $entity = $repository->find($id);

        if (!$entity) {
            throw new NotFoundHttpException("No entity found for class {$admin->getEntity()} and id {$id}");
        }
        $request->attributes->set($argument->getName(), $entity);

        return [];
    }
}
