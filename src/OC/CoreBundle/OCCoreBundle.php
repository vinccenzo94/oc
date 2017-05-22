<?php
// src/OC/UserBundle/OCUserBundle.php

namespace OC\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class OCCoreBundle extends Bundle
{
  public function getParent()
  {
    return 'FOSUserBundle';
  }
}
