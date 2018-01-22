<?php
namespace Drupal\domain_creation\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;


class PageredirectSubscriber implements EventSubscriberInterface {
	
	private $redirectCode = 301;
	
	public function customRedirection(GetResponseEvent $event) {
		
	  $user = \Drupal::currentUser()->getRoles();
	  $request = \Drupal::request();
	  
	  $hostName = $request->getHost();
	  $requestUrl = $request->server->get('REQUEST_URI', null);
	  
	  /*
	   * To check the domain and redirecting to the particular language 
	   * home page
	   */
	  
	  if (!in_array("administrator", $user) && $requestUrl == '/') {	    
	  	$con = Database::getConnection();
	  	$query = $con->select('domain_creation', 'd')
			->condition('name', $hostName)
			->fields('d');
	  	$row = $query->execute()->fetchAssoc();
	  	$response = new RedirectResponse('/' . $row['language'], $this->redirectCode);
	  	$response->send();
	  	exit(0);
	  }
	}
	
	public static function getSubscribedEvents() {
		$events[KernelEvents::REQUEST][] = array('customRedirection');
		return $events;
	}
}