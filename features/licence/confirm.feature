@user
Feature: Confirming licence
  In order to let pay for licence
  As a confirmed administrator
  I need to be able to confirm this licence

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one unconfirmed licence exists

  Scenario: can confirm licence
    Given I am on "licences_index" page
    And I should see "Laukia patvirtinimo"
    When I click on the element with xpath "//a[@data-title='Patvirtinti']"
    Then I should see "Neapmokėta"
