<div id="newPostContainer" style="display:none" ng-controller="NewPostController">
	<div id="overlay" style="left: 27%; padding: 15px; width: 44%; text-align: center;">
		
		<!-- FIRST STEP -->
		<div class="uploadButtons" id="uploadMediaStep1" style="display:none">
			<button class="btn btnYamba icon-image noPhone" ng-click="showUploadForm( 'upload' )"><span>Image</span></button>
			<button class="btn btnYamba icon-video noPhone" ng-click="showUploadForm( 'upload' )"><span>Video</span></button>
			<button class="btn btnYamba icon-url" ng-click="showUploadForm( 'link' )"><span>URL/Media</span></button>
		</div>

		<!-- Second STEP -->
		<div class="uploadMediaCanvas" id="uploadMediaStep2" style="display:none">
			<div class="uploadMedia">
				<img ng-src="{{ current_user.storage_path | ybImagePath }}" class="userImage circle">
				<h3>Linus Ekenstam</h3>
				<div class="dropZone" ngf-drop ngf-select ng-model="files" class="drop-box" 
			        ngf-drag-over-class="dragover" ngf-allow-dir="true" ng-show="upload_type=='upload'"
			        accept="image/*, video/*">
					<span>Drop image or video here or <a href="">upload</a></span>
				</div>
				<div class="dropZone" ng-show="upload_type=='link'">
					<input type="text" name="link" placeholder="Paste or write the url in here 'http://' " />
				</div>
				<ul>
			        <li ng-repeat="f in files" style="font:smaller">{{f.name}}</li>
			    </ul>
				<a href="javascript:void(0)" ng-click="closeModal()" class="cancel">Cancel</a>
			</div>
			<div class="uploadButtonCanvas">
				<input type="button" class="button right" value="Next" ng-click="nextNewPostForm()">
			</div>
		</div>

		<!-- Third STEP -->
		<div class="uploadMediaCanvas" id="uploadMediaStep3" style="display:none;">
			<div class="uploadMedia">
				<div ng-if="newPostError != ''">{{ newPostError }}</div>
				<div class="uploadMeta">
					<img ng-show="files[0] != null" ngf-thumbnail="files[0]" class="mediaThumbnail">
					<input type="text" ng-model="post.title" class="uploadMetaTitle" placeholder="Post Title"/>
					<textarea ng-model="post.description" class="uploadMetaDescription" placeholder="Post Description"></textarea>
					<input type="text" ng-model="post.hashtags" ng-blur="hashtagCheker()" class="uploadMetaHashtags" placeholder="#hashtag1 #hashtag2 #hashtag2"/>

					<select ng-model="post.privacy" 
						ng-options="opts.name as opts.label for opts in [  {name:'public', label:'Public'},  {name:'private', label:'Private'} ] "> 
					</select>

				</div>
				<a href="javascript:void(0)" ng-click="closeModal()" class="cancel">Cancel</a>
			</div>
			<div class="uploadButtonCanvas">
				<input type="button" class="button right" value="Publish" ng-click="newPostSubmit()">
			</div>
		</div>

	</div>
	<div id="fade"  ng-click="closeModal()"></div>
</div>
