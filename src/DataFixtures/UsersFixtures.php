<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Generator;
use Faker\Factory;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{


    private Generator $faker;

    public function __construct(private UserPasswordHasherInterface $passwordEncoder)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {

        $admin = new Users();
        $admin->setEmail('admin@gmx.fr')
              ->setLastname('dothee')
              ->setFirstname('thierry')
              ->setAddress($this->faker->streetAddress())
              ->setZipcode(str_replace(' ','',$this->faker->postcode()))
              ->setCity($this->faker->city())
              ->setPassword(
                    $this->passwordEncoder->hashPassword($admin,'ArethiA75!')
              )
              ->setCreatedAt(new  \DateTimeImmutable())
              ->setRoles(['ROLE_ADMIN'])
              ->setIsVerified(true);
            $manager->persist($admin);

        for($i = 0; $i < 5; $i++)
        {
            $user  = new Users();
            $user->setEmail($this->faker->email())
                 ->setLastname($this->faker->lastName())
                 ->setFirstname($this->faker->firstName())
                 ->setAddress($this->faker->streetAddress())
                 ->setZipcode(str_replace(' ','',$this->faker->postcode()))
                 ->setCity($this->faker->city())
                ->setPassword(
                    $this->passwordEncoder->hashPassword($user,'ArethiA75!')
                )
                ->setCreatedAt(new \DateTimeImmutable())
                ->setRoles(['ROLE_USER'])
                ->setIsVerified(mt_rand(0,1) === 1 ? true : false );
            $manager->persist($user);
        }
        $manager->flush();
    }
}
