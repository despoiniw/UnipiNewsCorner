<?php
/**
 * Custom template comment for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package doo
 */
 
function doo_list_comments($comment,$args,$depth){
	if ( $comment->comment_type=='pingback' || $comment->comment_type=='trackback' ){
?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class(); ?>>
		<div class="comment-body">
			<?php echo esc_html__( 'Pingback:', 'doo' ); ?> <?php comment_author_link(); ?>
			<?php edit_comment_link( esc_html__( '(Edit)', 'doo' ), '<div class="comment-meta"><span class="edit-link">', '</span></div>' ); ?>
		</div>
<?php
	}else{	
?>
	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<div id="div-comment-<?php comment_ID(); ?>" class="comment-body">
            <div class="comment-author">
				<div class="comment-avatar"><?php echo get_avatar( $comment, 40 ); ?></div>
                <div class="fn"><?php echo get_comment_author_link();?></div>
                
                <div class="comment-meta">            
                    <span class="comment-metadata"><time datetime="<?php comment_time( 'c' ); ?>"><?php printf( esc_html_x( '%1$s at %2$s', '1: date, 2: time', 'doo' ), get_comment_date(), get_comment_time() ); ?></time></span>
                    <?php edit_comment_link( esc_html__( '(Edit)', 'doo' ), '<span class="edit-link">', '</span>' ); ?>
    
                    <?php if ( '0' == $comment->comment_approved ) : ?>
                    <p class="comment-awaiting-moderation"><?php echo esc_html__( 'Your comment is awaiting moderation.', 'doo' ); ?></p>
                    <?php endif; ?>
                </div>
                <div class="clear"></div>
            </div>
            
            <div class="comment_wrap">     
            	<div class="comment-content"><?php comment_text();?></div>
                <div class="clear"></div>
            </div>
            
            <div class="reply"><?php comment_reply_link( array_merge( $args, array('add_below' => 'div-comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?></div>
            <div class="clear"></div>
		</div>
<?php	
	}
}

function doo_comment_form_default_fields( $fields ) {
	$req = get_option( 'require_name_email' );
	$commenter = wp_get_current_commenter();
	$aria_req = ( $req ? " aria-required='true'" : '' );
	$fields['author'] = '<div class="comment-item"><label for="author" class="screen-reader-text">Name</label><input id="author" type="text" aria-required="true" size="22" value="'.esc_attr($commenter['comment_author']).'" name="author" '.$aria_req.' placeholder="'.esc_attr__('Your Name','doo').' '.($req?'*':'').'" /></div>';
	$fields['email'] = '<div class="comment-item"><label for="email" class="screen-reader-text">Email</label><input id="email" type="text" aria-required="true" size="22" value="'.esc_attr($commenter['comment_author_email']).'" name="email" '.$aria_req.' placeholder="'.esc_attr__('Your Email','doo').' '.($req?'*':'').'" /></div>';
	$fields['url'] = '<div class="comment-item"><label for="url" class="screen-reader-text">Website</label><input id="url" type="text" aria-required="true" size="22" value="'.esc_url($commenter['comment_author_url']).'" name="url" placeholder="'.esc_attr__('Your Website','doo').'" /></div>';
	return $fields;
}
add_filter( 'comment_form_default_fields', 'doo_comment_form_default_fields' );

function doo_comment_form_field_comment( $comment_field ) {
	$req = get_option( 'require_name_email' );
	$comment_field = '<div class="comment-item"><label for="comment" class="screen-reader-text">Comment</label><textarea id="comment" name="comment" placeholder="'.esc_attr__('Your comment','doo').' '.($req?'*':'').'" /></textarea></div>';
	return $comment_field;
}
add_filter( 'comment_form_field_comment', 'doo_comment_form_field_comment' );

function doo_comment_form_defaults($defaults){
	$defaults['comment_notes_before'] = '';
	$defaults['comment_notes_after'] = '';
	return $defaults;
}
add_filter( 'comment_form_defaults', 'doo_comment_form_defaults' );