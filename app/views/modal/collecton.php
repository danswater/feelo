<div id="overlay" ng-show="display">

  <div class="settings settingsModal">
    <section>
      <h2>Add to existing Collection</h2>
        <select ng-model="selected_collection" ng-options="collection.favcircle_id as collection.title for collection in collections"></select>
      <center><input type="button" ng-click="addProject()" value="Add to Collection" class="buttonOutlines active" /></center>

      <form method="post" ng-submit="addCollection(collectionForm)" name="collectionForm">
        <h2>Create new Collection</h2>
        <center><input type="text" placeholder="Name of Collection" required ng-model="collection.name" /></center>
        <h2>Upload Collection Cover Image</h2>
        <img ng-show="collection.file[0] != null" ngf-thumbnail="collection.file[0]" class="thumb">
        <center><input type="button" value="Choose File" class="buttonOutlines active" ngf-select ng-model="collection.file" name="file" accept="image/*" ngf-change="generateThumb(collection.file[0], $files)" required></center>
        
        <div class="radio">
          <h2>Collection Privacy</h2>
          <select>
            <option ng-model="collection.privacy" value="0">Public</option>
            <option ng-model="collection.privacy" value="1">Private</option>
          </select>
        </div>

        <center><input type="submit" value="Create Collection" class="buttonOutlines active" /></center>
      </form>
    </section>
  </div>

</div>
<div id="fade" ng-show="display" ng-click="close()"></div>