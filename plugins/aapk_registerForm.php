<?php
/*
Plugin Name: Register Form
Plugin URI:
Description: Creates a simple register form and save in database and show total number in the top of register form
Version: 0.11
Author: Matsumoto san (Modified)
Author URI:
License: 
*/

function register_form_style_script() {
	wp_enqueue_style( 'registerFormStyle', plugin_dir_url( __FILE__ ) . '/css/aapk_registerForm.css' );
	wp_enqueue_script( 'registerFormScript', plugin_dir_url( __FILE__ ) . '/js/aapk_registerForm.js', array( 'jquery' ), '1.0', true);
	wp_localize_script( 'registerFormScript', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
}
add_action( 'wp_enqueue_scripts', 'register_form_style_script' );

function specialchars_checked($data) {
	$data = htmlspecialchars( $data );
	return $data;
}

function display_form(){
	$nameErr = $ageErr = $phoneErr = $occupationErr ="";
	$name = $age = $phone = $occupation ="";

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if ( ! empty( $_POST['username'] ) && ! empty( $_POST['age'] ) && ! empty( $_POST['phone'] ) && ! empty( $_POST['occupation'] ) ) {
			$name = specialchars_checked( $_POST['username'] );
			$age = specialchars_checked( $_POST['age'] );
			$phone = specialchars_checked( $_POST['phone'] );
			$ocupation = specialchars_checked( $_POST['ocupation'] );
			store_data();
		} else {
			if ( empty( $_POST['username'] ) ) {
				$nameErr = 'Name is required';
			}
			if ( empty( $_POST['age'] ) ) {
				$ageErr = 'Age is required';
			}
			if ( empty( $_POST['phone'] ) ) {
				$phoneErr = 'Phone is required';
			}
			if ( empty( $_POST['occupation'] ) ) {
				$occupationErr = 'Occupation is required';
			}
		}
	}
	$total = get_total();

	echo <<< _HTML_
<form method="POST" action="">
<div id="reg_form">
<div><h2 id="header" >RegistrationForm</h2></div><div id="total">TotalRegistration= <label id="total_no" style="display:inline"> $total </label></div>
<table id="form_tbl">
<tr><th>Name</th><td><input type="text" name="username"/></td>
<td><span class="error"> $nameErr </span></td></tr>
<tr><th>Phone no.</th><td><input type="text" name="phone"/></td>
<td><span class="error"> $phoneErr </span></td></tr>
<tr><th> Age </th><td><input type="text" name="age"/></td>
<td><span class="error"> $ageErr </span> </td></tr>
<tr><th> Occupation </th>
<td><select name="occupation">
_HTML_;

	$occupations = get_occupation();
	foreach ( $occupations as $occupation ) {
		echo '<option>' . $occupation->job_title . '</option>';
	}

	echo <<< _HTML_
</select></td>
<td><span class="error">  $occupationErr </span> </td></tr>
 </table> <br/> 
 <div id="div_btn">
<input id="submit_btn" type="submit" name="submit" value="Submit">
<input id="show_btn" type="button" name="showBtn" value="Show Result">
<input id="hide_btn" type="button" name="hideBtn" value="Hide Result">
</div>
<hr>
</div></form>
_HTML_;
}

function display_input(){
	echo '<h3 id="result_header">Registered Members Information</h3>';
	$rows = get_all_data();
	echo <<< _HTML_
<div id="search_div"> Search:<input id="search_box" type="search" name="searchbox" /></div>
<div id="result_table">
<table>
<tr>
<th>Name</th>
<th>Phone No.</th>
<th>Age</th>
<th>Occupation</th>
</tr>
_HTML_;
	foreach( $rows as $row ) {
		echo '<tr>
			<td>'  .	$row->Name . '</td>
			<td>' . $row->Phone_No . '</td>
			<td>' . $row->Age . '</td>
			<td>' . $row->Occupation . '</td>
		</tr>';
	}
	echo '</table></div>';
	echo '<h3 id="footer">Thank you for visiting!!!</h3>';
}

function display_search() {
	echo '<h3 id="search_result_header">Searched Members Information</h3>';
	echo <<< _HTML_
<div id="search_table">
<table>
<thead>
<tr><th>Name</th><th>Phone</th><th>Age</th><th>Occupation</th</tr>
</thead>
<tbody></tbody>
</table>
</div>
_HTML_;
}

function store_data(){
	global $wpdb;
/*$test=$wpdb->select('testaa');
var_dump($test);*/

	$res = $wpdb->insert( 'wp_register_form',
		array( 
			'Name' => $_POST['username'] ,
			'Phone_No' => $_POST['phone'] ,
			'Age' => $_POST['age'], 
			'Occupation' => $_POST['occupation'],
		), 
		array( 
			'%s',
			'%d', 
			'%s',
		) 
	);
	//var_dump($res);
	echo $wpdb->insert_id;
}

function get_total(){
	global $wpdb;
	$user_count = $wpdb->get_var( "SELECT COUNT(*) FROM wp_register_form" );
	return $user_count;
}

function get_all_data(){
	global $wpdb;
	$rows = $wpdb->get_results( "SELECT * FROM wp_register_form" );
	return $rows;
}

function get_occupation(){
	global $wpdb;
	$occupations = $wpdb->get_results( "SELECT * FROM wp_occupation" );
	return $occupations;
}

add_action( 'wp_ajax_search_data_action', 'search_data_action' );
add_action( 'wp_ajax_nopriv_search_data_action', 'search_data_action' );

function search_data_action() {
	global $wpdb;
	$searchResult = null;
	if ( ! empty( $_POST['searchData'] ) ) {
		$user_search = $_POST['searchData'];
		$searchResult = $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM wp_register_form WHERE Name LIKE %s",
			'%' . $user_search . '%'
		) );
	}
	wp_send_json( $searchResult );
}
	
function rf_shortcode() {
	ob_start();
	display_form();
	display_input();
	display_search();
	return ob_get_clean();
}

add_shortcode( 'reg_form', 'rf_shortcode' );
