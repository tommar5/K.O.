@user
Feature: Deleting additional judges
  In order to delete judges when it is needed
  As a confirmed administrator
  I need to be able to delete additional judges

  Background:
    Given confirmed admin named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following competition information:
      | name  | date_from  | location     | description | email           | firstname | lastname  |
      | Ralis | 2015-12-01 | Druskininkai | ...         | luke@datadog.lt | Lilliana  | Rodriguez |
    And the following judges:
      | email            | firstname | lastname  |
      | user1@datadog.lt | One       | User      |
      | user2@datadog.lt | Other     | User      |

  Scenario: can delete judges
    Given I am on "competition_index" page
    When I click on the element with xpath "//a[@data-title='Peržiūrėti']"
    And I click on the element with xpath "//a[@data-title='Pašalinti']"
    Then I should see success notification "Teisėjas sėkmingai pašalintas iš varžybų."

