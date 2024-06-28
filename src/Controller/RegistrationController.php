<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Security\UsersAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, 
    Security $security, EntityManagerInterface $entityManager, SendMailService $mail, JWTService $jwt): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            )
            ->setRoles(['ROLE_USER']);
            $entityManager->persist($user);
            $entityManager->flush();

            // jwt
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];
            $payload = [
                 'user_id' =>$user->getId()  
            ];

            $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));

            // do anything else you need here, like send an email
            $mail->send('no-reply@e-commerce.com',
            $user->getEmail(),
            'Activation de votre compte sur e-commerce.com',
            'register',
            [
                'user'=>$user, 'token'=>$token
            ]
        );

            return $security->login($user, UsersAuthenticator::class, 'main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verif/{token}',name:'verify_user')]
    public function verifyUser($token,JWTService $jwt,UsersRepository $userRepository,EntityManagerInterface $em): Response
    {
        // check token valid and valid and clean
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token,$this->getParameter('app.jwtsecret'))){
            $payload  = $jwt->getPayload($token);
            $user = $userRepository->find($payload['user_id']);
            //check user and activiation
            if($user && !$user->getIsVerified()){
                $user->setIsVerified(true);
                $em->flush($user);
                $this->addFlash('success','L\'utilisateur activé !');
                return $this->redirectToRoute('app_profile_index');

            }

        }
        $this->addFlash('danger','Le token est invalid ou a expiré !');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif',name:'resend_verif')]
    public function resendVerif(JWTService $jwt,SendMailService $mail,UsersRepository $userRepository): Response
    {
        $user = $this->getUser();
        if(!$user){
            $this->addFlash('danger','Vous devez être connecté pour accéder à cette passe');
            return $this->redirectToRoute('app_login');
        }
        if($user->getIsVerified()){
            $this->addFlash('danger','Cet utilisateur est déjà activé');
            return $this->redirectToRoute('app_profile_index');
        }

                    // jwt
                    $header = [
                        'typ' => 'JWT',
                        'alg' => 'HS256'
                    ];
                    $payload = [
                         'user_id' =>$user->getId()  
                    ];
        
                    $token = $jwt->generate($header,$payload,$this->getParameter('app.jwtsecret'));
        
                    // do anything else you need here, like send an email
                    $mail->send('no-reply@e-commerce.com',
                    $user->getEmail(),
                    'Activation de votre compte sur e-commerce.com',
                    'register',
                    [
                        'user'=>$user, 'token'=>$token
                    ]
                );
                $this->addFlash('success','email de vérification envoyé !');
                return $this->redirectToRoute('app_profile_index');
                
    }
}
