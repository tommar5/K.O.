<?php

namespace AppBundle\Lasf;

use AppBundle\Entity\Language;
use AppBundle\Entity\UserInfo;
use Doctrine\ORM\EntityManager;
use DataDog\PagerBundle\Pagination;
use AppBundle\Entity\Application;
use AppBundle\Entity\Licence;

class FilterTypesService
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * FilterTypesService constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;    
    }

    /**
     * @return array
     */
    public function getSportsType()
    {
        $sports = $this->em->getRepository('AppBundle:MusicStyle')->findAll();
        $sportTypes = [
            Pagination::$filterAny => 'all',
        ];
        foreach ($sports as $sport) {
            $sportTypes[$sport->getId()] = $sport->getName();
        }
        
        return $sportTypes;
    }

    /**
     * @return array
     */
    public function getApplicationStatuses()
    {
        return [
            Pagination::$filterAny => 'all',
            Application::STATUS_PAID => 'application.status.paid',
            Application::STATUS_UNCONFIRMED => 'application.status.unconfirmed',
            Application::STATUS_DECLINED => 'application.status.declined',
            Application::STATUS_NOT_PAID => 'application.status.not_paid',
            Application::STATUS_CONTRACT_UPLOADED_BY_ORGANISATOR => 'application.status.signed_application_by_organisator',
            Application::STATUS_CONTRACT_BY_ORGANISATOR_DELETED => 'application.status.contract_by_organisator_deleted',
            Application::STATUS_CONTRACT_UPLOADED_BY_LASF => 'application.status.signed_application_by_lasf',
            Application::STATUS_CONTRACT_BY_LASF_DELETED => 'application.status.contract_by_lasf_deleted',
            Application::STATUS_CANCELLED => 'application.status.cancelled',
            Application::STATUS_CONFIRMED => 'application.status.confirmed'
        ];
    }

    /**
     * @return array
     */
    public function getAllLicenceStatuses()
    {
        return [
            Pagination::$filterAny => 'all',
            Licence::STATUS_UPLOADED => 'licences.status.uploaded',
            Licence::STATUS_PAID => 'licences.status.paid',
            Licence::STATUS_NOT_PAID => 'licences.status.not_paid',
            Licence::STATUS_WAITING_EDIT => 'licences.status.waiting_edit',
            Licence::STATUS_WAITING_CONFIRM => 'licences.status.waiting_confirm',
            Licence::STATUS_EXTEND => 'licences.status.extend',
            Licence::STATUS_CANCELLED => 'licences.status.cancelled',
            Licence::STATUS_DECLINED => 'licences.status.declined',
            Licence::STATUS_PRODUCED => 'licences.produced_or_membership',
            Licence::STATUS_INVOICE => 'licences.status.invoice',
        ];
    }

    /**
     * @return array
     */
    public function getAccountantLicenceStatuses()
    {
        return [
            Pagination::$filterAny => 'all',
            Licence::STATUS_PAID => 'licences.status.paid',
            Licence::STATUS_NOT_PAID => 'licences.status.not_paid',
            Licence::STATUS_INVOICE => 'licences.status.invoice',
        ];
    }

    /**
     * @return array
     */
    public function getGenderTypes()
    {
        return [
            Pagination::$filterAny => 'all',
            UserInfo::GENDER_FEMALE => 'user.label.genders.female',
            UserInfo::GENDER_MALE => 'user.label.genders.male',
        ];
    }

    /**
     * @return array
     */
    public function getDriverTypes()
    {
        return [
            Pagination::$filterAny => 'all',
            Licence::FIRST_DRIVER => 'licences.label.driver.first_driver',
            Licence::SECOND_DRIVER => 'licences.label.driver.second_driver',
        ];
    }

    /**
     * @return array
     */
    public function getLanguageTypes()
    {
        return [
            Pagination::$filterAny => 'all',
            Language::LT_LANGUAGE => 'languages.LT',
            Language::EN_LANGUAGE => 'languages.EN',
            Language::RU_LANGUAGE => 'languages.RU',
            Language::FR_LANGUAGE => 'languages.FR',
            Language::OTHER_LANGUAGE => 'languages.Other',
        ];
    }

    /**
     * @return array
     */
    public function getLegalTypes()
    {
        return [
            Pagination::$filterAny => 'all',
            'natural' => 'user.index.filter.natural',
            'legal-all' => 'user.index.filter.legal_all',
            'legal' => 'user.index.filter.legal',
            'legal-associated' => 'user.index.filter.legal_associated',
        ];
    }
}
