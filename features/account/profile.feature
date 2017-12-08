@user
Feature: Profile management
  In order to keep account up to date
  As a confirmed user
  I need to be able to update my profile

  Background:
    Given confirmed user named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And user "general.grievous@datadog.lt" has role "declarant"

  Scenario: can update without filling in password
    Given I am on "profile" page
    When I fill in "Apie mane" with "Dark"
    And I press "Išsaugoti atliktus pakeitimus"
    Then I should see success notification "Vartotojo informacija atnaujinta sėkmingai."

  Scenario: can change password
    Given I am on "profile" page
    When I fill in "Slaptažodis" with "S3cretpass"
    And I fill in "Slaptažodis (kartoti)" with "S3cretpass"
    And I press "Išsaugoti atliktus pakeitimus"
    Then I should see success notification "Vartotojo informacija atnaujinta sėkmingai."

  Scenario: declarants can fill extra info
    Given I am on "profile" page
    Then I should see declarant information
