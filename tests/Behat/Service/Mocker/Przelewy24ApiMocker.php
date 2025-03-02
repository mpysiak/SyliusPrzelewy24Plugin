<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusPrzelewy24Plugin\Behat\Service\Mocker;

use BitBag\SyliusPrzelewy24Plugin\Bridge\Przelewy24BridgeInterface;
use PSS\SymfonyMockerContainer\DependencyInjection\MockerContainer;

final class Przelewy24ApiMocker
{
    public function __construct(private readonly MockerContainer $mocker)
    {
    }

    public function mockApiSuccessfulVerifyTransaction(callable $action): void
    {
        $mockService = $this->mocker
            ->mock('bitbag_sylius_przelewy24_plugin.bridge.przelewy24', Przelewy24BridgeInterface::class)
        ;

        $mockService->shouldReceive('setAuthorizationData');
        $mockService->shouldReceive('trnVerify')->andReturn(true);
        $mockService->shouldReceive('createSign')->andReturn('test');

        $action();

        $this->mocker->unmock('bitbag_sylius_przelewy24_plugin.bridge.przelewy24');
    }
}
