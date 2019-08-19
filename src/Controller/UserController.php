<?php

namespace App\Controller;

use App\Entity\User;

use DOMDocument;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Verdant\XML2Array;
use XSLTProcessor;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\EasyAdminFormType;
use EasyCorp\Bundle\EasyAdminBundle\Event\EasyAdminEvents;


/**
 * @Route("/user")
 */
class UserController extends EasyAdminController
{
    /**
     * @Route("/", name="easyadmin")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */


    public function indexAction(Request $request)
    {

        //echo "aqui";
        //die();


        $this->initialize($request);

        if (null === $request->query->get('entity')) {
            return $this->redirectToBackendHomepage();
        }

        $action = $request->query->get('action', 'list');
        if (!$this->isActionAllowed($action)) {
            throw new ForbiddenActionException(array('action' => $action, 'entity' => $this->entity['name']));
        }

        if (isset($this->entity['permissions'][$action])) {
            $this->denyAccessUnlessGranted($this->entity['permissions'][$action],"403","Acesso Negado");
            //throw new AccessDeniedException('Need ROLE_USER!');
        }

        //echo $action; die();



        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

}