<!-- [post social buttons] -->


<!-- BEGIN social.twitter -->
<a href="https://twitter.com/share" class="twitter-share-button" data-url="{$planet.url}/?post_id={$post.id}" data-lang="{$planet.lang}" data-hashtags="{$planet.title}">{_Tweeter}</a>
<!-- END social.twitter -->

<!-- BEGIN social.shaarli -->
<a class="social-shaarli-button" href="javascript:shaare({$post.id})"><img src="{$planet.url}/themes/{$planet.theme}/images/shaarli.png"></a>
<!-- END social.shaarli -->

<!-- BEGIN social.google -->
<!-- Place this tag where you want the share button to render. -->
<div class="g-plus" data-action="share" data-annotation="bubble" data-height="15" data-href="{$planet.url}/?post_id={$post.id}"></div>
<!-- END social.google -->

