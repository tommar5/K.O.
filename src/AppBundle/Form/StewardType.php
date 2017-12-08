<?php namespace AppBundle\Form;

use AppBundle\Entity\Licence;
use AppBundle\Entity\Steward;
use AppBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Validator\Constraints\NotBlank;

class StewardType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('user', 'entity', [
                'class' => 'AppBundle:User',
                'label' => 'steward.label.user',
                'required' => true,
                'placeholder' => 'steward.user_select.placeholder',
                'choice_label' => 'fullName',
                'constraints' => [
                    new NotBlank()
                ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->select(['u', 'l'])
                        ->leftJoin('u.licences', 'l')
                        ->where('l.type = :licenceFirst or l.type = :licenceSecond or l.type = :licenceNational or l.type = :licenceInternational')
                        ->andWhere('BIT_AND(u.roles, :role) > 0')
                        ->setParameter('role', User::$roleMap['ROLE_JUDGE'])
                        ->setParameter('licenceFirst', Licence::TYPE_JUDGE_FIRST)
                        ->setParameter('licenceSecond', Licence::TYPE_JUDGE_SECOND)
                        ->setParameter('licenceNational', Licence::TYPE_JUDGE_NATIONAL)
                        ->setParameter('licenceInternational', Licence::TYPE_JUDGE_INTERNATIONAL);
                },
            ])
            ->add('sports', 'entity', [
                'class' => 'AppBundle:Sport',
                'label' => 'steward.label.sports',
                'required' => true,
                'placeholder' => 'steward.sport_select.placeholder',
                'choice_label' => 'name',
                'multiple' => true,

            ]);
        }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'steward';
    }

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Steward::class,
        ]);
    }
}
