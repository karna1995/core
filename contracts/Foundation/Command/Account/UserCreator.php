<?php

/**
 * Part of the Antares Project package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Antares Core
 * @version    0.9.0
 * @author     Antares Team
 * @license    BSD License (3-clause)
 * @copyright  (c) 2017, Antares Project
 * @link       http://antaresproject.io
 */
 namespace Antares\Contracts\Foundation\Command\Account;

use Antares\Contracts\Foundation\Listener\Account\UserCreator as Listener;

interface UserCreator
{
    /**
     * View create user page.
     *
     * @param  \Antares\Contracts\Foundation\Listener\Account\UserCreator  $listener
     *
     * @return mixed
     */
    public function create(Listener $listener);
}
