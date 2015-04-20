<div id="content" class="pages">
	<div class="row">


		<div class="post box col-md-12">
			<h1>Statistiques principales</h1>
			<ul>
				<li><p>Nombre de membres : {$nb.nb_users}</p></li>
				<li><p>Nombre de flux : {$nb.nb_feeds}</p></li>
				<li><p>Nombre d'article : {$nb.nb_posts}</p></li>
				<!-- BEGIN stats.votes.resume -->
				<li><p>Nombre de votes: {$nb_votes}</p></li>
				<!-- END stats.votes.resume -->
			</ul>
		</div>
	</div>
	<div class="row">
	<div class="post box col-md-12">
		<h1>Liste des membres les plus actifs :</h1>
		<table class="stats table">
			<thead>
				<tr class="bg">
					<th class="name">Nom</th>
					<th class="website">Site web</th>
					<th class="website">Nombre d'articles</th>
				</tr>
			</thead>
			<tbody>
			<!-- BEGIN stats.main.line -->
			<tr>
				<td class="tname">{$active.fullname}</td>
				<td class="twebsite"><a href="{$active.site_url}" title="{_Visit the website}">{$active.domain_url}</a></td>
				<td class="tposts">{$active.nb_posts}</td>
			</tr>
			<!-- END stats.main.line -->
		</tbody>
		</table>
	</div>
</div>
<div class="row">
	<!-- BEGIN stats.votes -->
	<div class="post box col-md-12">
		<h1>Liste des membres les mieux classés</h1>
		<table class="stats table">
		<thead>
			<tr class="bg">
				<th class="name">Nom</th>
				<th class="website">Site Web</th>
				<th class="website">Total de votes</th>
			</tr>
		</thead>
		<tbody>
			<!-- BEGIN stats.votes.line -->
			<tr>
				<td class="tname">{$votes.fullname}</td>
				<td class="twebsite"><a href="{$votes.site_url}" title="{_Visit the website}">{$votes.domain_url}</a></td>
				<td class="tposts">{$votes.score}</td>
			</tr>
			<!-- END stats.votes.line -->
		</tbody>
		</table>
	</div>
	<!-- END stats.votes -->
</div>
</div>
