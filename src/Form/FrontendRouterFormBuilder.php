<?php

namespace Drupal\frontend_router\Form;

use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\frontend_router\EventSubscriber\FrontendRouterResponseSubscriber;

/**
 * Wraps the form building and processing service.
 */
class FrontendRouterFormBuilder implements FormBuilderInterface {

  /**
   * The form building and processing service.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * Constructs a \Drupal\frontend_router\Form\FrontendRouterFormBuilder object.
   *
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form building and processing service.
   */
  public function __construct(FormBuilderInterface $form_builder) {
    $this->formBuilder = $form_builder;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId($form_arg, FormStateInterface &$form_state) {
    return $this->formBuilder->getFormId($form_arg, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function getForm($form_arg) {
    return call_user_func_array(
      [$this->formBuilder, 'getForm'],
      func_get_args()
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm($form_id, FormStateInterface &$form_state) {
    return $this->formBuilder->buildForm($form_id, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function rebuildForm($form_id, FormStateInterface &$form_state, $old_form = NULL) {
    return $this->formBuilder->rebuildForm($form_id, $form_state, $old_form);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm($form_arg, FormStateInterface &$form_state) {
    return $this->formBuilder->submitForm($form_arg, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function retrieveForm($form_id, FormStateInterface &$form_state) {
    return $this->formBuilder->retrieveForm($form_id, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function processForm($form_id, &$form, FormStateInterface &$form_state) {
    return $this->formBuilder->processForm($form_id, $form_state);
  }

  /**
   * Renders a form action URL (#lazy_builder callback).
   *
   * @return array
   *   A renderable array representing the form action.
   */
  public function renderPlaceholderFormAction() {
    $build = $this->formBuilder->renderPlaceholderFormAction();

    if (FrontendRouterResponseSubscriber::isRouted()) {
      $build['#markup'] = frontend_router_filter_query($build['#markup']);
    }

    return $build;
  }

  /**
   * Renders form CSRF token (#lazy_builder callback).
   *
   * @param string $placeholder
   *   A string containing a placeholder, matching the value of the form's
   *   #token.
   *
   * @return array
   *   A renderable array containing the CSRF token.
   */
  public function renderFormTokenPlaceholder($placeholder) {
    return $this->formBuilder->renderFormTokenPlaceholder($placeholder);
  }

  /**
   * {@inheritdoc}
   */
  public function prepareForm($form_id, &$form, FormStateInterface &$form_state) {
    return $this->formBuilder->prepareForm($form_id, $form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function doBuildForm($form_id, &$element, FormStateInterface &$form_state) {
    return $this->formBuilder->doBuildForm($form_id, $element, $form_state);
  }
}
