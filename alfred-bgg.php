<?php
  function callAPI( $url ) {
    // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, "https://www.boardgamegeek.com/xmlapi2/" . $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $result = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);

    return $result;
  }

  function searchURL( $query ){
    return "search?type=boardgame&query=" . $query;
  }

  function thingURL( $ids ){
    return "thing?stats=1&id=" . $ids;
  }

  function getIDs( $games ){
    $ids = array();

    foreach ($games as $game):
      $id = $game[0]['id'];
      array_push( $ids, $id );
    endforeach;

    return $ids;
  }

  function constructXMLResult( $games ){
    $items = new SimpleXMLElement("<items></items>"); 	// Create new XML element

    foreach ($games as $game):
      $title = htmlspecialchars( $game->name[0]['value'] );
      $subtitle = substr( htmlspecialchars( $game->description ), 0, 50 );
      $thumb = htmlspecialchars( $game->thumbnail );
      $c = $items->addChild( 'item' );
      $d = $c->addChild( 'title', $title);
      $e = $c->addChild( 'subtitle', $subtitle );
      $f = $c->addChild( 'icon', "http:" . $thumb );
      $f->addAttribute( 'type', 'fileicon' );
    endforeach;

    return $items;
  }

  // $query = $argv[1];
  $query = "suburbia";
  $query = urlencode( strtolower( trim( $query ) ) );

  $resultSearch = callAPI( searchURL( $query ) );

  $arrayIds = getIDs( simplexml_load_string( $resultSearch ) );
  $stringIds = implode( ",", $arrayIds );

  $resultThing = callAPI( thingURL( $stringIds ) );

  $items = constructXMLResult( simplexml_load_string( $resultThing ) );

  echo $items->asXML();
?>
