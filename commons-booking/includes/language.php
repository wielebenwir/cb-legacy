<?php

/**
 * Function wrapper for register,unregister,get language and get string for WPML, Polylang and Ceceppa Multilingua
 * 
 * example use https://gist.github.com/Mte90/fe687ceed408ab743238
 * 
 * @package   Commons_Booking
 * @author Mte90 <mte90net@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 
*/

/**
 * Return the language 2-4 letters code
 *
 * @since   0.0.1
 *
 * @return     string 4 letters cod of the locale
 */
function get_language() {
	if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
		return ICL_LANGUAGE_CODE;
	} elseif ( function_exists( 'cml_get_browser_lang' ) ) {
		return cml_get_browser_lang();
	} elseif ( function_exists( 'pll_current_language' ) ) {
		return pll_current_language();
	} else {
		//return a 2-4 letters code
		return get_locale();
	}
}

/**
 * Add registration for multilanguage string (contain hook)
 *
 * @since   0.0.1
 *
 * @param     string   $Commons_Booking_human_format  The Plugin name 
 * @param     string   $string_name               The name of the string
 * @param     string   $value					  The value
 */
function register_string( $Commons_Booking_human_format, $string_name, $value ) {
	if ( function_exists( 'icl_register_string' ) ) {
		icl_register_string( $Commons_Booking_human_format, $string_name, $value );
	} elseif ( has_filter( 'cml_my_translations' ) ) {
		add_filter( 'cml_my_translations', create_function( "$groups, $Commons_Booking_human_format","
            $Commons_Booking_human_format_replaced = str_replace( ' ', '-', $Commons_Booking_human_format );
            CMLTranslations:add( $string_name, $value, $Commons_Booking_human_format );
            $groups[$Commons_Booking_human_format_replaced] = $Commons_Booking_human_format;
            return $groups;"
        ) );
	} elseif ( function_exists( 'pll_register_string' ) ) {
		$Commons_Booking_human_format_replaced = str_replace( ' ', '-', $Commons_Booking_human_format );
		pll_register_string( $Commons_Booking_human_format_replaced, $string_name );
	}
}

/**
 * Unregister multilanguage string, Polylang missing support of this feature
 *
 * @since   0.0.1
 *
 * @param     string   $Commons_Booking_human_format  The Plugin name 
 * @param     string   $string_name               The name of the string
 */
function deregister_string( $Commons_Booking_human_format, $string_name ) {
	if ( function_exists( 'icl_unregister_string' ) ) {
		icl_unregister_string( $Commons_Booking_human_format, $string_name );
	} elseif ( has_filter( 'cml_my_translations' ) ) {
		$Commons_Booking_human_format_replaced = str_replace( ' ', '-', $Commons_Booking_human_format );
		CMLTranslations::delete( $Commons_Booking_human_format_replaced );
	}
}

/**
 * Get multilanguage string
 *
 * @since   0.0.1
 *
 * @param     string   $Commons_Booking_human_format  The Plugin name 
 * @param     string   $string_name               The name of the string
 * @param     string   $value					  The value
 */
function get_string( $Commons_Booking_human_format, $string_name, $value ) {
	if ( function_exists( 'icl_t' ) ) {
		return icl_t( $Commons_Booking_human_format, $string_name, $value );
	} elseif ( has_filter( 'cml_my_translations' ) ) {
		return CMLTranslations::get( CMLLanguage::get_current_id(), $string_name, str_replace( ' ', '-', $Commons_Booking_human_format ) );
	} elseif ( function_exists( 'pll__' ) ) {
		return pll__( $string_name );
	}
}
