<?php


namespace AppBundle\Utils\ListUtils\ListModels;

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

class ListModelsCreances extends  ListModel
{


    /**
     * @param $items
     * @param string $url
     * @return ListRenderer
     */
    public function getDefault($items, $url = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);

        $list->addColumn(new Column('Facture', function (Creance $item) { return $item->getFacture(); },'ref|raw'));
        $list->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_state|raw'));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));


        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "money"));
        /*
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));

        */

        $parameters = function (Creance $item) {
            return array(
                'creance' => $item->getId()
            );
        };

        $removeCondition = function (Creance $creance) {
            return !$creance->isFactured();
        };

        $list->addActionLine(new ActionLine('Voir', 'zoom', 'app_creance_show', $parameters, EventPostAction::ShowModal));

        /* todo CMR de NUR comment je fait pour que cette action soit uniquement dans les lignes et pas dans le bouton de mass? */
        $list->addActionLine(new ActionLine('Supprimer', 'remove', 'app_creance_remove', $parameters, EventPostAction::RefreshPage,$removeCondition));



        return $list;
    }


    /**
     * @param $items
     * @param null $url
     * @param Debiteur $debiteur
     * @return ListRenderer
     */
    public function getForDebiteur($items, $url = null,Debiteur $debiteur)
    {
        $twig = $this->twig;
        $router = $this->router;
        $list = self::getDefault($items, $url);

        $list->addActionList(new ActionList('Ajouter', 'add', 'app_creance_create',array('debiteur' => $debiteur->getId()), EventPostAction::ShowModal,null,'green'));

        $list->addActionList(new ActionList('Facturer', 'file text', 'app_debiteur_facturation',array('debiteur' => $debiteur->getId()), EventPostAction::RefreshList,null,'blue'));

        return $list;
    }

    /**
     * @param $items
     * @return ListRenderer
     */
    public function getSearchResults( $items, $url = null, $listSessionKey = null)
    {
        $twig = $this->twig;
        $router = $this->router;
        $list = new ListRenderer($twig, $items);
        $list->setUrl($url);
        $list->setSearchBar(true);


        $list->addColumn(new Column('Facture', function (Creance $item) { return $item->getFacture(); },'ref|raw'));
        $list->addColumn(new Column('Etat', function (Creance $item) { return $item; },'creance_state|raw'));

        $list->addColumn(new Column('Motif', function (Creance $item) {
            return $item->getTitre();
        }));

        $list->addColumn(new Column('Debiteur', function (Creance $item) {
            return $item->getDebiteur()->getOwnerAsString();
        }));


        $list->addColumn(new Column('Montant', function (Creance $item) {
            return $item->getMontantEmis();
        }, "money"));
        $list->addColumn(new Column('Montant perçu', function (Creance $item) {
            return $item->getMontantRecu();
        }, "money"));


        $creanceParameters = function (Creance $creance) {
            return array(
                "creance" => $creance->getId()
            );
        };

        $list->addActionLine(new ActionLine('Afficher', 'zoom', 'app_creance_show', $creanceParameters, EventPostAction::ShowModal,null,true,false));


        //si la créance est déjà facturée, on donne la possibilité de visionner la facture.
        $factureCondition = function (Creance $creance) {
            return $creance->isFactured();
        };

        $factureParameters = function (Creance $creance) {
            return array(
                "facture" => ($creance->isFactured() ? $creance->getFacture()->getId() : null)
            );
        };


        $list->addActionLine(new ActionLine('Afficher', 'edit', 'app_facture_show', $factureParameters, EventPostAction::ShowModal,$factureCondition,true,false));


        $list->addActionList(new ActionList('Facturer les créance ouvertes', 'file text', 'app_creance_facturation',array('list_session_key' => $listSessionKey), EventPostAction::RefreshPage,null,'blue'));

        return $list;
    }

}


?>