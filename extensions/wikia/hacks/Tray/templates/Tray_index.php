<div id="WikiaTrayHeader" class="WikiaTrayHeader">
	<div class="tray-header-tab user-tab">
		<?= $profileAvatar ?>
	</div>
	<div class="divider"></div>
	<div class="tray-header-tab search-tab">
		<input type="search" placeholder="Search">
	</div>
</div>


<aside id="WikiaTray" class="WikiaTray">
	<section class="search">
		<h1>Muppet Wiki Articles</h1>
		<ul class="wiki-matches">
		</ul>
		<h1>Articles on all the Wikia</h1>
		<ul>
			<li><a href="#">Article 1</a></li>
			<li><a href="#">Article 2</a></li>
			<li><a href="#">Article 3</a></li>
			<li><a href="#">Article 4</a></li>
			<li><a href="#">Article 5</a></li>
		</ul>

		<!-- Templates -->
		<script type="text/template" id="WikiaTray-wiki-match"><li><a href="{{href}}">{{name}}</a></li></script>
	</section>
	<section class="user">
		<h1>BladeBronson</h1>
		<ul>
			<li><a href="#">User page</a></li>
			<li><a href="#">Message Wall</a></li>
			<li><a href="#">Contributions</a></li>
			<li><a href="#">Preferences</a></li>
			<li><a href="#">Help</a></li>
			<li><a href="#">Log out</a></li>
		</ul>
		<h1>Message Wall</h1>
		<p class="empty">You have no new messages</p>
		<h1>Contribute</h1>
		<ul>
			<li><a href="#">Add a page</a></li>
			<li><a href="#">Add a photo</a></li>
			<li><a href="#">Add a video</a></li>
		</ul>
		<ul>
			<li><a href="#">Edit this page</a></li>
			<li><a href="#">What links here</a></li>
			<li><a href="#">Follow</a></li>
		</ul>
		<h1>Admin</h1>
		<ul>
			<li><a href="#">Admin Dashboard</a></li>
			<li><a href="#">Theme Designer</a></li>
			<li><a href="#">Edit wiki navigation</a></li>
		</ul>

	</section>
</aside>
