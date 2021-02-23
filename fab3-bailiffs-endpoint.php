<?php

namespace Drupal\api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;

/**
 * Provides a Get All Resource
 *
 * @RestResource(
 *   id = "getall_resource",
 *   label = @Translation("Get All Resource"),
 *   uri_paths = {
 *     "canonical" = "/api/getall",
 *	   "https://www.drupal.org/link-relations/create" = "/api/search"
 *   }
 * )
 */

class GetAllResource extends ResourceBase {
 /**
   * Responds to entity GET requests.
   * @return \Drupal\rest\ResourceResponse
   */
  public function get() {
	
	$nids = \Drupal::entityQuery('node')
	->condition('status', 1)
	->condition('type', 'bailiff')
	->condition('nid','9','!=')
    ->execute();

	$nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
	
	$competentBodies=array();
	
	if($nodes) {
		foreach ($nodes as $node) {
			$details=array();
			array_push($details, array('name'=>$node->field_name_latin->value.' '.$node->field_surname_latin->value,'lang'=>'el', 'address'=>transliterator_transliterate('Any-Latin;Latin-ASCII;', $node->field_streetname->value.' '.$node->field_number->value), 'postalCode'=>$node->field_postalCode->value, 'municipality'=>transliterator_transliterate('Any-Latin;Latin-ASCII;', $node->field_city->value), 'tel'=>$node->field_telephone->value));
			array_push($competentBodies, array('id'=>$node->getOwnerId(),'country'=>'GR','details'=>$details));
    }
    
	$response = ['state' => 'answered', 'competentBodies'=>$competentBodies];	
	}
	else {
		$response = ['state' => 'answered'];
	}
	return new ResourceResponse($response);
  }
  
   /**
     * Responds to POST requests.
     * @return \Drupal\rest\ResourceResponse
     * Returns a list of bundles for specified entity.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     *   Throws exception expected.
     */
    public function post(array $data) {
		
        $nids = \Drupal::entityQuery('node')
		->condition('status', 1)
		->condition('type','bailiff')
		->condition('nid','9','!=')
		->condition('field_postalCode',$data['postalCode'])
		->execute();
				
		$nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
		
		$competentBodies=array();
	
		if($nodes) {
			foreach ($nodes as $node) {
				$details=array();
			array_push($details, array('name'=>$node->field_name_latin->value.' '.$node->field_surname_latin->value,'lang'=>'el', 'address'=>transliterator_transliterate('Any-Latin;Latin-ASCII;', $node->field_street ->value.' '.$node->field_number->value), 'postalCode'=>$node->field_postalCode->value, 'municipality'=>transliterator_transliterate('Any-Latin;Latin-ASCII;', $node->field_city->value), 'tel'=>$node->field_telephone->value));
			array_push($competentBodies, array('id'=>$node->getOwnerId(),'country'=>'GR','details'=>$details));
		}
		
		$response = ['state' => 'answered', 'competentBodies'=>$competentBodies];	
		}
		else {
			$response = ['state' => 'answered'];
		}
		return new ResourceResponse($response);

       /* $build = array(
            '#cache' => array(
                'max-age' => 0,
            ),
        );

        return (new ResourceResponse($response))->addCacheableDependency($build);*/
    }
}
?>