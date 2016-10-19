<?php
/**
 * Template Name: User Profile
 *
 * Allow users to update their profiles from Frontend.
 *
 */
/* mods 
	6Jan14 zig - change logic surround email saves. 
	6Jan14 zig - change must be logged in text to include link to login page.
*/


/* Get user info. */
global $current_user, $wp_roles;
get_currentuserinfo();

/* Load the registration file. */
require_once( ABSPATH . WPINC . '/registration.php' );
$error = array();    
/* If profile was saved, update profile. */
if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

    /* Update user password. */
    if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
        if ( $_POST['pass1'] == $_POST['pass2'] )
            wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
        else
            $error[] = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
    }

    /* Update user information. */

    if ( !empty( $_POST['email'] ) ) {
        if (!is_email(esc_attr( $_POST['email'] ))) {
            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
        } else { 
            $email_exists = email_exists(esc_attr( $_POST['email'] ));
            if ( ($email_exists) && ($email_exists!= $current_user->id )) {
                $error[] = __('This email is already used by another user.  try a different one.', 'profile');
            } else {
                wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
            } 
        }
    } 

    if ( !empty( $_POST['first-name'] ) )
        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
    if ( !empty( $_POST['last-name'] ) )
        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
    if ( !empty( $_POST['description'] ) )
        update_user_meta( $current_user->ID, 'description', esc_attr( $_POST['description'] ) );

    /* Redirect so the page will show updated info.*/
  /*I am not Author of this Code- i dont know why but it worked for me after changing below line to if ( count($error) == 0 ){ */
    if ( count($error) == 0 ) {
        //action hook for plugins and extra fields saving
        do_action('edit_user_profile_update', $current_user->ID);
        wp_redirect( get_permalink() );
        exit;
    }
}

?>

<?php get_header();?>
<div class="prl-container">
    <div class="prl-grid prl-grid-divider">
        <section id="main" class="prl-span-9"> 
		   <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	    <div id="post-<?php the_ID(); ?>">
	        <div class="entry-content entry">
	            <?php  the_content();  ?>
	            <?php  /* if (function_exists( 'is_issuem_leaky_subscriber_logged_in') ) {
	            	if (is_issuem_leaky_subscriber_logged_in()) {
	            		echo '<p> Welcome</p>';
	            	} else {
	            		echo '<p> Please login </p>';
	            	}
	           	} else { echo '<p class="not_set_up"></p>'; } */ ?>
	            <?php if ( !is_user_logged_in() ) : ?>
	                    <p class="warning">
	                        <?php /* _e('You must be logged in to edit your profile.', 'profile');*/ ?>
	                           You <a href="<?php get_bloginfo('url'); ?>/membership/login/">must be logged in</a> to edit your profile.
	                    </p><!-- .warning -->
	            <?php else : ?>
	                <?php /* if ( count($error) > 0 ) echo '<p class="error">' . implode("<br />", $error) . '</p>'; */ ?>
	                <form method="post" id="eia-updateuser" action="<?php the_permalink(); ?>">
	                	 <input type='hidden' name='user_id' value='<?php echo $current_user->ID; ?>' />
	                	<table class="form-table"><tdbody>
	                		<tr class="form-username">
                				<th><label for="Username"><?php _e('User Name', 'profile'); ?></label></th>
                        		<td><input class="text-input" name="user-name" type="text" id="user-name" value="<?php the_author_meta( 'user_login', $current_user->ID ); ?>" disabled="disabled" /></td>
                        	</tr><!-- .form-username -->
                			<tr class="form-username">
                				<th><label for="first-name"><?php _e('First Name', 'profile'); ?></label></th>
                        		<td><input class="text-input" name="first-name" type="text" id="first-name" value="<?php the_author_meta( 'first_name', $current_user->ID ); ?>" /></td>
                        	</tr><!-- .form-username -->
		                    <tr class="form-lastname">
		                        <th><label for="last-name"><?php _e('Last Name', 'profile'); ?></label></th>
		                        <td><input class="text-input" name="last-name" type="text" id="last-name" value="<?php the_author_meta( 'last_name', $current_user->ID ); ?>" /></td>
		                    </tr><!-- .form-username -->
		                    <tr class="form-email">
		                        <th><label for="email"><?php _e('E-mail *', 'profile'); ?></label></th>
		                        <td><input class="text-input" name="email" type="text" id="email" value="<?php the_author_meta( 'user_email', $current_user->ID ); ?>" /></td>
		                    </tr><!-- .form-email -->
		                    <tr class="form-password">
		                        <th><label for="pass1"><?php _e('Password *', 'profile'); ?> </label></th>
		                        <td><input class="text-input" name="pass1" type="password" id="pass1" /></td>
		                    </tr><!-- .form-password -->
		                    <tr class="form-password">
		                        <th><label for="pass2"><?php _e('Repeat Password *', 'profile'); ?></label></th>
		                        <td><input class="text-input" name="pass2" type="password" id="pass2" /></td>
		                    </tr><!-- .form-password -->
		                    <tr class="form-textarea">
		                        <th><label for="description"><?php _e('Other Info', 'profile') ?></label></th>
		                        <td><textarea name="description" id="description" rows="3" cols="50"><?php the_author_meta( 'description', $current_user->ID ); ?></textarea></td>
		                    </tr><!-- .form-textarea -->

		                    <?php 
		                        //action hook for plugin and extra fields
		                        do_action('edit_user_profile',$current_user); 
		                    ?>
		                    <tr class="form-submit">
		                    	<th></th>
		                    	<td>
			                        <input name="updateuser" type="submit" id="updateuser" class="submit button" value="<?php _e('Update', 'profile'); ?>" />
			                        <?php wp_nonce_field( 'update-user' ) ?>
			                        <input name="action" type="hidden" id="action" value="update-user" />
		                       	</td>
		                    </tr><!-- .form-submit -->
		                </tbody></table>
	                </form><!-- #updateuser -->
	            <?php endif; ?>
	        </div><!-- .entry-content -->
	    </div><!-- .hentry .post -->
    <?php endwhile; ?>
<?php else: ?>
    <p class="no-data">
        <?php _e('Sorry, no page matched your criteria.', 'profile'); ?>
    </p><!-- .no-data -->
<?php endif; ?>
		
        </section>

        <aside id="sidebar" class="prl-span-3">
            <?php get_sidebar('custom');?>
        </aside>
    </div>
</div>
<?php get_footer();?>       
        