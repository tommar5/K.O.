@user
Feature: Editing competition
  In order to keep competition information up to date
  As a confirmed organisator
  I need to be able to edit competition

  Background:
    Given confirmed organisator named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following competition information:
      | name  | date_from  | location     | description | email           | firstname | lastname  |
      | Ralis | 2015-12-01 | Druskininkai | ...         | luke@datadog.lt | Lilliana  | Rodriguez |

  Scenario: can edit competition data
    Given I am on "competition_index" page
    When I click on the element with xpath "//a[@data-title='Redaguoti']"
    When I fill in "Pavadinimas" with "Naujos varžybos"
    And I fill in "Nuo" with "2015-11-07 17:00"
    And I fill in "Vieta" with "Kazlų rūda"
    And I fill in "Aprašymas" with "Čia vyks nuostabios varžybos"
    And I select "Lilliana Rodriguez" from "Vadovas"
    And I press "Išsaugoti"
    Then I should see success notification "Varžybos atnaujintos sėkmingai."