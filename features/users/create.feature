@user
Feature: Creating user
  In order to create login data for user
  As a confirmed administrator
  I need to be able to create a new user

  Background:
    Given confirmed admin named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions

  Scenario: can create new user filling all required fields
    Given I am on "user_new" page
    When I fill in "El. paštas" with "luke@datadog.lt"
    And I fill in "Vardas" with "Lilliana"
    And I fill in "Pavardė" with "Rodriguez"
    And I press "Išsaugoti"
    Then I should see success notification "Vartotojas sukurtas sėkmingai."

  Scenario: can't create without filling fields
    Given I am on "user_new" page
    When I press "Išsaugoti"
    Then I should see a form field error "Ši reikšmė negali būti tuščia."