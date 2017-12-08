@user
Feature: Creating competition
  In order to inform racers and judges about new competition
  As a confirmed organisator
  I need to be able to create a new competition

  Background:
    Given confirmed organisator named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following users:
      | email           | firstname | lastname  | role       |
      | luke@datadog.lt | Lilliana  | Rodriguez | ROLE_JUDGE |

  Scenario: can create new competition filling all required fields
    Given I am on "competition_new" page
    When I fill in "Pavadinimas" with "Ralis"
    And I fill in "Nuo" with "2015-11-05 16:00"
    And I fill in "Vieta" with "Druskininkai"
    And I fill in "Aprašymas" with "Čia vyks nuostabios varžybos"
    And I select "Lilliana Rodriguez" from "Vadovas"
    And I press "Išsaugoti"
    Then I should see success notification "Varžybos sukurtos sėkmingai."

  Scenario: can't enter date to earlier than date from
    Given I am on "competition_new" page
    When I fill in "Nuo" with "2015-11-05 16:00"
    And I fill in "Iki" with "2015-11-04 16:00"
    And I press "Išsaugoti"
    Then I should see a form field error "Pabaigos data negali būti ankstenė nei pradžios data"
