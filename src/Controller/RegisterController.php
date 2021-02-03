<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/inscription", name="register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function index(Request $request, UserPasswordEncoderInterface $encoder): Response
    {
        $notification = null;
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

            $search_email = $this->entityManager->getRepository(User::class)->findOneByEmail($user->getEMail());

            if(!$search_email) {
                $password = $encoder->encodePassword($user, $user->getPassword());

                $user->setPassword($password);

                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $mail = New Mail();
                $content = 'Bonjour '.$user->getFullName().'<br>Bienvenue sur la première boutique dédié au made in Portugal.<br><br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cumque cupiditate laudantium nemo placeat, quod tenetur totam? Adipisci architecto at blanditiis, delectus, deserunt doloribus ipsam recusandae similique soluta tenetur velit.';
                $mail->send($user->getEmail(), $user->getFullName(), 'Bienvenue sur la Boutique Portugaise', $content);

                $notification ='Votre inscription s\'est correctement déroulée';
            } else {
                $notification ='L\'email que vous avez renseigné existe déja';
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView(),
            'notification' => $notification
        ]);
    }
}
