<?php
/* election results short codes */

/* shortcode [electionresultsrace ] */
require_once(get_stylesheet_directory().'/inc/eai_election_result_racebars.php');

/* shortcode [electionresultspie] */
require_once(get_stylesheet_directory().'/inc/eai_election_results_simplepie.php');

/* shortcode [electionresultsimple ] - best for uncontested races */
require_once(get_stylesheet_directory().'/inc/eai_election_results_simple.php');

/* shortcode [electionresultstown ] */
require_once(get_stylesheet_directory().'/inc/eai_election_results_town.php');

?>
