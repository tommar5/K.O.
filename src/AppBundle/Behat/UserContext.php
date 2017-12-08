<?php

namespace AppBundle\Behat;

use AppBundle\Entity\Competition;
use AppBundle\Entity\CompetitionJudge;
use AppBundle\Entity\DeclarantRequest;
use AppBundle\Entity\FileUpload;
use AppBundle\Entity\Licence;
use AppBundle\Entity\User;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;

class UserContext extends BaseContext
{
    /**
     * @BeforeScenario
     */
    function resetSecurityContext()
    {
        $this->get('security.context')->setToken(null);
    }

    /**
     * @Given /^(confirmed|unconfirmed) (user|racer|organisator|admin) named "([^"]+)"$/
     */
    function userNamed($status, $type, $name)
    {
        $names = explode(' ', $name);
        list ($firstname, $lastname) = $names;

        $em = $this->get('em');
        $user = new User();
        if ('confirmed' === $status) {
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
        }
        $user->setEmail(strtolower(implode('.', $names)) . '@datadog.lt');
        $user->setRoles($type == 'user' ? ['ROLE_USER'] : ($type == 'racer' ? ['ROLE_RACER'] :
                            ($type == 'organisator' ? ['ROLE_ORGANISATOR'] :['ROLE_ADMIN'])));

        if ('unconfirmed' === $status) {
            $user->setEnabled(false);
        } else {
            $user->setEnabled(true);
            $encoder = $this->get('security.encoder_factory')->getEncoder($user);
            $user->setPassword($encoder->encodePassword('S3cretpassword', $user->getSalt()));
        }
        $em->persist($user);
        $em->flush();
        return $user;
    }

    /**
     * @Given /^I'm logged in as "([^"]*)"$/
     * @When /^I login as "([^"]*)" using password "([^"]*)"$/
     * @When /^I try to login as "([^"]*)" using password "([^"]*)"$/
     */
    function iTryToLoginAsUsingPassword($email, $password = 'S3cretpassword')
    {
        $this->visit('app_auth_login');
        $this->mink->fillField("username", $email);
        $this->mink->fillField("password", $password);
        $this->mink->pressButton("_submit");
    }

    /**
     * @Then /^I should be logged in$/
     */
    function iShouldBeLoggedIn()
    {
        $this->true($this->get('security.context')->getToken()->getUser() instanceof User);
    }

    /**
     * @Given /^I'm agreed with terms and conditions$/
     */
    function iAgreedWithTermsAndConditions()
    {
        $this->mink->checkOption("Sutinku su naudojimo taisyklėmis");
        $this->mink->pressButton("Patvirtinti");
    }

    /**
     * @Given /^the following users:$/
     */
    public function pushUsers(TableNode $usersTable)
    {
        $em = $this->get('em');
        foreach ($usersTable as $userHash) {
            $user = new User();
            $user->setEmail($userHash['email']);
            $user->setFirstname($userHash['firstname']);
            $user->setLastname($userHash['lastname']);
            $user->setRole(User::$roleMap[$userHash['role']]);
            $em->persist($user);
        }
        $em->flush();
    }

    /**
     * @Given /^the following judges:$/
     */
    public function pushJudges(TableNode $judgesTable)
    {
        $em = $this->get('em');
        foreach ($judgesTable as $judgeHash) {
            $user = new User();
            $user->setEmail($judgeHash['email']);
            $user->setFirstname($judgeHash['firstname']);
            $user->setLastname($judgeHash['lastname']);
            $user->setRole(User::$roleMap['ROLE_JUDGE']);
            $em->persist($user);

            $judge = new CompetitionJudge();
            $judge->setRole(CompetitionJudge::ROLE_SPORTO_KOMISARAS);
            $judge->setUser($user);

            /** @var Competition $competition */
            $competition = current($em->getRepository(Competition::class)->findAll());
            $competition->addJudge($judge);

            $judge->setCompetition($competition);
            $em->persist($judge);
        }
        $em->flush();
    }

