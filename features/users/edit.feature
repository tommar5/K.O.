@user
Feature: Editing user
  In order to change user information
  As a confirmed administrator
  I need to be able to edit user data

  Background:
    Given confirmed admin named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions

  Scenario: can edit user data
    Given the following users:
      | email            | firstname | lastname  | role           |
      | user1@datadog.lt | One       | User      | ROLE_DECLARANT |
    And I am on "user_index" page
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    And I fill in "El. paštas" with "luke@datadog.lt"
    And I fill in "Vardas" with "Lilliana"
    And I fill in "Pavardė" with "Rodriguez"
    And I select "One User" from "user_parent"
    And I press "Išsaugoti"
    Then I should see success notification "Vartotojo informacija atnaujinta sėkmingai."

  Scenario: can remove assigned declarant
    Given I have assigned this declarant:
      | email            | firstname | lastname  |
      | user1@datadog.lt | One       | User      |
    And I am on "user_index" page
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    And I select "pareiškėjas nepriskirtas" from "user_parent"
    And I press "Išsaugoti"
    Then I should see success notification "Vartotojo informacija atnaujinta sėkmingai."
