<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type\ContentType;

use EzSystems\RepositoryForms\Form\Type\ContentType\ContentTypeUpdateType as BaseContentTypeUpdateType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldDefinition\FieldDefinitionType;

class ContentTypeUpdateType extends BaseContentTypeUpdateType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('fieldDefinitionsData', CollectionType::class, [
            'entry_type' => FieldDefinitionType::class,
            'entry_options' => ['languageCode' => $options['languageCode']],
            'label' => 'content_type.field_definitions_data',
        ]);
    }
}
