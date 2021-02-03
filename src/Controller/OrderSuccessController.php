<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    /**
     * OrderValidateController constructor.
     * @param $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     * @param Cart $cart
     * @param $stripeSessionId
     * @return Response
     */
    public function index(Cart $cart, $stripeSessionId): Response
    {
        $order = $this->entityManager->getRepository(Order::class)->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if ($order->getState() == 0){
            // Vider la session "cart"
            $cart->remove();
            // Modifier le statut de isPaid de notre commande en mettant 1
            $order->setState(1);
            $this->entityManager->flush();

            $mail = New Mail();
            $content = 'Bonjour '.$order->getUser()->getFullName().'<br>Merci pour votre commmande.<br><br>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur cumque cupiditate laudantium nemo placeat, quod tenetur totam? Adipisci architecto at blanditiis, delectus, deserunt doloribus ipsam recusandae similique soluta tenetur velit.';
            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFullName(), 'Votre commande sur la Boutique Portugaise est bien validée.', $content);
        }

        // Afficher les quelques informations de la commande de l'utilisateur

        return $this->render('order_success/index.html.twig', [
            'order' => $order
        ]);
    }
}
