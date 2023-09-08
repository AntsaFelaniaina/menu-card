<?php

namespace App\Controller;

use App\Entity\Dishes;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    public function __construct(private ManagerRegistry $doctrine) {}

    #[Route('/order', name: 'orders')]
    public function index(OrderRepository $orderRepo): Response
    {
        $order = $orderRepo->findBy(
            ['tableorder' => 'table1']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $order,
        ]);
    }

    #[Route('/order/{id}', name: 'order')]
    public function order(Dishes $dishes)
    {
        $order = new Order();
        $order->setTableorder('table1');
        $order->setName($dishes->getName());
        $order->setPrice($dishes->getPrice());
        $order->setOrderNumber($dishes->getId());
        $order->setStatus('open');

        //entityManager
        $conn = $this->doctrine->getManager();
        $conn->persist($order);
        $conn->flush();

        //output a flash message
        $this->addFlash('order', $order->getName(). ' was added to the order');

        return $this->redirect($this->generateUrl('menu'));
    }

    #[Route('/status/{id},{status}', name: 'status')]
    public function status($id, $status){
        $conn = $this->doctrine->getManager();
        $order = $conn->getRepository(Order::class)->find($id);

        $order->setStatus($status);
        $conn->flush();

        return $this->redirect($this->generateUrl('orders'));
    }

    #[Route('/delete/{id}', name: 'deleteOrder')]
    public function delete($id, OrderRepository $orderRepo){
        //EntityManager
        $conn = $this->doctrine->getManager();
        $order = $orderRepo->find($id);
        $conn->remove($order);
        $conn->flush();

        //message
        $this->addFlash('success', 'the order was removed carefully');

        return $this->redirect($this->generateUrl('orders'));
    }
}
