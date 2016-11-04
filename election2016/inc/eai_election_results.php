<?php
/* election results short codes */
add_shortcode('electionresultstown', 'electionResults_Town');
add_shortcode('electionresultsrace', 'electionResults_Race');
//add_shortcode('electionresultsracesum', 'electionResults_RaceSummary');
add_shortcode('electionresultsimple', 'electionResults_RaceSimple');
/********** RESULTS BY TOWN ************************/
function electionResults_Town ($atts) {
/* shortcode to return all the results for a particular town */
    global $wpdb;
    $table = "votes2016";
    $eai_elections_enable_results = true;
    $votes_preview = false;
	  $a = shortcode_atts( array(
        'town' => 'something',
        'primary' => false,
    ), $atts );
    $town = $a['town'];
    $primary = $a['primary'];

    if ($eai_elections_enable_results) {
        /* initializations */
        $htmlreturn = '<div class="eai-results-wrapper"><div class="eai-town"><h4>Elections results for '.$town.".</h4>";
        $found_votes = false;
        // GET THE RACES for the given town
        $racesquery = 'SELECT  distinct `race`, r_d, r_g, r_r, r_u  FROM `'.$table.'` WHERE town="'. $town.'" ORDER BY raceorder';
        //*echo '<p>RacesQuery: '.$racesquery.'</p>'; // testing
        $racesresults = $wpdb->get_results($racesquery);
        //echo "<pre>";     var_dump($racesresults);      echo "</pre>"; // testing
        if ($racesresults) {
            foreach ($racesresults as $race) {
                if ($primary) {

                    switch ($race->party) {
                        case "R":
                             $total_voters = $race->r_r;
                            break;
                        case "D":
                            $total_voters = $race->r_d;
                            break;

                        case "G":
                            $total_voters = $race->r_g;
                            break;
                        case "U":
                            $total_voters = $race->r_u;
                            break;
                        default:
                             $total_voters = 1; // prevent divid by zero
                    }
                    //echo "<p>Primary for _".$indrace->party."_</p>";
                } else {
                     $total_voters = $race->r_d + $race->r_g + $race->r_r + $race->r_u;
                    //echo "<p>Election</p>";
                }

                //echo '<p> Race:'.$race->race.' Reg. voters:'.$total_voters.'</p>'; // testing
                $indracequery = 'SELECT DISTINCT candidate, party, votes, town, reported FROM '.$table.' WHERE town = "'.$town.'" AND race="'.$race->race.'"';
                //echo '<p>'.$indracequery.'</p>'; // testing
                $indraceresults = $wpdb->get_results($indracequery);

                if ($indraceresults) {
                    //$htmlreturn .= '<h4>'.$race->race.'</h4>';
                    $htmlreturn .= '<table class="eai-results eai-results-town"><tr class="eai-results-headerrow"><th class="eai-results-header">'.$race->race.'</th><th class="eai-result-votes">Votes</th></tr>';
                    $count_voted = 0;
                    $num_candidates = 0;
                    foreach ($indraceresults as $indrace) {
                        if ($indrace->reported) {
                            $found_votes = true;
                            $count_voted += $indrace->votes;
                        }
                        $num_candidates++;
                        // count number of candidates;
                    }
                    foreach ($indraceresults as $indrace) {
                        if ($indrace->party) {
                            $party_string = ' (<span class="party-'.$indrace->party.'">'.$indrace->party.'</span>) ';
                        } else {
                            $party_string = '';
                        }
                        if ($indrace->reported) {
                            //$found_votes = true;
                            if (($count_voted > 0) && ($num_candidates > 1)) {
                                $htmlreturn .= '<tr><td>'.$indrace->candidate.$party_string.'</td><td class="eai-result-votes">'.number_format_i18n($indrace->votes).' ( '.round(($indrace->votes/$count_voted)*100).'% )</td></tr>';
                            } else {
                                $htmlreturn .= '<tr><td>'.$indrace->candidate.$party_string.'</td><td class="eai-result-votes">'.number_format_i18n($indrace->votes).'</td></tr>';
                            }

                            //$count_voted += $indrace->votes;
                        } else {
                            //$htmlreturn .= "{reported = ".$indrace->reported."}";
                            $htmlreturn .= '<tr><td>'.$indrace->candidate.$party_string.'</td><td>not yet available</td></tr>';
                        }
                    }
                    $htmlreturn .= '</table>';
                    //$htmlreturn .= "totalvoters:  $total_voters";
                    if ( ($count_voted > 0) && ($total_voters>0) ) {

                        $htmlreturn .= '<p> Voter participation: '.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters);
                        $voter_participation = round(($count_voted/$total_voters)*100);
                        if ($voter_participation > 100) {
                          $voter_participation = 100;
                        }
                        if ($voter_participation > 0 ) {
                          $htmlreturn .= ' : '.$voter_participation.'%';
                        }
                        $htmlreturn .= '</p>';
                        $htmlreturn .= '<h6 class="eai-results-unofficial">All results are unofficial.</h6>';
                    }

                }
            }
        } else {
            $htmlreturn .="<p>No results</p>";
        }
        $htmlreturn .="</div></div>"; // end of shortcode
        if (!$found_votes) {
            $htmlreturn = '<img src="http://www.reachdowneast.com/elections2016/wp-content/themes/election2016/images/election_announcement_bar_tuesday.png">';
        } else {
           // $htmlreturn .= "<p>found votes..</p>";
        }
  } // not enabled.
    return $htmlreturn;
} /* end of electionresultstown */
/********** END OF RESULTS BY TOWN *****************/

