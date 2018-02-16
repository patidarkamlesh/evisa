<?php

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Component\Utility\Tags;
use Drupal\Component\Utility\Unicode;

class HelloController extends ControllerBase {

  public function sayhello() {
    return array(
      '#markup' => hello_hello_world(),
    );
  }

  public function welcome() {
      return array(
        '#markup' => hello_welcome(),
     );
  }
  
  public function handleAutocomplete(Request $request, $field_name, $count) {
    $results = [];

    // Get the typed string from the URL, if it exists.
    if ($input = $request->query->get('q')) {
      $typed_string = Tags::explode($input);
      $typed_string = Unicode::strtolower(array_pop($typed_string));
      $result = db_select('country', 'e')
                ->fields('e', array('cid','country_name', 'created'))
                ->condition('e.uid', 1)
                ->orderBy('e.created', 'DESC')
                ->range(0, 10)
                ->execute();
      foreach($result as $row) {
          $results[] = [
          'value' => $row->country_name . '(' . $row->cid . ')',
          'label' => $row->country_name,
        ];
      }
      
     /* for ($i = 0; $i < $count; $i++) {
        $results[] = [
          'value' => $field_name . '_' . $i . '(' . $i . ')',
          'label' => $field_name . ' ' . $i,
        ];
      }*/
    }

    return new JsonResponse($results);      
  } 
  
  
}