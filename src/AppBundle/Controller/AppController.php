<?php

namespace AppBundle\Controller;

use AppBundle\Utils\ListRenderer\ListContainer;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("", name="interne_homepage")
     */
    public function homePageAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastNews = $em->getRepository('InterneOrganisationBundle:News')->findForPaging(0, 1);

        return $this->render("AppBundle:Homepage:page_homepage.html.twig", array('lastNews' => $lastNews, 'user' => $this->getUser()));
    }

    /**
     * @route("test")
     */
    public function testAction()
    {

        $parametre = $this->get('parametres_container')->getParamter('bidon');

        return new Response();
    }


    /**
     * @route("hello/{nom}", name="exemple_hello_word")
     */
    public function helloWordAction($nom)
    {
        return new Response("Salut les ".$nom);
    }

}
