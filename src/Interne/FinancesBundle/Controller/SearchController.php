<?php

namespace Interne\FinancesBundle\Controller;

use Interne\FinancesBundle\Form\OwnerSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Interne\FinancesBundle\Entity\Rappel;
use Interne\FinancesBundle\Entity\Facture;
use Interne\FinancesBundle\Entity\Creance;
use Interne\FinancesBundle\Form\FactureSearchType;
use Interne\FinancesBundle\Form\CreanceSearchType;
use Interne\FinancesBundle\Entity\CreanceRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Class SearchController
 * @package Interne\FinancesBundle\Controller
 *
 * @Route("/search")
 */
class SearchController extends Controller
{


    /**
     * @Route("/", name="interne_fiances_search", options={"expose"=true})
     *
     * @return Response
     */
    public function searchAction()
    {

        $data = array();
        $searchForm = $this->createFormBuilder($data)
            ->add('creance', new CreanceSearchType)
            ->add('facture', new FactureSearchType)
            ->add('owner',new OwnerSearchType)
            ->add('searchMethode', 'choice',
                array(
                    'required' => true,
                    'mapped' => false,
                    'data' => 'new',
                    'choices' => array(
                        'new'   => 'Nouvelle recherche',
                        'add' => 'Ajouter à la recherche actuelle',
                        'substract'   => 'Soustraire à la recherche actuelle',
                    )))
            ->getForm();

        /*
         * On récupère la session qui contient la liste des factures/creances des
         * recherches précédentes
         */
        $session = $this->getRequest()->getSession();

        $request = $this->getRequest();

        if($request->isXmlHttpRequest()) {

            /*
             * On récupère le formulaire
             */
            $searchForm->submit($request);

            /*
             * On extrait les donnée du formulaire
             */
            //$creance = new Creance();
            //$facture = new Facture();
            $creance = $searchForm->get('creance')->getData();
            $facture = $searchForm->get('facture')->getData();
            $searchMethode = $searchForm->get('searchMethode')->getData();
            $creanceParameters = $this->extractSearchDataCreance($searchForm->get('creance'),$searchForm->get('owner'));
            $factureParameters = $this->extractSearchDataFacture($searchForm->get('facture'),$searchForm->get('owner'));


            $facture->addCreance($creance);

            $em = $this->getDoctrine()->getManager();

            $creances = $em->getRepository('InterneFinancesBundle:Creance')->findBySearch($creance,$creanceParameters);
            $factures = $em->getRepository('InterneFinancesBundle:Facture')->findBySearch($facture,$factureParameters);

            $this->manageSession($creances,$factures,$searchMethode);

            return $this->render('InterneFinancesBundle:Search:results.html.twig', array(
                'factures' => $session->get('factures'),
                'creances' => $session->get('creances'),
            ));

        }


        return $this->render('InterneFinancesBundle:Search:search.html.twig', array(
            'searchForm' => $searchForm->createView(),
            'factures' => $session->get('factures'),
            'creances' => $session->get('creances'),
        ));

    }

    private function query()
    {

    }

    private function searchByLink($creances,$factures)
    {
        /*
         * On commence par rechercher toute les factures
         * liée a des créance trouvée qui ne sont pas
         * dans le résultat de la recherche.
         */
        foreach($creances as $creance)
        {

        }
    }

    private function extractSearchDataCreance($creanceSearchForm,$ownerSearchForm)
    {
        return array(
            'montantEmisMaximum' => $creanceSearchForm->get('montantEmisMaximum')->getData(),
            'montantEmisMinimum' => $creanceSearchForm->get('montantEmisMinimum')->getData(),
            'montantRecuMaximum' => $creanceSearchForm->get('montantRecuMaximum')->getData(),
            'montantRecuMinimum' => $creanceSearchForm->get('montantRecuMinimum')->getData(),

            'dateCreationMaximum' => $creanceSearchForm->get('dateCreationMaximum')->getData(),
            'dateCreationMinimum' => $creanceSearchForm->get('dateCreationMinimum')->getData(),
            'datePayementMaximum' => $creanceSearchForm->get('dateCreationMaximum')->getData(),
            'datePayementMinimum' => $creanceSearchForm->get('dateCreationMinimum')->getData(),

            'isLinkedToFacture' => $creanceSearchForm->get('isLinkedToFacture')->getData(),

            'membreNom' => $ownerSearchForm->get('membreNom')->getData(),
            'membrePrenom' => $ownerSearchForm->get('membrePrenom')->getData(),
            'familleNom' => $ownerSearchForm->get('familleNom')->getData(),
        );
    }

