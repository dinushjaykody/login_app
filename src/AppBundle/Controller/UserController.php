<?php
// src/AppBundle/Controller/UserController.php
namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/admin", name="admin")
 */
class UserController extends Controller
{

    /**
     * Lists all User entities.
     *
     * @Route("/", name="admin")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:User')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="admin_create")
     * @Method("POST")
     * @Template("AppBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $this->get('app_bundle.user_manager')
                ->setUserPassword($entity, $entity->getPassword());
            $role = ($form->get('isAdmin')->getData()) ? 'ROLE_ADMIN' : 'ROLE_USER';
            $entity->setRoles(array($role));
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('admin_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('admin_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Create User',
            'attr'  => array('class' => 'btn btn-lg btn-primary')
        ));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="admin_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="admin_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="admin_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }


    //We have two things to do!
    //1. Create the view/display the form

    /**
     *   Displays a form to edit the password for existing User Entity
     *
     * @Route("/{id}/password", name="admin_password")
     * @Method("GET")
     * @Template()
     */
    public function passwordAction($id)
    {
        //get the entity manager
        //use the em to get our entity for the id given
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Undable to find User entity!');
        }

        $passwordForm = $this->createPasswordForm($entity);

        // pass objects to the view
        // because we are using the namespaced Template Class 
        // there is no need to call $this->render()
        return array(
            'entity' => $entity,
            'password_form' => $passwordForm->createView(),
        );

    }

    /**
     *   Creates a change password form!
     * @param User $entity
     * @return \Symfony\Component\Form\Form $passwordForm
     */
    private function createPasswordForm(User $entity)
    {
        // tell symfony to create a Form using the UserFormType
        // and the entity we pulled from the em a few lines back
        // must pass in the route where the form will post to
        // must also pass the id!
        $passwordForm = $this->createForm(new UserType, $entity, array(
            'action' => $this->generateUrl(
                'admin_password_update',
                array( 'id'=> $entity->getId())
            ),
            'method' => 'PUT',
        ));

        // remove the fields we don't want from the FormType Object
        $passwordForm->remove('username');
        $passwordForm->remove('firstname');
        $passwordForm->remove('lastname');
        $passwordForm->remove('email');

        // add a submit button
        $passwordForm->add('submit', 'submit', array(
            'label' => 'Save New Password',
            'attr' => array('class' => 'btn btn-lg btn-primary'),
        ));
        return $passwordForm;
    }




    //2. Handle the form! Then redirect

    /**
     * @Route("/{id}/password_update", name="admin_password_update")
     * @Method("PUT")
     * @Template("AppBundle:User:password.html.twig")
     */
    public function updatePasswordAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Undable to find User entity!');
        }

        $passwordForm = $this->createPasswordForm($entity);
        // symfony will handle the request
        $passwordForm->handleRequest($request);
        // check to see if the object is valid!
        if( $passwordForm->isValid())
        {
            // use our service to encode the password
            $this->get('app_bundle.user_manager')->setUserPassword($entity, $entity->getPassword());
            //the object is already persisted, so flush it to the database
            $em->flush();

            //TO DO! send with a flash message

            //redirect to the show this user page!
            return $this->redirect($this->generateUrl('admin_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'password_form' => $passwordForm->createView(),
        );
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType(), $entity, array(
            'action' => $this->generateUrl('admin_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->remove('password');
        $form->add('submit', 'submit', array(
            'label' => 'Save',
            'attr'  => array('class' => 'btn btn-lg btn-primary')
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="admin_update")
     * @Method("PUT")
     * @Template("AppBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $role = ($editForm->get('isAdmin')->getData()) ? 'ROLE_ADMIN' : 'ROLE_USER';
            $entity->setRoles(array($role));
            $em->flush();

            return $this->redirect($this->generateUrl('admin_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="admin_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array(
                'label' => 'Delete',
                'attr'  => array('class' => 'btn btn-lg btn-danger')
            ))
            ->getForm()
            ;
    }
}