/********** RESULTS BY RACE ************************/
function electionResults_Race ($atts) {
    /* short code function to display election results by Race.  Ex:  Governor's race */
  global $wpdb;
  $table = "votes2016";
  $eai_elections_enable_results = true; // turn on/off everything
   $a = shortcode_atts( array(
      'race' => '',
      'unvoted' => false,  // by default, dont show the unvoted
      'primary' => false,
      'title' => "yes",
      'charttype' => "pie",
      'partial' => "no",
  ), $atts );

  $votes_preview = false;
  $htmlreturn = "";
  $jsreturn = "";
  if ($eai_elections_enable_results) {
    // initializations
    $primary = $a['primary'];
    $race = $a['race'];
    if ($a['title'] == "yes") {
       $show_title = true;
    } else {
        $show_title = false;
    }
    $charttype = $a["charttype"];

    if ($a['unvoted'] ) {
        $show_unvoted = true;
    } else {
        $show_unvoted = false;
    }
    if ($a['partial'] != "no") {
      $show_partial_text = true;
    } else {
      $show_partial_text = false;
    }
    $unofficial_text = '<h6 class="eai-results-unofficial">All results are unofficial';
    if ($show_partial_text) {
        $unofficial_text .= " and show votes cast in Hancock County ONLY";
    }
    $unofficial_text .= '.</h6>';
    $count_precinct_reporting = 0;
    $count_precincts = 0;
    $count_voted = 0;
    $total_voters = 0;
    $found_votes = false;
    $all_reported = false;
    $all_towns_reported = true;
    $jsreturn = "";
    $count_unreported = 0;
    $count_unreported_d = 0;
    $count_unreported_r = 0;
    $count_unreported_g = 0;
    $count_unreported_u = 0;
    $arry_names = array();
    $arry_votes = array();
    $arry_gdata = array();
    /* get the candidates in the race */
    $candquery = 'SELECT  distinct `candidate`,party, raceorder FROM `'. $table.'` WHERE race="'. $race.'"';
    $candresult = $wpdb->get_results($candquery);
    //echo "<pre>"; var_dump($candresult);echo "</pre>";

    if ($candresult) {

        $racequery = 'SELECT distinct base.precinct, reported, r_d, r_g, r_r, r_u '; // cant have party in here (unless primary)
        $c=0;
        $sums = array();
        foreach ($candresult as $cand) {
            $raceorder = $cand->raceorder;
            $c++;
            $ctabname = (string)$c;
            $ctabname = 'c'.$ctabname;
            $candidate_name = $cand->candidate;
            $sums[$candidate_name] = 0;
            //echo "ctabname = ". $ctabname;
            //echo "<p>".$candidate_name."</p>";
            $racequery .= ', (select votes FROM `'.$table.'` '. $ctabname.' WHERE '.$ctabname.'.race="'.$race.'" AND '.$ctabname.'.candidate = "'.$candidate_name .'" and '.$ctabname.'.precinct = base.precinct) `'.$candidate_name.'` ';
        }
        $num_candidates = $c;
        $racequery .= ' FROM `'.$table.'` base ';
        $racequery .= ' WHERE base.race="'.$race.'"';
        $racequery .= ' ORDER BY reported DESC, base.precinct';

        //echo "-- Race Query -- <br>"; echo $racequery;  echo "</br></hr>";;// for testing

        $raceresults = $wpdb->get_results($racequery);
        //echo "<pre>"; var_dump($raceresults); echo "</pre>";  // for testing

        /* loop thought calc the sums & totals */
        foreach ($raceresults as $raceresult) {
            //$htmlreturn .= "<tr>";
            //$htmlreturn .= "<td>".$raceresult->precinct."</td>";
            if ($raceresult->reported == 1) {
                $found_votes = true;
                $count_precinct_reporting++;
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $race_amount = $raceresult->$candidate_name;
                    $sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                    $count_voted += $race_amount;
                }
            } else {
                $all_towns_reported = false;
                //$count_unreported += $raceresult->registeredvoters;
                $count_unreported_d += $raceresult->r_d;
                $count_unreported_r += $raceresult->r_r;
                $count_unreported_g += $raceresult->r_g;
                $count_unreported_u += $raceresult->r_u;
            }

            if ($primary) { // only count in party for primary
                 switch ($raceresult->party) {
                    case "R":
                         $registeredvoters = $raceresult->r_r;
                        break;
                    case "D":
                        $registeredvoters = $raceresult->r_d;
                        break;
                    case "G":
                        $registeredvoters = $raceresult->r_g;
                        break;
                    case "U":
                        $registeredvoters = $raceresult->r_u;
                        break;
                    default:
                        $registeredvoters = 0;
                }

            } else {
                 $registeredvoters = $raceresult->r_d + $raceresult->r_r + $raceresult->r_g + $raceresult->r_u;
            }
            $total_voters += $registeredvoters;
            $count_precincts++;
        }
        // more calcs once all counted.
    //$found_votes = true;
        if ($found_votes) {
            if ($count_unreported > 0) {
                $pct_unreported_r = round(($count_unreported_r / $count_unreported)*100,1);
                $pct_unreported_d = round(($count_unreported_d / $count_unreported)*100, 1);
                $pct_unreported_g = round(($count_unreported_g / $count_unreported)*100, 1);
                $pct_unreported_u = round(($count_unreported_u / $count_unreported)*100, 1);
            } else {
                $all_reported = true;
            }

            // build the data for the chart(s)
            $str_voterdata = "[['Unreported', 'Voters']";
            $str_voterdata .= ",['Republican',".$count_unreported_r."]";
            $str_voterdata .= ",['Democrat',".$count_unreported_d."]";
            $str_voterdata .= ",['Green',".$count_unreported_g."]";
            $str_voterdata .= ",['Independent',".$count_unreported_u."]";
            $str_voterdata .= "]";
            $str_votercolors = "colors:['#D33', '#1E73BE', '#4B874F', 'grey']";
            $str_piedata = "[['Candidate', 'Votes'";
            switch($charttype) {
              case "bar":
                $str_piedata .= ",{role: 'style'}";
              break;
            }
            $str_piedata .= "]"; // end of column headings.
            $str_colors = "";
            for ($i=0; $i< $num_candidates; $i++) {
               // if ($i > 0 ) { $str_piedata .= ","; }
                $candidate_name = $candresult[$i]->candidate;
                $candidtate_name_title = $candresult[$i]->candidate;
                if ($candresult[$i]->party) {
                  $candidtate_name_title .= "(".$candresult[$i]->party.")";
                }
                $str_piedata .= ",['".$candidtate_name_title."', ".$sums[$candidate_name];

                switch ($candidate_name) {
                 case 'Yes':
                        $str_colors .= ",'#4B874F'"; // a nice green
                        $cand_color = '#4B874F';
                        break;
                    case 'No':
                        $str_colors .= ",'grey'";
                        $cand_color = 'grey';
                        break;
                }
                switch ($candresult[$i]->party) {
                    case 'R':
                        $str_colors .= ",'#D33'";
                        $cand_color = "#D33";
                        break;
                    case 'D':
                        $str_colors .= ",'#1E73BE'"; // a nice blue
                        $cand_color = '#1E73BE';
                        break;
                    case 'G':
                        $str_colors .= ",'green'";
                        $cand_color = 'green';
                        break;
                    case 'U':
                        $str_colors .= ",'purple'";
                        $cand_color = 'purple';
                        break;
                    case 'Y':
                         $str_colors .= ",'#4B874F'"; // a nice green
                         $cand_color .= "#4B874F";
                         break;
                    case 'N':
                         $str_colors .= ",'grey'";
                         $cand_color = 'grey';
                         break;
                    case 'I':
                    case 'L':
                        $str_colors .= ",'grey'";
                        $cand_color = 'grey';
                        break;
                }
                switch ($charttype) {
                  case "bar":
                    $str_piedata .= ",'color: ".$cand_color.";'";
                }
                $str_piedata .= "]"; // end of candidate row
            } // end for
            if ($str_colors <> "") {
                $str_colors = ',colors :['.substr($str_colors,1).']';
            }
            $str_piedata .= "]"; // end of piedata
        } // found votes
//$htmlreturn .= "IN SHORTCODE with type: ".$charttype;

        /* ********** build the display **************/

        $htmlreturn .= '<div class="eai-resultsrace-wrapper">';

        //$htmlreturn .= '<h4 class="eia-race-title" >'.$race.'</h4>';
        $htmlreturn .= "<!--open wrapper-->";
//$found_votes = false;
        if ($raceresults) {
            $htmlreturn .= '<div class="eai-racesum">';
            if ($show_title) {
                $htmlreturn .= '<h2 style="text-align: center;">'.$race."</h2>";
            }
            // in racesum: 1st the piechart
            if ($found_votes) {

                $htmlreturn .= '<div class="eai-race-vote-pie">';
                if ($num_candidates > 1) {
                  $htmlreturn.= '<h5>Votes</h5>';
                  $htmlreturn .= '<div id="racedisplay'.$raceorder.'" class ="eai-race-grx"></div>';
                }
                $htmlreturn .= '</div>';
            }
            // in racesum: was 2nd- display some of the totals & counts
           /*  $htmlreturn .= '<div class="vote-count">';
            $htmlreturn .= "<h3>Vote Count</h3>";
            $htmlreturn .= '<ul class="eai-results-sum">';
            for ($i=0; $i< $num_candidates; $i++) {
                $candidate_name = $candresult[$i]->candidate;
                $arry_names[] = $candidate_name;

                if ($candresult[$i]->party) {
                        //$party_string = ' ('.$candresult[$i]->party.') ';
                        $party_string = ' (<span class="party-'.$candresult[$i]->party.'">'.$candresult[$i]->party.'</span>) ';
                    } else {
                        $party_string = '';
                }
                $htmlreturn .= "<li>". $candidate_name.$party_string;
                if ($found_votes) {
                    $htmlreturn .= ' : '.number_format_i18n($sums[$candidate_name]).' - '.round(($sums[$candidate_name]/$count_voted)*100) .'%';
                    $arry_votes[] = $sums[$candidate_name];
                }
                $htmlreturn .= "</li>";
            }
            $htmlreturn .= '</ul>';
            $htmlreturn .= '</div>';
            */
            // in racesum: 2nd  - display precincts reporting
            if ($found_votes ) {

                $htmlreturn .= '<div class="eai-precincts-reporting" >';
                if ($count_precinct_reporting < $count_precincts ) {
                  $htmlreturn .= '<h3 class="eai-precincts-precent">'.round(($count_precinct_reporting/$count_precincts)*100).'%</h3><h3 class="eai-precincts-title">Towns</br>reporting</h3>';
                  $htmlreturn .= '<p class="eai-precincts-subtitle">'.$count_precinct_reporting.' of '.$count_precincts.'</p>';
                } else {
                  // all precints have reported.
                  //$htmlreturn .= '<p class="eai-precincts-subtitle">All towns reported</p>';
                }

                //$htmlreturn .= '<p>'.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' voters. Participation: '.round(($count_voted/$total_voters)*100).'%</p>';
                $htmlreturn .="</div>";
            }
             // in racesum: 3nd  - display voter participation
            if ($found_votes & $all_towns_reported ) {
                $voter_participation = round(($count_voted/$total_voters)*100);
                if ($voter_participation = 0) {
                  $htmlreturn .= '<div class="eai-voter-partcip" >';
                  $htmlreturn .= '<h3 class="eai-voter-precent">'.$htmlreturn.'%</h3><h3 class="eai-voter-title">Voter</br>Participation</h3>';
                  //$htmlreturn .= "<p>".$count_precinct_reporting.' of '.$count_precincts.' Precincts reporting:</p>';
                  //$htmlreturn .= '<p>'.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' voters. Participation: '.round(($count_voted/$total_voters)*100).'%</p>';
                  /* $count_voted = 0;
                  $total_voters = 0; */
                  //$htmlreturn .= "(counted = ".$count_voted." / total_voters = ".$total_voters.")";
                  $htmlreturn .="</div>";
                }

            }


            // in racesum: 4th - piechart of remaining voters affiliates
            if ($found_votes && $show_unvoted) {
                $htmlreturn .= '<div class="eai-unvoted"><h5>Profile of unreported precincts</h3>';
                $htmlreturn .= '<div id="eai-unvoted-affl" class="eai-voter-grx"></div>';
                $htmlreturn .= '</div>';
            }
            $htmlreturn .= '</div>'; // end of race-ssum
            $htmlreturn .=" <!-- end  of race sum -->";
            if ($found_votes)    {
                $htmlreturn .= $unofficial_text;
                // now the table of all the results
                $htmlreturn .= '<table class="eai-results-race-details">';
                // put totals at top of table as well as bottom
                $htmlreturn .= '<tr class="eai-results-totalrow"><td>Totals</td>';
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $htmlreturn .= '<td class="eia-result-totals">'.number_format_i18n($sums[$candidate_name])."</td>"; // $sumresult->
                }
                $htmlreturn .= "</tr>";

                $htmlreturn .= '<tr class="eai-results-headerrow"><th>Town</th>';
                foreach ($candresult as $cand) {
                   if ($cand->party) {
                            //$party_string = ' ('.$cand->party.') ';
                            $party_string = ' (<span class="party-'.$cand->party.'">'.$cand->party.'</span>) ';
                        } else {
                            $party_string = '';
                    }
                   $htmlreturn .= '<th class="eai-result-votes">'.$cand->candidate.$party_string.'</th>';
                }
                $htmlreturn .= "</tr>";

                //
                foreach ($raceresults as $raceresult) {
                    $htmlreturn .= "<tr>";
                    $htmlreturn .= "<td>".$raceresult->precinct.'</td>';

                    for ($i=0; $i< $num_candidates; $i++) {
                        $candidate_name = $candresult[$i]->candidate;
                        if ($raceresult->reported) {
                            $race_amount = $raceresult->$candidate_name; // name of column is candidates name.
                            $race_amount_str = number_format_i18n($race_amount);
                        } else {
                            $race_amount_str = 'Not yet reported.';
                        }
                        //$sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                        $htmlreturn .= '<td class="eai-result-votes">'.$race_amount_str."</td>";
                    }
                    $htmlreturn .= "</tr>";
                }

                // put the sums at the bottom of the table

                $htmlreturn .= '<tr class="eai-results-totalrow"><td>Totals</td>';
                for ($i=0; $i< $num_candidates; $i++) {
                    $candidate_name = $candresult[$i]->candidate;
                    $htmlreturn .= '<td class="eia-result-totals">'.number_format_i18n($sums[$candidate_name])."</td>"; // $sumresult->
                }
                $htmlreturn .= "</tr>";
                $htmlreturn .= "<tr><th>Town</th>";
                foreach ($candresult as $cand) {
                    if ($cand->party) {
                            $party_string = ' (<span class="party-'.$cand->party.'">'.$cand->party.'</span>) ';
                        } else {
                            $party_string = '';
                    }
                   $htmlreturn .= '<th class="eai-result-votes">'.htmlspecialchars_decode($cand->candidate).$party_string.'</th>';
                }
                $htmlreturn .= "</tr>";
                $htmlreturn .="</table>";
                /* $htmlreturn .= '<p>Total unreported:'.number_format_i18n($count_unreported);
                $htmlreturn .= ' d:'.number_format_i18n($count_unreported_d).', ';
                $htmlreturn .= ' r:'.number_format_i18n($count_unreported_r).', ';
                $htmlreturn .= ' u:'.number_format_i18n($count_unreported_u).', ';
                $htmlreturn .= ' g:'.number_format_i18n($count_unreported_g);
                $htmlreturn .= '</p>'; */
                $htmlreturn .= $unofficial_text;

                /* now for the javascript to build the graphics */
                //$raceorder = "";

                if ($charttype && ($charttype != "none"))  {
                  if (  $num_candidates > 1) {
                    switch ($charttype) {
                      case 'bar':
                          $chart_areaoption =  ",chartArea:{'width': '50%','height': '90%'}";
                          break;
                      case 'pie':
                          $chart_areaoption =  ",chartArea:{'width': '90%','height': '90%'}";
                          break;
                    }

                    //$chart_options = "{title:'".$race."'".$str_colors.$chart_areaoption."}"; // ,chartArea:{'width':'50%', height:'50%'}
                    $voter_options = "{title:'Profile of unreturned precincts',".$str_votercolors.$chart_areaoption."}";
                    $jsreturn = "<script>";
                    //$jsreturn = "google.charts.load('current', {'packages':['corechart','bar']});";
                    $jsreturn .= "google.setOnLoadCallback(drawChart);";
                    $jsreturn .= "function drawChart(){";
                    $jsreturn .= 'var data = google.visualization.arrayToDataTable('.$str_piedata.');';
                    switch ($charttype) {
                        case "pie" :
                          $chart_options = "{title:'".$race."'".$str_colors.$chart_areaoption."}"; // ,chartArea:{'width':'50%', height:'50%'}
                          $jsreturn .= "var chart = new google.visualization.PieChart(document.getElementById('racedisplay".$raceorder."'));";
                          break;
                        case "bar":
                          $jsreturn .= "data.sort([{column: 1, desc: true  }]);";
                          $chart_options = "{title:'".$race."'";
                        //  $chart_options .= $str_colors.$chart_areaoption;
                          $chart_options .= $chart_areaoption;
                          //$chart_options .= ", vAxis:{ title:'Candidate' }";
                          $chart_options .= ", legend: 'none'";
                          $chart_options .= "}";

                          $jsreturn .= "var chart = new google.visualization.BarChart(document.getElementById('racedisplay".$raceorder."'));";
                          break;
                    }
    $htmlreturn .= "<p>PieData</p><pre>".$str_piedata."</pre>";
    $htmlreturn .= "<pre>Chart options:".$chart_options."</pre>";
                    $jsreturn .= "var options = ".$chart_options.";";
                    $jsreturn .= "chart.draw(data,options);";
                    $jsreturn .="} </script>";
                  } // more than one Candidate
                } // end chartype
            } else {
                // no votes yet.
                // $htmlreturn .= '<p class="eai-checkback">Polls close at 8 p.m. Check back then for results as they come in.</p>';
                $htmlreturn .= '<img src="http://www.reachdowneast.com/elections2016/wp-content/themes/election2016/images/election_announcement_bar_tuesday.png">';
            }
        } else {
            $htmlreturn .= "<p>No results.</p>";
            //var_dump($raceresults);
            //echo $racequery;
        }

    } else {
        $htmlreturn .= "<p>No Candidates for ".$race."</p>";
    }
    $htmlreturn .="</div>"; // end of wrapper & ident div
    $htmlreturn .= "<!-- end of wrapper race -->";
  } // end enabled.
  else {$htmlreturn = "<!-- nada -->";$jsreturn = ""; }
    return $htmlreturn.$jsreturn;
}
/********** END OF RESULTS BY RACE *****************/

