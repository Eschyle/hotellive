<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Room;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\CustomerRepository;
use DatePeriod;
use DateInterval;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * @param BookingRepository $bookingRepository
     * @return Response
     */
    public function create(Request $request, BookingRepository $bookingRepository, CustomerRepository $customerRepository): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);

        $form->handleRequest($request);
//        $booking->setCreatedAt(new DateTime());

        if ($form->isSubmitted() && $form->isValid()){
            $check = $bookingRepository->checkRoomIsAvailable($booking);
            if ($check){
                //check customer exist
                $customer = $customerRepository->findOneBy([
                    'email' => $booking->getCustomer()->getEmail()
                ]);
                if ($customer) {
                    $booking->setCustomer($customer);
                    $this->addFlash('warning', 'l\'email renseigné existe déjà pour un utilisateur');
                }
                //save
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

    /**
     * @Route ("/unavailable/{id}", name="unavailable_rooms", methods={"GET"})
     * @param Room $room
     * @param BookingRepository $bookingRepository
     * @return Response
     */
    public function getUnavailableDatesByRom(Room $room, BookingRepository $bookingRepository) {
        $bookings = $bookingRepository->getBookingsUnavailable($room, $bookingRepository);
        $unavailableDates = [];
        /** @var Booking $booking */
        foreach ($bookings as $booking) {
            $startDate = $booking->getStartDate();
            $endDate = $booking->getEndDate();
            $interval = new DateInterval('P1D');
            $endDate->add($interval);
            $period = new DatePeriod($startDate, $interval, $endDate);
            foreach ($period as $date) {
                $unavailableDates[] = $date->format('d/m/Y');
//                $unavailableDates[] = $date->format('Y/m/d');
            }
        }
        return new JsonResponse($unavailableDates);
    }

    /**
     * @Route("/{id}", name="booking_show")
     * @return Response
     */
    public function show(Booking $booking){
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }
}
