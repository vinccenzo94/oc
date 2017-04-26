<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 24/04/2017
 * Time: 10:53
 */

// src/OC/PlatformBundle/Purge/OCPurge.php

namespace OC\PlatformBundle\Purge;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class OCPurge : Système de nettoyage des entités
 * @package OC\PlatformBundle\Purge
 */
class OCPurge
{
  /**
   * @var EntityManager
   */
  private $em;

  /**
   * OCPurge constructor.
   * @param EntityManager $em
   */
  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  /**
   * Supprimer les annonces antérieures à $days
   *
   * @param $days
   * @return array
   */
  public function purgerAdvert($days)
  {
    // On récupère toutes les annonces qui sont antérieures à $days
    $queryAdverts = $this->em->createQueryBuilder()
      ->select('a')
      ->from('OCPlatformBundle:Advert', 'a')
      ->where('a.date < :date')
      ->setParameter('date', new \DateTime('-'.$days.' day'))
    ;

    $listAvertsToDelete = $queryAdverts
      ->getQuery()
      ->getResult()
    ;

    foreach($listAvertsToDelete as $Advert)
    {
      // On récupère toutes les candidatures associées à l'annonce
      $listAvertApplications = $Advert->getApplications();

      foreach ($listAvertApplications as $Application)
      {
        // On supprime les candidatures
        $Advert->removeApplication($Application);
        $this->em->remove($Application);
      }

      // On supprime les annonces
      $this->em->remove($Advert);
    }

    $this->em->flush();

    return $listAvertsToDelete;
  }
}