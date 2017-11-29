<?php
require('simple_html_dom.php');
// Create DOM from URL or file
$mainUrl = 'http://dl.tehmovies.org/94/series/';
$html = file_get_html($mainUrl);

$allSeriesLinks = array();
$allSeasonsLinks = array();
$allEpisodesLinks = array();
// Find all links 
foreach($html->find('a') as $element){
    $suffix = $element->href;
    if(strcasecmp($suffix,"../")!== 0){
       array_push($allSeriesLinks, $mainUrl.$suffix);
    }
}
// print_r($allSeriesLinks);
// $eachSeriesUrlArr = array();
// foreach($allSeriesLinks as $seriesLink){
//     $linkDom = file_get_html($seriesLink);
//     array_push($eachSeriesUrlArr, $linkDom);
// }

function sortEpisodesByQuality($EPISODES,$SEASON,$SERIES){
    
    $SERIES = str_ireplace("http://dl.tehmovies.org/94/series/","",$SERIES);
    $SERIES = str_ireplace(["/","."]," ",$SERIES);
    $SEASON = str_ireplace("http://dl.tehmovies.org/94/series/","",$SEASON);
    $SEASON = str_ireplace(["/","."]," ",$SEASON);
    $epQuality = "";
    foreach($EPISODES as $episode){
        $splitEpisode = str_ireplace('/',' ',$episode);
        if(stripos($splitEpisode,"480p")!== false){
            $epQuality = "480p";
        }elseif(stripos($splitEpisode,"720p")!== false){
            $epQuality = "720p";
        }elseif(stripos($splitEpisode,"1080p")!== false){
            $epQuality = "1080p";
        }else{
            $epQuality ="HD";
        }
        $seasonAndEpisode = getSeasonAndEpisode($splitEpisode);
        
        echo($SERIES. " - " . $seasonAndEpisode[0] . " - " . $seasonAndEpisode[1] . " - " . $epQuality . " - " . $episode ."<br>");
    }
}
    
function getSeasonAndEpisode($PATH){
    $episodeAndSeasonArr = array();
    $possibleSeasons = array("S01","S02","S03","S04","S05","S06","S07","S08","S09","S10","S11","S12","S13","S14","S15","S16","S17","S18","S19","S20");
    
    foreach($possibleSeasons as $season){
        if(stripos($PATH,$season)!== false){
                $actualSeason = "Season ". str_ireplace(["S0","S"],"",$season);
                array_push($episodeAndSeasonArr,$actualSeason);
        }                    
    }

    $possibleEpisodes = array("E00","E01","E02","E03","E04","E05","E06","E07","E08","E09","E10",
                            "E11","E12","E13","E14","E15","E16","E17","E18","E19","E20",
                            "E21","E22","E23","E24","E25","E26","E27","E28","E29","E30",
                            "E31","E32","E33","E34","E35","E36","E37","E38","E39","E40","Special");
    
    foreach($possibleEpisodes as $episode){
        if(stripos($PATH,$episode)!== false){
                $actualEpisode = "Episode ". str_ireplace(["E0","E"],"",$episode);
                array_push($episodeAndSeasonArr,$actualEpisode);
        }               
    }

    return $episodeAndSeasonArr;
}

foreach($allSeriesLinks as $seriesLink){

    $counter = 1;
    $particularSeriesLinkDOM = file_get_html($seriesLink);

    foreach($particularSeriesLinkDOM->find('a') as $element){
        $prefix = $seriesLink;
        $suffix = $element->href;
        if(strcasecmp($suffix,"../")!== 0){
        array_push($allSeasonsLinks, $prefix.$suffix);
        }
    }
    // print_r($allEpisodesLinks);
    for($i=0; $i < count($allSeasonsLinks); $i++){
        $epLinks = array();

        $particularSeasonLinkDOM = file_get_html($allSeasonsLinks[$i]);

        foreach($particularSeasonLinkDOM->find('a') as $element){
            $prefix = $allSeasonsLinks[$i];
            $suffix = $element->href;
            if(strcasecmp($suffix,"../")!== 0){
            array_push($epLinks, $prefix.$suffix);
            }
        }
        array_push($allEpisodesLinks, $epLinks);

        sortEpisodesByQuality($epLinks,$allSeasonsLinks[$i],$seriesLink);
        // print_r($epLinks);
        echo"</hr>";
    }
    $counter = $counter+1;
    if($counter == 5){
        exit;
    }

   
}




?>