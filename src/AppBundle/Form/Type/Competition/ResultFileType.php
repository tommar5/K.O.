<?php namespace AppBundle\Form\Type\Competition;

use AppBundle\Entity\Competition;
use AppBundle\Entity\FileUpload;
use AppBundle\Form\DocumentType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultFileType extends AbstractType
{
    /**
     * @var Competition
     */
    private $comp;

    public function __construct(Competition $comp)
    {
        $this->comp = $comp;

        $this->comp->addDocument(new FileUpload(FileUpload::TYPE_COMP_RESULT));
        $this->comp->addDocument(new FileUpload(FileUpload::TYPE_OTHER));
        $this->comp->addDocument(new FileUpload(FileUpload::TYPE_OTHER));
    }
    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'resultFiles';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('documents', 'collection', [
            'by_reference' => false,
            'type' => new DocumentType(null, false, $this->comp->getLicence()),
        ]);
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
            'validation_groups' => ['documents']
        ]);
    }
}
