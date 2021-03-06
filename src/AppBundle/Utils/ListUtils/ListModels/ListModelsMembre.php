<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Membre;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsMembre extends  ListModel
{


    /**
     * @param $items
     * @param null $url
     * @return ListRenderer
     *
     * todo CMR rajouter le groupe en argument pour pouvoir donner l'attribution correct en lien avec le groupe
     */
    public function getEffectifs( $items, $url = null)
    {
        $list = $this->getDefault($items, $url);

        $list->setName('effectifs');

        $attributionParameters = function (Membre $membre) {
            // TODO CMR : ne marche pas s'il y a plusieurs attributions
            return array(
                "attribution" => $membre->getActiveAttribution()->getId()
            );
        };

        /* Supprimer l'attribution courante */
        $list->addActionLine(new ActionLine('Supprimer', 'delete', 'app_attribution_delete', $attributionParameters, EventPostAction::RefreshList));

        return $list;
    }

    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault( $items, $url = null)
    {
        $list = new ListRenderer($this->twig, $items);
        $list->setUrl($url);

        $list->setSearchBar(true);
        $router = $this->router;

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

    public function getFraterie( $items, $url= null)
    {
        $list = new ListRenderer($this->twig, $items);
        $list->setUrl($url);
        $router = $this->router;
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