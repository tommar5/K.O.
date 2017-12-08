<?php namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\ORM\QueryBuilder;
use DataDog\PagerBundle\Pagination;
use DataDog\AuditBundle\Entity\AuditLog;

class AuditController extends Controller implements VerifyTermsInterface
{
    use DoctrineController;

    /**
     * @Route("/audit")
     * @Method("GET")
     * @Template
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function indexAction(Request $request)
    {
        Pagination::$defaults = array_merge(Pagination::$defaults, ['limit' => 10]);

        $qb = $this->get('em')->getRepository(AuditLog::class)
            ->createQueryBuilder('a')
            ->addSelect('s', 't', 'b')
            ->innerJoin('a.source', 's')
            ->leftJoin('a.target', 't')
            ->leftJoin('a.blame', 'b');

        $options = [
            'sorters' => ['a.loggedAt' => 'DESC'],
            'applyFilter' => [$this, 'filters'],
        ];

        $sourceClasses = [
            Pagination::$filterAny => $this->get('translator')->trans('audit.pagination.any_source'),
        ];

        foreach ($this->getDoctrine()->getManager()->getMetadataFactory()->getAllMetadata() as $meta) {
            if ($meta->isMappedSuperclass || strpos($meta->name, 'DataDog\AuditBundle') === 0) {
                continue;
            }
            $parts = explode('\\', $meta->name);
            $sourceClasses[$meta->name] = end($parts);
        }

        $users = [
            Pagination::$filterAny => $this->get('translator')->trans('audit.pagination.any_user'),
            'null' => $this->get('translator')->trans('audit.pagination.unknown'),
        ];
        foreach ($this->get('em')->getRepository(User::class)->findAll() as $user) {
            $users[$user->getId()] = (string) $user;
        }

        $logs = new Pagination($qb, $request, $options);
        return compact('logs', 'sourceClasses', 'users');
    }

    /**
     * @Route("/audit/diff/{id}")
     * @Method("GET")
     * @Security("has_role('ROLE_ADMIN')")
     * @Template
     */
    public function diffAction(AuditLog $log)
    {
        return compact('log');
    }

    public function filters(QueryBuilder $qb, $key, $val)
    {
        switch ($key) {
        case 'history':
            if ($val) {
                $orx = $qb->expr()->orX();
                $orx->add('s.fk = :fk');
                $orx->add('t.fk = :fk');

                $qb->andWhere($orx);
                $qb->setParameter('fk', intval($val));
            }
            break;
        case 'class':
            $orx = $qb->expr()->orX();
            $orx->add('s.class = :class');
            $orx->add('t.class = :class');

            $qb->andWhere($orx);
            $qb->setParameter('class', $val);
            break;
        case 'blamed':
            if ($val === 'null') {
                $qb->andWhere($qb->expr()->isNull('a.blame'));
            } else {
                // this allows us to safely ignore empty values
                // otherwise if $qb is not changed, it would add where the string is empty statement.
                $qb->andWhere($qb->expr()->eq('b.fk', ':blame'));
                $qb->setParameter('blame', $val);
            }
            break;
        default:
            // if user attemps to filter by other fields, we restrict it
            throw new \Exception("filter not allowed");
        }
    }

}
