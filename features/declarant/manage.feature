@user
Feature: Managing of requests for new declarants
  In order to allow or not to have new declarant for racer
  As a confirmed administrator
  I need to be able to accept or deny requests for new declarants

  Background:
    Given confirmed admin named "General Grievous"
    And I'm logged in as "general.grievous@datadog.lt"
    And I'm agreed with terms and conditions
    And the following users and request for declarant:
      | email            | firstname | lastname  | role           |
      | user1@datadog.lt | One       | User      | ROLE_RACER     |
      | user2@datadog.lt | Other     | User      | ROLE_DECLARANT |
      | user3@datadog.lt | Another   | User      | ROLE_DECLARANT |

  Scenario: can accept request
    Given I am on "declarant_index" page
    And I should not see "Patvirtinta"
    When I click on the element with xpath "//a[@data-title='Patvirtinti']"
    Then I should see "Patvirtinta"

  Scenario: can deny request
    Given I am on "declarant_index" page
    And I should not see "Atmesta"
    When I click on the element with xpath "//a[@data-title='Atmesti']"
    Then I should see "Atmesta"



