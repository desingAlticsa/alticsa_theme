<?php
/**
 * Theme storage manipulations
 *
 * @package CHINCHILLA
 * @since CHINCHILLA 1.0
 */

// Disable direct call
if ( ! defined( 'ABSPATH' ) ) {
	exit; }

// Get theme variable
if ( ! function_exists( 'chinchilla_storage_get' ) ) {
	function chinchilla_storage_get( $var_name, $default = '' ) {
		global $CHINCHILLA_STORAGE;
		return isset( $CHINCHILLA_STORAGE[ $var_name ] ) ? $CHINCHILLA_STORAGE[ $var_name ] : $default;
	}
}

// Set theme variable
if ( ! function_exists( 'chinchilla_storage_set' ) ) {
	function chinchilla_storage_set( $var_name, $value ) {
		global $CHINCHILLA_STORAGE;
		$CHINCHILLA_STORAGE[ $var_name ] = $value;
	}
}

// Check if theme variable is empty
if ( ! function_exists( 'chinchilla_storage_empty' ) ) {
	function chinchilla_storage_empty( $var_name, $key = '', $key2 = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return empty( $CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return empty( $CHINCHILLA_STORAGE[ $var_name ][ $key ] );
		} else {
			return empty( $CHINCHILLA_STORAGE[ $var_name ] );
		}
	}
}

// Check if theme variable is set
if ( ! function_exists( 'chinchilla_storage_isset' ) ) {
	function chinchilla_storage_isset( $var_name, $key = '', $key2 = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			return isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			return isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] );
		} else {
			return isset( $CHINCHILLA_STORAGE[ $var_name ] );
		}
	}
}

// Delete theme variable
if ( ! function_exists( 'chinchilla_storage_unset' ) ) {
	function chinchilla_storage_unset( $var_name, $key = '', $key2 = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( ! empty( $key ) && ! empty( $key2 ) ) {
			unset( $CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] );
		} elseif ( ! empty( $key ) ) {
			unset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] );
		} else {
			unset( $CHINCHILLA_STORAGE[ $var_name ] );
		}
	}
}

// Inc/Dec theme variable with specified value
if ( ! function_exists( 'chinchilla_storage_inc' ) ) {
	function chinchilla_storage_inc( $var_name, $value = 1 ) {
		global $CHINCHILLA_STORAGE;
		if ( empty( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = 0;
		}
		$CHINCHILLA_STORAGE[ $var_name ] += $value;
	}
}

// Concatenate theme variable with specified value
if ( ! function_exists( 'chinchilla_storage_concat' ) ) {
	function chinchilla_storage_concat( $var_name, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( empty( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = '';
		}
		$CHINCHILLA_STORAGE[ $var_name ] .= $value;
	}
}

// Get array (one or two dim) element
if ( ! function_exists( 'chinchilla_storage_get_array' ) ) {
	function chinchilla_storage_get_array( $var_name, $key, $key2 = '', $default = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( empty( $key2 ) ) {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) ? $CHINCHILLA_STORAGE[ $var_name ][ $key ] : $default;
		} else {
			return ! empty( $var_name ) && ! empty( $key ) && isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] ) ? $CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] : $default;
		}
	}
}

// Set array element
if ( ! function_exists( 'chinchilla_storage_set_array' ) ) {
	function chinchilla_storage_set_array( $var_name, $key, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$CHINCHILLA_STORAGE[ $var_name ][] = $value;
		} else {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ] = $value;
		}
	}
}

// Set two-dim array element
if ( ! function_exists( 'chinchilla_storage_set_array2' ) ) {
	function chinchilla_storage_set_array2( $var_name, $key, $key2, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ] = array();
		}
		if ( '' === $key2 ) {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ][] = $value;
		} else {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ][ $key2 ] = $value;
		}
	}
}

