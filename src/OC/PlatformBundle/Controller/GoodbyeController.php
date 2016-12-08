<?php

// src/OC/PlatformBundle/Controller/GoodbyeController.php

namespace OC\PlatformBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class GoodbyeController extends Controller
{
  public function indexAction()
  {
    $content = $this->get('templating')->render('OCPlatformBundle:Goodbye:index.html.twig', array('nom' => 'Vincenzo'));
    return new Response($content);
  }
}
