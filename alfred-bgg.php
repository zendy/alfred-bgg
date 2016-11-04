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
        $stringNumberOfPlayers = $max . " players";
      }
    } else {
      $stringNumberOfPlayers = $min . "-" . $max . " players";
    }

    return $stringNumberOfPlayers;
  }

  function getPlayingTime( $playing_time ){
    $stringPlayingTime = "";
    if ( $playing_time == 0) {
      $stringPlayingTime = "Not Set";
    } else {
      $stringPlayingTime = $playing_time . " minutes";
    }

    return $stringPlayingTime;
  }

  function getWeight( $avg_weight ){
    $stringAvgWeight = "";

    if( $avg_weight > 0 ) {
      $stringAvgWeight = roundFloat( $avg_weight );
    } else {
      $stringAvgWeight = "Not Set";
    }
    return $stringAvgWeight;
  }

  function roundFloat( $number ) {
    return round( $number, 2 );
  }

  function constructXMLResult( $games ){
    $items = new SimpleXMLElement("<items></items>"); 	// Create new XML element

    foreach ($games as $game):
      $id = $game[0]['id'];
      $title = htmlspecialchars( $game->name[0]['value'] );
      $description = htmlspecialchars( $game->description );
      $avg_rating = (float)htmlspecialchars( $game->statistics->ratings->average[0]['value'] );
      $geek_rating = (float)htmlspecialchars( $game->statistics->ratings->bayesaverage[0]['value'] );
      $avg_weight = (float)htmlspecialchars( $game->statistics->ratings->averageweight[0]['value'] );
      $year_published = htmlspecialchars( $game->yearpublished[0]['value'] );
      $min_players = (int)htmlspecialchars( $game->minplayers[0]['value'] );
      $max_players = (int)htmlspecialchars( $game->maxplayers[0]['value'] );
      $playing_time = (int)htmlspecialchars( $game->playingtime[0]['value'] );
      $rank = htmlspecialchars( $game->statistics->ratings->ranks->rank[0]['value'] );
      $weight = htmlspecialchars( $game->averageweight[0]['value'] );
      $thumb = htmlspecialchars( $game->thumbnail );

      $avg_rating = roundFloat( $avg_rating );
      $geek_rating = roundFloat( $geek_rating );
      $combinedTitle = $title;
      $combinedDescription =  $year_published . " | rating: " . $avg_rating . " | rank: " . $rank . " | weight: " . getWeight( $avg_weight ) . " | " . getNumberOfPlayers( $min_players, $max_players ) . " | " . getPlayingTime( $playing_time );

      $c = $items->addChild( 'item' );
      $c->addAttribute( 'uid', $id );
      $c->addAttribute( 'arg', $id );
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
