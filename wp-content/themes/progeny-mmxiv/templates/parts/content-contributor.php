<?php
/**
 * Template part used to display the author box on post pages.
 *
 * @package Progeny_MMXIV
 * @since 1.0.0
 */

$facebook_url = get_the_author_meta( 'facebook', $contributor_id );
$twitter_username = get_the_author_meta( 'twitter', $contributor_id );
$website_url = get_the_author_meta( 'user_url', $contributor_id );
?>

<div class="contributor">
	<div class="contributor-info">
		<div class="contributor-avatar">
			<?php echo get_avatar( get_the_author_meta( 'user_email', $contributor_id ), 132 ); ?>
		</div>

		<div class="contributor-summary">
			<h2 class="contributor-name">
				<?php echo progeny_allowed_tags( get_the_author_meta( 'display_name', $contributor_id ) ); ?>
			</h2>

			<p class="contributor-bio">
				<?php echo progeny_allowed_tags( get_the_author_meta( 'description', $contributor_id ) ); ?>
			</p>

			<p class="contributor-links">
				<?php if ( ! empty( $post_count ) ) : ?>
					<a class="contributor-posts-link" href="<?php echo esc_url( get_author_posts_url( $contributor_id ) ); ?>">
						<?php echo esc_html( sprintf( _n( '%d Article', '%d Articles', $post_count, 'progeny-mmixv' ), $post_count ) ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $twitter_username ) ) : ?>
					<a class="contributor-twitter-link" href="<?php echo esc_url( 'http://twitter.com/' . $twitter_username ); ?>" target="_blank">
						<?php esc_html_e( 'Twitter', 'progeny-mmixv' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $facebook_url ) ) : ?>
					<a class="contributor-facebook-link" href="<?php echo esc_url( $facebook_url ); ?>" target="_blank">
						<?php esc_html_e( 'Facebook', 'progeny-mmixv' ); ?>
					</a>
				<?php endif; ?>

				<?php if ( ! empty( $website_url ) ) : ?>
					<a class="contributor-web-link" href="<?php echo esc_url( $website_url ); ?>" target="_blank">
						<?php esc_html_e( 'Website', 'progeny-mmixv' ); ?>
					</a>
				<?php endif; ?>
			</p>

		</div><!-- .contributor-summary -->
	</div><!-- .contributor-info -->
</div><!-- .contributor -->