    private function extractSearchDataFacture($factureSearchForm,$ownerSearchForm)
    {
        return array(
            'nombreRappel' => $factureSearchForm->get('nombreRappel')->getData(),

            'montantRecu' => $factureSearchForm->get('montantRecu')->getData(),
            'montantRecuMaximum' => $factureSearchForm->get('montantRecuMaximum')->getData(),
            'montantRecuMinimum' => $factureSearchForm->get('montantRecuMinimum')->getData(),

            'montantEmis' => $factureSearchForm->get('montantRecu')->getData(),
            'montantEmisMaximum' => $factureSearchForm->get('montantRecuMaximum')->getData(),
            'montantEmisMinimum' => $factureSearchForm->get('montantRecuMinimum')->getData(),

            'dateCreationMaximum' => $factureSearchForm->get('dateCreationMaximum')->getData(),
            'dateCreationMinimum' => $factureSearchForm->get('dateCreationMinimum')->getData(),

            'datePayementMaximum' => $factureSearchForm->get('datePayementMaximum')->getData(),
            'datePayementMinimum' => $factureSearchForm->get('datePayementMinimum')->getData(),
        );
    }

    private function manageSession($creances,$factures,$searchMethode)
    {
        $session = $this->getRequest()->getSession();

        if($searchMethode == 'new')
        {
            /*
             * Nouvelle recherche, on met a jour les session avec les nouveaux resultats
             */
            $session->set('factures',$factures);
            $session->set('creances',$creances);
        }
        if($searchMethode == 'add')
        {
            /*
             * On vérifie que les creances/factures trouvée n'existe
             * pas déjà dans la liste contenue en session.
             */
            $facturesSession = $session->get('factures');
            foreach($factures as $facture)
            {
                $found = false;
                foreach($facturesSession as $factureSession)
                {
                    if($facture->getId() == $factureSession->getId())
                        $found = true;
                }
                if(!$found)
                {
                    array_push($facturesSession,$facture);
                }
            }
            $session->set('factures',$facturesSession);

            $creancesSession = $session->get('creances');
            foreach($creances as $creance)
            {
                $found = false;
                foreach($creancesSession as $creanceSession)
                {
                    if($creance->getId() == $creanceSession->getId())
                        $found = true;
                }
                if(!$found)
                {
                    array_push($creancesSession,$creance);
                }
            }
            $session->set('creances',$creancesSession);
        }
        if($searchMethode == 'substract')
        {
            /*
             * On enleve les creances/factures trouvée de la liste
             */
            $facturesSession = $session->get('factures');
            $newFactureSession = array();

            foreach($facturesSession as $factureSession)
            {
                $found = false;
                foreach($factures as $facture)
                {
                    if($facture->getId() == $factureSession->getId())
                        $found = true;
                }
                if(!$found)
                {
                    array_push($newFactureSession,$factureSession);
                }
            }
            $session->set('factures',$newFactureSession);

            $creancesSession = $session->get('creances');
            $newCreancesSession = array();

            foreach($creancesSession as $creanceSession)
            {
                $found = false;
                foreach($creances as $creance)
                {
                    if($creance->getId() == $creanceSession->getId())
                        $found = true;
                }
                if(!$found)
                {
                    array_push($newCreancesSession,$creanceSession);
                }
            }
            $session->set('creances',$newCreancesSession);
        }

    }





}