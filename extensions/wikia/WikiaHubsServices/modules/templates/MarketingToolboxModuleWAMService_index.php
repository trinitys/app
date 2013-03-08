<div>
	<span class="social-links">
		
	</span>
	<div class="search">
		<form method="get" action="index.php?title=Special:Search" class="WikiaSearch" id="WikiaSearch">
			<input type="text" value="" accesskey="f" autocomplete="off" placeholder="<?= wfMessage('wikiahubs-search-placeholder')->text(); ?>" name="search" id="HubSearch" />
			<input type="hidden" value="0" name="fulltext" />
			<input type="submit" />
			<button class="wikia-button">
				<img width="21" height="17" class="sprite search" src="data:image/gif;base64,R0lGODlhAQABAIABAAAAAP///yH5BAEAAAEALAAAAAABAAEAQAICTAEAOw%3D%3D" />
			</button>
		</form>
	</div>
</div>
<div class="wam-content">
	<h2>
		<?= wfMessage('wikiahubs-wam-header')->text(); ?>
	</h2>
	<a href="<?= $wamPageUrl; ?>"><?= wfMessage('wikiahubs-wam-see-full-wam-ranking')->text(); ?></a>
	<h3>
		<?= wfMessage('wikiahubs-wam-top-wikis-headline', $verticalName)->text(); ?>
	</h3>
	<table>
		<thead>
			<th><?= wfMessage('wikiahubs-wam-rank')->text() ?></th>
			<th><?= wfMessage('wikiahubs-wam-score')->text() ?></th>
			<th></th>
			<th><?= wfMessage('wikiahubs-wam-wiki-url')->text() ?></th>
		</thead>
		<?php if( !empty($ranking) ): ?>
			<tbody>
				<?php foreach($ranking as $wiki): ?>
					<td><?= $wiki['rank']; ?></td>
					<td><?= $wiki['wamScore']; ?></td>
					<td><img src="<?= $wiki['imageUrl']; ?>" width="<?= $wiki['imageWidth']; ?>" height="<?= $wiki['imageHeight']; ?>" /></td>
					<td><a href="<?= $wiki['wikiUrl']; ?>" target="_blank"><?= $wiki['wikiName']; ?></a></td>
				<?php endforeach; ?>
			</tbody>
		<?php endif; ?>
	</table>
</div>