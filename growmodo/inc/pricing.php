<?php
/**
 * Estimated cost breakdown for a listing.
 *
 * The design's pricing tables are not invented figures — every one of them
 * derives from the listing price at a published rate, which is why the section
 * can exist honestly at all. Checked against the Figma's own worked example on a
 * $1,250,000 listing: transfer tax $25,000, monthly property tax $1,250,
 * additional fees $29,700, down payment $250,000, mortgage $1,000,000. The rates
 * below reproduce those numbers exactly.
 *
 * They are still estimates, and the section says so out loud rather than
 * presenting them as quotes.
 *
 * @since 1.0.0
 *
 * @package Growmodo
 */

defined( 'ABSPATH' ) || exit;

/**
 * The rates every figure on the page is derived from.
 *
 * Filterable because they are jurisdiction-specific: a site outside the
 * template's assumptions changes them here rather than editing templates.
 *
 * @since 1.0.0
 *
 * @return array<string,float|int>
 */
function growmodo_pricing_rates() {
	/**
	 * Filters the rates behind the estimated cost breakdown.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string,float|int> $rates {
	 *     @type float $transfer_tax   Transfer tax as a fraction of the sale price.
	 *     @type int   $legal_fees     Flat conveyancing cost.
	 *     @type int   $inspection     Flat survey cost.
	 *     @type int   $insurance_year Annual buildings insurance.
	 *     @type float $property_tax   Annual property tax as a fraction of the price.
	 *     @type int   $hoa_month      Monthly association fee.
	 *     @type float $down_payment   Deposit as a fraction of the price.
	 * }
	 */
	return apply_filters(
		'growmodo_pricing_rates',
		array(
			'transfer_tax'   => 0.02,
			'legal_fees'     => 3000,
			'inspection'     => 500,
			'insurance_year' => 1200,
			'property_tax'   => 0.012,
			'hoa_month'      => 300,
			'down_payment'   => 0.20,
		)
	);
}

/**
 * Build the four cost groups shown on a property page.
 *
 * Each row is `array( label, value, note )`, where the value is already
 * formatted — some of them are words ("Varies") rather than numbers, so
 * formatting cannot be left to the template.
 *
 * @since 1.0.0
 *
 * @param int $price Listing price in whole dollars.
 * @return array[] Groups, each with a `title` and a list of `rows`. Empty when
 *                 there is no price to derive anything from.
 */
function growmodo_pricing_groups( $price ) {
	$price = absint( $price );

	if ( $price < 1 ) {
		return array();
	}

	$rates = growmodo_pricing_rates();

	$transfer  = (int) round( $price * $rates['transfer_tax'] );
	$tax_month = (int) round( $price * $rates['property_tax'] / 12 );
	$insurance = (int) $rates['insurance_year'];
	$deposit   = (int) round( $price * $rates['down_payment'] );
	$mortgage  = $price - $deposit;

	// The design excludes the "varies" mortgage fee from this total, so this does.
	$additional = $transfer + (int) $rates['legal_fees'] + (int) $rates['inspection'] + $insurance;

	$varies = __( 'Varies', 'growmodo' );

	return array(
		array(
			'title' => __( 'Additional Fees', 'growmodo' ),
			'rows'  => array(
				array( __( 'Property Transfer Tax', 'growmodo' ), growmodo_format_price( $transfer ), __( 'Based on the sale price and local regulations', 'growmodo' ) ),
				array( __( 'Legal Fees', 'growmodo' ), growmodo_format_price( $rates['legal_fees'] ), __( 'Approximate cost for legal services, including title transfer', 'growmodo' ) ),
				array( __( 'Home Inspection', 'growmodo' ), growmodo_format_price( $rates['inspection'] ), __( 'Recommended for due diligence', 'growmodo' ) ),
				array( __( 'Property Insurance', 'growmodo' ), growmodo_format_price( $insurance ), __( 'Annual cost for comprehensive property insurance', 'growmodo' ) ),
				array( __( 'Mortgage Fees', 'growmodo' ), $varies, __( 'If applicable, consult with your lender for specific details', 'growmodo' ) ),
			),
		),
		array(
			'title' => __( 'Monthly Costs', 'growmodo' ),
			'rows'  => array(
				array( __( 'Property Taxes', 'growmodo' ), growmodo_format_price( $tax_month ), __( 'Approximate monthly property tax based on the sale price and local rates', 'growmodo' ) ),
				array( __( 'Homeowners\' Association Fee', 'growmodo' ), growmodo_format_price( $rates['hoa_month'] ), __( 'Monthly fee for common area maintenance and security', 'growmodo' ) ),
			),
		),
		array(
			'title' => __( 'Total Initial Costs', 'growmodo' ),
			'rows'  => array(
				array( __( 'Listing Price', 'growmodo' ), growmodo_format_price( $price ), '' ),
				array( __( 'Additional Fees', 'growmodo' ), growmodo_format_price( $additional ), __( 'Property transfer tax, legal fees, inspection, insurance', 'growmodo' ) ),
				array(
					__( 'Down Payment', 'growmodo' ),
					growmodo_format_price( $deposit ),
					sprintf(
						/* translators: %s: deposit as a percentage, e.g. "20%". */
						__( '%s of the listing price', 'growmodo' ),
						/* translators: %s: a whole-number percentage. */
						sprintf( __( '%s%%', 'growmodo' ), number_format_i18n( $rates['down_payment'] * 100 ) )
					),
				),
				array( __( 'Mortgage Amount', 'growmodo' ), growmodo_format_price( $mortgage ), __( 'If applicable', 'growmodo' ) ),
			),
		),
		array(
			'title' => __( 'Monthly Expenses', 'growmodo' ),
			'rows'  => array(
				array( __( 'Property Taxes', 'growmodo' ), growmodo_format_price( $tax_month ), '' ),
				array( __( 'Homeowners\' Association Fee', 'growmodo' ), growmodo_format_price( $rates['hoa_month'] ), '' ),
				array( __( 'Mortgage Payment', 'growmodo' ), __( 'Varies based on terms and interest rate', 'growmodo' ), __( 'If applicable', 'growmodo' ) ),
				array( __( 'Property Insurance', 'growmodo' ), growmodo_format_price( (int) round( $insurance / 12 ) ), __( 'Approximate monthly cost', 'growmodo' ) ),
			),
		),
	);
}