// Merge array elements
if ( ! function_exists( 'chinchilla_storage_merge_array' ) ) {
	function chinchilla_storage_merge_array( $var_name, $key, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array_merge( $CHINCHILLA_STORAGE[ $var_name ], $value );
		} else {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ] = array_merge( $CHINCHILLA_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Add array element after the key
if ( ! function_exists( 'chinchilla_storage_set_array_after' ) ) {
	function chinchilla_storage_set_array_after( $var_name, $after, $key, $value = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			chinchilla_array_insert_after( $CHINCHILLA_STORAGE[ $var_name ], $after, $key );
		} else {
			chinchilla_array_insert_after( $CHINCHILLA_STORAGE[ $var_name ], $after, array( $key => $value ) );
		}
	}
}

// Add array element before the key
if ( ! function_exists( 'chinchilla_storage_set_array_before' ) ) {
	function chinchilla_storage_set_array_before( $var_name, $before, $key, $value = '' ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( is_array( $key ) ) {
			chinchilla_array_insert_before( $CHINCHILLA_STORAGE[ $var_name ], $before, $key );
		} else {
			chinchilla_array_insert_before( $CHINCHILLA_STORAGE[ $var_name ], $before, array( $key => $value ) );
		}
	}
}

// Push element into array
if ( ! function_exists( 'chinchilla_storage_push_array' ) ) {
	function chinchilla_storage_push_array( $var_name, $key, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( '' === $key ) {
			array_push( $CHINCHILLA_STORAGE[ $var_name ], $value );
		} else {
			if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) ) {
				$CHINCHILLA_STORAGE[ $var_name ][ $key ] = array();
			}
			array_push( $CHINCHILLA_STORAGE[ $var_name ][ $key ], $value );
		}
	}
}

// Pop element from array
if ( ! function_exists( 'chinchilla_storage_pop_array' ) ) {
	function chinchilla_storage_pop_array( $var_name, $key = '', $defa = '' ) {
		global $CHINCHILLA_STORAGE;
		$rez = $defa;
		if ( '' === $key ) {
			if ( isset( $CHINCHILLA_STORAGE[ $var_name ] ) && is_array( $CHINCHILLA_STORAGE[ $var_name ] ) && count( $CHINCHILLA_STORAGE[ $var_name ] ) > 0 ) {
				$rez = array_pop( $CHINCHILLA_STORAGE[ $var_name ] );
			}
		} else {
			if ( isset( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) && is_array( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) && count( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) > 0 ) {
				$rez = array_pop( $CHINCHILLA_STORAGE[ $var_name ][ $key ] );
			}
		}
		return $rez;
	}
}

// Inc/Dec array element with specified value
if ( ! function_exists( 'chinchilla_storage_inc_array' ) ) {
	function chinchilla_storage_inc_array( $var_name, $key, $value = 1 ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( empty( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ] = 0;
		}
		$CHINCHILLA_STORAGE[ $var_name ][ $key ] += $value;
	}
}

// Concatenate array element with specified value
if ( ! function_exists( 'chinchilla_storage_concat_array' ) ) {
	function chinchilla_storage_concat_array( $var_name, $key, $value ) {
		global $CHINCHILLA_STORAGE;
		if ( ! isset( $CHINCHILLA_STORAGE[ $var_name ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ] = array();
		}
		if ( empty( $CHINCHILLA_STORAGE[ $var_name ][ $key ] ) ) {
			$CHINCHILLA_STORAGE[ $var_name ][ $key ] = '';
		}
		$CHINCHILLA_STORAGE[ $var_name ][ $key ] .= $value;
	}
}

// Call object's method
if ( ! function_exists( 'chinchilla_storage_call_obj_method' ) ) {
	function chinchilla_storage_call_obj_method( $var_name, $method, $param = null ) {
		global $CHINCHILLA_STORAGE;
		if ( null === $param ) {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $CHINCHILLA_STORAGE[ $var_name ] ) ? $CHINCHILLA_STORAGE[ $var_name ]->$method() : '';
		} else {
			return ! empty( $var_name ) && ! empty( $method ) && isset( $CHINCHILLA_STORAGE[ $var_name ] ) ? $CHINCHILLA_STORAGE[ $var_name ]->$method( $param ) : '';
		}
	}
}

// Get object's property
if ( ! function_exists( 'chinchilla_storage_get_obj_property' ) ) {
	function chinchilla_storage_get_obj_property( $var_name, $prop, $default = '' ) {
		global $CHINCHILLA_STORAGE;
		return ! empty( $var_name ) && ! empty( $prop ) && isset( $CHINCHILLA_STORAGE[ $var_name ]->$prop ) ? $CHINCHILLA_STORAGE[ $var_name ]->$prop : $default;
	}
}
