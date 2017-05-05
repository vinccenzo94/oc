<?php
/**
 * Created by PhpStorm.
 * User: vgrimelli
 * Date: 05/05/2017
 * Time: 14:20
 */

namespace OC\PlatformBundle\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Antiflood
 * @package OC\PlatformBundle\Validator
 * @Annotation
 */
class Antiflood extends Constraint
{
  public $message = "Vous avez déjà posté un message il y a moins de 15 secondes, merci d'attendre un peu.";
}