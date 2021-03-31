<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

class SecurityController extends AbstractController
{
    private $passwordEncoder;
    private $token;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $token)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->token = $token;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/registration", name="user_registration", methods={"GET","POST"})
     * @param Request $request
     * @param MailerInterface $mailer
     * @return RedirectResponse|Response
     * @throws TransportExceptionInterface
     */
    public function registration(Request $request, MailerInterface $mailer)
    {
        if($request->getMethod() == 'GET'){
            return $this->render('security/registration.html.twig');
        }
        if(!$this->isCsrfTokenValid('form_registration', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire register');
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
                ->setEmail($donnees['email'])->setIsActive('0')
                ->setTokenMail($this->token->generateToken());
            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();

            $link = 'http://127.0.0.1:8000/login/'.$client->getTokenMail();

            $email = new Email();
            $email->from(new Address('maxime.noel2000@gmail.com', 'Support Contact'))
                ->to(new Address($client->getEmail()))
                ->subject("Account validation")
                ->text("Hello ".$client->getUsername().",\n".
                    "\n".
                    "click here to validate and activate your account : ".$link);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                throw $e;
            }

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

    /**
     * @Route("/login/{token}", name="app_login_token", methods={"GET"})
     * @param UserRepository $userRepository
     * @param String|null $token
     * @return Response
     */
    public function loginWithToken(UserRepository $userRepository, String $token): Response
    {
        if($userRepository->findToken($token) != null) {
            $user = $userRepository->find($userRepository->findToken($token)[0]);
            if($user->getTokenMail() == $token && $user->getIsActive()==0) {
                $user->setIsActive(1);
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success_account',"Compte validé avec succès");
            }
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/forgottenpassword", name="forgotten_password", methods={"GET","POST"})
     * @param UserRepository $userRepository
     * @param MailerInterface $mailer
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     */
    public function forgottenPassword(UserRepository $userRepository, MailerInterface $mailer, Request $request): Response
    {
        if($request->getMethod() == 'GET'){
            return $this->render('security/mailPassword.html.twig');
        }

        if(!$this->isCsrfTokenValid('form_forgotten_password', $request->get('token'))) {
            throw new  InvalidCsrfTokenException('Invalid CSRF token formulaire forgotten password');
        }

        // Vérification du mail
        $email = $_POST['email'];
        if(strcmp($email,'')==0) {
            $erreurs['email'] = 'Veuillez entrer une email';
        } else {
            $erreurs = NULL;
        }
        if ($erreurs != NULL) {
            return $this->render('security/mailPassword.html.twig',[
                'erreurs' => $erreurs
            ]);
        }
        $id = $userRepository->findByMail($email)[0]['id'];
        $client = $this->getDoctrine()->getRepository(User::class)->find($id);

        if ($client) {
            $client->setTokenMail($this->token->generateToken());
            $this->getDoctrine()->getManager()->persist($client);
            $this->getDoctrine()->getManager()->flush();

            $link = 'http://127.0.0.1:8000/resetpassword/'.$client->getTokenMail();

            $email = new Email();
            $email->from(new Address('maxime.noel2000@gmail.com', 'Support mot de passe'))
                ->to(new Address($client->getEmail()))
                ->subject("Mot de passe oublié")
                ->text("Hello ".$client->getUsername().",\n".
                    "\n".
                    "click here to validate and activate your account : ".$link);
            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                throw $e;
            }

            $this->addFlash('forgotten_password','Mail envoyé avec succès !');
            return $this->redirectToRoute('index_index');
        }

        $erreurs = "Votre email n'existe pas.";

        return $this->render('security/mailPassword.html.twig',[
            'erreurs' => $erreurs
        ]);
    }

    /**
     * @Route("/resetpassword/{token}", name="reset_password_token")
     * @param UserRepository $userRepository
     * @param String|null $token
     * @param Request $request
     * @return Response
     */
    public function resetPasswordToken(UserRepository $userRepository, String $token, Request $request): Response
    {
        if($userRepository->findToken($token) != null) {
            $user = $userRepository->find($userRepository->findToken($token)[0]);
            if($user->getTokenMail() == $token && $user->getIsActive()==1) {
                if ($request->getMethod() == 'GET') {
                    return $this->render('security/resetPassword.html.twig',[
                        'id' => $user->getTokenMail()
                    ]);
                }
            }
        }
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/changepassword/{id}", name="change_password")
     * @param String $id
     * @param UserRepository $userRepository
     * @return Response
     */
    public function resetPassword(String $id, UserRepository $userRepository) {
        if ($userRepository->findToken($id) != null) {
            $user = $userRepository->find($userRepository->findToken($id)[0]);
            if ($_POST['mdp1'] == $_POST['mdp2']) {
                $password = $this->passwordEncoder->encodePassword($user, $_POST['mdp1']);
                $user->setPassword($password)
                    ->setTokenMail($this->token->generateToken());
                $this->getDoctrine()->getManager()->persist($user);
                $this->getDoctrine()->getManager()->flush();
                return $this->redirectToRoute('app_login');
            } else {
                $erreurs = "Les mots de passes ne matchent pas";
                return $this->render('security/resetPassword.html.twig', [
                    'erreurs' => $erreurs,
                    'id' => $id
                ]);
            }
        }
        return $this->render('security/login.html.twig');
    }
}
