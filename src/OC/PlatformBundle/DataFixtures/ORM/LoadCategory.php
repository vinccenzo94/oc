<?php

/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 13/04/2017
 * Time: 08:52
 */

// src/OC/PlatformBundle/DataFixtures/ORM/LoadCategory.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Category;
use Symfony\Component\Security\Http\Firewall\AbstractPreAuthenticatedListener;

class LoadCategory extends AbstractFixture implements OrderedFixtureInterface
{
  // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
  public function load(ObjectManager $manager)
  {
    // TODO: Implement load() method.

    // Liste des noms de catégorie à ajouter
    $names = array(
      'Développment web',
      'Développement mobile',
      'Graphisme',
      'Intégration',
      'Réseau'
    );

    foreach ($names as $name) {
      // On crée la catégorie
      $category = new Category();
      $category->setName($name);

      // On la persiste
      $manager->persist($category);
    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }

  public function getOrder()
  {
    // TODO: Implement getOrder() method.
    return 2;
  }
}