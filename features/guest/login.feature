@user
Feature: Logging in
  In order to use application and manage private resources
  As a registered user
  I need to be able to login

  Background:
    Given confirmed user named "Vienas Vartotojas"

  Scenario: can't login with incorrect credentials
    Given I am on "login" page
    When I try to login as "vienas.vartotojas@datadog.lt" using password "any"
    Then I should see error notification "Neteisingas el. pašto adresas arba slaptažodis."

  Scenario: confirmed user is able to login
    Given I am on "login" page
    When I try to login as "vienas.vartotojas@datadog.lt" using password "S3cretpassword"
    Then I should be logged in

  Scenario: unconfirmed user cannot login
    Given unconfirmed user named "Antras Vartotojas"
    And I am on "login" page
    When I try to login as "antras.vartotojas@datadog.lt" using password "S3cretpassword"
    Then I should see error notification "Vartotojas užblokuotas."
