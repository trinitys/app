<div id="WikiaTrayHeader" class="WikiaTrayHeader">
	<div class="tray-header-tab user-tab">
		<img src="/extensions/wikia/hacks/Tray/images/christian.jpg" class="avatar">
		<span class="reddot">2</span>
	</div>
	<div class="divider"></div>
	<div class="tray-header-tab search-tab">
		<input type="search" placeholder="Search">
		<img src="http://images.wikia.com/common/skins/common/images/ajax.gif" class="loading">
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
		<h1>BladeBronson <a href="#">Log out</a></h1>
		<ul class="graphics">
			<li><a href="#"><img src="/extensions/wikia/hacks/Tray/images/preferences.png"><br>Preferences</a></li>
			<li><a href="#"><img src="/extensions/wikia/hacks/Tray/images/help.png"><br>Help</a></li>
		</ul>
		<ul>
			<li><a href="#">User page</a></li>
			<li><a href="#">Message Wall</a></li>
			<li><a href="#">Contributions</a></li>
		</ul>
		<h1>Message Wall <span class="reddot-wrapper"><span class="reddot">2</span></span></h1>
		<h2>Muppet Wiki</h2>
		<ul class="messages">
			<li>
				<img src="/extensions/wikia/hacks/Tray/images/toughpigs.jpg" class="avatar">
				<span class="what"><a href="#">Can we chat about Category pages?</a></span>
				<span class="who">Posted by <a href="#">Toughpigs</a> on your <a href="#">Message Wall</a></span>
				<span class="when">22 minutes ago</span>
			</li>
		</ul>
		<h2>Scrubs Wiki</h2>
		<ul class="messages">
			<li>
				<img src="/extensions/wikia/hacks/Tray/images/ohmyn0.jpg" class="avatar">
				<span class="what"><a href="#">Bug reports?</a></span>
				<span class="who">Posted by <a href="#">Ohmyn0</a> on <a href="#">Forums</a></span>
				<span class="when">31 minutes ago</span>
			</li>
		</ul>
		<h1>Contribute</h1>
		<div class="tint">
			<div class="columns-2 first">
				<h2>New Content</h2>
				<ul>
					<li><a href="#">Add a page</a></li>
					<li><a href="#">Add a photo</a></li>
					<li><a href="#">Add a video</a></li>
				</ul>
			</div>
			<div class="columns-2 last">
				<h2>This Article</h2>
				<ul>
					<li><a href="#">Edit this page</a></li>
					<li><a href="#">What links here</a></li>
					<li><a href="#">Follow</a></li>
				</ul>
			</div>
		</div>
		<h1>Admin</h1>
		<ul>
			<li><a href="#">Admin Dashboard</a></li>
			<li><a href="#">Theme Designer</a></li>
			<li><a href="#">Edit wiki navigation</a></li>
		</ul>

		<h1>Random Links</h1>
		<ul>
			<li><a href="#">Recent Changes</a></li>
			<li><a href="#">Special New Files</a></li>
			<li><a href="#">Special User Rights</a></li>
		</ul>

	</section>
</aside>
