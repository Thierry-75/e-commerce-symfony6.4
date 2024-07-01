<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use App\Entity\Image;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ImageFixtures extends Fixture implements DependentFixtureInterface
{
    
    private Generator $faker;

    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');

    }
    
    public function load(ObjectManager $manager): void
    {
        $this->faker->addProvider(new PicsumPhotosProvider($this->faker));

        for($i=0;$i<900;$i++)
        {
            $image = new Image();
            $image->setName($this->faker->imageUrl(500,500));
            $product = $this->getReference('prod-' .Rand(1,10));
            $image->setProduct($product);
            $manager->persist($image); 
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ProductFixtures::class];
    }
}
