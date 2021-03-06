<?php

namespace AppBundle\Utils\ListUtils\ListModels;


use AppBundle\Entity\PayementFile;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListModelInterface;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;

class ListModelsPayementFile extends  ListModel
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
        $router = $this->router;
        $list->addColumn(new Column('Fichier', function (PayementFile $file) use ($router) {
            return $file->getFilename();
        }));

        $list->addColumn(new Column('Information', function (PayementFile $file) use ($router) {
            return $file->getInfos();
        }));


        $list->addColumn(new Column('Date', function (PayementFile $file) use ($router) {
            return $file->getDate();
        },'date(global_datetime_format)'));

/*
        $userParameters = function (User $user) {
            return array(
                "user" => $user->getId()
            );
        };

        $list->addActionLine(new ActionLine('Modifier', 'edit', 'app_user_edit', $userParameters, EventPostAction::Link));

        $list->addActionLine(new ActionLine('Voir', 'zoom', 'app_user_show', $userParameters, EventPostAction::Link));


        //return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';

*/
        return $list;
    }

}


?>