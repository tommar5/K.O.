@user
Feature: Licence is marking as paid
  In order to allow for racer to participate in competitions
  As a confirmed administrator
  I need to be able to mark as paid racer's licence

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one confirmed licence exists

  Scenario: can mark as paid
    Given I am on "licences_index" page
    And I should see "Neapmokėta"
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    And I follow "pažymėti kaip apmokėtą"
    Then I should see "Apmokėta"