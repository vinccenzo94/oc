<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Skill;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
  public function indexAction($page, $nbPerPage=3)
  {
    // On ne sait pas combien de pages il y a
    // Mais on sait qu'une page doit être supérieure ou égale à 1
    if ($page < 1) {
      // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    $em = $this->getDoctrine()->getManager();
    $repository = $em->getRepository('OCPlatformBundle:Advert');
    $listAdverts = $repository->getAdverts($page, $nbPerPage);
    $nbAdverts = count($listAdverts);

    $nbPages = ceil($nbAdverts / $nbPerPage);
    $next = 0;
    $previous = 0;

    if ($page > $nbPages) {
      // On déclenche une exception NotFoundHttpException, cela va afficher
      // une page d'erreur 404 (qu'on pourra personnaliser plus tard d'ailleurs)
      throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
    }

    // Mais pour l'instant, on ne fait qu'appeler le template
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'page' => $page,
      'nbPages' => $nbPages,
      'next' => $next,
      'previous' => $previous
    ));
  }

  public function viewAction($id)
  {
    // On récupère le repository
    $em = $this->getDoctrine()->getManager();
    $repository = $em->getRepository('OCPlatformBundle:Advert');

    // On récupère l'entité correspondante à l'id $id
    $advert = $repository->find($id);
    // Autre syntaxe depuis un contrôleur
    //$advert = $this->getDoctrine()->getManager()->find('OCPlatformBundle:Advert', $id);

    // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
    // ou null si l'id $id n'existe pas, d'où ce if :
    if ($advert === null) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert)
      );

    // On récupère maintenant la liste des AdvertSkill
    $listAdvertSkills = $em
      ->getRepository('OCPlatformBundle:AdvertSkill')
      ->findBy(array('advert' => $advert)
      );

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'advert' => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
    ));
  }

  public function addAction(Request $request)
  {
    $advert = new Advert();
    $advert->setTitle('Recherche développeur Symfony.');
    $advert->setAuthor('Alexandre');
    $advert->setEmail('vgrimelli@easyvista.com');
    $advert->setContent('Nous recherchons un développeur Symfony débutant sur Lyon. Blabla...');

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

    // On récupère l'EntityManager
    $em = $this->getDoctrine()->getManager();

    // On récupère toutes les compétences possibles
    $listSkills = $em->getRepository('OCPlatformBundle:Skill')->findAll();

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
      $em->persist($advertSkill);
    }

    // Etape 1 : On "persiste" l'entité
    $em->persist($advert);

    // Étape 1 bis : si on n'avait pas défini le cascade={"persist"},
    // on devrait persister à la main l'entité $image
    // $em->persist($image);

    // Étape 1 ter : pour cette relation pas de cascade lorsqu'on persiste Advert, car la relation est
    // définie dans l'entité Application et non Advert. On doit donc tout persister à la main ici.
    $em->persist($application1);
    $em->persist($application2);

    // Étape 2 : On déclenche l'enregistrement
    $em->flush();

    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');
      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig', array('advert' => $advert));

    /*// On récupère le service
    $antispam = $this->container->get('oc_platform.antispam');

    // Je pars du principe que $text contient le texte d'un message quelconque
    $text = '...';
    if ($antispam->isSpam($text)) {
      throw new \Exception('Votre message a été détecté comme spam !');
    }
    // ici le message n'est pas un spam

    // La gestion d'un formulaire est particulière, mais l'idée est la suivante :

    // Si la requête est en POST, c'est que le visiteur a soumis le formulaire
    if ($request->isMethod('POST')) {
      // Ici, on s'occupera de la création et de la gestion du formulaire

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      // Puis on redirige vers la page de visualisation de cettte annonce
      return $this->redirectToRoute('oc_platform_view', array('id' => 5));
    }

    // Si on n'est pas en POST, alors on affiche le formulaire
    return $this->render('OCPlatformBundle:Advert:add.html.twig');*/
  }

  public function editAction($id, Request $request)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if ($advert === null) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

    // On boucle sur les catégories pour les lier à l'annonce
    foreach ($listCategories as $category) {
      $advert->addCategory($category);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // Étape 2 : On déclenche l'enregistrement
    $em->flush();

    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
  }

  public function deleteAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    // Ensuite, on va supprimer l'annonce avec ses liaisons
    $repository = $em->getRepository('OCPlatformBundle:Advert');

    // On récupère l'annonce $id
    $advert = $repository->find($id);

    if ($advert === null) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On va d'abord supprimer les liaisons entre les annonces et les compétences
    $repository = $em->getRepository('OCPlatformBundle:AdvertSkill');
    $listAdvertSkill = $repository->findBy(array('advert' => $advert));

    foreach($listAdvertSkill as $advertSkill)
    {
      $em->remove($advertSkill);
    }

    // -- On supprime les catégories
    foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
    }

    // -- On supprime les candidatures associées
    foreach ($advert->getApplications() as $application) {
      $advert->removeApplication($application);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // On supprime l'annonce
    $em->remove($advert);

    // On déclenche la modification
    $em->flush();

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }

  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();
    $repository = $em->getRepository('OCPlatformBundle:Advert');
    $listAdverts = $repository->findBy(
      array(),                  // Pas de critère
      array('date' => 'desc'),  // On trie par date décroissante
      $limit,                   // On sélection $limit annonces
      0                  // à partir du premier
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      // Tout l'intérêt est ici : le contrôleur passe
      // les variables nécessaires au template !
      'listAdverts' => $listAdverts
    ));
  }

  public function purgeAction($days)
  {
    // On récupère le service
    $purge = $this->container->get('oc_platform.purger.advert');

    // On appelle la méthode de purge des annonces
    $listPurgedAdverts = $purge->purgerAdvert($days);

    return $this->render('OCPlatformBundle:Advert:purge.html.twig', array(
      'listPurgeAdverts' => $listPurgedAdverts
    ));
  }
}
