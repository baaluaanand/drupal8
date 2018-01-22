<?php
namespace Drupal\domain_creation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfirmFormBase;
use Drupal\Core\Url;
use Drupal\Core\Render\Element;
/**
 * Class DeleteForm.
 *
 * @package Drupal\mydata\Form
 */
class DomainDeletionForm extends ConfirmFormBase {
	
	public function getFormId() {
		return 'delete_form';
	}
	public $id;
	public function getQuestion() {
		return t('Do you want to delete %id?', array('%id' => $this->id));
	}	
	public function getCancelUrl() {
		return new Url('domain.list');
	}
	public function getConfirmText() {
		return t('Delete');
	}
	
	public function getCancelText() {
		return t('Cancel');
	}
	
	public function buildForm(array $form, FormStateInterface $form_state, $id = NULL) {
		$this->id = $id;
		return parent::buildForm($form, $form_state);
	}
	
	public function submitForm(array &$form, FormStateInterface $form_state) {
		$query = \Drupal::database();
		$query->delete('domain_creation')
		->condition('id',$this->id)
		->execute();
		drupal_set_message("succesfully deleted");
		$form_state->setRedirect('domain.list');
	}
}