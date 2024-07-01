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
        $parent = $this->createCategory('informatique',null,$manager);
        $this->createCategory('Ordinateur portable', $parent,$manager);         
        $this->createCategory('Ecran', $parent,$manager);
        $this->createCategory('Souris', $parent,$manager);  
        $this->createCategory('Clavier', $parent,$manager); 
        $this->createCategory('Carte graphique', $parent,$manager); 
        $this->createCategory('Mémoire vive', $parent,$manager); 
        $this->createCategory('Imprimante', $parent,$manager); 
        $this->createCategory('CPU', $parent,$manager); 
        $this->createCategory('Disque dur', $parent,$manager); 
        $this->createCategory('Webcam', $parent,$manager); 
        $this->createCategory('Boitier', $parent,$manager); 
        $this->createCategory('Carte son', $parent,$manager); 
        $manager->flush();
        $parent = $this->createCategory('Image',null,$manager);
        $this->createCategory('Téléviseur', $parent,$manager);         
        $this->createCategory('Vidéoprojecteur', $parent,$manager);
        $this->createCategory('Barre de son', $parent,$manager); 
        $this->createCategory('Ecran de projection', $parent,$manager); 
        $this->createCategory('Home Cinéma', $parent,$manager); 
        $this->createCategory('Casque audio', $parent,$manager); 
        $this->createCategory('Hifi', $parent,$manager); 
        $this->createCategory('Meuble tv', $parent,$manager); 

        $manager->flush();
    }

    public function createCategory(string $name,Categorie $parent = null,ObjectManager $manager)
    {
        $categorie = new Categorie();
        $categorie->setName($name)
                  ->setSlug($this->slugger->slug($categorie->getName())->lower())
                  ->setParent($parent)
                  ->setCategoryOrder(random_int(1,19));
                  
               
        $manager->persist($categorie);
        $this->addReference('cat-'.$this->count,$categorie);
        $this->count++;
        return $categorie;
    }
}
