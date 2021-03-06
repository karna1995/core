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

namespace Antares\Auth\TestCase;

use Antares\Auth\SessionGuard as Guard;
use Mockery as m;

class GuardTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Provider instance.
     *
     * @var Illuminate\Contracts\Auth\UserProvider
     */
    private $provider = null;

    /**
     * Session instance.
     *
     * @var Illuminate\Session\Store
     */
    private $session = null;

    /**
     * Event dispatcher instance.
     *
     * @var Illuminate\Event\Dispatcher
     */
    private $events = null;

    /**
     * The request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request = null;

    /**
     * Setup the test environment.
     */
    public function setUp()
    {
        $this->provider = m::mock('\Illuminate\Contracts\Auth\UserProvider');
        $this->session  = m::mock('\Illuminate\Session\Store');
        $this->events   = m::mock('\Illuminate\Contracts\Events\Dispatcher');
        $this->request  = m::mock('\Illuminate\Http\Request');
    }

    /**
     * Test Antares\Auth\Guard::setup() method.
     *
     * @test
     */
    public function testSetupMethod()
    {
        $events   = $this->events;
        $callback = function () {
            return ['editor'];
        };

        $events->shouldReceive('forget')->once()->with('antares.auth: roles')->andReturn(null)
                ->shouldReceive('listen')->once()->with('antares.auth: roles', $callback)->andReturn(null);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);

        $stub->setup($callback);
    }

    /**
     * Test Antares\Auth\Guard::roles() method returning valid roles.
     *
     * @test
     */
    public function testRolesMethod()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'admin'],
            ['id' => 1, 'name' => 'user']
        ]);

        $user->shouldReceive('getAuthIdentifier')->once()->andReturn(1);



        $events->shouldReceive('until')->once()
                ->with('antares.auth: roles', m::any())->andReturn(['admin', 'user']);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $expected = ['admin', 'user'];
        $output   = $stub->roles();


        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Auth\Guard::roles() method when user is not logged in.
     *
     * @test
     */
    public function testRolesMethodWhenUserIsNotLoggedIn()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'Guest'],
        ]);
        $user->shouldReceive('getAuthIdentifier')->once()->andReturn(1);

        $events->shouldReceive('until')->once()
                ->with('antares.auth: roles', m::any())->andReturn(null);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $expected = ['Guest'];
        $output   = $stub->roles();

        $this->assertEquals($expected, $output);
    }

    /**
     * Test Antares\Support\Auth::is() method returning valid roles.
     *
     * @test
     */
    public function testIsMethod()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'admin'],
            ['id' => 1, 'name' => 'user'],
        ]);
        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('until')->once()
                ->with('antares.auth: roles', m::any())->andReturn(['admin', 'user']);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->is('admin'));
        $this->assertTrue($stub->is('user'));
        $this->assertFalse($stub->is('reseller'));

        $this->assertTrue($stub->is(['admin', 'user']));
        $this->assertFalse($stub->is(['admin', 'reseller']));
    }

    /**
     * Test Antares\Support\Auth::is() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsMethodWhenInvalidRolesIsReturned()
    {
        $events      = $this->events;
        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'foo'],
        ]);

        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('until')
                ->with('antares.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertFalse($stub->is('admin'));
        $this->assertFalse($stub->is('editor'));
        $this->assertFalse($stub->is('user'));

        $this->assertFalse($stub->is(['admin', 'editor']));
        $this->assertFalse($stub->is(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::isAny() method returning valid roles.
     *
     * @test
     */
    public function testIsAnyMethod()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([['id' => 0, 'name' => 'admin'], ['id' => 1, 'name' => 'user'], ['id' => 2, 'name' => 'editor'],]);
        $user->shouldReceive('getAuthIdentifier')->times(3)->andReturn(1);

        $events->shouldReceive('until')->once()->with('antares.auth: roles', m::any())->andReturn(['admin', 'editor']);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isAny(['admin', 'user']));
        $this->assertTrue($stub->isAny(['user', 'editor']));
        $this->assertFalse($stub->isAny(['superadmin', 'superuser']));
    }

    /**
     * Test Antares\Support\Auth::isAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsAnyMethodWhenInvalidRolesIsReturned()
    {
        $events      = $this->events;
        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection();
        $user->shouldReceive('getAuthIdentifier')->twice()->andReturn(1);

        $events->shouldReceive('until')
                ->with('antares.auth: roles', m::any())->twice()
                ->andReturn('foo');

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertFalse($stub->isAny(['admin', 'editor']));
        $this->assertFalse($stub->isAny(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::isNot() method returning valid roles.
     *
     * @test
     */
    public function testIsNotMethod()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'admin'],
        ]);
        $user->shouldReceive('getAuthIdentifier')->times(4)->andReturn(1);

        $events->shouldReceive('until')->once()
                ->with('antares.auth: roles', m::any())->andReturn(['admin', 'editor']);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNot('superadmin'));
        $this->assertTrue($stub->isNot('user'));
        $this->assertFalse($stub->isNot('admin'));

        $this->assertTrue($stub->isNot(['superadmin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::isNot() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotMethodWhenInvalidRolesIsReturned()
    {
        $events      = $this->events;
        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection();
        $user->shouldReceive('getAuthIdentifier')->times(5)->andReturn(1);

        $events->shouldReceive('until')
                ->with('antares.auth: roles', m::any())->times(5)
                ->andReturn('foo');

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNot('admin'));
        $this->assertTrue($stub->isNot('editor'));
        $this->assertTrue($stub->isNot('user'));

        $this->assertTrue($stub->isNot(['admin', 'editor']));
        $this->assertTrue($stub->isNot(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::isAny() method returning valid roles.
     *
     * @test
     */
    public function testIsNotAnyMethod()
    {
        $events = $this->events;

        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'editor']
        ]);
        $user->shouldReceive('getAuthIdentifier')->times(3)->andReturn(1);

        $events->shouldReceive('until')->once()
                ->with('antares.auth: roles', m::any())->andReturn(['admin', 'editor']);

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNotAny(['administrator', 'user']));
        $this->assertFalse($stub->isNotAny(['user', 'editor']));
        $this->assertFalse($stub->isNotAny(['admin', 'editor']));
    }

    /**
     * Test Antares\Support\Auth::isNotAny() method when invalid roles is
     * returned.
     *
     * @test
     */
    public function testIsNotAnyMethodWhenInvalidRolesIsReturned()
    {
        $events      = $this->events;
        $user        = m::mock('\Illuminate\Contracts\Auth\Authenticatable');
        $user->roles = new \Illuminate\Support\Collection([
            ['id' => 0, 'name' => 'administrator']
        ]);
        $user->shouldReceive('getAuthIdentifier')->twice()->andReturn(1);

        $events->shouldReceive('until')
                ->with('antares.auth: roles', m::any())->once()
                ->andReturn('foo');

        $stub = new Guard('web', $this->provider, $this->session);
        $stub->setDispatcher($events);
        $stub->setUser($user);

        $this->assertTrue($stub->isNotAny(['admin', 'editor']));
        $this->assertTrue($stub->isNotAny(['admin', 'user']));
    }

    /**
     * Test Antares\Support\Auth::logout() method.
     *
     * @test
     */
    public function testLogoutMethod()
    {
        $events           = $this->events;
        $provider         = $this->provider;
        $session          = $this->session;
        $request          = $this->request;
        $cookie           = m::mock('\Illuminate\Contracts\Cookie\QueueingFactory');
        $request->cookies = $cookie;

        $events->shouldReceive('until')->never()
                ->with('antares.auth: roles', m::any())->andReturn(['admin', 'editor'])
                ->shouldReceive('dispatch')
                ->with(m::type('\Illuminate\Auth\Events\Logout'))->andReturnNull()
                ->shouldReceive('fire')
                ->withAnyArgs('auth.logout', m::any())->andReturn(['admin', 'editor']);
        $provider->shouldReceive('updateRememberToken');
        $session->shouldReceive('remove')->andReturnNull();

        $stub = new Guard('web', $this->provider, $this->session, $request);
        $stub->setDispatcher($events);
        $stub->setCookieJar($cookie);

        $cookie->shouldReceive('get')->andReturn('remember_web_' . sha1(get_class($stub)))
                ->shouldReceive('queue')->andReturn($cookie)
                ->shouldReceive('forget')->andReturn(null);


        $refl      = new \ReflectionObject($stub);
        $user      = $refl->getProperty('user');
        $userRoles = $refl->getProperty('userRoles');

        $user->setAccessible(true);
        $userRoles->setAccessible(true);

        $userStub = m::mock('\Illuminate\Contracts\Auth\Authenticatable');

        $userStub->shouldReceive('getAuthIdentifier')->andReturn(1)
                ->shouldReceive('setRememberToken');

        $user->setValue($stub, $userStub);
        $userRoles->setValue($stub, [1 => ['admin', 'editor']]);

        $this->assertEquals(['admin', 'editor'], $stub->roles());

        $stub->logout();

        $this->assertNull($userRoles->getValue($stub));
    }

}
