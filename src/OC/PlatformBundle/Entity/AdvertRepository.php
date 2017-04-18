<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 18/04/2017
 * Time: 10:52
 */

// src/OC/PlatformBundle/Entity/AdvertRepository.php

namespace OC\PlatformBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;


class AdvertRepository extends EntityRepository
{
  public function myFindAll()
  {
    // Méthode 1 : en passant par l'EntityManager
    $queryBuilder = $this->_em->createQueryBuilder()
      ->select('a')
      ->from($this->_entityName, 'a')
    ;

    // Dans un repository, $this->_entityName est le namespace de l'entité gérée
    // Ici, il vaut donc OC\PlatformBundle\Entity\Advert

    // Méthode 2 : en passant par le raccourci (je recommande)
    $queryBuilder = $this->createQueryBuilder('a');

    // On n'ajoute pas de critère ou tri particulier, la construction
    // de notre requête est finale

    // On récupère la Query à partir du QueryBuilder
    $query = $queryBuilder->getQuery();

    // On récupère les résultats à partir de la Query
    $results = $query->getResult();

    // On retourne ces résultats
    return $results;
  }

  public function myFindOne($id)
  {
    $qd = $this->createQueryBuilder('a');

    $qd
      ->where('a.id = :id')
      ->setParameter('id', $id)
    ;

    return $qd
      ->getQuery()
      ->getResult()
      ;
  }

  public function findByAutorAndDate($author, $year)
  {
    $qd = $this->createQueryBuilder('a');

    $qd->where('a.author = :author')
      ->setParameter('author', $author)
      ->andWhere('a.date < :year')
      ->setParameter('year', $year)
      ->orderBy('a.date', 'DESC')
    ;

    return $qd
      ->getQuery()
      ->getResult()
      ;
  }

  public function whereCurrentYear(QueryBuilder $qd)
  {
    $qd
      ->andWhere('a.date BETWEEN :start AND :end')
      ->setParameter('start', new \Datetime(date('Y').'-01-01')) // Date entre le 1er janvier de cette année
      ->setParameter('end', new \Datetime(date('Y').'-12-31')) // Et le 31 décembre de cette année
    ;
  }

  public function myFind()
  {
    $qd = $this->createQueryBuilder('a');

    // On peut ajouter ce qu'on veut avant
    $qd
      ->where('a.author')
      ->setParameter('author', 'Marine')
      ;

    // On applique notre condition sur le QueryBuilger
    $this->whereCurrentYear($qd);

    // On peut ajouter ce qu'on veut après
    $qd->orderBy('a.date', 'DESC');

    return $qd
      ->getQuery()
      ->getResult()
      ;
  }

  public function myFindAllDQL()
  {
    // With Doctrine Query Language (DQL)
    $query = $this->_em->createQuery('SELECT a FROM OCPlatformBundle:Advert a');
    $results = $query->getResult();

    return $results;
  }

  public function getAdvertWithApplications()
  {
    $qd = $this
      ->createQueryBuilder('a')
      ->leftJoin('a.application', 'app', 'WITH', 'YEAR(app.date) > 2013')
      ->addSelect('app')
      ;

    return $qd
      ->getQuery()
      ->getResult()
      ;
  }

  public function getAdvertWithCategories(array $categoryNames)
  {
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->innerJoin('a.categories', 'c')
      ->addSelect('c')
    ;

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    $qb->where($qb->expr()->in('c.name', $categoryNames));
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
      ;
  }
}