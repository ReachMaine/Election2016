<?php
/*
Template name: Election Vote Input
*/
$have_town = false;
$updated = false;
global $wpdb;
$table = "elections2016"; 

if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !empty( $_GET['action'] ) && $_GET['action'] == 'updatetown' ) {

    $have_town = true;
    $intown = $_GET['town_ddl'];
} 
if ( 'GET' == $_SERVER['REQUEST_METHOD'] && !empty( $_GET['action'] ) && $_GET['action'] == 'updatevotes' ) {
    $have_town = false;
    $updated = $_GET;

}

get_header(); ?>

<?php if( has_excerpt() ) { ?>
<div class="page-header">
	<?php the_excerpt(); ?>
</div>
<?php } ?>

<div  class="page-wrapper page-left-sidebar">
<div class="row">

<div id="content" class="large-9 right columns" role="main">
	<div class="page-inner">
		<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'page' ); ?>
		<?php endwhile; // end of the loop. ?>
			<?php if ($have_town) { echo "<p>have town = ".$intown."</p>"; }
			      if ($updated) {echo "<p>updated.</p>"; echo"<pre>";var_dump($updated);echo"</pre>"; } ?>
			<form method="get" id="town_form" action="<?php the_permalink(); ?>">
				<select name="town_ddl" class="amw" >
					<option value="">Select Town</option>
					<option value="Amherst">Amherst</option>
					<option value="Aurora">Aurora</option>
					<option value="Bar Harbor">Bar Harbor</option>
					<option value="Blue Hill">Blue Hill</option>
					<option value="Brooklin">Brooklin</option>
					<option value="Bucksport">Bucksport</option>
					<option value="Castine">Castine</option>
					<option value="Cranberry Isles">Cranberry Islands</option>
					<option value="Dedham">Dedham</option>
					<option value="Deer Isle">Deer Isle</option>
					<option value="Eastbrook">Eastbrook</option>
					<option value="Ellsworth">Ellsworth</option>
					<option value="Franklin">Franklin</option>
					<option value="Frenchboro">Frenchboro</option>
					<option value="Gouldsboro">Gouldsboro</option>
					<option value="Great Pond">Great Pond</option>
					<option value="Hancock">Hancock</option>
					<option value="Lamoine">Lamoine</option>
					<option value="Mount Desert">Mount Desert</option>
					<option value="Orland">Orland</option>
					<option value="Osborn">Osborn</option>
					<option value="Otis">Otis</option>
					<option value="Penobscot">Penobscot</option>
					<option value="Sedgwick">Sedgwick</option>
					<option value="Sorrento">Sorrento</option>
					<option value="Southwest Harbor">Southwest Harbor</option>
					<option value="Stonington">Stonington</option>
					<option value="Sullivan">Sullivan</option>
					<option value="Surry">Surry</option>
					<option value="Swans Island">Swan’s Island</option>
					<option value="Tremont">Tremont</option>
					<option value="Trenton">Trenton</option>
					<option value="Verona Island">Verona Island</option>
					<option value="Waltham">Waltham</option>
					<option value="Winter Harbor" >Winter Harbor</option> 
				</select>
				<input name="updatetown" type="submit" id="updatetown" class="submit button" value="GO" />
			    <input name="action" type="hidden" id="action" value="updatetown" />
			</form>  
			<form method="get" id="voting_form" action="<?php the_permalink(); ?>" class="eai-election-inputs-form">
				<?php /* */ 
				   $racesquery = 'SELECT  distinct `race` FROM `'.$table.'` WHERE town="'. $intown.'" ORDER BY raceorder';
				    //*echo '<p>RacesQuery: '.$racesquery.'</p>'; // testing
				    $racesresults = $wpdb->get_results($racesquery); 
				    //echo "<pre>"; var_dump($racesresults); echo "</pre>";// testing
				    $htmlout = "";
				    if ($racesresults) {
        				foreach ($racesresults as $race) {
				            //echo '<p> Race:'.$race->race.' Reg. voters:'.$race->registeredvoters.'</p>'; // testing
				            $indracequery = 'SELECT DISTINCT electionrecordid, candidate, party, votes, town, reported FROM '.$table.' WHERE town = "'.$intown.'" AND race="'.$race->race.'"';
				            //echo '<p>'.$indracequery.'</p>'; // testing
				            $indraceresults = $wpdb->get_results($indracequery); 
							//echo "<pre>";var_dump($indraceresults); echo "</pre>";// testing
				            if ($indraceresults) {
				            	
				            	//$htmlout .= '<p>'.$race->race.'</p>';
				            	$htmlout .= "<fieldset>";
				            	$htmlout .= '<legend>'.$race->race.'.</legend>';
				            	$htmlout .= '<dl class="eai-election-inputs-list" >';
				            	foreach ($indraceresults as $indrace) {
				            		$electionrecordid = $indrace->electionrecordid;
				            		$htmlout .= '<input type="hidden" name="id[]"  value="'.$electionrecordid.'">';
				            		$htmlout .= '<dt class="eai-election-inputs-item-cand">'.$indrace->candidate.'</dt>';
				            		if ($indrace->reported) {
				            			$htmlout .= '<dd><input name="reported[]" type="checkbox" label="reported"></dd>';
				            			$htmlout .= '<dd><input type="text" name="votes[]" label="'.$indrace->candidate.'" value="'.$indrace->votes.'"></dd>';
				            		} else {
				            			$htmlout .= '<dd><input name="reported[]" type="checkbox" label="reported" checked></dd>';
				            			$htmlout .= '<dd><input type="text" name="votes[]" label="'.$indrace->candidate.'" ></dd>';
				            		}
				            		$htmlout .= "<br>";
				        
			
				                   // $htmlout .= '</br>';
				                } /*end for individual race */
				                $htmlout .= "</dl>";
				                 $htmlout .= "</fieldset>";
				            }  else {
	        					echo "<p>no ind. results </p>";
	        					$htmlout = "<p>no ind. results </p> ";
	        				}
        				} /* end for raceresults */
        			} else { $htmlout =  "<p>No results</p>";}
        			echo $htmlout;
				?>
				<input name="updatevotes" type="submit" id="updatevotes" class="submit button" value="Save" />
				<input name="action" type="hidden" id="action" value="updatevotes" />
			</form>  

			<form method="post" id="clearing_form" action="<?php the_permalink(); ?>" class="eai-election-inputs-clear">
				<input name="clearform" type="submit" id="clearform" class="submit button" value="Clear" />
				<input name="action" type="hidden" id="action" value="clearform" />
			</form>   


	</div><!-- .page-inner -->
</div><!-- end #content large-9 left -->

<div class="large-3 columns left">
<?php get_sidebar(); ?>
</div><!-- end sidebar -->

</div><!-- end row -->
</div><!-- end page-right-sidebar container -->


<?php get_footer(); ?>
