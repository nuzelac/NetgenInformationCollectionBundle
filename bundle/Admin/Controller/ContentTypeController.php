<?php

declare(strict_types=1);

namespace Netgen\Bundle\InformationCollectionBundle\Admin\Controller;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeDraft;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use EzSystems\EzPlatformAdminUi\Form\Data\ContentType\ContentTypesDeleteData;
use EzSystems\EzPlatformAdminUi\Form\Factory\FormFactory;
use EzSystems\EzPlatformAdminUi\Form\SubmitHandler;
use EzSystems\EzPlatformAdminUi\Notification\NotificationHandlerInterface;
use EzSystems\RepositoryForms\Data\Mapper\ContentTypeDraftMapper;
use EzSystems\RepositoryForms\Form\ActionDispatcher\ActionDispatcherInterface;
use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\Form;
use eZ\Publish\API\Repository\Exceptions\BadStateException;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\Translation\Exception\InvalidArgumentException as TranslationInvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use EzSystems\EzPlatformAdminUiBundle\Controller\Controller;

class ContentTypeController extends Controller
{
    /** @var NotificationHandlerInterface */
    private $notificationHandler;

    /** @var TranslatorInterface */
    private $translator;

    /** @var ContentTypeService */
    private $contentTypeService;

    /** @var ActionDispatcherInterface */
    private $contentTypeActionDispatcher;

    /** @var array */
    private $languages;

    /** @var FormFactory */
    private $formFactory;

    /** @var SubmitHandler */
    private $submitHandler;

    /** @var int */
    private $defaultPaginationLimit;

    /**
     * ContentTypeController constructor.
     *
     * @param NotificationHandlerInterface $notificationHandler
     * @param TranslatorInterface $translator
     * @param ContentTypeService $contentTypeService
     * @param ActionDispatcherInterface $contentTypeActionDispatcher
     * @param FormFactory $formFactory
     * @param SubmitHandler $submitHandler
     * @param array $languages
     * @param int $defaultPaginationLimit
     */
    public function __construct(
        NotificationHandlerInterface $notificationHandler,
        TranslatorInterface $translator,
        ContentTypeService $contentTypeService,
        ActionDispatcherInterface $contentTypeActionDispatcher,
        FormFactory $formFactory,
        SubmitHandler $submitHandler,
        array $languages,
        int $defaultPaginationLimit
    ) {
        $this->notificationHandler = $notificationHandler;
        $this->translator = $translator;
        $this->contentTypeService = $contentTypeService;
        $this->contentTypeActionDispatcher = $contentTypeActionDispatcher;
        $this->formFactory = $formFactory;
        $this->submitHandler = $submitHandler;
        $this->languages = $languages;
        $this->defaultPaginationLimit = $defaultPaginationLimit;
    }

    public function viewAction(ContentTypeGroup $group, ContentType $contentType): Response
    {
        $fieldDefinitionsByGroup = [];
        foreach ($contentType->fieldDefinitions as $fieldDefinition) {
            $fieldDefinitionsByGroup[$fieldDefinition->fieldGroup ?: 'content'][] = $fieldDefinition;
        }

        return $this->render('@NetgenInformationCollection/admin/content_type/view.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentType,
            'field_definitions_by_group' => $fieldDefinitionsByGroup,
        ]);
    }

    public function updateAction(Request $request, ContentTypeGroup $group, ContentTypeDraft $contentTypeDraft): Response
    {
        $form = $this->createUpdateForm($group, $contentTypeDraft);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $result = $this->submitHandler->handle($form, function () use ($form, $group, $contentTypeDraft) {
                $languageCode = reset($this->languages);

                foreach ($this->languages as $prioritizedLanguage) {
                    if (isset($contentTypeDraft->names[$prioritizedLanguage])) {
                        $languageCode = $prioritizedLanguage;
                        break;
                    }
                }

                $this->contentTypeActionDispatcher->dispatchFormAction(
                    $form,
                    $form->getData(),
                    $form->getClickedButton() ? $form->getClickedButton()->getName() : null,
                    ['languageCode' => $languageCode]
                );

                if ($response = $this->contentTypeActionDispatcher->getResponse()) {
                    return $response;
                }

                $this->notificationHandler->success(
                    $this->translator->trans(
                    /** @Desc("Content type '%name%' updated.") */
                        'content_type.update.success',
                        ['%name%' => $contentTypeDraft->getName()],
                        'content_type'
                    )
                );

                $routeName = 'publishContentType' === $form->getClickedButton()->getName()
                    ? 'ezplatform.content_type.view'
                    : 'ezplatform.content_type.update';

                return $this->redirectToRoute($routeName, [
                    'contentTypeGroupId' => $group->id,
                    'contentTypeId' => $contentTypeDraft->id,
                ]);
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return $this->render('@NetgenInformationCollection/admin/content_type/edit.html.twig.html.twig', [
            'content_type_group' => $group,
            'content_type' => $contentTypeDraft,
            'form' => $form->createView(),
        ]);
    }
}
