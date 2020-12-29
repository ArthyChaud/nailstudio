<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/registration", name="user_registration", methods={"GET","POST"})
     */
    public function registration(Request $request)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('security/registration.html.twig');
        }
        if(!$this->isCsrfTokenValid('form_client', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire depense');
        }
        $donnees['username']=$_POST['username'];
        $donnees['password']=$_POST['password'];
        $donnees['email']=$_POST['email'];

        //dd($donnees);
        $erreurs=$this->validatorDepense($donnees);
        #dd($erreurs);
        if( empty($erreurs))
        {
            $client = new User();
            $password = $this->passwordEncoder->encodePassword($client, $donnees['password']);
            $client->setPassword($password);
            $client->setRoles(['ROLE_CLIENT'])->setUsername($donnees['username'])
                ->setEmail($donnees['email'])->setIsActive('1');
            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('app_login');
        }
        return $this->render('security/registration.html.twig', ['donnees'=>$donnees,'erreurs'=>$erreurs]);
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    public function validatorDepense($donnees){
        $erreurs = array();

        if(strcmp($donnees['username'],'')==0)
            $erreurs['username'] = 'Veuillez entrer une username';

        if(strcmp($donnees['password'],'')==0)
            $erreurs['password'] = 'Veuillez entrer une password';

        if(strcmp($donnees['email'],'')==0)
            $erreurs['email'] = 'Veuillez entrer une email';

        return $erreurs;
    }
}
