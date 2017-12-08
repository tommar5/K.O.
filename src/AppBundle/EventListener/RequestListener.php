<?php
namespace AppBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class RequestListener
{
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            // don't do anything if it's not the master request
            return;
        }

        $request = $event->getRequest();
        if ($request->query->has('sorters') || $request->query->has('filters')) {
            $request->getSession()->set($request->get('_route'), $request->query->all());
        } else {
            if ($request->getSession()->has($request->get('_route'))) {
                $request->getSession()->remove($request->get('_route'));
            }
        }
    }
}