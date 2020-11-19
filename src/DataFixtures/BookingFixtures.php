<?php

namespace App\DataFixtures;

use App\Entity\Booking;
use App\Entity\Customer;
use App\Entity\Option;
use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class BookingFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->seed(0);

        //Option
        //3 options
        $optionDejeuner = new Option();
        $optionDejeuner
            ->setName("Jacuzzi pour 4 personnes")
            ->setPrice(47.99)
        ;
        $manager->persist($optionDejeuner);

        $optionTV = new Option();
        $optionTV
            ->setName("Télévision cablé 20 chaines payantes")
            ->setPrice(15)
        ;
        $manager->persist($optionTV);

        $optionPark = new Option();
        $optionPark
            ->setName("Place de parking privé")
            ->setPrice(25)
        ;
        $manager->persist($optionPark);
        $manager->flush();

        //Room
        //une 10aine de room
        $rooms = [];
        for ($i=1; $i<=12; $i++){
            $room = new Room();
            $room
                ->setPrice($faker->randomFloat(2, 50, 150))
                ->setName($faker->word)
                ->setNumber(10+$i)
            ;
            if ($i%3 == 0)
                $room->addOption($optionDejeuner);
            if ($i%4 == 0)
                $room->addOption($optionTV);
            if ($i%5 == 0)
                $room->addOption($optionPark);

            $manager->persist($room);
            $rooms[] = $room;
        }
        $manager->flush();

        //Customer
        //un 50aine de customer
        for ($j=0; $j<50; $j++){
            $customers = [];
            $customer = new Customer();
            $gender = ($j%2 == 0) ? 'male' : 'female';
            $customer
                ->setEmail($faker->safeEmail)
                ->setLastname($faker->lastName)
                ->setFirstname($faker->firstName($gender))
            ;

            $manager->persist($customer);
            $customers[] = $customer;
        }
        $manager->flush();

        //Booking
        //10-30 booking/room
        $nbCustomer = count($customers)-1;
        foreach ($rooms as $room) {
            $nbBooking = $faker->numberBetween(10, 30);
            for ($k=0; $k<$nbBooking; $k++){
                $startDate = $faker->dateTimeBetween('-6 months', '+6 months', 'Europe/Paris');
                $startDate->setTime(0,0,0,0);

                $nbJours = $faker->numberBetween(1,10);
                $endDate = (clone $startDate)->modify("+$nbJours days");

                $createdDate = (clone $startDate)->modify("-$nbJours days");

                $booking = new Booking();
                $booking
                    ->setCreatedAt($createdDate)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setRoom($room)
                    ->setCustomer($customers[$faker->numberBetween(0, $nbCustomer)])
                    ->setComment($faker->sentence)
                ;
                $manager->persist($booking);
            }
            $manager->flush();
        }
//        $user = $this->getReference('admin');
    }

    public function getDependencies(){
        return array(
            UserFixtures::class
        );
    }
}
