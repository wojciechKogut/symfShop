<?php

namespace App\Controller;

use App\Entity\ResetPassword;
use App\Form\ResetPasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/account")
 */
class AccountController extends Controller
{
    /**
     * @Route("/", name="account_index")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        return $this->profile($request, $passwordEncoder);
    }

    /**
     * @Route("/profile", name="account_profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function profile(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $user = new ResetPassword();
        $form = $this->createForm(ResetPasswordType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $password = $passwordEncoder->encodePassword($user, $user->getNewPassword());
            $loggedUser = $this->getUser();
            $loggedUser->setPassword($password);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($loggedUser);
            $entityManager->flush();

            return $this->redirect('/');
        }

        return $this->render('account/account.html.twig', ['form' => $form->createView()]);
    }
}