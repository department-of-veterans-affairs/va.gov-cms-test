<?php

namespace tests\phpunit\va_gov_form_builder\unit\Form\Base;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Drupal\va_gov_form_builder\Form\Base\FormBuilderStepBase;
use Drupal\va_gov_form_builder\Service\DigitalFormsService;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use tests\phpunit\va_gov_form_builder\Traits\AnonymousFormClass;
use Tests\Support\Classes\VaGovUnitTestBase;

/**
 * Unit tests for the abstract class FormBuilderStepBase.
 *
 * @todo DRY this up.
 * This test file includes a lot of code that is repeated
 * from FormBuilderFormBaseTest. The entire suite should be
 * examined for ways to DRY things up.
 *
 * @group unit
 * @group all
 *
 * @coversDefaultClass \Drupal\va_gov_form_builder\Form\Base\FormBuilderStepBase
 */
class FormBuilderStepBaseTest extends VaGovUnitTestBase {

  /**
   * The Digital Forms service.
   *
   * @var \Drupal\va_gov_form_builder\Service\DigitalFormsService
   */
  protected $digitalFormsService;

  /**
   * An instance of an anonymous class that extends the abstract class.
   *
   * @var \Drupal\Core\Form\FormBuilderBase
   */
  private $classInstance;

  /**
   * Setup the environment for each test.
   */
  public function setUp(): void {
    parent::setUp();

    $entityTypeManager = $this->createMock(EntityTypeManagerInterface::class);
    $this->digitalFormsService = $this->createMock(DigitalFormsService::class);
    $session = $this->createMock(SessionInterface::class);

    // Create an anonymous instance of a class that extends our abstract class.
    $this->classInstance = new class(
      $entityTypeManager,
      $this->digitalFormsService,
      $session,
    ) extends FormBuilderStepBase {
      use AnonymousFormClass;

      /**
       * getFields.
       */
      protected function getFields() {
        return [
          'test_field_1',
          'test_field_2',
        ];
      }

      /**
       * setStepParagraphFromFormState.
       */
      protected function setStepParagraphFromFormState(FormStateInterface $form_state) {
        // Do nothing. We'll set the step paragraph via reflection.
      }

    };
  }

  /**
   * Test that the buildForm method throws error when paramaters missing.
   */
  public function testBuildFormThrowsError() {
    $form = [];
    $formStateMock = $this->createMock(FormStateInterface::class);

    // Call `buildForm` without $digitalForm or $stepParagraph parameters.
    $this->expectException(\InvalidArgumentException::class);
    $form = $this->classInstance->buildForm($form, $formStateMock);
  }

  /**
   * Helper function to set up tests for violations.
   *
   * @param \Symfony\Component\Validator\ConstraintViolationList $violationList
   *   The expected violations.
   */
  private function setUpViolationTest($violationList = NULL) {
    if (!$violationList) {
      $violationList = new ConstraintViolationList([]);
    }

    $stepParagraph = $this->getMockBuilder(Paragraph::class)
      ->disableOriginalConstructor()
      ->onlyMethods(['validate'])
      ->getMock();

    $stepParagraph->method('validate')
      ->willReturnCallback(function () use ($violationList) {
          return $violationList;
      });

    $reflection = new \ReflectionClass($this->classInstance);
    $stepParagraphProperty = $reflection->getProperty('stepParagraph');
    $stepParagraphProperty->setAccessible(TRUE);
    $stepParagraphProperty->setValue($this->classInstance, $stepParagraph);
  }

  /**
   * Test the validateForm method with a step paragraph with no violations.
   */
  public function testValidateFormWithNoViolations() {
    $this->setUpViolationTest();

    $form = [];

    $formStateMock = $this->createMock(FormStateInterface::class);
    $formStateMock->expects($this->never())
      ->method('setErrorByName');

    $this->classInstance->validateForm($form, $formStateMock);
  }

  /**
   * Test validateForm method with a step paragraph with applicable violations.
   */
  public function testValidateFormWithApplicableViolations() {
    // Has violations on fields related to this form;
    // should raise errors.
    $violationList = new ConstraintViolationList([
      new ConstraintViolation('Invalid value 1', '', [], '', 'test_field_1', 'Invalid value'),
      new ConstraintViolation('Invalid value 2', '', [], '', 'test_field_2', 'Invalid value'),
    ]);

    $this->setUpViolationTest($violationList);

    $form = [];

    $formStateMock = $this->createMock(FormStateInterface::class);
    $formStateMock->expects($this->exactly(2))
      ->method('setErrorByName')
      ->withConsecutive(
        ['test_field_1', 'Invalid value 1'],
        ['test_field_2', 'Invalid value 2'],
      );

    $this->classInstance->validateForm($form, $formStateMock);
  }

  /**
   * Test validateForm method with a step paragraph with other violations.
   */
  public function testValidateFormWithOtherViolations() {
    // Has violations, but not on fields related to this form;
    // should not raise errors.
    $violationList = new ConstraintViolationList([
      new ConstraintViolation('Invalid value 3', '', [], '', 'test_field_3', 'Invalid value'),
      new ConstraintViolation('Invalid value 4', '', [], '', 'test_field_4', 'Invalid value'),
    ]);

    $this->setUpViolationTest($violationList);

    $form = [];

    $formStateMock = $this->createMock(FormStateInterface::class);
    $formStateMock->expects($this->never())
      ->method('setErrorByName');

    $this->classInstance->validateForm($form, $formStateMock);
  }

  /**
   * Test validateForm method with a deeply-nested violation path.
   */
  public function testValidateFormWithNestedViolationPath() {
    // Has violation with a nested path; should raise an error the same way
    // as if the path were not nested (on `test_field_1`).
    $violationList = new ConstraintViolationList([
      new ConstraintViolation('Invalid value 1', '', [], '', 'test_field_1.0.value', 'Invalid value'),
    ]);

    $this->setUpViolationTest($violationList);

    $form = [];

    $formStateMock = $this->createMock(FormStateInterface::class);
    $formStateMock->expects($this->exactly(1))
      ->method('setErrorByName')
      ->withConsecutive(
        ['test_field_1', 'Invalid value 1'],
      );

    $this->classInstance->validateForm($form, $formStateMock);
  }

}
