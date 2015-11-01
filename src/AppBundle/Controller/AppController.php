<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Utils\ListRenderer\ListContainer;
use AppBundle\Utils\ListRenderer\ListRenderer;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Utils\Menu\Menu;

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
     * @Route("test", name="test")
     * @Menu("Test",block="test",order=1)
     */
    public function testAction()
    {
        return new Response();
    }


}
