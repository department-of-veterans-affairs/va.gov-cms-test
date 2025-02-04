<?php

namespace tests\phpunit\va_gov_form_builder\functional\Form;

use tests\phpunit\va_gov_form_builder\Traits\SharedConstants;
use tests\phpunit\va_gov_form_builder\Traits\TestPageLoads;
use Tests\Support\Classes\VaGovExistingSiteBase;

/**
 * Functional test of the FormInfo form.
 *
 * @group functional
 * @group all
 *
 * @coversDefaultClass \Drupal\va_gov_form_builder\Form\FormInfo
 */
class FormInfoTest extends VaGovExistingSiteBase {

  use SharedConstants;
  use TestPageLoads;

  /**
   * {@inheritdoc}
   */
  private static $modules = ['va_gov_form_builder'];

  /**
   * Returns the url for this form.
   *
   * @param string|null $nid
   *   The node id. If null, returns create-mode url.
   *   If populated, returns edit-mode url.
   */
  private function getFormPageUrl($nid = NULL) {
    if (empty($nid)) {
      return '/form-builder/form-info';
    }

    return "/form-builder/{$nid}/form-info";
  }

  /**
   * Set up the environment for each test.
   */
  public function setUp(): void {
    parent::setUp();

    $this->loginFormBuilderUser();
    $this->drupalGet($this->getFormPageUrl());
  }

  /**
   * Test that the page is accessible to a user with the correct privilege.
   */
  public function testPageLoads() {
    $this->sharedTestPageLoads($this->getFormPageUrl(), 'Name this form');
  }

  /**
   * Test that the page is not accessible to a user without privilege.
   */
  public function testPageDoesNotLoad() {
    $this->sharedTestPageDoesNotLoad($this->getFormPageUrl());
  }

  /**
   * Test that the page has the expected subtitle.
   */
  public function testPageSubtitle() {
    $this->sharedTestPageHasExpectedSubtitle($this->getFormPageUrl(), 'Build a form');
  }

  /**
   * Test that the page loads correctly in create mode.
   *
   * Ensure form fields are empty (not pre-populated).
   */
  public function testPageLoadsInCreateMode() {
    $page = $this->getSession()->getPage();

    // Ensure form-name field is empty.
    $formNameInput = $page->findField('edit-title');
    $this->assertEquals($formNameInput->getValue(), '');

    // Ensure form-number field is empty.
    $formNumberInput = $page->findField('edit-field-va-form-number');
    $this->assertEquals($formNumberInput->getValue(), '');
  }

  /**
   * Test that the page loads correctly in edit mode.
   *
   * Ensure form fields are populated as expected.
   */
  public function testPageLoadsInEditMode() {
    $title = 'Test Digital Form ' . uniqid();
    $formNumber = '99-9999';

    // Create a new Digital Form node.
    $node = $this->createNode([
      'type' => 'digital_form',
      'title' => $title,
      'field_chapters' => [],
      'field_va_form_number' => $formNumber,
    ]);

    // Ensure page loads.
    $this->sharedTestPageLoads($this->getFormPageUrl($node->id()), 'Name this form');

    $page = $this->getSession()->getPage();

    // Ensure form-name field is populated correctly.
    $formNameInput = $page->findField('edit-title');
    $this->assertEquals($formNameInput->getValue(), $title);

    // Ensure form-number field is populated correctly.
    $formNumberInput = $page->findField('edit-field-va-form-number');
    $this->assertEquals($formNumberInput->getValue(), $formNumber);
  }

  /**
   * Test that the form submission succeeds.
   *
   * When proper information is entered, form should be submitted.
   */
  public function testFormSubmissionSucceeds() {
    // Fill in the form fields.
    $formInput = [
      'title' => 'Test Title',
      'field_va_form_number' => self::getUniqueVaFormNumber(),
      'field_omb_number' => '1111-1111',
      'field_respondent_burden' => '15',
      'field_expiration_date' => '2024-10-03',
    ];
    $this->submitForm($formInput, 'Save and continue');

    // Successful submission should take user to next page.
    $nextPageUrl = $this->getSession()->getCurrentUrl();
    $this->assertStringContainsString('/layout', $nextPageUrl);
  }

  /**
   * Test the form submission fails when missing required field.
   */
  public function testFormSubmissionFailsOnMissingRequiredField() {
    // Fill in the form fields.
    $formInput = [
      'title' => 'Test Title',
      'field_va_form_number' => self::getUniqueVaFormNumber(),
      'field_omb_number' => '1111-1111',
      'field_respondent_burden' => '15',
      // 'field_expiration_date' is required but missing
    ];
    $this->submitForm($formInput, 'Save and continue');

    // Check if the form submission was successful.
    $this->assertSession()->pageTextContains('Expiration date field is required.');
  }

}
