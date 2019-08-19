<?php


namespace App\Controller;

//use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;
use EasyCorp\Bundle\EasyAdminBundle\Exception\ForbiddenActionException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends EasyAdminController
{
    public function createNewUserEntity()
    {
        return $this->get('fos_user.user_manager')->createUser();
    }

    public function persistUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::persistEntity($user);
    }

    public function updateUserEntity($user)
    {
        $this->get('fos_user.user_manager')->updateUser($user, false);
        parent::updateEntity($user);
    }

    private function checkPermissions()
    {
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw $this->createAccessDeniedException();
        }

        $easyAdmin = $this->request->attributes->get('easyadmin');

        $action = $this->request->query->get('action');

        $perms = $easyAdmin['entity'][$action]['require_permission'];
        $roles = $this->get('security.context')->getToken()->getUser()->getRoles();

        foreach ($roles as $key => $value)
        {

            $permessi_file = $value;

            if (in_array($permessi_file, $perms)) {

                $requiredPermission = $permessi_file;

            }
            else
            {
                $view = $easyAdmin['view'];
                $entity = $easyAdmin['entity']['name'];
                $requiredPermission = 'ROLE_'.strtoupper($view).'_'.strtoupper($entity);
                # Or any other default strategy
            }
            $this->denyAccessUnlessGranted(
                $requiredPermission, null, $requiredPermission.' permission required'
            );
        }

    }
    /**
     * @Route("/", name="easyadmin")
     *
     * @param Request $request
     *
     * @return RedirectResponse|Response
     */


    public function indexAction(Request $request)
    {

        //echo $request;

       // die();
        $this->initialize($request);

        if (null === $request->query->get('entity')) {
            return $this->redirectToBackendHomepage();
        }

        $action = $request->query->get('action', 'list');
        if (!$this->isActionAllowed($action)) {
            throw new ForbiddenActionException(array('action' => $action, 'entity' => $this->entity['name']));
        }

        if (isset($this->entity['permissions'][$action])) {
            $this->denyAccessUnlessGranted($this->entity['permissions'][$action]);
        }



        return $this->executeDynamicMethod($action.'<EntityName>Action');
    }

}