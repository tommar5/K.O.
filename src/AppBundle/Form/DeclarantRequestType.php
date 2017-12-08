<?php namespace AppBundle\Form;

use AppBundle\Entity\DeclarantRequest;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DeclarantRequestType extends AbstractType
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newDeclarant', 'entity', [
                'label' => 'declarant.label.new',
                'class' => User::class,
                'query_builder' => function (EntityRepository $er) {
                    $declarants = $er->createQueryBuilder('u')
                        ->where('BIT_AND(u.roles, :role) > 0')
                        ->andWhere('u.enabled = 1')
                        ->andWhere('u.confirmed = 1')
                        ->setParameter('role', User::$roleMap['ROLE_DECLARANT']);

                    $currentDecl = $this->user->getParent();
                    if ($currentDecl) {
                        $declarants->andWhere('u.id != :currentDeclarant')
                            ->setParameter('currentDeclarant', $currentDecl->getId());
                    }

                    return $declarants;
                },
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('comment', 'textarea', [
                'label' => 'declarant.label.comment',
                'required' => false,
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
            'data_class' => DeclarantRequest::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'declarant';
    }
}
