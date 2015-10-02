<div ng-if="projects.length > 0">
	<h4>Related Posts</h4>
	<ul>
		<li ng-repeat="project in projects">
			<a href="#!/main/post/{{ project.project_id }}">
				<div class="realatedItemImage" style="background-image: url('{{ project.Media.storage_path | ybImagePath }}')"></div>
				<h5> {{ project.title }} </h5>
				<small>#{{ project.Hashtag.text}} </small>
			</a>
		</li>
	</ul>	
</div>