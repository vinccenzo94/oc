<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 28/04/2017
 * Time: 16:21
 */

// src/OC/PlatformBundle/Repository/CategoryRepository.php

namespace OC\PlatformBundle\Repository;


use Doctrine\ORM\EntityRepository;

class CategoryRepository extends EntityRepository
{
  public function getLikeQueryBuilder($pattern)
  {
    return $this
      ->createQueryBuilder('c')
      ->where('c.name LIKE :pattern')
      ->setParameter('pattern', $pattern)
      ;
  }
}