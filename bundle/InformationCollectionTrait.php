<?php

namespace Netgen\Bundle\InformationCollectionBundle;

use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Netgen\Bundle\InformationCollectionBundle\Event\InformationCollected;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;

trait InformationCollectionTrait
{
    use ContainerAwareTrait;

    /**
     * Builds Form, checks if Form is valid and dispatches InformationCollected event
     *
     * @param \eZ\Publish\Core\MVC\Symfony\View\ContentValueView $view
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    protected function collectInformation(ContentValueView $view, Request $request)
    {
        $isValid = false;

        if (!$view instanceof LocationValueView) {
            throw new \BadMethodCallException("eZ view needs to implement LocationValueView interface");
        }

        /** @var \Netgen\Bundle\InformationCollectionBundle\Form\Builder\FormBuilder $formBuilder */
        $formBuilder = $this->container
            ->get('netgen_information_collection.form.builder');

        $form = $formBuilder->createFormForLocation($view->getLocation())
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid() && $form->isSubmitted()) {
            $isValid = true;

            $event = new InformationCollected($form->getData());

            /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
            $dispatcher = $this->container
                ->get('event_dispatcher');

            $dispatcher->dispatch(Events::INFORMATION_COLLECTED, $event);
        }

        return [
            'is_valid' => $isValid,
            'form' => $form->createView(),
        ];
    }
}