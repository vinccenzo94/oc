<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 25/04/2017
 * Time: 10:25
 */

namespace OC\PlatformBundle\DataFixtures\ORM;


use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;

class LoadAvertApplications extends AbstractFixture implements OrderedFixtureInterface
{
  public function load(ObjectManager $manager)
  {
    // -- Candidature #1
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setEmail('vgrimelli@easyvista.com');
    $advert->setContent('Nous recherchons un développeur Symfony débutant sur Lyon. Blabla...');
    $advert->setDate('2017-04-01');

    // Création de l'entité Image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');

    // On lie l'image à l'annonce
    $advert->setImage($image);

    // Création d'une première candidature
    $application1 = new Application();
    $application1->setAuthor('Marine');
    $application1->setEmail('marine@club-internet.fr');
    $application1->setContent("J'ai toutes les qualités requises.");

    // Création d'une deuxième candidature par exemple
    $application2 = new Application();
    $application2->setAuthor('Pierre');
    $application2->setEmail('pierre@neuf.com');
    $application2->setContent("Je suis très motivé.");

    // On lie candidatures à l'annonce
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);
    
    // On récupère toutes les compétences possibles
    $listSkills = $manager->getRepository('OCPlatformBundle:Skill')->findAll();

    // Pour chaque compétence
    foreach ($listSkills as $skill) {
      // On crée une nouvelle "relation entre 1 annonce et 1 compétence"
      $advertSkill = new AdvertSkill();

      // On la lie à l'annonce, qui est ici toujours la même
      $advertSkill->setAdvert($advert);
      // On la lie à la compétence, qui change ici dans la boucle foreach
      $advertSkill->setSkill($skill);

      // Arbitrairement, on dit que chaque compétence est requise au niveau expert
      $advertSkill->setLevel('Novice');

      // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
      $manager->persist($advertSkill);
    }

    // Etape 1 : On "persiste" l'entité
    $manager->persist($advert);

    // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
    // on devrait persister à la main l'entité $image
    // $manager->persist($image);

    // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    $manager->persist($application1);
    $manager->persist($application2);

    // Étape 2 : On déclenche l'enregistrement
    //$manager->flush();

    // -- Candidature #2
    $advert = new Advert();
    $advert->setTitle('Recherche Graphiste Photoshop.');
    $advert->setAuthor('Julien');
    $advert->setEmail('julien@societe.com');
    $advert->setContent('Nous recherchons un graphiste sénior sur Paris. Blabla...');
    $advert->setDate('2017-03-01');

    // Création de l'entité Image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');

    // On lie l'image à l'annonce
    $advert->setImage($image);

    // Création d'une première candidature
    $application1 = new Application();
    $application1->setAuthor('Cyril');
    $application1->setEmail('cyril@masociete.fr');
    $application1->setContent("J'ai toutes les qualités requises.");

    // Création d'une deuxième candidature par exemple
    $application2 = new Application();
    $application2->setAuthor('Sébastien');
    $application2->setEmail('sebastien@masociete.com');
    $application2->setContent("Je suis très motivé.");

    // On lie candidatures à l'annonce
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);

    // Pour chaque compétence
    foreach ($listSkills as $skill) {
      // On crée une nouvelle "relation entre 1 annonce et 1 compétence"
      $advertSkill = new AdvertSkill();

      // On la lie à l'annonce, qui est ici toujours la même
      $advertSkill->setAdvert($advert);
      // On la lie à la compétence, qui change ici dans la boucle foreach
      $advertSkill->setSkill($skill);

      // Arbitrairement, on dit que chaque compétence est requise au niveau expert
      $advertSkill->setLevel('Expert');

      // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
      $manager->persist($advertSkill);
    }

    // Etape 1 : On "persiste" l'entité
    $manager->persist($advert);

    // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
    // on devrait persister à la main l'entité $image
    // $manager->persist($image);

    // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    $manager->persist($application1);
    $manager->persist($application2);

    // Étape 2 : On déclenche l'enregistrement
    //$manager->flush();

    // -- Candidature #3
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Delphi.');
    $advert->setAuthor('Michel');
    $advert->setEmail('michel@societe.com');
    $advert->setContent('Nous recherchons un développeur delphi sénior sur Noisy-le-Grand. Blabla...');
    $advert->setDate('2017-02-01');

    // Création de l'entité Image
    $image = new Image();
    $image->setUrl('http://sdz-upload.s3.amazonaws.com/prod/upload/job-de-reve.jpg');
    $image->setAlt('Job de rêve');

    // On lie l'image à l'annonce
    $advert->setImage($image);

    // Création d'une première candidature
    $application1 = new Application();
    $application1->setAuthor('Jean-Paul');
    $application1->setEmail('jpp@masociete.fr');
    $application1->setContent("J'ai toutes les qualités requises.");

    // Création d'une deuxième candidature par exemple
    $application2 = new Application();
    $application2->setAuthor('Paul');
    $application2->setEmail('paul@masociete.com');
    $application2->setContent("Je suis très motivé.");

    // On lie candidatures à l'annonce
    $application1->setAdvert($advert);
    $application2->setAdvert($advert);

    // Pour chaque compétence
    foreach ($listSkills as $skill) {
      // On crée une nouvelle "relation entre 1 annonce et 1 compétence"
      $advertSkill = new AdvertSkill();

      // On la lie à l'annonce, qui est ici toujours la même
      $advertSkill->setAdvert($advert);
      // On la lie à la compétence, qui change ici dans la boucle foreach
      $advertSkill->setSkill($skill);

      // Arbitrairement, on dit que chaque compétence est requise au niveau expert
      $advertSkill->setLevel('Expert');

      // Et bien sûr, on persiste cette entité de relation, propriétaire des deux autres relations
      $manager->persist($advertSkill);
    }

    // Etape 1 : On "persiste" l'entité
    $manager->persist($advert);

    // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
    // on devrait persister à la main l'entité $image
    // $manager->persist($image);

    // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    $manager->persist($application1);
    $manager->persist($application2);

    // Étape 2 : On déclenche l'enregistrement
    $manager->flush();
  }

  public function getOrder()
  {
    // TODO: Implement getOrder() method.
    return 3;
  }
}