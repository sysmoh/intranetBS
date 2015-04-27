<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Membre;
use AppBundle\Entity\ObtentionDistinction;
use AppBundle\Form\ObtentionDistinctionType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ObtentionDistinctionController
 * @package AppBundle\Controller
 *
 * @Route("/obtention-distinction")
 */
class ObtentionDistinctionController extends Controller
{

    /**
     * @Route("/get-modal", name="obtention-distinction_get_modal", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function getObtentionDistinctionFormAjaxAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        /*
         * On envoie le formulaire en modal
         */

        $idMembre = $request->request->get('idMembre');
        $idObtentionDistinction = $request->request->get('idObtentionDistinction');

        $obtention = null;
        $obtentionForm = null;
        if ($idObtentionDistinction == null) {
            /*
             * Ajout
             */
            $obtention = new ObtentionDistinction();
            $obtention->setMembre($em->getRepository('AppBundle:Membre')->find($idMembre));
            $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention,
                array('action' => $this->generateUrl('obtention-distinction_add', array('member'=>$idMembre))));

        } else {
            /*
             * Modification
             */
            //TODO: pas testé
            $obtention = $em->getRepository('AppBundle:ObtentionDistinction')->find($idObtentionDistinction);
            $obtentionForm = $this->createForm(new ObtentionDistinctionType(), $obtention,
                array('action' => $this->generateUrl('obtention-distinction_edit', array('obtention'=>$idObtentionDistinction))));

        }

        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
                'form' => $obtentionForm->createView())
        );
    }


    /**
     * @Route("/add", name="obtention-distinction_add", options={"expose"=true})
     *
     * @param Request $request
     * @return Response
     */
    public function addObtentionDistinctionAction(Request $request)
    {
        $newObtention = new ObtentionDistinction();
        $newObtentionForm = $this->createForm(new ObtentionDistinctionType(), $newObtention);

        $newObtentionForm->handleRequest($request);

        if($newObtentionForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($newObtention);
            $em->flush();

            return new JsonResponse(true);
        }

        return $this->render('AppBundle:ObtentionDistinction:obtention-distinctions_form_modal.html.twig', array(
            'form' => $newObtentionForm->createView(),
            'postform' => $newObtentionForm));
    }

    /**
     * @Route("/edit/{obtention-distinction}", name="obtention-distinction_edit", options={"expose"=true})
     *
     * @param Request $request
     * @param ObtentionDistinction $obtention
     * @return Response
     * @ParamConverter("obtention", class="AppBundle:ObtentionDistinction")
     */
    public function editObtentionDistinction(ObtentionDistinction $obtention, Request $request)
    {
        //TODO: modifier une obtention (ou peut-être ne veut-on que les supprimer ?)
    }
}

?>