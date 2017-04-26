<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 13/04/2017
 * Time: 11:16
 */

// src/OC/PlatformBundle/DataFixtures/ORM/LoadSkill.php

namespace OC\PlatformBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Skill;


class LoadSkill extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // Liste des noms de compétence à ajouter
    $names = array('PHP', 'Symfony', 'C++', 'Java', 'Photoshop', 'Blender', 'Bloc-note');

    foreach($names as $name) {
      // On crée la compétence
      $skill = new Skill();
      $skill->setName($name);

      // On la persiste
      $manager->persist($skill);
    }

    // On déclenche l'enregistrement de toutes les catégories
    $manager->flush();
  }

  public function getOrder()
  {
    return 1;
  }
}