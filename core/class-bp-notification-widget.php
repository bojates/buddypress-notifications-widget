<?php
/**
 * Notification widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 0 ); // Do not allow direct access.
}

/**
 * BuddyPress Notifications widget class
 */
class BuddyDev_BPNotification_Widget extends WP_Widget {
	/**
	 * Constructor
	 */
	public function __construct() {
		$name = __( '(BuddyDev) BP Notifications', 'buddypress-notifications-widget' );
		parent::__construct( false, $name );
	}

	/**
	 * Display widget output.
	 *
	 * @param array $args widget args.
	 * @param array $instance current instance settings.
	 */
	public function widget( $args, $instance ) {

		// do not show anything if user is not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// let us get the notifications for the user.
		if ( function_exists( 'bp_notifications_get_notifications_for_user' ) ) {
			$notifications = bp_notifications_get_notifications_for_user( get_current_user_id(), 'string' );
		} else {
			$notifications = bp_core_get_notifications_for_user( get_current_user_id(), 'string' );
		}

		// will be set to false if there are no notifications.
		if ( empty( $notifications ) ) {
			$count = 0;
		} else {
			$count = count( $notifications );
		}

		// do not show this widget.
		if ( $count <= 0 ) {
			return;
		}

		echo $args['before_widget'];
		echo $args['before_title'];
		echo $instance['title'];

		if ( $instance['show_count_in_title'] ) {
			printf( "<span class='notification-count-in-title'>(%d)</span>", $count );
		}

		echo $args['after_title'];

		echo "<div class='bpnw-notification-list bp-notification-widget-notifications-list'>";

		if ( ! empty( $instance['show_count'] ) ) {
			printf( __( 'You have %d new Notifications', 'buddypress-notifications-widget' ), $count );
		}

		if ( $instance['show_list'] ) {
			self::print_list( $notifications, $count );
		}

		if ( $count > 0 && $instance['show_clear_notification'] ) {
			echo '<a class="bp-notifications-widget-clear-link" href="' . bp_core_get_user_domain( get_current_user_id() ) . '?clear-all=true' . '&_wpnonce=' . wp_create_nonce( 'clear-all-notifications-for-' . bp_loggedin_user_id() ) . '">' . __( '[x] Clear All Notifications', 'buddypress-notifications-widget' ) . '</a>';
		}

		echo '</div>';
		echo $args['after_widget'];

	}

	/**
	 * Update widget instance settings.
	 *
	 * @param array $new_instance new settings.
	 * @param array $old_instance ols settings.
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance['title']                   = strip_tags( $new_instance['title'] );
		$instance['show_count_in_title']     = intval( $new_instance['show_count_in_title'] );
		$instance['show_count']              = intval( $new_instance['show_count'] );
		$instance['show_list']               = intval( $new_instance['show_list'] );
		$instance['show_clear_notification'] = intval( $new_instance['show_clear_notification'] );

		return $instance;
	}

	/**
	 * Display widget form.
	 *
	 * @param array $instance cuerrent instance.
	 */
	public function form( $instance ) {
		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title'                   => __( 'Notifications', 'bpdnw' ),
				'show_count'              => 1,
				'show_count_in_title'     => 0,
				'show_list'               => 1,
				'show_clear_notification' => 1,
			)
		);

		$title                   = strip_tags( $instance['title'] );
		$show_count_in_title     = $instance['show_count_in_title']; // show notification count.
		$show_count              = $instance['show_count']; // show notification count.
		$show_list               = $instance['show_list']; // show notification list.
		$show_clear_notification = $instance['show_clear_notification'];
		?>
        <p>
            <label for="bp-notification-title"><strong><?php _e( 'Title:', 'buddypress-notifications-widget' ); ?> </strong>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
                       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
                       value="<?php echo esc_attr( $title ); ?>" style="width: 100%"/>
            </label>
        </p>
        <p>
            <label for="bp-show-notification-count-in-title"><?php _e( 'Show Notification count in Title', 'buddypress-notifications-widget' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count_in_title' ); ?>"
                       name="<?php echo $this->get_field_name( 'show_count_in_title' ); ?>" type="checkbox" value="1"
				       <?php if ( $show_count_in_title ) {
					       echo 'checked="checked"';
				       } ?>style="width: 30%"/>
            </label>
        </p>
        <p>
            <label for="bp-show-notification-count"><?php _e( 'Show Notification count', 'buddypress-notifications-widget' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_count' ); ?>"
                       name="<?php echo $this->get_field_name( 'show_count' ); ?>" type="checkbox" value="1"
				       <?php if ( $show_count ) {
					       echo 'checked="checked"';
				       } ?>style="width: 30%"/>
            </label>
        </p>
        <p>
            <label for="bp-show-notification-list"><?php _e( 'Show the list of Notifications', 'buddypress-notifications-widget' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_list' ); ?>"
                       name="<?php echo $this->get_field_name( 'show_list' ); ?>" type="checkbox"
                       value="1" <?php if ( $show_list ) {
					echo 'checked="checked"';
				} ?> style="width: 30%"/>
            </label>
        </p>
        <p>
            <label for="bp-show_clear_notification"><?php _e( 'Show the Clear Notifications button', 'buddypress-notifications-widget' ); ?>
                <input class="widefat" id="<?php echo $this->get_field_id( 'show_clear_notification' ); ?>"
                       name="<?php echo $this->get_field_name( 'show_clear_notification' ); ?>" type="checkbox"
                       value="1" <?php if ( $show_clear_notification ) {
					echo 'checked="checked"';
				} ?> style="width: 30%"/>
            </label>
        </p>
		<?php
	}

	/**
	 * Print notifications list
	 *
	 * @param $notifications
	 * @param $count
	 */
	public function print_list( $notifications, $count ) {

		echo '<ul class="bp-notification-list">';

		if ( $notifications ) {

			$counter = 0;

			for ( $i = 0; $i < $count; $i ++ ) {

				$notification_item = '';
				if ( is_array( $notifications[ $i ] ) ) {
					$notification_item = sprintf( '<a href="%s">%s</a>', $notifications[ $i ]['link'], $notifications[ $i ]['text'] );
				} else {
					$notification_item = $notifications[ $i ];
				}


				$alt = ( 0 == $counter % 2 ) ? ' class="alt"' : ''; ?>

                <li <?php echo $alt ?>><?php echo $notification_item ?></li>

				<?php $counter ++;
			}
		} else { ?>

            <li><?php _e( 'You don\'t have any new notification.', 'buddypress-notifications-widget' ); ?></li>

			<?php
		}

		echo '</ul>';
	}
}
