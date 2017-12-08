@user
Feature: Request for new declarant
  In order to get a new declarant when I want
  As a confirmed racer
  I need to be able to send request for new declarant

  Background:
    Given confirmed racer named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And the following users:
      | email           | firstname | lastname  | role           |
      | luke@datadog.lt | Lilliana  | Rodriguez | ROLE_DECLARANT |

  Scenario: can send request
    Given I am on "declarant_request" page
    When I select "Lilliana Rodriguez" from "Norimas pareiškėjas"
    And I press "Išsaugoti"
    Then I should see success notification "Prašymas išsiųstas sėkmingai"