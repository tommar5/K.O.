@user
Feature: Editing licence
  In order to keep licence information up to date
  As a confirmed administrator
  I need to be able to edit licence

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one confirmed licence exists

  Scenario: can edit expiration date
    Given I am on "licences_index" page
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    And I fill in "Pabaigos data" with "2015-12-15"
    And I press "Išsaugoti"
    Then I should see success notification "Licencijos informacija atnaujinta sėkmingai"
