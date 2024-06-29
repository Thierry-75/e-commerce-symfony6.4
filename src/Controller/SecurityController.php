<?php

namespace App\Controller;

use App\Service\SendMailService;
use App\Form\RequestPasswordType;
use App\Form\ResetPasswordType;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_profile_index');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forget-pass', name: 'forgotten-password', methods: ['GET', 'POST'])]
    public function forgottenPassword(
        Request $request,
        ValidatorInterface $validator,
        UsersRepository $usersRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $em,
        SendMailService $mail
    ): Response {
        $form_request_password = $this->createForm(RequestPasswordType::class);
        $form_request_password->handleRequest($request);
        if ($request->isMethod('POST')) {
            $error = $validator->validate($request);
            if (count($error) > 0) {
                return $this->render('security/reset_password_request.html.twig', ['form_request_password' => $form_request_password->createView(), 'error' => $error]);
            }
            if ($form_request_password->isSubmitted() && $form_request_password->isValid()) {
                $user = $usersRepository->findOneByEmail($form_request_password->get('email')->getData());
                if (isset($user)) {
                    //token
                    $token = $tokenGeneratorInterface->generateToken();
                    $user->setResetToken($token);
                    $em->persist($user);
                    $em->flush();
                    //link init
                    $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                    //email
                    $context = ['url' => $url, 'user' => $user];
                    $mail->send(
                        'no-reply@e-commerce.com',
                        $user->getEmail(),
                        'Réinitialisation du password',
                        'password_reset',  // template
                        $context
                    );
                    $this->addFlash('success', 'email envoyé avec succès');
                    return $this->redirectToRoute('app_login');
                }
                $this->addFlash('danger', 'Un problème est survenue');
                return $this->redirectToRoute('app_login');
            }
        }
        return $this->render('security/reset_password_request.html.twig', ['form_request_password' => $form_request_password->createView()]);
    }

    #[Route('/oubli-pass/{token}', name: 'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UsersRepository $usersRepository,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        //ckeck token
        $user = $usersRepository->findOneByResetToken($token);
        if (isset($user)) {
            $form_reset = $this->createForm(ResetPasswordType::class);
            $form_reset->handleRequest($request);
            if ($request->isMethod('POST')) {
                $error = $validator->validate($request);
                if (count($error) > 0) {
                    return $this->render('security/reset_password.html.twig', ['form_request_password' => $form_reset->createView(), 'error' => $error]);
                }
                if ($form_reset->isSubmitted() && $form_reset->isValid()) {
                    $user->setResetToken('');
                    $user->setPassword(
                        $passwordHasher->hashPassword($user, $form_reset->get('password')->getData())
                    );
                    $em->persist($user);
                    $em->flush();

                    $this->addFlash('success', 'Mot de passe changé avec succès');
                    return $this->redirectToRoute('app_login');
                }
            }
            return $this->render('security/reset_password.html.twig', ['form_reset' => $form_reset->createView()]);
        }
        $this->addFlash('danger', 'Jeton invalid');
        return $this->redirectToRoute('app_login');
    }
}
