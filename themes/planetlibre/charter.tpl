<div id="content" class="pages">

	<!-- BEGIN subscription.flash -->
	<div class="post box">
		<div id="flashmsg">
			{$flashmsg}
		</div>
	</div>
	<!-- END subscription.flash -->
	<div class="post box">
		<div>{$subscription_content}</div>
	</div>
	<div class="post box">
		<h1 class="post-title">{_Test your RSS feed}</h1>
		<p>
            {_Before submitting your feed, we advise you to test your RSS / Atom feed to be sure that they will correctly be processed by our planet. You can tests your feed on the website of Simplepie or on the Feedvalidator site of W3C :}
            <ul>
			<li><a href="http://simplepie.org/demo/" target="_blank" title="test" rel="nofollow">{_Simplepie}</a></li>
            <li><a href='http://feedvalidator.org/check.cgi' target='_blank'>FeedValidator</a></li>
            </ul>
		</p>
	</div>

	<div class="post box">
		<h1 class="post-title">{_Contact us}</h1>
        {_If you need to contact the administrator of this planet (problem with a feed, help, ...) you are kindly invited to do this on our contact page :}
        <a href='contact.php'>{_contact us}</a>.</p>
	</div>
</div>
