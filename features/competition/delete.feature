@user
Feature: Deleting competition
  In order to remove unnecessary competition
  As a confirmed organisator
  I need to be able to delete competition

  Background:
    Given confirmed organisator named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following competition information:
      | name  | date_from  | location     | description | email           | firstname | lastname  |
      | Ralis | 2015-12-01 | Druskininkai | ...         | luke@datadog.lt | Lilliana  | Rodriguez |

  Scenario: can delete competition
    Given I am on "competition_index" page
    When I click on the element with xpath "//a[@data-title='Pašalinti']"
    Then I should see success notification "Varžybos pašalintos sėkmingai."
