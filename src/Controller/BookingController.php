<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTime;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route ("/admin/booking")
 */
class BookingController extends AbstractController
{
    /**
     * @Route("/", name="booking_index", methods={"GET"})
     * @param BookingRepository $bookingRepository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(BookingRepository $bookingRepository,  PaginatorInterface $paginator, Request $request): Response
    {

        $bookings = $bookingRepository->findAll();
        $articles = $paginator->paginate(
            $bookings,
            $request->query->getInt('page', 1),
            25
        );
        return $this->render('booking/index.html.twig', [
            'bookings' => count($bookings),
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/create", name="booking")
     * @param Request $request
     * @return Response
     */
    public function show(Request $request, BookingRepository $bookingRepository): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
//        $booking->setCreatedAt(new DateTime());

        if ($form->isSubmitted() && $form->isValid()){
            $check = $bookingRepository->checkRoomIsAvailable($booking);
            if ($check){
                $manager = $this->getDoctrine()->getManager();
                $manager->persist($booking);
                $manager->flush();

                $this->addFlash('success', 'Votre enregistrement a bien été créé');
                return $this->redirectToRoute('booking_index');
            } else {
                $this->addFlash('danger', 'La chambre n\'est pas disponnible pour les dates sélectionnées');
                return $this->redirectToRoute('booking');
            }
        }

        return $this->render('booking/booking.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
