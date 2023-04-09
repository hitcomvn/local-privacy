function log_location() {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $location_info = get_location_info();
    $location_string = '';
    if ( !empty( $location_info['city'] ) ) {
        $location_string .= $location_info['city'] . ', ';
    }
    if ( !empty( $location_info['region'] ) ) {
        $location_string .= $location_info['region'] . ', ';
    }
    if ( !empty( $location_info['country'] ) ) {
        $location_string .= $location_info['country'];
    }
    if ( !empty( $location_string ) ) {
        $location_string .= ' (' . $location_info['lat'] . ', ' . $location_info['lon'] . ')';
    }
    $log_message = sprintf( __( 'Location: %s, IP: %s, User Agent: %s' ), $location_string, $location_info['ip'], $user_agent );
    error_log( $log_message );
}
