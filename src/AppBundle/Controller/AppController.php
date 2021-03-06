<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Utils\Menu\Menu;


/**
 * Class AppController
 * @package AppBundle\Controller
 * @Route("/intranet")
 */
class AppController extends Controller
{
    /**
     * Page d'accueil de l'application
     * @Route("")
     */
    public function homeAction()
    {
        return $this->render("AppBundle:App:page_home.html.twig", array('user' => $this->getUser()));
    }

}
