<?php

/**
 * iG_Utility - Static class containing utility functions
 *
 * @author Amit Gupta http://igeek.info/
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU GPL v2
 *
 * @since 2013-03-21
 *
 * @version 2013-06-03 Amit Gupta
 */

if( ! class_exists( 'iG_Utility' ) ) {
	class iG_Utility {

		/**
		 * This function returns a comma seperated sentence with the last element is joined by the connector
		 *
		 * @param array $items The array whose elements are to be converted into a sentence
		 * @param string $words_connector The string which is used to join (N-1) elements of the array if N is greater than 2
		 * @param string $two_words_connector The string which is used to join (N-1)th and Nth elements of the array if N equals 2
		 * @param string $last_word_connector The string which is used to join (N-1)th and Nth elements of the array if N is greater than 2
		 *
		 * @return string This function returns a string with all the elements of the array joined using appropriate word connectors.
		 *
		 * @since 2013-03-21 Amit Gupta
		 * @version 2013-06-01 Amit Gupta
		 */
		public static function to_sentence( $items, $words_connector = ', ', $two_words_connector = ' and ', $last_word_connector = ' and ' ) {
			if( empty( $items ) || ! is_array( $items ) ) {
				return $items;
			}

			$words_connector = ( empty( $words_connector ) ) ? ', ' : $words_connector;
			$two_words_connector = ( empty( $two_words_connector ) ) ? ' and ' : $two_words_connector;
			$last_word_connector = ( empty( $last_word_connector ) ) ? ' and ' : $last_word_connector;

			$last_item = array_pop( $items );

			switch( count( $items ) ) {
				case 0:
					return $last_item;
					break;
				case 1:
					return array_pop( $items ) . $two_words_connector . $last_item;
					break;
				default:
					return implode( $words_connector, $items ) . $last_word_connector . $last_item;
					break;
			}
		}

	//end of class
	}
}

//EOF
