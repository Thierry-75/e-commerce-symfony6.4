<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductFixtures extends Fixture
{

    private Generator $faker;

    public function __construct(private SluggerInterface $slugger)
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for($prod= 1;$prod<=100;$prod++)
        {
            $product = new Product();
            $product->setName($this->faker->text(15))
                    ->setDescription($this->faker->text())
                    ->setSlug($this->slugger->slug($product->getName())->lower())
                    ->setPrice($this->faker->randomFloat(2,100,500))
                    ->setStock($this->faker->numberBetween(0,30))
                    ->setCreatedAt(new \DateTimeImmutable());
                    $category = $this->getReference('cat-'. rand(1,20));
                    $product->setCategorie($category);
                    $this->setReference('prod-'.$prod,$product);
                    $manager->persist($product);
                    
        }

        $manager->flush();
    }
}
