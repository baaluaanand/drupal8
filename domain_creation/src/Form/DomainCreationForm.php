<?php

namespace Drupal\domain_creation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Language\LanguageManager;
use Symfony\Component\HttpFoundation\RedirectResponse;

class DomainCreationForm extends FormBase {
  
  public function getFormId() {
  	return 'domain_creation_form';  	
  }
  
  public function buildForm(array $form, FormStateInterface $form_state) {
    $con = Database::getConnection();
    $record = array();
    
    if (isset($_GET['id'])) {
      $query = $con->select('domain_creation', 'd')
        ->condition('id', $_GET['id'])
        ->fields('d');
      $row = $query->execute()->fetchAssoc();
    }
    
    /*
     * Creating a form with a textfield and language list
     */
    
    $form['domain_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Domain Name:'),
      '#required' => TRUE,
      '#maxlength' => 10,
      '#default_value' => (isset($row['name'])) ? $row['name'] : '',
      );
    
    $form['language'] = array(
      '#type' => 'select',
      '#title' => t('Language:'),
      '#options' => $this->getLanguageList(),
      '#default_value' => (isset($row['language'])) ? $row['language'] : '',
     );
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Add'),
    ];
    
    return $form;    
  }
  
  public function validateForm(array &$form, FormStateInterface $form_state) {  	
  	$values = $form_state->getValues();
  	
  	if (Unicode::strlen($values['domain_name']) > 10) {
  		$form_state->setErrorByName('domain_name', t('Domain name should not exist the limit'));  		
  	}  	
  }
  
  public function submitForm(array &$form, FormStateInterface $form_state) {
  	$field = $form_state->getValues();
  	$name = $field['domain_name'];
  	$language = $field['language'];
  	
  	$fields = array(
  	  'name' => $name,
  	  'language' => $language,
  	);
  	
  	$query = \Drupal::database();
  	
  	if (isset($_GET['id'])) {
  	  $query->update('domain_creation')
  		->fields($fields)
  		->condition('id', $_GET['id'])
  		->execute();
  	  drupal_set_message("succesfully updated"); 
  	  $form_state->setRedirect('domain.list');
  	}
  	else {
  	  $query ->insert('domain_creation')
  		->fields($fields)
  		->execute();
  	  drupal_set_message("succesfully saved");
  	}
  }
  
  
  public function getLanguageList() {
  	
  	$languages = LanguageManager::getStandardLanguageList();
  	foreach ($languages as $key=>$language) {
  	  $lang[$key] = $language[0];
  	}
  	
  	$con = Database::getConnection();
  	$id = isset( $_GET['id']) ?  $_GET['id'] : 0;
  	$query = $con->select('domain_creation', 'd')
  	->fields('d')
  	->condition('id', $id, '!=');	
  	$rows = $query->execute()->fetchAll();
  	foreach ($rows as $key=>$row) {
  	  unset($lang[$row->language]);
  	}
  	return $lang;
  }
}