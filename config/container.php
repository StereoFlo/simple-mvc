<?php

/**
 * DI config file
 */

$container = new \Core\Container();

$container->set(new \DateTime('now', \DateTimeZone::EUROPE));