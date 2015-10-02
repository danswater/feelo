<div id="overlay" ng-show="display" style="background:#fff">
  	
  	<label> {{ user.displayname }} </label><br/> 
   <img ng-src="{{ user.storage_path | ybImagePath }}"/>
   <table width="100%">
   		<tr ng-repeat="box in boxes">
   			<td ng-repeat="b in box"> 
   				<input type="checkbox" ng-click="addBox( b.circle_id )" /> {{ b.title }}
   			</td>
   		</tr>
   </table>

</div>
<div id="fade" ng-show="display" ng-click="close()"></div>