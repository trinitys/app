<div id="WikiaTrayHeader" class="WikiaTrayHeader">
	<div class="tray-header-tab user-tab">
		<img src="/extensions/wikia/hacks/Tray/images/christian.jpg" class="avatar">
		<span class="reddot">2</span>
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
		<ul class="wikia-matches">
		</ul>

		<h1>Photos</h1>
		<div class="photo-carousel-wrapper">
			<div class="previous"></div>
			<div class="next"></div>
			<div class="photo-carousel">
				<div>
					<ul class="carousel">
					</ul>
				</div>
			</div>		
		</div>
		
		<!-- Templates -->
		<script type="text/template" id="WikiaTray-wiki-match"><li><a href="{{href}}">{{name}}</a></li></script>
		<script type="text/template" id="WikiaTray-wikia-match"><li><a href="{{href}}">{{name}}</a> <span class="wiki"><span>on</span> {{wiki}}</span></li></script>
		<script type="text/template" id="WikiaTray-photos"><li><img src="{{url}}"></li></script>
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
