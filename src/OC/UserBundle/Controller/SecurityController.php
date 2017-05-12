<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 12/05/2017
 * Time: 10:59
 */

namespace OC\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class SecurityController extends Controller
{
  public function loginAction(Request $request)
  {
    // Si le visiteur est déjà enregistré, on le redirige vers l'accueil
    if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
      return $this->redirectToRoute('oc_platform_accueil');
    }

    // Le service authentication_utils permet de récupérer le nom d'utilisateur
    // et l'erreur dans le cas où le formulaire a déjà été soumis mais était invalide
    // (mauvais mot de passe par exemple)
    $authenticationUtils = $this->get('security.authentication_utils');

    return $this->render('OCUserBundle:Security:login.html.twig', array(
      'last_username' => $authenticationUtils->getlastUsername(),
      'error'         => $authenticationUtils->getlastAuthenticationError(),
    ));
  }
}