<!-- Sidebar -->

		<!-- BEGIN search.box -->
		<div class="search box firstbox">
			<form id="search_form">
				<!-- BEGIN search.popular -->
				<input type="hidden" id="popular" name="popular" value="{$params.popular}" />
				<!-- END search.popular -->
				<!-- BEGIN search.user_id -->
				<input type="hidden" id="user_id" name="user_id" value="{$params.user_id}" />
				<!-- END search.user_id -->
				<!-- BEGIN search.filter -->
				<input type="hidden" id="filter" name="filter" value="{$params.filter}" />
				<!-- END search.filter -->
				<input type="text" id="search_text" class="search-field" name="search" onFocus="if (this.value=='Rechercher ...') this.value='';" onblur="if (this.value=='') this.value='Rechercher ...';" value="Rechercher ..." />
				<input type="submit" class="search-submit" value="" />
			</form>
		</div>
		<!-- END search.box -->



	<!-- BEGIN sidebar.alert -->
	<div class="box">
		<h2>Info Flash</h2>
		<p>{$planet.msg_info}</p>
	</div>
	<!-- END sidebar.alert -->


	<!-- BEGIN postlist.state -->
	<div class="box">
		<div id="filter-status">
			<h2 id="filter-title">{_Etat de la page}</h2>
			<div id="filter-nb-items">{_Nombre d'articles :} <span id="filter-nb-items-content">
				<a href="#" onclick="javascript:set_nb_items(10)">10</a>,
				<a href="#" onclick="javascript:set_nb_items(15)">15</a>,
				<a href="#" onclick="javascript:set_nb_items(20)">20</a>
				</span></div>
			<div id="filter-page" style="display:none">{_Page :}
				<span id="filter-page-content"></span></div>
			<div id="filter-search" style="display:none">{_Recherche avec :}
				<span id="filter-search-content"></span></div>
			<div id="filter-period" style="display:none">{_Periode des articles }
				<span id="filter-period-content"></span></div>
			<div id="filter-popular" style="display:none">{_Articles populaires}</div>
			<div id="filter-tags" style="display:none">{_Filtre de tags :}
				<span id="filter-tags-content"></span></div>
			<div id="filter-users" style="display:none">{_Filtre d'auteurs :}
				<span id="filter-users-content"></span></div>
			<div id="filter-feed" style="display:none">
				<a id="filter-feed" href="feed.php?type=atom">{_Flux avec ces paramètres}</a>
				</div>
		</div>
	</div>
	<!-- END postlist.state -->


	<!-- BEGIN sidebar.widget -->
	<div class="box sidebar-widget" id="widget{$sidebar-widget.id}">
		<h2 class="sidebar-widget" id="widget{$sidebar-widget.id}">{$sidebar-widget.title}</h2>
		{$sidebar-widget.html}
	</div>
	<!-- END sidebar.widget -->


	<!-- BEGIN memberlist.box -->
	<div class="box membersBox">
		<h2>Membres</h2>
		<ul>
			<!-- BEGIN sidebar.users.list -->
			<li><a href="#" onclick="javascript:add_user('{$user.id}')" title="Afficher les articles de {$user.fullname}">
			<img src="{$planet.url}/themes/{$planet.theme}/images/user-feed.png" alt="feed" /></a>&nbsp;&nbsp;
			<a href="{$user.site_url}" title="Site web de {$user.fullname}" target="_blank">{$user.fullname}</a></li>
			<!-- END sidebar.users.list -->
		</ul>
	</div>
	<!-- END memberlist.box -->


<div class="clear"></div>