    /**
     * @Given /^the following users and request for declarant:$/
     */
    public function pushUsersAndDeclRequest(TableNode $usersTable)
    {
        $users = [];
        $em = $this->get('em');
        foreach ($usersTable as $userHash) {
            $user = new User();
            $user->setEmail($userHash['email']);
            $user->setFirstname($userHash['firstname']);
            $user->setLastname($userHash['lastname']);
            $user->setRole(User::$roleMap[$userHash['role']]);
            $em->persist($user);
            $users[] = $user;
        }

        $declarantRequest = new DeclarantRequest();
        $declarantRequest->setRacer($users[0]);
        $declarantRequest->setCurrentDeclarant($users[1]);
        $declarantRequest->setNewDeclarant($users[2]);
        $declarantRequest->setStatus('status.waiting');
        $em->persist($declarantRequest);

        $em->flush();
    }

    /**
     * @Given /^the following competition information:$/
     */
    public function pushCompetitions(TableNode $competitionsTable)
    {
        $em = $this->get('em');
        foreach ($competitionsTable as $compHash) {
            $competition = new Competition();
            $competition->setName($compHash['name']);
            $competition->setDateFrom(new \DateTime($compHash['date_from']));
            $competition->setLocation($compHash['location']);
            $competition->setDescription($compHash['description']);

            $judge = new User();
            $judge->setEmail($compHash['email']);
            $judge->setFirstname($compHash['firstname']);
            $judge->setLastname($compHash['lastname']);
            $judge->setRole(User::$roleMap['ROLE_JUDGE']);

            $competition->setMainJudge($judge);
            $competition->setUser(current($em->getRepository(User::class)->findAll()));

            $em->persist($competition);
            $em->persist($judge);
        }
        $em->flush();
    }

    /**
     * @Given /^one (confirmed|unconfirmed) licence exists$/
     */
    public function pushLicences($status)
    {
        $em = $this->get('em');

        $user = new User();
        $user->setEmail('a@a.com');
        $user->setFirstname('a');
        $user->setLastname('a');
        $user->setRole(User::$roleMap['ROLE_RACER']);
        $em->persist($user);

        $licence = new Licence();
        $licence->setType(Licence::TYPE_DRIVER_M);
        $licence->setUser($user);
        $licence->setExpiresAt(new \DateTime(date('Y-12-31')));
        $licence->addDocument(new FileUpload(FileUpload::TYPE_DRIVERS_LICENCE));
        $licence->addDocument(new FileUpload(FileUpload::TYPE_MED_CERT));

        foreach ($licence->getDocuments() as $doc) {
            $doc->setStatus($status == 'confirmed' ? FileUpload::STATUS_APPROVED : FileUpload::STATUS_NEW);
            $doc->setFileName('file');
            $user->addDocument($doc);
            $em->persist($doc);
        }

        $licence->setStatus($status == 'confirmed' ? Licence::STATUS_NOT_PAID : Licence::STATUS_UPLOADED);

        $em->persist($licence);
        $em->flush();
    }

    /**
     * @When /^I click on the element with xpath "([^"]*)"$/
     */
    public function iClickOnTheElementWithXPath($xpath)
    {
        $session = $this->getSession();
        $element = $session->getPage()->find(
            'xpath',
            $session->getSelectorsHandler()->selectorToXpath('xpath', $xpath)
        );

        if (null === $element) {
            throw new \InvalidArgumentException(sprintf('Could not evaluate XPath: "%s"', $xpath));
        }

        $element->click();
    }

    /**
     * @Given I have assigned this declarant:
     */
    public function iHaveDeclarant(TableNode $declarantTable)
    {
        $em = $this->get('em');

        foreach ($declarantTable as $declHash) {
            $declarant = new User();
            $declarant->setEmail($declHash['email']);
            $declarant->setFirstname($declHash['firstname']);
            $declarant->setLastname($declHash['lastname']);
            $declarant->setRole(User::$roleMap['ROLE_DECLARANT']);

            /** @var User $user */
            $user = current($em->getRepository(User::class)->findAll());
            $user->setParent($declarant);

            $em->persist($declarant);
        }

        $em->flush();
    }

    /**
     * @Given /^user "([^"]*)" has role "([^"]*)"$/
     */
    public function userHasRole($user, $role)
    {
        /** @var User $user */
        $user = $this->repo(User::class)->findOneBy(['email' => $user]);
        $user->addRole(strtoupper('role_'.$role));

        $this->get('em')->flush();
    }

    /**
     * @Then /^I should see declarant information$/
     */
    public function iShouldSeeDeclarentInformation()
    {
        $this->mink->assertElementOnPage('#profile_memberName');
    }

}
