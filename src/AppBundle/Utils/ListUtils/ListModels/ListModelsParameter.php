<?php


namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\Parameter;
use AppBundle\Entity\Payement;
use AppBundle\Entity\PayementFile;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ListRenderer;
use AppBundle\Entity\Creance;
use Symfony\Component\Routing\Router;
use AppBundle\Utils\ListUtils\ActionList;
use AppBundle\Entity\Debiteur;

class ListModelsParameter extends ListModel
{

    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        $list = new ListRenderer($this->twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);


        $list->addColumn(new Column('Parametre', function (Parameter $item) { return $item->getDescription(); }));

        $list->addColumn(new Column('Donnée', function (Parameter $item) {

            switch($item->getType())
            {
                case Parameter::TYPE_EMAIL:
                case Parameter::TYPE_STRING:
                    return $item->getData();
                case Parameter::TYPE_TEXT:
                    return substr($item->getData(),0,30).'...';



                case Parameter::TYPE_PNG:
                    return '<img class="ui image" src="'.$item->getData().'">';

                case Parameter::TYPE_CHOICE:
                default:
                    return 'Représentaiton impossible';

            }
        }));

        $list->addColumn(new Column('Type', function (Parameter $item) { return $item->getType(); }));

        if($this->isGranted('ROLE_PARAMETER'))
        {
            $parameters = function (Parameter $item) {
                return array(
                    "parameter" => $item->getId()
                );
            };
            $list->addActionLine(new ActionLine('Editer', 'edit', 'app_parameter_edit', $parameters, EventPostAction::ShowModal,null,true,false));
        }


        return $list;
    }


}


?>