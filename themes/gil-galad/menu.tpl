		<ul id="menu">
			<li class="firstLi first active"><a class="a_header" href="{$planet.url}">{_Home}</a></li>
			<!-- BEGIN menu.votes -->
			<li><a class="a_header" href="{$planet.url}/?popular=true">{_Top 10}</a></li>
			<!-- END menu.votes -->
			<li><a class="a_header" href="{$planet.url}/stats.php">{_Statistics}</a></li>
			<!-- BEGIN menu.subscription -->
			<li><a class="a_header" href="{$planet.url}/inscription.php">{_Registration}</a></li>
			<!-- END menu.subscription -->
			<li><a class="a_header" href="{$planet.url}/archives.php">{_Archives}</a></li>
			<!-- BEGIN menu.contact -->
			<li class="preLastLi"><a class="a_header" href="{$planet.url}/contact.php">{_Contact}</a></li>
			<!-- END menu.contact -->
			<li class="lastLi">

				<!-- BEGIN search.box -->
					<form id="search_form" action="">
						<fieldset>
						<!-- BEGIN search.popular -->
						<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
						<!-- END search.popular -->
						<!-- BEGIN search.user_id -->
						<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
						<!-- END search.user_id -->
						<!-- BEGIN search.filter -->
						<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
						<!-- END search.filter -->
						<input type="text" id="search_text" name="search" value="{$search_value}" />
						<input type="submit" id="recherche_global_btn" value="" />
						</fieldset>
					</form>
				<!-- END search.box -->
			</li>
		</ul>

