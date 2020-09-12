<?php

namespace Tests;

use App\Exceptions\Handler;
use App\Role;
use App\User;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Testing\TestResponse;

/**
 * @property ExceptionHandler|mixed oldExceptionHandler
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp (): void
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }

    protected function signIn ( $user = null ): TestCase
    {
        $user = $user ?: factory(User::class)->create();
        $this->actingAs($user);
        return $this;
    }

    protected function disableExceptionHandling (): void
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}
            public function report($e) {}
            public function render($request, $e) {
                throw $e;
            }
        });
    }

    protected function withExceptionHandling(): TestCase
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }

    protected function seedRoles (): void
    {
        (new \RoleSeeder())->run();
    }

    protected function seedUsers (): void
    {
        if ( !Role::all()->count() ) {
            $this->seedRoles();
        }

        (new \UserSeeder())->run();
        Artisan::call('passport:install',['-vvv' => true]);
    }

    protected function getMe (): User
    {
        $me = factory(User::class)->create([
            'email' => 'me@example.com'
        ]);

        if ( !Role::all()->count() ) {
            $this->seedRoles();
        }

        $me->attachRole(Role::where('name', 'client')->first());

        return $me;
    }

    protected function getRoot (): User
    {
        $email = 'root@example.com';
        $root = factory(User::class)->create([
            'email' => $email
        ]);

        if ( !Role::all()->count() ) {
            $this->seedRoles();
        }

        $root->attachRole(Role::where('name', 'root')->first());

        return $root;
    }

    protected function assertCollection ( TestResponse $response, $service = null ): void
    {
        $response->assertJsonStructure([
            'message',
            'data' => [
                'collection',
                'pagination'
            ]
        ]);

        self::assertIsArray($response['data']['collection']);
        self::assertArrayHasKey('total', $response['data']['pagination']);
        self::assertArrayHasKey('count', $response['data']['pagination']);
        self::assertArrayHasKey('per_page', $response['data']['pagination']);
        self::assertArrayHasKey('current_page', $response['data']['pagination']);
        self::assertArrayHasKey('total_pages', $response['data']['pagination']);
        self::assertArrayHasKey('first_page_url', $response['data']['pagination']);
        self::assertArrayHasKey('previous_page_url', $response['data']['pagination']);
        self::assertArrayHasKey('next_page_url', $response['data']['pagination']);
        self::assertArrayHasKey('last_page_url', $response['data']['pagination']);

        if ( $service ) {
            $serviceResponse = $service->execute();
            self::assertCount(count($serviceResponse->collection), $response['data']['collection']);
        }
    }

    protected function assertEntity ( Array $response, $resource ): void
    {
        foreach ( $resource as $key => $value ) {
            if ( is_object($value) ) {
                $this->assertEntity($response[$key], $value->jsonSerialize());
            } elseif ( is_array($value) ) {
                $this->assertEntity($response[$key], $value);
            } else {
                self::assertEquals($response[$key], $value);
            }
        }
    }
}
