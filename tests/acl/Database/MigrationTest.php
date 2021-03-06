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

namespace Antares\Acl\Tests\Database;

use Mockery as m;
use Antares\Acl\Database\Migration;
use Illuminate\Routing\Route;
use Antares\Acl\Action;
use Antares\Acl\RoleActionList;

class MigrationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Mockery
     */
    protected $container;

    /**
     * @var RoleActionList
     */
    protected $roleActionList;

    public function setUp()
    {
        parent::setUp();

        $this->container = m::mock('\Illuminate\Container\Container');

        $this->roleActionList = new RoleActionList();

        $this->roleActionList->add('admin', [
            new Action('index', 'List'),
            new Action('add', 'Add Item'),
        ]);

        $this->roleActionList->add('customer', [
            new Action('index', 'List'),
        ]);
    }

    public function tearDown()
    {
//        parent::tearDown();
//        m::close();
    }

    /**
     * @return Migration
     */
    protected function getMigrationClass()
    {
        return new Migration($this->container, 'some_component_name');
    }

    public function testUpMethods()
    {
        $component = m::mock('\Antares\Contracts\Memory\Provider');
        $roles     = m::mock('\Antares\Authorization\Fluent')->makePartial();
        $actions   = m::mock('\Antares\Authorization\Fluent')->makePartial();

        $memoryManager = m::mock('\Antares\Memory\MemoryManager')
                ->shouldReceive('make')
                ->with('component')
                ->andReturn($component)
                ->getMock();

        $authorization = m::mock('\Antares\Authorization\Authorization')
                ->shouldReceive('attach')
                ->with($component)
                ->once()
                ->andReturnNull()
                ->shouldReceive('roles')
                ->once()
                ->andReturn($roles)
                ->shouldReceive('actions')
                ->once()
                ->andReturn($actions)
                ->shouldReceive('allow')
                ->times(2)
                ->with(m::type('String'), m::type('Array'))
                ->andReturnNull()
                ->getMock();

        $acl = m::mock('\Antares\Authorization\Factory')
                ->shouldReceive('make')
                ->with('antares/some_component_name')
                ->once()
                ->andReturn($authorization)
                ->getMock();


        $this->container
                ->shouldReceive('make')
                ->with('antares.acl')
                ->once()
                ->andReturn($acl)
                ->shouldReceive('make')
                ->with('antares.memory')
                ->once()
                ->andReturn($memoryManager)
                ->getMock();

        //$this->getMigrationClass()->up($this->roleActionList);
    }

    public function testDownMethod()
    {
        $component = m::mock('\Antares\Contracts\Memory\Provider')
                ->shouldReceive('forget')
                ->with('acl_antares/some_component_name')
                ->andReturnNull()
                ->getMock();

        $memoryManager = m::mock('\Antares\Memory\MemoryManager')
                ->shouldReceive('make')
                ->with('component')
                ->andReturn($component)
                ->getMock();

        $this->container
                ->shouldReceive('make')
                ->with('antares.memory')
                ->once()
                ->andReturn($memoryManager)
                ->getMock();

        $this->getMigrationClass()->down();
    }

}
