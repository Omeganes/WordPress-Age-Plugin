<?php

/**
 * App Class
 * @package AgePlugIn
 */

 
class AgePlugin 
{
	/**
	 * AgePlugin constructor.
	 */
	function __construct() {
		$this->add_age_options();
    }

	/**
	 * Integrates the age feature in Wordpress Website
	 */
    function add_age_options() {
	    // Admin creating user
	    add_action( 'user_new_form', array($this, 'admin_registration_form') );
	    add_action( 'edit_user_created_user', array($this, 'admin_user_register') );
	    // Personal profile edit page
	    add_action( 'show_user_profile', array($this, 'show_age_field') );
	    add_action( 'personal_options_update', array($this ,'update_profile_age_field') );
	    // Admin edit page
	    add_action( 'edit_user_profile', array($this,'show_age_field') );
	    add_action( 'edit_user_profile_update', array($this,'update_profile_age_field') );
	    // Errors filter
	    add_filter( 'user_profile_update_errors', array($this, 'user_profile_update_errors'), 10, 3 );
	    // Rest API
	    add_action('rest_api_init', array($this, 'add_age_rest_api'));
    }


	function activate() {
        flush_rewrite_rules();
    }

	function deactivate() {
		flush_rewrite_rules();
	}

	/**
     * Adds age field during creation of new users by admins
     *
	 * @param $operation
	 */
	function admin_registration_form( $operation ) {
		if ( 'add-new-user' !== $operation ) {
			return;
		}
	
		$age = ! empty( $_POST['age'] ) ? intval( $_POST['age'] ) : '';
	
		?>
		<h3><?php esc_html_e( 'Personal Information', 'crf' ); ?></h3>
	
		<table class="form-table">
			<tr>
				<th><label for="age"><?php esc_html_e( 'Age', 'crf' ); ?></label> <span class="description"><?php esc_html_e( '(required)', 'crf' ); ?></span></th>
				<td>
					<input type="number"
					   min="0"
					   step="1"
					   id="age"
					   name="age"
					   value="<?php echo esc_attr( $age ); ?>"
					   class="regular-text"
					/>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
     * Create new user
     *
	 * @param $user_id
	 */
	function admin_user_register( $user_id ) {
		if ( ! empty( $_POST['age'] ) ) {
			update_user_meta( $user_id, 'age', intval( $_POST['age'] ) );
		}
	}

	/**
     * Show age field in user edit profile
     *
	 * @param $user
	 */
	function show_age_field( $user ) {
		$age = get_the_author_meta( 'age', $user->ID );
		?>
		<h3><?php esc_html_e( 'Personal Information', 'crf' ); ?></h3>
	
		<table class="form-table">
			<tr>
				<th><label for="age"><?php esc_html_e( 'Age', 'crf' ); ?></label></th>
				<td>
					<input type="number"
					   min="0"
					   step="1"
					   id="age"
					   name="age"
					   value="<?php echo esc_attr( $age ); ?>"
					   class="regular-text"
					/>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
     * Updates profile age field
     *
	 * @param $user_id
	 * @return false
	 */
	function update_profile_age_field( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}
	
		if ( ! empty( $_POST['age'] ) && intval( $_POST['age'] ) > 0 ) {
			update_user_meta( $user_id, 'age', intval( $_POST['age'] ) );
		}

		if(empty($_POST['age'])) {
			delete_user_meta($user_id, 'age');
		}
	}

	/**
     * Filters age requirements: positive numerical value.
     *
	 * @param $errors
	 * @param $update
	 * @param $user
	 */
	function user_profile_update_errors( $errors, $update, $user ) {
		if (!empty($_POST['age']))
		{
			if (!is_numeric($_POST['age']) || intval($_POST['age'])  < 0 ) {
				$errors->add( 'age_error', __( '<strong>Error</strong>: Age must be a positive numerical value.', 'crf' ) );
			}
		}
    }

	/**
     * Gets user age for RestAPI
     *
	 * @param $user
	 * @param $field_name
	 * @param $request
	 *
	 * @return mixed
	 */
    function get_user_age( $user, $field_name, $request ) { 
        return get_user_meta( $user['id'], $field_name, true );
    }

	/**
     * Updates user age via RestAPI
     *
	 * @param $user
	 * @param $meta_value
	 */
    function update_user_age( $user, $meta_value ) { 
        update_user_meta( $user['id'], 'age', $meta_value );
	}

	/**
	 * Adds age field to RestAPI
	 */
	function add_age_rest_api() {
        register_rest_field( 'user', 'age', array(
            'get_callback'    => array($this, 'get_user_age'),
            'update_callback' => array($this, 'update_user_age'),
            'schema'          => [
                                    'type'        => 'number',
                                    'description' => 'age of the user',
                                    'context'     => [ 'view', 'edit' ],
                                ],
        ));
    }
}