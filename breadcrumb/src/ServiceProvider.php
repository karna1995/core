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


namespace Antares\Breadcrumb;

use DaveJamesMiller\Breadcrumbs\ServiceProvider as BreadcrumbServiceProvider;

class ServiceProvider extends BreadcrumbServiceProvider
{

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['breadcrumbs'] = $this->app->share(function($app) {
            $breadcrumbs = $this->app->make('Antares\Breadcrumb\Manager');
            $reflection  = new \ReflectionClass(get_parent_class());
            $viewPath    = dirname($reflection->getFileName()) . '/../views/';

            $this->loadViewsFrom($viewPath, 'breadcrumbs');
            $this->loadViewsFrom($viewPath, 'laravel-breadcrumbs');
            $breadcrumbs->setView($app['config']['breadcrumbs.view']);

            return $breadcrumbs;
        });
    }

}
