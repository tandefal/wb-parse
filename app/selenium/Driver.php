<?php

namespace App\selenium;

use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\DriverCommand;
use Facebook\WebDriver\Remote\HttpCommandExecutor;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\WebDriverCommand;

class Driver extends RemoteWebDriver
{
    public static function create(
        $url = 'http://localhost:4444/wd/hub',
        $desired_capabilities = null,
        $connection_timeout_in_ms = null,
        $request_timeout_in_ms = null,
        $http_proxy = null,
        $http_proxy_port = null
    ) {
        $url = rtrim($url, '/');

        // Passing DesiredCapabilities as $desired_capabilities is encouraged but
        // array is also accepted for legacy reason.
        if ($desired_capabilities instanceof DesiredCapabilities) {
            $desired_capabilities = $desired_capabilities->toArray();
        }

        $executor = new HttpCommandExecutor($url, $http_proxy, $http_proxy_port);
        if ($connection_timeout_in_ms !== null) {
            $executor->setConnectionTimeout($connection_timeout_in_ms);
        }
        if ($request_timeout_in_ms !== null) {
            $executor->setRequestTimeout($request_timeout_in_ms);
        }

        $command = new WebDriverCommand(
            "",//фикс ошибки
            DriverCommand::NEW_SESSION,
            array('desiredCapabilities' => $desired_capabilities)
        );

        $response = $executor->execute($command);

        $driver = new static();
        $driver->setSessionID($response->getSessionID())
            ->setCommandExecutor($executor);

        return $driver;
    }
}
