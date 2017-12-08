@user
Feature: Denying document
  In order to prevent from invalid documents
  As a confirmed administrator
  I need to be able to deny document

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one unconfirmed licence exists

  Scenario: can edit expiration date
    Given I am on "licences_index" page
    When I click on the element with xpath "//a[@data-title='Atmesti']"
    And I press "Išsaugoti"
    Then the response code should be 201