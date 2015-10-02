<div id="leftMenuCanvas" class="leftMenuCanvasHidden" ng-controller="LeftMenuController" >
    <ul class="groupsSlectionMenu">
        <li><a href="javascript:void(0)" ui-sref="main.activity_feed"><strong>View main feed</strong></a></li>

        <li ng-repeat="box in boxes">
            <a ui-sref="main.activity_feed_box({ box_id: box.circle_id })">{{ box.title }}</a>
            <a ui-sref="main.group({ box_id: box.circle_id })" class="cog"> </a>
        </li>

        <li><a href="javascript:void(0)"><strong>+ Add new group</strong></a></li>
    </ul>
</div>