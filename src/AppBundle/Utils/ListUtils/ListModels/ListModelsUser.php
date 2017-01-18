<?php

namespace AppBundle\Utils\ListUtils\ListModels;

use AppBundle\Entity\User;
use AppBundle\Utils\Event\EventPostAction;
use AppBundle\Utils\ListUtils\AbstractList;
use AppBundle\Utils\ListUtils\ActionLine;
use AppBundle\Utils\ListUtils\Column;
use AppBundle\Utils\ListUtils\ListModel;
use AppBundle\Utils\ListUtils\ListRenderer;
use Symfony\Component\Routing\Router;
use AppBundle\Utils\ListUtils\ActionList;

class ListModelsUser extends AbstractList
{

    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        $this->setItems($items);
        $this->setUrl($url);


        $router = $this->router;
        $this->addColumn(new Column('Utilisateur', function (User $user) use ($router) {
            return '<a href="' . $router->generate('app_user_show', array('user' => $user->getId())) . '">' . $user->getUsername() . '</a>';
        }));

        $this->addColumn(new Column('Dernière connexion', function (User $user) use ($router) {
            return (is_null($user->getLastConnexion())? '-' : $user->getLastConnexion()->format('d/m/Y'));
        }));

        $this->addColumn(new Column('Actif', function (User $user) use ($router) {
            if($user->getIsActive())
            {
                return '<div class="ui green label">oui</div>';
            }
            else
            {
                return '<div class="ui red label">non</div>';
            }
        }));

        $this->addColumn(new Column('Roles choisis', function (User $user) use ($router) {
            $roles = '';
            foreach($user->getSelectedRoles() as $role)
            {
                $roles = $roles.$role.'<br>';
                //$roles = $roles.$role->getRole().'<br>';
            }
            return $roles;
        }));

        $this->addColumn(new Column('Membre lié', function (User $user) use ($router) {
            if(is_null($user->getMembre())){
                return 'Pas de membre';
            }
            else{
                return $user->getMembre()->getPrenom().' '. $user->getMembre()->getNom();
            }
        }));

        $this->addColumn(new Column('Roles via Membre', function (User $user) use ($router) {

            $roles = $user->getMembreRoles();
            if(!empty($roles))
            {
                $roles = '';
                foreach($user->getMembreRoles() as $role)
                {
                    $roles = $roles.$role.'<br>';
                    //$roles = $roles.$role->getRole().'<br>';
                }
                return $roles;
            }
            return 'Aucun roles fournit par le membre';
        }));

        if($this->isGranted('ROLE_ADMIN'))
        {

            $userParameters = function (User $user) {
                return array(
                    "user" => $user->getId()
                );
            };

            $this->addActionLine(new ActionLine('Modifier', 'edit', 'app_user_edit', $userParameters, EventPostAction::Link));

            $this->addActionLine(new ActionLine('Voir', 'zoom', 'app_user_show', $userParameters, EventPostAction::Link));


            //return '<a href="' . $router->generate('app_membre_show', array('membre' => $membre->getId())) . '">' . $membre->getPrenom() . '</a>';


            $this->addActionList(new ActionList('Ajouter', 'add', 'app_user_create',null, EventPostAction::Link,null,'green'));

        }
        return $this;
    }

}


?>