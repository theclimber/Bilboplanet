<div id="main_stats">
	<h2>{_Main statistics}</h2>
	<ul>
		<li>{_Number of members :} {$nb.nb_users}</li>
		<li>{_Number of feeds :} {$nb.nb_feeds}</li>
		<li>{_Number of posts :} {$nb.nb_posts}</li>
		<li>{_Number of votes :} {$nb.nb_votes}</li>
	</ul>

	<h2>{_List of the most active members}</h2>
	<table>
		<tr class='table_th'>
			<th>{_Name}</th>
			<th>{_Website}</th>
			<th>{_Qtity of posts}</th>
		</tr>

		<!-- BEGIN stats.main.line -->
		<tr>
			<td>{$active.fullname}</td>
			<td><a href="{$active.site_url}" title="{_Visit the website}">{$active.domain_url}</a></td>
			<td>{$active.nb_posts}</td>
		</tr>
		<!-- END stats.main.line -->
	</table>
</div>

<!-- BEGIN stats.votes -->
<div id="vote_stats">
	<h2>{_List of the best ranked members}</h2>

	<table>
		<tr class='table_th'>
			<th>{_Name}</th>
			<th>{_Website}</th>
			<th>{_Total of votes}</th>
		</tr>

		<!-- BEGIN stats.votes.line -->
		<tr>
			<td>{$votes.fullname}</td>
			<td><a href="{$votes.site_url}" title="{_Visit the website}">{$votes.domain_url}</a></td>
			<td>{$votes.score}</td>
		</tr>
		<!-- END stats.votes.line -->

	</table>
</div>
<!-- END stats.votes -->
