<?php

namespace Testing;

use Mockery;

use NoelDeMartin\LaravelDusk\Browser;

use Illuminate\Support\Facades\Storage;

class BrowserTest extends TestCase
{
    public function test_fake()
    {
        $facade = $this->faker->word;
        $args = $this->faker->words();
        $driver = Mockery::mock(StdClass::class);
        $driver
            ->shouldReceive('executeAsyncScript')
            ->with(
                Mockery::on(function ($argument) use ($facade, $args) {
                    return str_contains($argument, '/_dusk-mocking/mock')
                        && str_contains($argument, 'facade='.urlencode($facade))
                        && str_contains($argument, 'arguments='.urlencode(json_encode($args)));
                })
            )
            ->twice();
        $driver->shouldReceive('navigate->to');
        $driver->shouldReceive('getPageSource');
        $driver->shouldReceive('manage->timeouts->setScriptTimeout');
        $this->prepareFacadeMock('url')->shouldReceive('to');
        Browser::$baseUrl = 'http://laravel.dev';
        $browser = new Browser($driver);

        $browser->fake($facade, ...$args);
        $browser->mock($facade, ...$args);
    }

    public function test_default_javascript_requests_timeout_can_be_overridden()
    {
        Browser::$javascriptRequestsTimeout = 5;

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeAsyncScript');
        $driver->shouldReceive('manage->timeouts->setScriptTimeout')->with(5);
        $browser = new Browser($driver);

        $browser->executeJavascriptRequest('GET', 'https://example.com');
    }

    public function test_register()
    {
        $facade = $this->faker->word;
        $fake = $this->faker->word;
        $driver = Mockery::mock(StdClass::class);
        $driver
            ->shouldReceive('executeAsyncScript')
            ->with(
                Mockery::on(function ($argument) use ($facade, $fake) {
                    return str_contains($argument, '/_dusk-mocking/register')
                        && str_contains($argument, 'facade='.urlencode($facade))
                        && str_contains($argument, 'fake='.urlencode($fake));
                })
            )
            ->once();
        $driver->shouldReceive('navigate->to');
        $driver->shouldReceive('getPageSource');
        $driver->shouldReceive('manage->timeouts->setScriptTimeout');
        $this->prepareFacadeMock('url')->shouldReceive('to');
        Browser::$baseUrl = 'http://laravel.dev';
        $browser = new Browser($driver);

        $browser->registerFake($facade, $fake);
    }
}
