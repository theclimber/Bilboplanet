		<ul id="menu">
			<li class="firstLi first active"><a class="a_header" href="{$planet.url}">{_Home}</a></li>
			<li><a class="a_header" href="{$planet.url}/?popular=true">{_Top 10}</a></li>
			<li><a class="a_header" href="{$planet.url}/stats.php">{_Statistics}</a></li>
			<li><a class="a_header" href="{$planet.url}/inscription.php">{_Registration}</a></li>
			<li><a class="a_header" href="{$planet.url}/archives.php">{_Archives}</a></li>
			<li class="preLastLi"><a class="a_header" href="{$planet.url}/contact.php">{_Contact}</a></li>
			<li class="lastLi">
			
				<!-- BEGIN search.box -->
					<form id="recherche_global" action="index.php" method="get">
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
						<input type="text" id="recherche" name="search" value="{$search_value}" />
						<input type="submit" id="recherche_global_btn" value="" />
						<fieldset>
					</form>
				<!-- END search.box -->
			</li>
		</ul>
