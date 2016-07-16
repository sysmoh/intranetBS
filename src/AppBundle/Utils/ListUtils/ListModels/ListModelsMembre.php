<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsMembre implements ListModelInterface
{


    static public function getEffectifs(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = ListModelsMembre::getDefault($twig, $router, $items, $url);

        $list->setName('effectifs');

        $attributionParameters = function (Membre $membre) {
            // TODO: ne marche pas s'il y a plusieurs attributions
            return array(
                "attribution" => $membre->getActiveAttribution()->getId()
            );
        };

        /* Supprimer l'attribution courante */
        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'app_attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        return $list;
    }

    /**
     * @param \Twig_Environment $twig
     * @param Router $router
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    static public function getDefault(\Twig_Environment $twig, Router $router, $items, $url = null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);

        $list->addColumn(new Column('Prénom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';
        }));
        $list->addColumn(new Column('Nom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('app_famille_show', array('famille' => $membre->getFamille()->getId())) . '">' . $membre->getNom() . '</a>';
        }));
        $list->addColumn(new Column('Fonction', function (Membre $membre) use ($router) {
            return $membre->getActiveAttribution();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $membre) {
            return $membre->GetNaissance();
        },
            'date(global_date_format)'));

        return $list;
    }

    static public function getFraterie(\Twig_Environment $twig, Router $router, $items, $url= null)
    {
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);

        $list->addColumn(new Column('Prénom', function (Membre $membre) use ($router) {
            return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';
        }));
        $list->addColumn(new Column('Fonction', function (Membre $membre) {
            if ($membre->getActiveAttribution() != null)
                return $membre->getActiveAttribution()->getFonction();
        }));
        $list->addColumn(new Column('Naissance', function (Membre $membre) {
            return $membre->GetNaissance();
        },
            'date(global_date_format)'));

        $list->setDatatable(false);
        $list->setStyle('very basic');

        return $list;
    }
}


?>