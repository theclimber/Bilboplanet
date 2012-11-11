<!-- [post social buttons] -->
<ul>
<!-- BEGIN social.statusnet -->
<li><iframe height="32" width="32" scrolling="no" frameborder="0" src="{$planet.url}/api/identishare.php?post_id={$post.id}&title={$post.title}&height=32&nocount" border="0" marginheight="0" marginwidth="0" allowtransparency="true" class="identishare">
<div id="identishare" style="vertical-align: bottom;"></div>
<script type="text/javascript" src="{$planet.url}/api/identishare.php?post_id={$post.id}" defer="defer"></script>
</iframe></li>
<!-- END social.statusnet -->

<!-- BEGIN social.shaarli -->
<li><a class="social-shaarli-button" href="javascript:shaare({$post.id})"><img width=32 src="{$planet.url}/themes/{$planet.theme}/user/images/social.png"></a></li>
<!-- END social.shaarli -->

<!-- BEGIN social.twitter -->
<li><a href="https://twitter.com/share" class="twitter-share-button" data-url="{$planet.url}/?post_id={$post.id}" data-lang="{$planet.lang}" data-hashtags="{$planet.title}">{_Tweeter}</a></li>
<!-- END social.twitter -->

<!-- BEGIN social.google -->
<!-- Place this tag where you want the share button to render. -->
<li><div class="g-plus" data-action="share" data-annotation="bubble" data-height="20" data-href="{$planet.url}/?post_id={$post.id}"></div></li>
<!-- END social.google -->
</ul>
