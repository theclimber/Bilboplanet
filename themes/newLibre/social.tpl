<!-- [post social buttons] -->

<!-- BEGIN social.statusnet -->
<iframe height="61" width="61" scrolling="no" frameborder="0" src="{$planet.url}/api/identishare.php?post_id={$post.id}&title={$post.title}&noscript" border="0" marginheight="0" marginwidth="0" allowtransparency="true" class="identishare">
<div id="identishare" style="vertical-align: bottom;"></div>
<script type="text/javascript" src="{$planet.url}/api/identishare.php?post_id={$post.id}" defer="defer"></script>
</iframe>
<!-- END social.statusnet -->

<!-- BEGIN social.google -->
<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share" data-annotation="bubble" data-height="15" data-href="{$planet.url}/?post_id={$post.id}"></div>
<!-- END social.google -->

<!-- BEGIN social.twitter -->
<a href="https://twitter.com/share" class="twitter-share-button" data-url="{$planet.url}/?post_id={$post.id}" data-lang="{$planet.lang}" data-hashtags="{$planet.title}">{_Tweeter}</a>
<!-- END social.twitter -->

<!-- BEGIN social.shaarli -->
<a class="social-shaarli-button" href="javascript:shaare({$post.id})">{_Shaare link}</a>
<!-- END social.shaarli -->
