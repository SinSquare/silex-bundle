<?php

namespace SinSquare\Bundle;

use Silex\Application;

abstract class AbstractController
{
    protected $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }
}
