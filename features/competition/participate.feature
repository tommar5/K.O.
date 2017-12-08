@user
Feature: Participation
  In order to inform organisators that racer is participating in competition
  As a confirmed racer
  I need to be able to mark that I participate in competition

  Background:
    Given confirmed racer named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And the following competition information:
      | name  | date_from  | location     | description | email           | firstname | lastname  |
      | Ralis | 2015-12-01 | Druskininkai | ...         | luke@datadog.lt | Lilliana  | Rodriguez |

  Scenario: can mark that participating
    Given I am on "competition_index" page
    When I click on the element with xpath "//a[@data-title='Pažymėti, jog vyksiu į varžybas']"
    Then I should see success notification "Apie jūsų prašymą dalyvauti informavome organizatorius."