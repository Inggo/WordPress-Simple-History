<?php

defined( 'ABSPATH' ) or die();

/**
 * Logs user switching from the great User Switching plugin
 * Plugin URL: https://wordpress.org/plugins/user-switching/
 * 
 * @since 2.2
 */
class PluginUserSwitchingLogger extends SimpleLogger {

	public $slug = __CLASS__;

	/**
	 * Get array with information about this logger
	 * 
	 * @return array
	 */
	function getInfo() {

		$arr_info = array(			
			"name" => _x("User Switching Logger", "PluginUserSwitchingLogger", "simple-history"),
			"description" => _x("Logs user switches", "PluginUserSwitchingLogger", "simple-history"),
			"capability" => "edit_users",
			"messages" => array(
				'switched_to_user' => _x('Switched to user "{user_login_to}" from user "{user_login_from}"', "PluginUserSwitchingLogger", "simple-history"),
				'switched_back_user' => _x('Switched back to user "{user_login_to}" from "{user_login_from}"', "PluginUserSwitchingLogger", "simple-history"),
				'switched_off_user' => _x('Switched off user "{user_login}"', "PluginUserSwitchingLogger", "simple-history"),
			),
		);
		
		return $arr_info;

	}

	function loaded() {

		add_action( 'switch_to_user', array( $this, "on_switch_to_user" ), 10, 2 );
		add_action( 'switch_back_user', array( $this, "on_switch_back_user" ), 10, 2 );
		add_action( 'switch_off_user', array( $this, "on_switch_off_user" ), 10, 1 );
 
	}

	function on_switch_to_user( $user_id, $old_user_id ) {

		$user_to = get_user_by( "id", $user_id );
		$user_from = get_user_by( "id", $old_user_id );

		if ( ! is_a($user_to, "WP_User") || ! is_a($user_from, "WP_User") ) {
			return;
		}

		$this->infoMessage(
			"switched_to_user",
			array(
				// It is the old user who initiates the switching
				"_initiator" => SimpleLoggerLogInitiators::WP_USER,
				"_user_id" => $old_user_id,

				"user_id" => $user_id,
				"old_user_id" => $old_user_id,
				"user_login_to" => $user_to->user_login,
				"user_login_from" => $user_from->user_login,
			)
		);

	}

	function on_switch_back_user( $user_id, $old_user_id ) {

		$user_to = get_user_by( "id", $user_id );
		$user_from = get_user_by( "id", $old_user_id );

		if ( ! is_a($user_to, "WP_User") || ! is_a($user_from, "WP_User") ) {
			return;
		}

		$this->infoMessage(
			"switched_back_user",
			array(
				// It is the old user who initiates the switching
				// or probably the new user, but we can't be sure about that.
				// anyway: we log who was logged in as the initiator
				"_initiator" => SimpleLoggerLogInitiators::WP_USER,
				"_user_id" => $old_user_id,

				"user_id" => $user_id,
				"old_user_id" => $old_user_id,
				"user_login_to" => $user_to->user_login,
				"user_login_from" => $user_from->user_login
			)
		);

	}

	function on_switch_off_user( $user_id ) {

		$user = get_user_by( "id", $user_id );

		if ( ! is_a($user, "WP_User") ) {
			return;
		}

		$this->infoMessage(
			"switched_off_user",
			array(
				"_initiator" => SimpleLoggerLogInitiators::WP_USER,
				"_user_id" => $user_id,

				"user_id" => $user_id,
				"user_login" => $user->user_login
			)
		);

	}

}