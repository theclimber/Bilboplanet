	<article id="post{$post.id}">
		<header class="article">
			<img src="{$post.author_avatar}&size=32" />
			<h2>{$post.title}</h2>
			<description>{_Published on} {$post.date} - {$post.hour}</description>
		</header>
		<description>
			<content>
			{$post.content}
			</content>
		</description>
		<comments>
			<div id="rating1">
				<input type="radio" name="rating1" value="0.5" title="Very poor">
				<input type="radio" name="rating1" value="1" title="Very poor">
				<input type="radio" name="rating1" value="1.5" title="Not that bad">
				<input type="radio" name="rating1" value="2" title="Not that bad">
				<input type="radio" name="rating1" value="2.5" title="Average">
				<input type="radio" name="rating1" value="3" title="Average">
				<input type="radio" name="rating1" value="3.5" title="Good">
				<input type="radio" name="rating1" value="4" title="Good">
				<input type="radio" name="rating1" value="4.5" title="Perfect">
				<input type="radio" name="rating1" value="5" title="Perfect">
			</div>

			<a href="#">2 commentaires</a>

			<comment>
				<info>
					Posté le 24 mai 2011 par Toto à 14h35
				</info>
				<content>
					Preums !
				</content>
			</comment>

			<comment>
				<info>
					Posté le 27 juin 2011 par Tim à 14h35
				</info>
				<content>
					Youpie trallala<br>
					Voici un contenu un peu plus important
					Arrivé en haut vers 18h, nous ne sommes pas encore arrivés au bout de nos peines. Le rocher est pourri et impossible de trouver par où ça sort pour rejoindre le chemin de descente. Finalement Guillaume enchaîne encore 2 longueurs sur les arrêtes du sommet pour seulement parvenir à un semblant de chemin qui va se confirmer plus loin comme étant le bon chemin. Ce n’était donc pas 10 longueurs, mais bien 12 longueurs. Nous mettons donc les pieds sur le chemin vers 19h30 pour enfin commencer à entamer notre descente.
				</content>
			</comment>

			<div id="commentForm">
				<h1>Post a comment</h1>
				<form>
					<input type="text" name="name" placeholder="Nom" />
					<input type="email" name"email" placeholder="Email" />
					<input type="url" name="site" placeholder="http://" />
					<textarea name="comment">
					</textarea>
					<input type="submit" class="button" value="Send" />
				</form>
			</div>

		</comments>
	</article>

