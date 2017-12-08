<?php namespace AppBundle\Form;

use AppBundle\Entity\ApplicationAgreement;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ApplicationAgreementType extends AbstractType
{

    /**
     * @var ApplicationAgreement
     */
    private $agreement;

    public function __construct(ApplicationAgreement $agreement)
    {
        $this->agreement = $agreement;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', 'textarea', [
            'label' => 'application_agreement.label.content',
            'required' => true,
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'application_agreement';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ApplicationAgreement::class,
        ]);
    }
}
