<?php

namespace Interne\SecurityBundle\Securer\Ressource\Drivers;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Interne\SecurityBundle\Securer\Ressource\CoreRessourceSecurer;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Interne\SecurityBundle\Securer\Annotations\SecureRessource;


class SecureResourceDriver
{
    private $reader;
    private $securer;
    private $em;

    public function __construct(Reader $reader, EntityManager $em, CoreRessourceSecurer $securer)
    {
        $this->reader  = $reader;
        $this->securer = $securer;
        $this->em      = $em;
    }

    public function onKernelController(FilterControllerEvent $event)
    {

        /*
         * L'annotation ne peut être utilisée que dans un controller
         * En effet, on ne sécurise pas de ressources dans les services ou ailleurs
         */
        if (!is_array($controller = $event->getController()))
            throw new \Exception("L'annotation @SecureRessource ne peut être utilisée que dans des controllers.");


        $object = new \ReflectionObject($controller[0]);
        $method = $object->getMethod($controller[1]);
        $annotations = new ArrayCollection();
        $params = new ArrayCollection();

        foreach ($this->reader->getMethodAnnotations($method) as $configuration) {

            //On récupère notre annotation
            if ($configuration instanceof SecureRessource)
                $annotations->add($configuration);
            else if ($configuration instanceof \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter)
                $params->add($configuration);
        }

        /**
         * A ce stade, on a récupéré l'ensemble des secure et des paramsConverters
         * On va donc pour chaque secure rechercher l'objet lié à travers les différents paramsConverters, et les
         * analyser
         * @var SecureRessource $ann
         * @var \Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter $entry
         */
        foreach ($annotations as $ann) {

            $name = $ann->value;

            $param = $params->filter(

                function ($entry) use ($name) {
                    if ($entry->getName() == $name) return $entry;
                }

            );

            $param = array_values($param->toArray())[0];

            $entityId = $event->getRequest()->attributes->get('_route_params')[$name];
            $entity = $this->em->getRepository($param->getClass())->find($entityId);

            $this->securer->grantable($ann->action, $entity, true);
        }

    }
}