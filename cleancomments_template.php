<div id="comments" class="comments-area">
<?php
$post_ID = get_the_ID();
?>
<div id="cltq_comments"> </div>
<script>
/* * * CONFIGURATION VARIABLES * * */
var cltq_site_key = '<?php $val_CleanCommentsID = stripslashes(get_option('CleanCommentsID')); echo $val_CleanCommentsID ?>';
// if not host name wil be taken from document

/* * * OPTIONAL PARAMETERS * * */

// if not page title is provided in script it will be taken from document
var cltq_page_title = '<?php echo get_the_title( $post_ID ); ?>'; //assigned by WP
// if not host name wil be taken from document
// if cltq_ignore_hash is false then separate comments will be generated for every hash, only in case if page id is not provided
//cltq_page_ignore_hash=false;
// page id will be generated if not provided
var cltq_page_id = '<?php echo $post_ID; ?>'; //assigned by WP


/* * * DON'T EDIT BELOW THIS LINE * * */

(function() {
var cltq = document.createElement('script'); cltq.type = 'text/javascript';
cltq.async = true;
cltq.src = '//cleancomments.com/iframe/embed.js';
(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(cltq);
})();
</script>
<noscript>Please enable JavaScript to view the <a href="http://www.cleancomments.com/" rel="nofollow">comments powered by CleanComments.</a></noscript>
</div><!-- #comments -->