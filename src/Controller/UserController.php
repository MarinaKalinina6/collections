<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup')]
    public function registration(
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        Security $security,
    ): Response {
        $formBuilder = $this->createFormBuilder()
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 5, 'max' => 20]),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options' => [
                    'label' => 'Password',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['min' => 5, 'max' => 20]),
                    ],
                ],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit']);

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $user = new User();
            $user->setUsername($data['username']);

            $findUsername = $entityManager
                ->getRepository(User::class)
                ->findOneBy(['username' => $data['username']]);
            if ($findUsername !== null) {
                $form->get('username')->addError(new FormError('Username already exists'));
            }

            if ($form->isValid() === true) {
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $data['password'],
                    ),
                );

                $entityManager->persist($user);
                $entityManager->flush();

                $security->login($user);

                return $this->redirectToRoute('main');
            }
        }

        return $this->render('signup.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/signin', name: 'signin')]
    public function login(authenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $formBuilder = $this->createFormBuilder(null, ['csrf_field_name' => 'token', 'csrf_token_id' => 'signin'])
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
                'data' => $lastUsername,
            ])
            ->add('password', PasswordType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Submit']);


        return $this->render('signin.html.twig', [
            'form' => $formBuilder->getForm(),
            'error' => $error,
        ]);
    }
}