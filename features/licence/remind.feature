@user
Feature: Reminding about not paid licence
  In order to inform racer about not paid licence
  As a confirmed administrator
  I need to be able to remind about not paid licence

  Background:
    Given confirmed admin named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions
    And one confirmed licence exists

  Scenario: remind about not paid licence
    Given I am on "licences_index" page
    When I click on the element with xpath "//a[@data-title='Išsiųsti priminimą apie neapmokėtą licenciją']"
    Then I should see success notification "Pranešimas apie neapmokėtą licenciją išsiųstas sėkmingai"