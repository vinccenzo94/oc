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
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    // On crée un objet Advert
    $advert = new Advert();

    // On crée le formBuilder grâce au service form factory
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $advert);

    // On ajoute les champs de l'entité que l'on veut à notre formulaire
    $formBuilder
      ->add('date',       DateType::class)
      ->add('title',      TextType::class)
      ->add('content',    TextareaType::class)
      ->add('author',     TextType::class)
      ->add('published',  CheckboxType::class)
      ->add('save',       SubmitType::class)
      ;
    // Pour l'instant, pas de candidatures, catégories, etc..., on les gèrera plus tard

    // à partir du formBuilder, on génère le formulaire
    $form = $formBuilder->getForm();

    // On passe la méthode createView() du formulaire à la vue
    // afin qu'elle puisse afficher le formulaire toute seule
    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
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
