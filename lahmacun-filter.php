<?php
/*
 * Plugin Name: Lahmacun Filter
 * Description: Filter bad words in content and title
 * Author: Mustafa Zahid EFE
 * Author URI: https://zahidefe.net
 */

add_action( 'admin_menu', 'lhm_create_options_page' );
add_action( 'admin_init', 'lhm_register_settings' );

$lhm_settings = get_option( 'lhm_settings' );

function lhm_option( $key, $default = '' ) {
	global $lhm_settings;

	if ( isset( $lhm_settings[ $key ] ) ) {
		return $lhm_settings[ $key ];
	}

	return $default;
}

function lhm_create_options_page() {
	add_options_page( __( 'Bad Word Filter Settings', 'lahmacun-filter' ), __( 'Filter Settings', 'lahmacun-filter' ), 'manage_options', 'lahmacun-filter', 'lhm_options_page' );
}

function lhm_register_settings() {
	register_setting( 'lhm_settings_group', 'lhm_settings' );
}

function lhm_options_page() {
	?>
    <div class="wrap">
        <h3>Lahmacun Bad Words Filter</h3>
        <form action="options.php" method="POST">
            <table class="form-table">
				<?php settings_fields( 'lhm_settings_group' ); ?>
                <!--	            --><?php //do_settings_sections( '' ); ?>
                <tr valign="top">
                    <th scope="row">Enable Filtering</th>
                    <td><input type="checkbox" name="lhm_settings[enable_filter]" value="1" <?php echo lhm_option( 'enable_filter' ) ? 'checked' : null; ?>></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Case Sensitive</th>
                    <td><input type="checkbox" name="lhm_settings[case_sensitive]" value="1" <?php echo lhm_option( 'case_sensitive' ) ? 'checked' : null; ?>></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Filtered Words List</th>
                    <td><textarea name="lhm_settings[filtered_words]" id="filtered_words" cols="50" rows="10"><?php echo lhm_option( 'filtered_words' ) ?></textarea></td>
                </tr>

                <!--                <tr valign="top">-->
                <!--                    <th scope="row">Some Other Option</th>-->
                <!--                    <td><input type="text" name="some_other_option" value="--><?php //echo esc_attr( get_option('some_other_option') ); ?><!--" /></td>-->
                <!--                </tr>-->
            </table>
			<?php submit_button( 'Save Filter Options' ); ?>
        </form>
    </div>
	<?php
}

// Filter The Words
add_filter( 'the_content', 'lhm_filter_content' );
add_filter( 'the_title', 'lhm_filter_content' );

function lhm_filter_content( $content ) {
	if ( ! lhm_option( 'enable_filter', false ) ) {
		return $content;
	}
	$filtered_words = lhm_option( 'filtered_words' );
	$filtered_words = explode( PHP_EOL, $filtered_words );
	$filtered_words = array_map( 'trim', $filtered_words );
	$censored_data  = [];
	foreach ( $filtered_words as $word ) {
		$censored_data[] = str_repeat( '*', strlen( $word ) );
	}
	if ( lhm_option( 'case_sensitive', false ) ) {
		$content = str_replace( $filtered_words, $censored_data, $content );
	} else {
		$content = str_ireplace( $filtered_words, $censored_data, $content );
	}

	return $content;
}