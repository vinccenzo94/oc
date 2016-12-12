<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdvertController extends Controller
{
	public function indexAction()
	{
		/*$content = $this->get('templating')->render('OCPlatformBundle:Advert:index.html.twig', array('nom' => 'winzou'));
		return new Response($content);*/

    $url = $this->get('router')->generate(
      'oc_platform_view',
      array('id' => 5)
    );

    return new Response("L'URL de l'annonce d'id 5 est :".$url);
	}

	public function viewAction($id, Request $request)
  {
    //return new Response("Affichage de l'annonce d'id : ".$id);

    /*
    // On récupère notre paramètre tag
    $tag = $request->query->get('tag');

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'id' => $id,
      'tag' => $tag
    ));
    */

    /*
    // Récupération de l'URL de l'accueil du Site
    $url = $this->get('router')->generate('oc_platform_home');

    return $this->redirect($url);
    */

    //return $this->redirectToRoute('oc_platform_home');

    /*
    // On récupère notre paramètre tag
    $tag = $request->query->get('tag');

    /*
    // Créons nous-même la réponse en JSON, grâce à la fonction json_encode()
    $response = new Response(json_encode(array('id' => $id, 'tag' => $tag)));

    $response->headers->set('Content-Type', 'application/json');

    return $response;
    */

    //return new JsonResponse(array('id' => $id, 'tag' => $tag));

    // Récupération de la session
    $session = $request->getSession();

    // On récupère le contenu de la variable user_id
    $userId = $session->get('user_id');

    // On définit une nouvelle valeur pour cette variable user_id
    $session->set('user_id', 91);

    return new Response("<body>Je suis une page de test, je n'ai rien à dire</body>");
  }

  public function viewSlugAction($slug, $year, $_format)
  {
    return new Response(
      "On pourrait afficher l'annonce correspondant au slug '".$slug."', créée en ".$year." et au format ".$_format."."
    );
  }

  public function addAction(Request $request)
  {
    $session = $request->getSession();

    // Bien sûr, cette méthode devra réellement ajouter l'annonce

    // Mais faisons comme si c'était le cas
    $session->getFlashBag()->add('info', 'Annonce bien enregistrée');

    // Le « flashBag » est ce qui contient les messages flash dans la session
    // Il peut bien sûr contenir plusieurs messages :
    $session->getFlashBag()->add('info', 'Oui oui, elle est bien enregistrée !');

    // Puis on redirige vers la page de visualisation de cette annonce
    return $this->redirectToRoute('oc_platform_view', array('id' => 5));
  }

  public function editAction($id, Request $request)
  {
    // Ici, on récupèrera l'annonce correspondante à $id

    // Même mécanisme que pour l'ajout
    if ($request->isMethod('POST')) {
      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => 5));
    }

    return $this->render('OCPlatformBundle:Advert:edit.html.twig');
  }

  public function deleteAction($id)
  {
    // Ici, on récupérera l'annonce correspondant à $id

    // Ici, on gérera la suppression de l'annonce en question

    return $this->render('OCPlatformBundle:Advert:delete.html.twig');
  }
}
