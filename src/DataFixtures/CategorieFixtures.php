<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategorieFixtures extends Fixture
{
    private $count = 1;

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique',null,$manager);
        $this->createCategory('Ordinateur portable', $parent,$manager);         
        $this->createCategory('Ecran', $parent,$manager);
        $this->createCategory('Souris', $parent,$manager);  
        
        $parent = $this->createCategory('Mode',null,$manager);
        $this->createCategory('Homme', $parent,$manager);         
        $this->createCategory('Femme', $parent,$manager);
        $this->createCategory('Enfant', $parent,$manager); 

        $manager->flush();
    }

    public function createCategory(string $name,Categorie $parent = null,ObjectManager $manager)
    {
        $categorie = new Categorie();
        $categorie->setName($name)
                  ->setSlug($this->slugger->slug($categorie->getName())->lower())
                  ->setParent($parent);
                  
               
        $manager->persist($categorie);
        $this->addReference('cat-'.$this->count,$categorie);
        $this->count++;
        return $categorie;
    }
}
