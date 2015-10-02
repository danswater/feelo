<style type="text/css">
#overlay {
    position: fixed;
    left: 25%;
    top: 25%;
    padding: 25px; 
    border: 2px solid black;
    background-color: #ffffff;
    width: 50%;
    height: 50%;
    z-index: 100;
}
#fade {
    position: fixed;
    left: 0%;
    top: 0%;
    background-color: black;
    -moz-opacity: 0.7;
    opacity: .70;
    filter: alpha(opacity=70);
    width: 100%;
    height: 100%;
    z-index: 90;
}
</style>
<div id="overlay" ng-show="display">
    This is a custom modal. The angular-modal-service doesn't depend on bootstrap, you
    can use any modal you want.
    <a href ng-click="close()">Close</a>
</div>
<div id="fade" ng-show="display" ng-click="close()"></div>