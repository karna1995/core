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


namespace Antares\Brands\TestCase;

use Antares\Brands\Composers\BrandSelectorPlaceHolder;
use Antares\Testbench\TestCase;
use Antares\Widget\WidgetManager;
use Mockery as m;

class BrandSelectorPlaceHolderTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    /**
     * test contructor
     */
    public function testConstruct()
    {
        $brandsSelectorHandlerMock = m::mock('\Antares\Brands\Http\Handlers\BrandsSelectorHandler');
        $stub                      = new BrandSelectorPlaceHolder($this->app, $brandsSelectorHandlerMock);
        $this->assertEquals(get_class($stub), 'Antares\Brands\Composers\BrandSelectorPlaceHolder');
    }

    /**
     * test on booting placeholder
     */
    public function testOnBootExtension()
    {
        $this->app['antares.widget']                    = new WidgetManager($this->app);
        $viewFactory                                    = m::mock('Illuminate\Contracts\View\Factory');
        $viewFactory->shouldReceive('make')->with(m::type('string'), m::type('array'), m::type('array'))->andReturnSelf();
        $this->app['Illuminate\Contracts\View\Factory'] = $viewFactory;
        $brandsSelectorHandlerMock                      = m::mock('\Antares\Brands\Http\Handlers\BrandsSelectorHandler');
        $brandsSelectorHandlerMock->shouldReceive('handle')->andReturnSelf();
        $stub                                           = new BrandSelectorPlaceHolder($this->app, $brandsSelectorHandlerMock);
        $this->assertNull($stub->onBootExtension());
    }

}