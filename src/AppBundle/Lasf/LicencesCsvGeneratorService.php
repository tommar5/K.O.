<?php
namespace AppBundle\Lasf;

use AppBundle\Entity\Licence;
use AppBundle\Entity\LicenceRepository;
use AppBundle\Entity\User;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\Translation\TranslatorInterface;

class LicencesCsvGeneratorService
{
    const DRIVER_TRANSLATOR_PREFIX = 'licences.label.driver.';
    const GENDER_TRANSLATOR_PREFIX = 'user.label.genders.';

    const LIMIT = 3000;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $noData;

    /**
     * @var LicenceRepository
     */
    private $licenceRepo;

    /**
     * @var FilterLicencesService
     */
    private $licenceFilter;

    /**
     * @param TranslatorInterface $translator
     * @param LicenceRepository $licenceRepo
     * @param FilterLicencesService $licenceFilter
     */
    public function __construct(TranslatorInterface $translator, LicenceRepository $licenceRepo, FilterLicencesService $licenceFilter)
    {
        $this->translator = $translator;
        $this->noData = $translator->trans('licences.label.no_data');
        $this->licenceRepo = $licenceRepo;
        $this->licenceFilter = $licenceFilter;
    }

    /**
     * @param array $filters
     * @param User $user
     */
    public function licenceCSVGenerator(array $filters, User $user)
    {
        $licences = $this->licenceRepo->getLicencesQueryBuilder($user);

        if (!empty($filters)) {
            foreach ($filters['filters'] as $key => $val) {
                $licences = $this->licenceFilter->licencesFilter($licences, $key, $val);
            }
        }

        $licences->getQuery();

        $handle = fopen('php://output', 'w+');

        //Making UTF-8 CSV for Excel (bom = byte order mark)
        fputs($handle, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        $paginator = new Paginator($licences);

        $total = $paginator->count();
        $maxBatches = ceil($total / self::LIMIT);

        $batch = 1;

        if ($total) {
            do {
                $paginator->getQuery()
                    ->setFirstResult(self::LIMIT * ($batch - 1))
                    ->setMaxResults(self::LIMIT);
                foreach ($paginator->getIterator() as $key => $licence) {
                    if ($licence->getUser()->isEnabled() == true) {
                        $gender = $driver = $this->noData;

                        if ($licence->getType() != Licence::MEMBERSHIP_TYPE) {
                            $gender = $licence->getUser()->getGender() == User::GENDER_MALE ?
                                $this->translator->trans(self::GENDER_TRANSLATOR_PREFIX . 'male') :
                                $this->translator->trans(self::GENDER_TRANSLATOR_PREFIX . 'female');
                        }

                        if ($licence->isFirstDriver() && $licence->isSecondDriver()) {
                            $driver = $this->translator->trans(self::DRIVER_TRANSLATOR_PREFIX . 'first_driver') . ', ' .
                                $this->translator->trans(self::DRIVER_TRANSLATOR_PREFIX . 'second_driver');
                        } elseif ($licence->isFirstDriver()) {
                            $driver = $this->translator->trans(self::DRIVER_TRANSLATOR_PREFIX . 'first_driver');
                        } elseif ($licence->isSecondDriver()) {
                            $driver = $this->translator->trans(self::DRIVER_TRANSLATOR_PREFIX . 'second_driver');
                        }

                        $fields = [
                            'licences.label.id' => $licence->getId(),
                            'user.label.firstname' => $licence->getUser()->getFirstname(),
                            'user.label.lastname' => $licence->getUser()->getLastname(),
                            'user.label.gender' => $gender,
                            'user.label.email' => $licence->getUser()->getUsername() . ';',
                            'licences.label.user_phone' => $licence->getUser()->getPhone() ? $licence->getUser()->getPhone() : $this->noData,
                            'licences.label.phone_in_licence' => $licence->getMobileNumber() ? $licence->getMobileNumber() : $this->noData,
                            'licences.label.city' => $licence->getUser()->getCity() ? $licence->getUser()->getCity() : $this->noData,
                            'licences.label.type' => $this->translator->trans('licences.type.' . $licence->getType()),
                            'licences.label.status' => $this->translator->trans('licences.status.' . $licence->getStatus()),
                            'licences.label.serial_code' => $licence->getSeries() ? $licence->getLicenceNumber() : $this->noData,
                            'licences.label.created_at' => $licence->getCreatedAt()->format('Y-m-d'),
                            'licences.label.expires_at' => $licence->getExpiresAt()->format('Y-m-d'),
                            'licences.label.driver_type' => $driver,
                            'licences.label.sports' => $licence->getSports()->toArray() ? implode(', ', $licence->getSports()->toArray()) : $this->noData,
                            'licences.label.declarant.team_name' => $this->getDeclarantTeamName($licence),
                            'licences.label.declarant.lasf_name' => $this->getDeclarantLasfName($licence),
                            'licences.label.declarant.personal_code' => $this->getDeclarantPersonalCode($licence),
                            'licences.label.declarant.lasf_address' => $this->getDeclarantLasfAddress($licence),
                        ];

                        $header = [];

                        if ($key == 0) {
                            foreach ($fields as $headerTitle => $field) {
                                array_push($header, $this->translator->trans($headerTitle));
                            }
                            // Add the header of the CSV file
                            fputcsv($handle, $header, ';');
                        }
                        fputcsv($handle, $fields, ';');
                    }
                }
                $batch++;
            } while ($batch <= $maxBatches);
        }
        fclose($handle);
    }

    /**
     * @param Licence $licence
     * @return string
     */
    private function getDeclarantLasfName(Licence $licence)
    {
        if (in_array($licence->getType(), Licence::$judgeTypes)) {
            $var = $licence->getDeclarant()->getMemberName();
        } elseif ($licence->getType() == Licence::TYPE_MEMBERSHIP) {
            $var = $licence->getUser()->getMemberName();
        } else {
            $var = $licence->getLasfName();
        }

        return $var ? $var : $this->noData;
    }

    /**
     * @param Licence $licence
     * @return string
     */
    private function getDeclarantPersonalCode(Licence $licence)
    {
        if (in_array($licence->getType(), Licence::$judgeTypes)) {
            $var = $licence->getDeclarant()->getMemberCode();
        } elseif ($licence->getType() == Licence::TYPE_MEMBERSHIP) {
            $var = $licence->getUser()->getMemberCode();
        } else {
            $var = $licence->getPersonalCode();
        }

        return $var ? $var : $this->noData;
    }

    /**
     * @param Licence $licence
     * @return string
     */
    private function getDeclarantLasfAddress(Licence $licence)
    {
        if (in_array($licence->getType(), Licence::$judgeTypes)) {
            $var = $licence->getDeclarant()->getAddress();
        } elseif ($licence->getType() == Licence::TYPE_MEMBERSHIP) {
            $var = $licence->getUser()->getAddress();
        } else {
            $var = $licence->getLasfAddress();
        }

        return $var ? $var : $this->noData;
    }

    /**
     * @param Licence $licence
     * @return string
     */
    private function getDeclarantTeamName(Licence $licence)
    {
        $var = $this->noData;
        if (!(in_array($licence->getType(), Licence::$judgeTypes) || $licence->getType() == Licence::TYPE_MEMBERSHIP || $licence->getType() == Licence::TYPE_TRACK)) {
            $var = $licence->getLicence() ? $licence->getLicence()->getTeamName() : $licence->getTeamName();
        }

        return $var;
    }
}
