<?php

namespace AppBundle\Behat;

class PageContext extends BaseContext
{
    /**
     * @Given /^I am on "([^"]+)" page$/
     * @When /^I visit "([^"]+)" page$/
     */
    function iAmOnLandingPage($name)
    {
        switch ($name) {
        case "landing":
            return $this->visit("homepage");
        case "login":
            return $this->visit("app_auth_login");
        case "profile":
            return $this->visit("app_user_profile");
        case "user_index":
            return $this->visit("app_user_index");
        case "user_new":
            return $this->visit("app_user_new");
        case "competition_index":
            return $this->visit("app_competition_index");
        case "competition_new":
            return $this->visit("app_competition_new");
        case 'declarant_index':
            return $this->visit("app_declarant_index");
        case 'declarant_request':
            return $this->visit("app_declarant_change");
        case 'licences_index':
            return $this->visit("app_licences_index");
        case 'my_licences':
            return $this->visit("app_licences_my");
        default:
            throw new \InvalidArgumentException("Page: {$name} route is not defined yet.");
        }
    }

    /**
     * @Then /^I should see "([^"]+)" on page headline$/
     * @Then /^I should see "([^"]+)" in page headline$/
     */
    function iShouldSeeTextOnPageHeadline($text)
    {
        $this->notNull(
            $this->find('xpath', '//h1[contains(., "' . $text . '")] | //h2[contains(., "' . $text . '")] | //h3[contains(., "' . $text . '")]'),
            "Text '$text' was not found on page headline"
        );
    }

    /**
     * @Then /^the response code should be (\d+)$/
     */
    function theResponseCodeShouldBe($code)
    {
        $this->same(intval($code), $actual = $this->getSession()->getStatusCode(), "Invalid response code, expected $code, got $actual");
    }

    /**
     * @Then /^I should see (error|danger|success|info|notice) notification "([^"]+)"$/
     */
    function iShouldSeeNotification($type, $text)
    {
        switch ($type) {
        case 'error':
            $type = 'danger';
            break;
        case 'notice':
            $type = 'info';
            break;
        }

        $q = '//div[contains(@class, "alert") and contains(@class, "alert-' . $type . '") and contains(., "' . $text . '")]';
        $this->notNull($this->find('xpath', $q), "Notification of type '$type' with message '$text' was not found on page");
    }

    /**
     * @Then /^I should see a form field error "([^"]+)"$/
     */
    function iShouldSeeAFormFieldError($text)
    {
        $q = '//div[contains(@class, "has-error")]//span[contains(@class, "help-block") and contains(., "' . $text . '")]';
        $this->notNull($this->find('xpath', $q), "Form field error '$text' was not found on page");
    }
}
