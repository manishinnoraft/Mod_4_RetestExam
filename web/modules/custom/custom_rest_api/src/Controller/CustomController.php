<?php

namespace Drupal\custom_rest_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CustomController extends ControllerBase {
  public function fetchData(Request $request) {
    $tag = $request->query->get('tag');
    // Check if a tag parameter is provided.
    if ($tag) {
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'news')
        ->condition('status', 1)
        ->accessCheck(FALSE)
        ->condition('field_tags_news.entity.name', $tag); 
    }
    else {
      //if noting passed, fetch all data
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'news')
        ->condition('status', 1)
        ->accessCheck(FALSE);
    }

    $nids = $query->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);

    // Check if node empty
    if (empty($nodes)) {
      $response = new JsonResponse(['message' => 'No news for the Tag was found.']);
    }
    else {
      $data = [];
      foreach ($nodes as $node) {
        $published_date = \Drupal::service('date.formatter')->format($node->getCreatedTime(), 'custom', 'd-m-Y');
        // Fetch all nodes
        $tags = [];
        foreach ($node->get('field_tags_news') as $term) {
          $tags[] = $term->entity->getName();
        }
        $data[] = [
          'title' => $node->getTitle(),
          'body' => $node->get('field_body')->value,
          'image' => $node->get('field_images')->entity->getFileUri(),
          'published_date' => $published_date,
          'tags' => $tags, 
          'featured' => $node->get('field_featured')->value,
        ];
      }
      $count = count($data); // Count data item
      $response = new JsonResponse(['count' => $count, 'data' => $data]);
    }

    return $response;
  }
}
