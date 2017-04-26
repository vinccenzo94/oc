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

class OCPurge
{
  private $em;

  public function __construct(EntityManager $em)
  {
    $this->em = $em;
  }

  public function __purgerAdvert($days)
  {
    $queryAdverts = $this->em->createQueryBuilder()
      ->delete('Advert', 'a')
      ->where('a.date < DATE_SUB(CURDATE(), INTERVAL :days DAY)')
      ->setParameter('days', $days)
      ;

    return $queryAdverts
      ->getQuery()
      ->getResult()
      ;
  }

  public function _purgerAdvert($days)
  {
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
      $this->em->remove($Advert);
    }

    $this->em->flush();

    return $listAvertsToDelete;
  }

  public function purgerAdvert($days)
  {
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
        $Advert->removeApplication($Application);
        $this->em->remove($Application);
      }

      $this->em->remove($Advert);
    }

    $this->em->flush();

    return $listAvertsToDelete;
  }
}