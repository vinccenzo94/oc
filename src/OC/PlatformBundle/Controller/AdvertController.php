<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
    // On récupère notre paramètre tag
    $tag = $request->query->get('tag');

    return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
      'id' => $id,
      'tag' => $tag
    ));
  }

  public function viewSlugAction($slug, $year, $_format)
  {
    return new Response(
      "On pourrait afficher l'annonce correspondant au slug '".$slug."', créée en ".$year." et au format ".$_format."."
    );
  }
}
