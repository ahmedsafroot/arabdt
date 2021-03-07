<?php

namespace Drupal\task\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

class ArabDTForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * The returned ID should be a unique string that can be a valid PHP function
   * name, since it's used in hook implementation names such as
   * hook_form_FORM_ID_alter().
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'task_arab_dt_form';
  }

    /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please enter the following data.'),
    ];

    $form['title'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#description' => $this->t('Enter the title  Note that the title must be more than 9 characters in length.'),
        '#required' => TRUE,
    ];

    $form['cv'] = [
        '#type' => 'managed_file',
        '#title' => $this->t('file'),
        '#upload_location' => 'public://files',
        '#description' => $this->t('upload file Not max sixe is 5MB.'),
        '#upload_validators' => [
        ],
        '#required' => TRUE,
      ];
    
    $form['date'] = [
            '#type' => 'date',
            '#title' => $this->t('Date'),
            '#description' => $this->t('Enter the Date Note the date must be greater than today'),
            '#required' => TRUE,
    ];

    $form['dropdown'] = [
        '#type' => 'select',
        '#title' => $this->t('Select stage'),
        '#description' => $this->t('select stage'),
        '#options' => [],
        '#empty_option'=>'Select Stage',
        '#empty_value'=>'',
        '#attributes' => array('class' => array('task-dropdown')),
        '#validated' => TRUE,

    ];
    

    $form['agree'] = [
        '#type' => 'checkbox',
        '#title' => $this->t('check this if you want'),
        '#description' => $this->t('if difference between selected date and today is 8 days Check box field must be required'),
        '#states' => [
            'required' => [
              ':input[name="date"]' => ['value' => date('Y-m-d', strtotime('+8 days'))],
            ],
          ],
    ];
  

    // Add a submit button that handles the submission of the form.
    $form['actions']['submit'] = [
    '#type' => 'submit',
    '#value' => $this->t('Submit'),
    ];

    return $form;

    }

      /**
   * Validate the title and the checkbox of the form
   * 
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * 
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $title = $form_state->getValue('title');
    $date = $form_state->getValue('date');
    $form_file = $form_state->getValue('cv');
    if (strlen($title) < 10) {
      $form_state->setErrorByName('title', $this->t('The title must be greater than 9 characters long.'));
    }

    if ($date<date('Y-m-d')){
      $form_state->setErrorByName('date', $this->t('date must be greater than today'));
    }
    $file = File::load($form_file[0]);
    $limit_size=5*1024*1024; // 5MB
    if (!empty($file) && $file->getSize()>$limit_size){
        $form_state->setErrorByName('cv', $this->t('Max Size of file is 5MB'));
    }
      

  }

    /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $messenger = \Drupal::messenger();
    $messenger->addMessage('your data is okay');

    // Redirect to home
    $form_state->setRedirect('<front>');

  } 

}