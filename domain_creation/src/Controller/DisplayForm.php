<?php
namespace Drupal\domain_creation\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;

class DisplayForm extends ControllerBase {
  
  public function displayForm() {
  	$form = \Drupal::formBuilder()->getForm('Drupal\domain_creation\Form\DomainCreationForm');
  	return $form;
  }
  
  
  /*
   * Displaying the added domain list with option to
   * edit or delete the values.
   */  
  public function displayList() {
    $headers = array(
  	  'id'=>    t('SrNo'),
  	  'name' => t('Domain Name'),
  	  'language' => t('Language'),
      'opt' => t('Edit'),
      'opt1' => t('Delete'),
  	);
  		
  	$query = \Drupal::database()->select('domain_creation', 'd');
  	$query->fields('d');
  	$results = $query->execute()->fetchAll();
  	$rows = array();
  	foreach ($results as $data) {
  	  $delete = Url::fromUserInput('/domain/delete/'.$data->id);
  	  $edit   = Url::fromUserInput('/domain?id='.$data->id);
  	  $rows[] = array(
  		'id' =>$data->id,
  		'name' => $data->name,
  		'language' => $data->language,
  	  	\Drupal::l('Delete', $delete),
  	  	\Drupal::l('Edit', $edit),
  	  );
  	}
  	$form['table'] = [
  	  '#type' => 'table',
  	  '#header' => $headers,
  	  '#rows' => $rows,
  	];
  	return $form;
  }  
  
}