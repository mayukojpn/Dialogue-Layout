<?php
/*
  Plugin Name: Dialogue Layout
  description: An easy way to make conversation layout with avatars in post contents.
  Author: Mayo Moriyama
  Version: 0.1
  Author URI: http://blog.mayuko.me
  Text Domain: dialogue-layout
*/

add_action( 'plugins_loaded', function () { load_plugin_textdomain( 'dialogue-layout', false, dirname( plugin_basename( __FILE__ ) ).'/language' ); } );


function dialogue_layout_style( $mce_css ){
  if ( !empty( $mce_css ) )
  $mce_css .= ',';
  $mce_css .= plugins_url( 'dialogue-layout.css', __FILE__ );
  return $mce_css;
}

add_action( 'init', function() {

  add_filter( 'mce_css', 'dialogue_layout_style' );

  add_shortcode( 'dialogue', function( $atts, $content = null ) {

    $a = shortcode_atts( array(
        'img'    => '',
        'img_id' => '',
        'alt'    => __( 'Speaker:', 'dialogue-layout' ),
    ), $atts );

    if ( !empty( $a['img_id'] ) )
    {
      $img = wp_get_attachment_url( $a['img_id'] );
    }

    if ( empty( $img ) && !empty( $a['img'] ) )
    {
      $img = $a['img'];
    }

    if ( empty( $img ) )
    {
      $img = get_avatar_data( '', array('size'=>'90') )['url'];
    }

    $image = sprintf(
      '<span class="speaker" style="background-image:url(%1$s)"><span class="screen-reader-text">%2$s</span></span>',
      $img,
      $a['alt']
    );

    $output = sprintf(
      '<p class="dialogue-layout">%1$s<span class="speech">%2$s</span></p>',
      $image,
      $content
    );

    return $output;
  } );

  if ( function_exists( 'shortcode_ui_register_for_shortcode' ) ) :
      shortcode_ui_register_for_shortcode(
        'dialogue',
        array(
          'label' => __( 'Dialogue', 'dialogue-layout' ),
          'listItemImage' => 'dashicons-format-chat',
          'attrs' => array(
            array(
              'label' => __( 'Avatar image URL' , 'dialogue-layout' ).' '.__( '*This will be overritten by avatar image setting.' , 'dialogue-layout' ),
              'attr'  => 'img',
              'type'  => 'url',
            ),
            array(
              'label' => __( 'Avatar image' , 'dialogue-layout' ),
              'attr'  => 'img_id',
              'type'  => 'attachment',
            ),
            array(
              'label' => __( 'Speaker Name' , 'dialogue-layout' ),
              'attr'  => 'alt',
              'type'  => 'text',
              'meta'   => array(
                'placeholder' => esc_html__( 'Speaker:', 'dialogue-layout' ),
              ),
            ),
          ),
          'inner_content' => array(
            'label' => esc_html__( 'Content', 'dialogue-layout' ),
          ),
        )
      );
  endif;

} );

add_action( 'wp_enqueue_scripts', function() {
  wp_enqueue_style(
		'dialogue-layout-style',
		plugins_url( 'dialogue-layout.css', __FILE__ )
	);
  $css = "
  .dialogue-layout {
    position: relative;
    min-height: 90px;
    display: table; }
    .dialogue-layout .speaker {
      position: absolute;
      top: 0;
      bottom: 0;
      left: 0;
      width: 90px;
      height: 90px;
      background-position: center center;
      background-size: cover;
      border-radius: 100%; }
    .dialogue-layout .speech {
      display: table-cell;
      margin: 0;
      padding-left: 110px;
      vertical-align: middle; }
    @media screen and (max-width: 640px) {
      .dialogue-layout {
        min-height: 70px; }
        .dialogue-layout .speaker {
          width: 70px;
          height: 70px; }
        .dialogue-layout .speech {
          padding-left: 80px; } }";
  wp_add_inline_style( 'dialogue-layout-style', $css );
});
