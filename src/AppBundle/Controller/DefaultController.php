<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/test", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->addRole(User::ROLE_DEFAULT);

            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $this->get('security.password_encoder')
                ->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('registration_success');
        }

        return $this->render(
            'registration/registration.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("registration/success", name="registration_success")
     */
    public function registrationSuccess(Request $request) {
        return new Response('You have been registered successfully');
    }

    /**
     * @Route("/users", name="get_all_users", methods={"GET"})
     */
    public function getAllUsers() {
        $users = $this->getDoctrine()->getRepository('AppBundle:User')->findAll();

        $data = array();
        foreach ($users as $user) {
            $entry = array();

            $entry['firstName'] = $user->getFirstName();
            $entry['lastName'] = $user->getLastName();
            $entry['email'] = $user->getEmail();
            $entry['username'] = $user->getUsername();

            $data[] = $entry;
        }

        return new JsonResponse($data);
    }

    /**
     * @Route("/user", name="post_user", methods={"POST"})
     */
    public function postUser(Request $request) {
        $userData = $request->request;

        $user = new User();
        $user->setFirstName($userData->get('firstName'));
        $user->setLastName($userData->get('lastName'));
        $user->setEmail($userData->get('email'));
        $user->setUsername($userData->get('username'));
        $user->setPassword($userData->get('password'));

        $this->getDoctrine()->getEntityManager()->persist($user);
        $this->getDoctrine()->getEntityManager()->flush();

        $data = array('status' => 200, $user);
        return new JsonResponse($data);
    }
}
