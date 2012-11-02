<?php

namespace Jordo\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class JordoUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
