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

  function getNumberOfPlayers( $min, $max ){
    $stringNumberOfPlayers = "";

    if ( $min > $max) {
      $stringNumberOfPlayers = $min . " players";
    } elseif ( $min == $max ) {
      if( $min == 0 ) {
        $stringNumberOfPlayers = "Not Set";
      } else {
        $stringNumberOfPlayers = $max . "+" . $min . " players";
      }
    } else {
      $stringNumberOfPlayers = $min . "-" . $max . " players";
    }

    return $stringNumberOfPlayers;
  }

  function getPlayingTime( $playingtime ){
    $stringPlayingTime = "";
    if ( $playingtime == 0) {
      $stringPlayingTime = "Not Set";
    } else {
      $stringPlayingTime = $playingtime . " minutes";
    }

    return $stringPlayingTime;
  }

  function constructXMLResult( $games ){
    $items = new SimpleXMLElement("<items></items>"); 	// Create new XML element

    foreach ($games as $game):
      $title = htmlspecialchars( $game->name[0]['value'] );
      $description = htmlspecialchars( $game->description );
      $avg_rating = (float)htmlspecialchars( $game->statistics->ratings->average[0]['value'] );
      $geek_rating = (float)htmlspecialchars( $game->statistics->ratings->bayesaverage[0]['value'] );
      $year_published = htmlspecialchars( $game->yearpublished[0]['value'] );
      $min_players = (int)htmlspecialchars( $game->minplayers[0]['value'] );
      $max_players = (int)htmlspecialchars( $game->maxplayers[0]['value'] );
      $playing_time = (int)htmlspecialchars( $game->playingtime[0]['value'] );
      $rank = htmlspecialchars( $game->statistics->ratings->ranks->rank[0]['value'] );
      $weight = htmlspecialchars( $game->averageweight[0]['value'] );
      $thumb = htmlspecialchars( $game->thumbnail );

      $avg_rating = round( $avg_rating, 2 );
      $geek_rating = round( $geek_rating, 2 );
      $combinedTitle = $title;
      $combinedDescription =  $year_published . " | rating: " . $avg_rating . " | rank: " . $rank . " | " . getNumberOfPlayers( $min_players, $max_players ) . " | " . getPlayingTime( $playing_time );

      $c = $items->addChild( 'item' );
      $d = $c->addChild( 'title', $combinedTitle);
      $e = $c->addChild( 'subtitle', $combinedDescription );
    endforeach;

    return $items;
  }

  $query = $argv[1];
  $query = urlencode( strtolower( trim( $query ) ) );

  $resultSearch = callAPI( searchURL( $query ) );

  $arrayIds = getIDs( simplexml_load_string( $resultSearch ) );
  $stringIds = implode( ",", $arrayIds );

  $resultThing = callAPI( thingURL( $stringIds ) );

  $items = constructXMLResult( simplexml_load_string( $resultThing ) );

  echo $items->asXML();
?>
