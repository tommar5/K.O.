@user
Feature: Adding additional judges
  In order to add judges when it is needed
  As a confirmed administrator
  I need to be able to add additional judges

  Background:
    Given confirmed admin named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following competition information:
      | name  | date_from  | location     | description | email           | firstname | lastname  |
      | Ralis | 2015-12-01 | Druskininkai | ...         | luke@datadog.lt | Lilliana  | Rodriguez |
    And the following users:
      | email            | firstname | lastname  | role       |
      | user1@datadog.lt | One       | User      | ROLE_JUDGE |
      | user2@datadog.lt | Other     | User      | ROLE_JUDGE |

  Scenario: can add additional judge
    Given I am on "competition_index" page
    When I click on the element with xpath "//a[@data-title='Peržiūrėti']"
    And I select "Other User" from "Teisėjas"
    And I press "Priskirti teisėją"
    Then I should see success notification "Teisėjas varžyboms priskirtas sėkmingai."
