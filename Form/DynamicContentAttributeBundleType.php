<?php

namespace PN\ContentBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use PN\ContentBundle\Entity\DynamicContentAttribute;
use Symfony\Component\Validator\Constraints\Length;

class DynamicContentAttributeBundleType extends AbstractType {

    protected $em;
    protected $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->em = $container->get("doctrine")->getManager();
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $data = $options["data"];

        foreach ($data as $attribute) {
            $lable = "" . $attribute->getTitle() . " #" . $attribute->getId();
            $attr = ["placeholder" => $attribute->getTypeName()];
            $constraints = [];
            $inputValue = $attribute->getValue();
            switch ($attribute->getType()) {
                case DynamicContentAttribute::TYPE_TEXT:
                    $inputType = TextType::class;
                    $constraints = [new Length(["min" => 0, "max" => 100])];
                    $attr["maxlength"] = 100;
                    break;
                case DynamicContentAttribute::TYPE_LONGTEXT:
                    $inputType = TextareaType::class;
                    break;
                case DynamicContentAttribute::TYPE_LINK:
                    $inputType = UrlType::class;
                    break;
                case DynamicContentAttribute::TYPE_IMAGE:
                    $inputType = FileType::class;
                    $attr["accept"] = "image/*";
                    $attr["class"] = "file-styled";
                    break;
                case DynamicContentAttribute::TYPE_DOCUMENT:
                    $inputType = FileType::class;
                    $attr["class"] = "file-styled";
                    break;
                default :
                    $inputType = null;
                    break;
            }
            if ($inputType !== null) {
                $builder->add($attribute->getId(), $inputType, ["label" => $lable, "constraints" => $constraints, 'data' => $inputValue, "required" => false, "attr" => $attr, "data_class" => null]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            'data_class' => null
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix() {
        return 'pn_bundle_cmsbundle_dynamiccontentattribute_eav';
    }

}
