<?php
  $query = $argv[1];
  $query = strtolower( trim( $query ) );

  $bgg_url = "https://www.boardgamegeek.com/xmlapi2/search?query=" . urlencode( $query );

  // create curl resource
  $ch = curl_init();

  // set url
  curl_setopt($ch, CURLOPT_URL, $bgg_url);

  //return the transfer as a string
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

  // $output contains the output string
  $result = curl_exec($ch);

  // close curl resource to free up system resources
  curl_close($ch);

  // $games = simplexml_load_file( "simple.xml" );
  $games = simplexml_load_string( $result );

  $items = new SimpleXMLElement("<items></items>"); 	// Create new XML element

  foreach ($games as $game):
    $title = htmlspecialchars( $game->name[0]['value'] );
    $c = $items->addChild( 'item' );
    $d = $c->addChild( 'title', $title );
    // $d = $c->addChild( 'title', urlencode( $query ) );
  endforeach;

  echo $items->asXML();
?>
