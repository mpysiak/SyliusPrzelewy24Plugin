<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPrzelewy24Plugin\Action;

use BitBag\SyliusPrzelewy24Plugin\Bridge\Przelewy24BridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\InvalidArgumentException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\Notify;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class NotifyAction implements ActionInterface, ApiAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function __construct(private Przelewy24BridgeInterface $przelewy24Bridge)
    {
    }

    public function setApi($api): void
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported.Expected to be set as array.');
        }

        $this->przelewy24Bridge->setAuthorizationData($api['merchant_id'], $api['crc_key'], $api['environment']);
    }

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        $this->gateway->execute($httpRequest = new GetHttpRequest());

        if (!isset($httpRequest->request['p24_session_id']) || $details['p24_session_id'] !== $httpRequest->request['p24_session_id']) {
            throw new NotFoundHttpException();
        }

        if (false === $this->verifySign($httpRequest)) {
            throw new InvalidArgumentException('Invalid sign.');
        }

        $details['p24_order_id'] = $httpRequest->request['p24_order_id'];

        if ($this->przelewy24Bridge->trnVerify($this->getPosData($details))) {
            $details['p24_status'] = Przelewy24BridgeInterface::COMPLETED_STATUS;

            return;
        }

        $details['p24_status'] = Przelewy24BridgeInterface::FAILED_STATUS;
    }

    public function supports($request): bool
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }

    private function getPosData(ArrayObject $details): array
    {
        return ['p24_session_id' => $details['p24_session_id'], 'p24_amount' => $details['p24_amount'], 'p24_currency' => $details['p24_currency'], 'p24_order_id' => $details['p24_order_id']];
    }

    private function verifySign(GetHttpRequest $request): bool
    {
        $sign = $this->przelewy24Bridge->createSign([
            $request->request['p24_session_id'],
            $request->request['p24_order_id'],
            $request->request['p24_amount'],
            $request->request['p24_currency'],
        ]);

        return $sign === $request->request['p24_sign'];
    }
}