function electionResults_RaceSimple ($atts) {
    /* short code function to display Summary of election results by Race.  Ex:  Governor's race */
    global $wpdb;
    $table = "votes2016";
    $eai_elections_enable_results = true;
    $a = shortcode_atts( array(
        'race' => '',
        'link' => '',
        'unvoted' => false,  // by default, dont show the unvoted
        'primary' => false,
        'title' => "yes",
        'partial' => "no", // partial result of race (i.e. state races, dont have ALL results)
    ), $atts );

    $primary = $a['primary'];
    if ($a['title'] == "yes") {
       $show_title = true;
    } else {
        $show_title = false;
    }
    if ($a['unvoted'] ) {
        $show_unvoted = true;
    } else {
        $show_unvoted = false;
    }
    if ($a['partial'] != "no") {
      $show_partial_text = true;
    } else {
      $show_partial_text = false;
    }
    // initializations
    $unofficial_text = '<h6 class="eai-results-unofficial">All results are unofficial';
    if ($show_partial_text) {
        $unofficial_text .= " and show votes cast in Hancock County ONLY";
    }
    $unofficial_text .= '.</h6>';
    $race = $a['race'];
    $link = $a['link'];
    $count_precinct_reporting = 0;
    $count_precincts = 0;
    $count_voted = 0;
    $total_voters = 0;
    $found_votes = false;
    $count_unreported = 0;
    $count_unreported_d = 0;
    $count_unreported_r = 0;
    $count_unreported_g = 0;
    $count_unreported_u = 0;
    $pct_unreported_d = 0;
    $pct_unreported_r = 0;
    $pct_unreported_g = 0;
    $pct_unreported_u = 0;
    $jsreturn = "";
    if ($eai_elections_enable_results) {
     /* get the candidates in the race */
      $candquery = 'SELECT  distinct `candidate`, party, raceorder FROM `'. $table.'` WHERE race="'. $race.'"';
      //echo "<p>Canidate Query: ".$candquery."</p>";
      $candresult = $wpdb->get_results($candquery);
      //echo "<pre>"; var_dump($candresult);echo "</pre>";

      if ($candresult) {

          $racequery = 'SELECT distinct base.precinct, reported, party, r_d, r_g, r_r, r_u ';
          $c=0;
          $sums = array();
          foreach ($candresult as $cand) {
              $raceorder = $cand->raceorder;
              $c++;
              $ctabname = (string)$c;
              $ctabname = 'c'.$ctabname;
              $candidate_name = $cand->candidate;
              $sums[$candidate_name] = 0;
              //echo "ctabname = ". $ctabname;
              //echo "<p>".$candidate_name."</p>";
              $racequery .= ', (select votes FROM `'.$table.'` '. $ctabname.' WHERE '.$ctabname.'.race="'.$race.'" AND '.$ctabname.'.candidate = "'.$candidate_name .'" and '.$ctabname.'.precinct = base.precinct) `'.$candidate_name.'` ';
          }
          $num_candidates = $c;
          $racequery .= ' FROM `'.$table.'` base ';
          $racequery .= ' WHERE base.race="'.$race.'"';
          $racequery .= ' ORDER BY reported DESC, base.precinct';

          //echo "<p>-- Race Query -- <br>"; echo $racequery;  echo "</br></hr></p>";;// for testing

          $raceresults = $wpdb->get_results($racequery);
          //echo "<pre>"; var_dump($raceresults); echo "</pre>";  // for testing

              /* loop thought calc the sums & totals */
          foreach ($raceresults as $raceresult) {
              //$htmlreturn .= "<tr>";
              //$htmlreturn .= "<td>".$raceresult->precinct."</td>";
              if ($raceresult->reported == 1) {
                  $found_votes = true;
                  $count_precinct_reporting++;
                  for ($i=0; $i< $num_candidates; $i++) {
                      $candidate_name = $candresult[$i]->candidate;
                      $race_amount = $raceresult->$candidate_name;
                      $sums[$candidate_name ] = $sums[$candidate_name] + $race_amount;
                      $count_voted += $race_amount;
                  }
              } else {
                  $all_towns_reported = false;
                  //$count_unreported += $raceresult->registeredvoters;
                  $count_unreported_d += $raceresult->r_d;
                  $count_unreported_r += $raceresult->r_r;
                  $count_unreported_g += $raceresult->r_g;
                  $count_unreported_u += $raceresult->r_u;
              }

              if ($primary) { // only count in party for primary
                   switch ($raceresult->party) {
                      case "R":
                           $registeredvoters = $raceresult->r_r;
                          break;
                      case "D":
                          $registeredvoters = $raceresult->r_d;
                          break;

                      case "G":
                          $registeredvoters = $raceresult->r_g;
                          break;
                      case "U":
                          $registeredvoters = $raceresult->r_u;
                          break;
                      default:
                          $registeredvoters = 0;
                  }

              } else {
                   $registeredvoters = $raceresult->r_d + $raceresult->r_r + $raceresult->r_g + $raceresult->r_u;
              }
              $total_voters += $registeredvoters;
              $count_precincts++;
          }
          // more calcs once all counted.
          if ($found_votes) {
              if ($count_unreported > 0) {
                  $pct_unreported_r = round(($count_unreported_r / $count_unreported)*100,1);
                  $pct_unreported_d = round(($count_unreported_d / $count_unreported)*100, 1);
                  $pct_unreported_g = round(($count_unreported_g / $count_unreported)*100, 1);
                  $pct_unreported_u = round(($count_unreported_u / $count_unreported)*100, 1);
              }
              // build the data for the pie chart.
              $str_piedata = "[['Candidate', 'Votes']";
              $str_colors = "";
              for ($i=0; $i< $num_candidates; $i++) {
                 // if ($i > 0 ) { $str_piedata .= ","; }
                  $candidate_name = $candresult[$i]->candidate;

                  $str_piedata .= ",['". $candresult[$i]->candidate."', ".$sums[$candidate_name]."]";
                  switch ($candidate_name) {
                   case 'Yes':
                          $str_colors .= ",'#4B874F'"; // a nice green
                          break;
                      case 'No':
                          $str_colors .= ",'grey'";
                          break;
                  }
                  switch ($candresult[$i]->party) {
                      case 'R':
                          $str_colors .= ",'#D33'";
                          break;
                      case 'D':
                          $str_colors .= ",'#1E73BE'"; // a nice blue
                          break;
                      case 'G':
                          $str_colors .= ",'green'";
                          break;
                      case 'U':
                          $str_colors .= ",'purple'";
                          break;
                      case 'I':
                          $str_colors .= ",'grey'";
                          break;

                  }

              }
              if ($str_colors <> "") {
                  $str_colors = ',colors :['.substr($str_colors,1).']';
              }
              $str_piedata .= "]";
          }

          /* ********** start building html ************ */

          $htmlreturn = '<div class="eai-resultsimple-wrapper">';


          $htmlreturn .= '<div class="eai-racesimple"><h4>';
          $title = $race;
          if ($link) {
              $htmlreturn .= '<a href="'.$link.'">'.$title.'</a>';
          } else {
                $htmlreturn .= $title;
          }
          $htmlreturn .= '</h4>';

          /* display the results */
          if ($raceresults) {
              // first some of the totals & counts
              $htmlreturn .= '<ul class="eai-results-sum">';
              for ($i=0; $i< $num_candidates; $i++) {
                  $candidate_name = $candresult[$i]->candidate;
                  if ($candresult[$i]->party) {
                          $party_string = ' (<span class="party-'.$candresult[$i]->party.'">'.$candresult[$i]->party.'</span>) ';
                      } else {
                          $party_string = '';
                  }
                  $htmlreturn .= "<li>". $candidate_name.$party_string;
                  if ($found_votes) {
                      $htmlreturn .= ': '.number_format_i18n($sums[$candidate_name]);
                  }
                  if (($count_voted > 0) && ($all_towns_reported)) {
                      $htmlreturn .= ' - '.round(($sums[$candidate_name]/$count_voted)*100).'%';
                  }
                  $htmlreturn .='</li>';
              }
              $htmlreturn .= '</ul>';
              if ($found_votes) {
                  if (!$all_towns_reported) {
                      $htmlreturn .= "<p>".$count_precinct_reporting.' of '.$count_precincts.' Towns reported.</p>';
                  }
                  //$htmlreturn .= "<p>".$count_precinct_reporting.' of '.$count_precincts.' Precincts reporting.'; // : '.round(($count_precinct_reporting/$count_precincts)*100).'%</p>';
                  //$htmlreturn .= '<p>'.number_format_i18n($count_voted).' of '.number_format_i18n($total_voters).' voters. Participation: '.round(($count_voted/$total_voters)*100).'%</p>';

                   /* $htmlreturn .= '<script>var piedata'.$raceorder.' = google.visualization.arrayToDataTable('.$str_piedata.');</script>';
                  $htmlreturn .= '<p>Total unreported:'.number_format_i18n($count_unreported);
                  $htmlreturn .= ' d:'.number_format_i18n($count_unreported_d).'('.$pct_unreported_d.'%), ';
                  $htmlreturn .= ' r:'.number_format_i18n($count_unreported_r).'('.$pct_unreported_r.'%), ';
                  $htmlreturn .= ' u:'.number_format_i18n($count_unreported_u).'('.$pct_unreported_u.'%), ';
                  $htmlreturn .= ' g:'.number_format_i18n($count_unreported_g).'('.$pct_unreported_g.'%) ';
                  $htmlreturn .= '</p>';  */
                   if ($link) {
                      $htmlreturn .= '<span> <a href="'.$link.'"> More details >>> </a></span>';
                  }
                  $htmlreturn .= $unofficial_text;


              } else {
                   $htmlreturn .= '<p class="eai-checkback">Polls close at 8 p.m. Check back then for results as they come in.</p>';
              }

          } else {
              $htmlreturn .= "<p>No results</p>";
          }

      } else {
          $htmlreturn .= "<p>No Candidates for ".$race.".</p>";
      }
      $htmlreturn .="</div></div>"; // end of wrapper & identifying div.
    } // end of $eai_elections_enable_results
    return $htmlreturn;
}
?>
