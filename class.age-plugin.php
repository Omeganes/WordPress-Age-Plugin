<?php


class AgePlugin 
{
	function __construct() {
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

	function activate() {
        flush_rewrite_rules();
    }

	function deactivate() {}
	function uninstall() {}


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

	function admin_user_register( $user_id ) {
		if ( ! empty( $_POST['age'] ) ) {
			update_user_meta( $user_id, 'age', intval( $_POST['age'] ) );
		}
	}

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

	function user_profile_update_errors( $errors, $update, $user ) {
		if (!empty($_POST['age']))
		{
			if (!is_numeric($_POST['age']) || intval($_POST['age'])  < 0 ) {
				$errors->add( 'age_error', __( '<strong>Error</strong>: Age must be a positive numerical value.', 'crf' ) );
			}
		}
    }
    
    function get_user_age( $user, $field_name, $request ) { 
        return get_user_meta( $user['id'], $field_name, true );
    }
    
    function update_user_age( $user, $meta_value ) { 
        update_user_meta( $user['id'], 'age', $meta_value );
    }
}