<?php
/*
Plugin Name: Simple Related Posts in Page
Plugin URI: http://www.checksite.jp/develop/simple-related-posts-in-page/
Description: This plugin will show related post links to page if page title and post tag is equal. Use shortcode [show_posts_link].
Version: 1.0
Author: checksite.jp
Author URI: http://www.checksite.jp/
License: GPL2
*/

/* Copyright 2012 Check!Site.jp
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2,
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

function simple_related_posts_in_page() {
	if (function_exists('checksite_srpip_showposts')){
		$showposts = checksite_srpip_showposts();
		if (!is_numeric($showposts)){
			$showposts = 10;
		} elseif (is_null($showposts)){
			$showposts = 10;
		}
	} else {
		$showposts = 10;
	}

	if (function_exists('checksite_srpip_displayword')){
		$displayword = checksite_srpip_displayword();
		if (is_null($displayword)){
			$displayword = "Related Posts";
		} elseif (strlen($displayword) == 0){
			$displayword = "Related Posts";
		}
	} else {
		$displayword = "Related Posts";
	}

	$args = array(
		'post_type' => 'post',
		'tag' => get_the_title( $post ),
		'post_status' => 'publish',
		'orderby' => 'post_date',
		'showposts' => $showposts,
	);
	query_posts( $args );
	if(have_posts()){
		$myOutput = "<BR>";
		$myOutput .= $displayword;
		$myOutput .= '<ul>';
		while( have_posts() ){
			the_post();
			$myOutput .= '<li><a href="'. get_permalink( $post ) . '">'. get_the_title( $post ) .'</a></li>';
		}
		$myOutput .= '</ul>';
	}
	wp_reset_query();
	$args = array();
	return $myOutput;
}

add_shortcode( 'show_posts_link' , 'simple_related_posts_in_page' );

function add_simple_related_posts_in_page(){
	add_options_page('Simple Related Posts in Page','Simple Related Posts in Page','manage_options','checksite-related-posts-in-page.php','admin_related_posts_in_page');
}

add_action('admin_menu','add_simple_related_posts_in_page');

function admin_related_posts_in_page(){
	?>
	<div class="wrap">
	<?php screen_icon(); ?>
	<h2>Simple Related Posts in Page</h2>

	<?php
		// isset - 変数がセットされていること、そして NULL でないことを検査する
		if (isset($_POST['checksite_srpip_showposts'])){
			check_admin_referer('checksite_srpip_action','checksite_srpip_nonce');

			$checksite_srpip_showposts = stripslashes($_POST['checksite_srpip_showposts']);

			if (is_numeric($checksite_srpip_showposts)){
				if($checksite_srpip_showposts == 0){
					$showposts_str = '<p style="color: #F00">Please set number exclude 0.</p>';
				}else{
					update_option('checksite_srpip_showposts', $checksite_srpip_showposts);
					$showposts_str = '<p>Updated showposts number.</p>';
				}
			} else {
				$showposts_str = '<p style="color: #F00">Please set number.</p>';
			}
		} else {
			$checksite_srpip_showposts = get_option('checksite_srpip_showposts');
			$showposts_str = '<p>Please set showposts number.</p>';
		}
	?>
	<form action="" method="post">
		<table><tr><td width="200">
		<?php echo $showposts_str; ?>
		</td><td>
		<?php wp_nonce_field('checksite_srpip_action','checksite_srpip_nonce'); ?>
		<input type="TEXT" name="checksite_srpip_showposts" value="<?php echo esc_attr($checksite_srpip_showposts); ?>" />
		</td></tr></table>
		<?php submit_button(); ?>
	</form>
	<BR><BR>

	<?php
		// isset - 変数がセットされていること、そして NULL でないことを検査する
		if (isset($_POST['checksite_srpip_displayword'])){
			check_admin_referer('checksite_srpip_action','checksite_srpip_nonce');

			$checksite_srpip_displayword = stripslashes($_POST['checksite_srpip_displayword']);

			update_option('checksite_srpip_displayword', $checksite_srpip_displayword);
			$displayword_str = '<p>Updated Display Word.</p>';
		} else {
			$checksite_srpip_displayword = get_option('checksite_srpip_displayword');
			$displayword_str = '<p>Please set Display Word.<BR>Default Word is [Related Posts].</p>';
		}
	?>
	<form action="" method="post">
		<table><tr><td width="200">
		<?php echo $displayword_str; ?>
		</td><td>
		<?php wp_nonce_field('checksite_srpip_action','checksite_srpip_nonce'); ?>
		<input type="TEXT" name="checksite_srpip_displayword" value="<?php echo esc_attr($checksite_srpip_displayword); ?>" />
		</td></tr></table>
		<?php submit_button(); ?>
	</form>

	</div>
	<?php
}

function checksite_srpip_showposts(){
	// esc_attr 小なり、大なり、アンパサンド、ダブルクォート、シングルクォートをエンコード
	// optionsデータベーステーブルから、指定したオプションの値を取得
	$showposts = esc_attr(get_option('checksite_srpip_showposts'));
	return $showposts;
}
function checksite_srpip_displayword(){
	// esc_attr 小なり、大なり、アンパサンド、ダブルクォート、シングルクォートをエンコード
	// optionsデータベーステーブルから、指定したオプションの値を取得
	$displayword = esc_attr(get_option('checksite_srpip_displayword'));
	return $displayword;
}
?>
