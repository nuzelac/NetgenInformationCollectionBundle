<?php

namespace Netgen\Bundle\InformationCollectionBundle\Form\Type\FieldDefinition;

use EzSystems\RepositoryForms\Form\Type\FieldDefinition\FieldDefinitionType as BaseFieldDefinitionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

class FieldDefinitionType extends BaseFieldDefinitionType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('isInfoCollector', CheckboxType::class, [
            'required' => false, 'label' => 'field_definition.is_infocollector'
        ]);
    }
}
