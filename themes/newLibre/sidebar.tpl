<!-- Sidebar -->

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

<div class="clear"></div>
