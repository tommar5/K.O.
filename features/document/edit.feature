@user
Feature: Editing document
  In order to keep correct data of document
  As a confirmed administrator
  I need to be able to edit document

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one unconfirmed licence exists

  Scenario: can edit expiration date
    Given I am on "licences_index" page
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    And I fill in "Dokumento numeris" with "5154456"
    And I fill in "Galioja iki" with "2015-12-15"
    And I press "Išsaugoti"
    Then the response code should be 201
