@user
Feature: Adding new licence
  In order to participate in competitions
  As a confirmed racer
  I need to be able to add new licence

  Background:
    Given confirmed racer named "Pierce Krajcik"
    And I'm logged in as "pierce.krajcik@datadog.lt"
    And I'm agreed with terms and conditions

  Scenario: can add licences
    Given I am on "my_licences" page
    When I follow "Pateikti licencijos prašymą"
    And I press "Sekantis žingsnis"
    And I attach the file "" to "licence_info_documents_0_file"
    And I attach the file "" to "licence_info_documents_1_file"
    And I press "Pateikti prašymą išrašyti licenciją"
    Then I should see success notification "Failai sėkmingai pateikti peržiūrai."
