<?php namespace AppBundle\Controller;

use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use AppBundle\Form\Type\Licence\TypeSelectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DepartmentController extends Controller implements VerifyTermsInterface, ChangePasswordInterface
{
    /**
     * @Route("/users-and-licences")
     * @Security("has_role('ROLE_DEPARTMENT')")
     * @Template
     */
    public function indexAction()
    {
        return [
            'roles' => $this->countUsersByRoles(),
            'licences' => $this->countLicences(),
        ];
    }

    /**
     * @return array
     */
    private function countUsersByRoles()
    {
        $users = $this->get('em')->getRepository(User::class)->createQueryBuilder('u')
            ->where('u.confirmed = 1')
            ->andWhere('u.enabled = 1')
            ->getQuery()->getResult();

        $counts = [];
        foreach (User::$roleMap as $name => $role) {
            $counts[$name] = 0;
        }

        foreach ($users as $user) {
            foreach ($user->getRoles() as $role) {
                $counts[$role]++;
            }
        }

        unset ($counts['ROLE_USER']);

        $data = [];
        foreach ($counts as $role => $count) {
            $data[$this->get('translator')->trans(User::toTranslation($role))] = $count;
        }

        ksort($data);

        return $data;
    }

    /**
     * @return array
     */
    private function countLicences()
    {
        $licences = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->select('l.type, count(l)')
            ->where('l.status IN(:status)')
            ->andWhere('l.expiresAt > :ea')
            ->groupBy('l.type')
            ->setParameter('status', Licence::$completedStatuses)
            ->setParameter('ea', (new \DateTime())->format('Y-m-d'))
            ->getQuery()->getResult();

        $licenceStatuses = $this->get('em')->getRepository(Licence::class)->createQueryBuilder('l')
            ->select('l.type, l.status, count(l)')
            ->where('l.status IN(:status)')
            ->andWhere('l.expiresAt > :ea')
            ->groupBy('l.type, l.status')
            ->setParameter('status', Licence::$visibleStatuses)
            ->setParameter('ea', (new \DateTime())->format('Y-m-d'))
            ->getQuery()->getResult();

        $filterTypes = (new TypeSelectType())->getTypesForFilter(false);
        $types = [];
        array_walk_recursive($filterTypes, function($val, $key) use (&$types) {
            $types[$key] = $val;
        });

        $counts = [];
        foreach ($types as $val => $name) {
            $counts[$val] = ['count' => 0, 'statuses' => []];
        }

        foreach ($licences as $licence) {

            $statuses = [];
            foreach ($licenceStatuses as $status) {
                if ($status['type'] == $licence['type']) {
                    $statuses[$status['status']] = [
                        'status' => $status['status'],
                        'count' => (int)$status[1],
                    ];
                }
            }

             $counts[$licence['type']] = [
                 'count' => (int)$licence[1],
                 'statuses' => $statuses
             ];
        }

        $data = [];
        foreach ($counts as $type => $count) {
            if (!$count['count']) {
                continue;
            }
            $data[$this->get('translator')->trans('licences.type.'.$type)] = $count;
        }
        ksort($data);

        return $data;
    }
}
