// rounded corners
var GroupCircle = new Class({
    
	Implements: [Options],
    
	// settings
	options: {
		userElement: 'div.item-holder div.item',
		circleElement: 'div.b-circle',
		dragParentPriorityClass: 'drag-parent-active',
		dragMoveClass: 'drag-move',
		dragHoverClass: 'drag-hover',
		dropMinWidth: 100,
		dropMinHeight: 100,
		dropMaxWidth: 200,
		dropMaxHeight: 200,
		circlesSlideDuration: 300,
		dropHoverClass: 'drop-hover',
		circleHoverClass: 'circle-hover',
		dropFullClass: 'droppable-full',
		dropHLClass: 'droppable-highlight',
		hintShowClass: 'show-hint',
		dropMouseoverOffset: '-100px 0 0 -100px',
		dropMouseoutOffset: '-50px 0 0 -50px',
		circleHint: 'div.circle-hint',
		membersTitle: 'span.members',
		friendsUrl: en4.core.baseUrl + 'boxes/get-friends',
		friendsDBError: 'Connection Error',
		userTemplate: '<div class="item-holder">\
			<div class="item" friendid="{id}" user_id="{user_id}">\
				<img src="{userpic}" alt="" />\
				{name}\
			</div>\
			<div class="user-title">\
				<img src="{userpic}" alt="" />\
				{name}\
			</div>\
		</div>',
		circleTemplate: '<div class="circle-holder">\
			<div class="b-circle {fullClass}" circleid="{id}">\
				<img src="application/modules/Whmedia/externals/images/circle.png" class="img-empty" alt="" />\
				<img src="application/modules/Whmedia/externals/images/circle-hl.png" class="img-hl" alt="" />\
				<img src="application/modules/Whmedia/externals/images/circle-hover.png" class="img-hover" alt="" />\
				<img src="application/modules/Whmedia/externals/images/circle-full.png" class="img-full" alt="" />\
				{users}\
			</div>\
			<div class="circle-hint">\
				<strong>{name}:</strong> <br /> Members - <span class="members">{members}</span>\
				<p><a href="javascript:void(0);" onclick="window.location=\''+(en4.core.baseUrl=='/'?'/':en4.core.baseUrl)+'box/view/{list_id}\'" >View</a> | <a href="javascript:void(0);" onclick="javascript:Smoothbox.open(\''+(en4.core.baseUrl=='/'?'/':en4.core.baseUrl)+'box/edit/{list_id}\')">Edit</a> | <a href="javascript:void(0);" onclick="Smoothbox.open(\''+(en4.core.baseUrl=='/'?'/':en4.core.baseUrl)+'box/delete/{list_id}\')" >Delete</a></p>\
			</div>\
		</div>',
		circleMemberTemplate: '<div class="circle-user circle-user-{id}" userid="{userid}" user_id="{user_id}">\
			<a href="javascript:void(0);" title="{name}"  style="background-image:url({userpic});"></a>\
		</div>',
		circleMemberTemplateIE: '<div class="circle-user circle-user-{id}" userid="{userid}" user_id="{user_id}">\
			<a href="javascript:void(0);" title="{name}">\
				<span>\
					<v:oval style="width:100%;height:100%;position:relative;left:0;top:0;zoom:1;" strokecolor="#b4d5f8" strokeweight="0" class="vml">\
						<v:fill type="frame" src="{userpic}" class="vml" />\
					</v:oval>\
				</span>\
			</a>\
		</div>',
		maxUsers: 25,
		loading: 'loading'
    },
	
	initialize: function(usersId, circlesId, options) {
		var network = this;
		network.setOptions(options);
		network.db = {};
		
		var usersHolder = $(usersId);
		var circlesHolder = $(circlesId);
		network.loading = $(network.options.loading);
		
		if(usersHolder && circlesHolder) {
			
			// detect Internet Explorer
			function _detectIE() {
				var _rv = -1;
				if (navigator.appName == 'Microsoft Internet Explorer') {
					var _ua = navigator.userAgent;
					var _re  = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
					if (_re.exec(_ua) != null) {
						_rv = parseFloat(RegExp.$1);
					}
				}
				if(_rv>-1 && _rv<9) {
					return true;
				} else {
					return false;
				}
			}
			network.ie = _detectIE();
			
			// create vml stylesheet / namespace for MSIE
			if(network.ie) {
				if(typeof document.createStyleSheet()!='undefined') {
					gplusStyleSheet = document.createStyleSheet();
					gplusStyleSheet.cssText = ".vml {display:inline-block;behavior:url(#default#VML);position:absolute;left:0;top:0;}";
				}
				try {
					if (!document.namespaces["v"]) {
						document.namespaces.add("v", "urn:schemas-microsoft-com:vml");
					}
				} catch(e) {}
			}
			
			// load network table
			var getNetworkData = new Request.JSON({
				method: 'get',
                data: {
                    'page'        : ($('page').value?$('page').value:'1'),
                    'displayname' : ($('displayname').value?$('displayname').value:'')
                },
				noCache: true,
				url: network.options.friendsUrl,
				onSuccess: function(responseJSON) {
					network.loading.setStyle('display', 'none');
					$('add_circles').setStyle('display', 'block');
					if(responseJSON.error==1) {
						alert(network.options.friendsDBError);
					} else {
						network.db = responseJSON;
						if($('circles_pagination') && network.db.pagination_control){
                                                    $('circles_pagination').set('html', network.db.pagination_control);
                                                }

						// supplant template string
						function _supplantTemplate(objrepl, objtxt) {
							return objrepl.replace(/{([^{}]*)}/g,
							function(a, b) {
								var r = objtxt[b];
								return typeof r === 'string' || typeof r === 'number' ? r : a;
							});
						}
						
						// change circle members after adding a friend
						function _resetCircleTitle(circleItem, membersArray) {
							var circleFriends = 0;
							for(_i=0,_n=membersArray.length; _i<_n; _i++) {
								if(membersArray[_i]==1) {
									circleFriends++;
								}
							}
							circleItem.getParent().getElement(network.options.membersTitle).set('text', circleFriends);
						}
						
						// drag/drop friend icons in circle
						function _usersDroppable() {
							network.circles.each(function(_item, _index) {
								var itemUsers = _item.getElements('div.circle-user');
								itemUsers.each(function(_itemUser, _indexUser) {
									var itemTitleLink = _itemUser.getElements('a')[0];
									if(_itemUser.getElements('.title').length==0) {
										var itemTitle = itemTitleLink.get('title');
										itemTitleLink.set('title', '');
										_itemUser.innerHTML += '<div class="title">' + itemTitle + '</div>';
									}
								});
								itemUsers.makeDraggable({
									droppables: _item,
									onStart: function(draggable, droppable) {
										draggable.getParents(network.options.circleElement)[0].addClass('parent-hover');
										draggable.setStyles({
											margin: 0,
											left: 'auto',
											top: 'auto',
											zIndex: 3
										});
									},
									onDrop: function(draggable, droppable) {
										draggable.getParents(network.options.circleElement)[0].removeClass('parent-hover');
										draggable.setStyles({
											left: '',
											top: '',
											margin: '',
											zIndex: 2
										});
										if(!droppable) {
											// remove friend from circle
											network.loading.setStyle('display', 'block');
                                                                                        $('add_circles').setStyle('display', 'none');
											var _circle = draggable.getParents(network.options.circleElement)[0];
											var _circleId = _circle.get('circleid');
											var _friendId = draggable.get('userid');
											var _user_id = draggable.get('user_id');
											var activeCircle = false;
											$each(network.db.circles, function(circleItem, index) {
												if(index==_circleId) {
													activeCircle = circleItem;
												}
											});
											var removeFriendRequest = new Request.JSON({
												url: network.options.friendsUrl,
												noCache: true,
												onSuccess: function(responseJSON){
													if(responseJSON.error==1) {
														alert(network.options.friendsDBError);
													} else {
														//network.db = responseJSON; // renew circles database from server
														network.loading.setStyle('display', 'none');
                                                                                                                $('add_circles').setStyle('display', 'block');
														draggable.destroy();
														if(_circle.getElements('div.circle-user').length==0) {
															_circle.removeClass(network.options.dropFullClass);
														}
														activeCircle.members[_friendId.replace('user', '')] = 0;
														_resetCircleTitle(_circle, activeCircle.members);
														
														// reset small icons after delete one
														
														for(_i=0; _i<network.options.maxUsers; _i++) {
															if(_circle.getElements('div.circle-user-' + _i).length) {
																_circle.getElements('div.circle-user-' + _i).removeClass('circle-user-' + _i);
															}
														}
														_circle.getElements('div.circle-user-hidden').removeClass('circle-user-hidden');
														_circle.getElements('div.circle-user').each(function(userEl, userIn) {
															if(userIn<network.options.maxUsers) {
																userEl.addClass('circle-user-' + userIn);
															} else {
																userEl.addClass('circle-user-hidden');
															}
														});
													}
												}
											}).send({
												method: 'post',
												data: 'action=remove&user_id='+_user_id+'&friend=' + _friendId.replace('user', '') + '&circle=' + _circleId.replace('group', '')
											});
										}
									}
								});
								if(network.ie) {
									_item.getElements('a').each(function(itemIELink, indexIELink) {
										
									});
								}
							});
						}
						
						// create friends
						$each(network.db.friends, function(friendItem, index) {
							var _userHTML = {
								id: index,
								user_id: friendItem.user_id,
								name: friendItem.name,
								userpic: friendItem.userpic
							};
							usersHolder.innerHTML += _supplantTemplate(network.options.userTemplate, _userHTML);
						});
						network.users = usersHolder.getElements(network.options.userElement);
						
						// create circles
						$each(network.db.circles, function(circleItem, circleIndex) {
							var circleItemMembers = 0;
							var _membersHTML = '';
							var _circleMemberHTML = {
								id: false,
								name: false,
								userid: false,
								userpic: false,
								profile: false
							};
							
							// add members
							circleItem.members.each(function(membersArr, memberIndex) {
								if(membersArr==1) {
									_circleMemberHTML.id = circleItemMembers;
									circleItemMembers++;
									$each(network.db.allfriends, function(friendItem, friendIndex) {
										if(friendIndex.replace('user', '')==memberIndex) {
											_circleMemberHTML.userpic = friendItem.userpic;
											_circleMemberHTML.profile = friendItem.profile;
											_circleMemberHTML.userid = 'user' + friendItem.index;
											_circleMemberHTML.user_id = friendItem.user_id;
											_circleMemberHTML.name = friendItem.name;
										}
									});
									if(_circleMemberHTML.userpic && circleItemMembers<=network.options.maxUsers) {
										if(network.ie) {
											_membersHTML += _supplantTemplate(network.options.circleMemberTemplateIE, _circleMemberHTML);
										} else {
											_membersHTML += _supplantTemplate(network.options.circleMemberTemplate, _circleMemberHTML);
										}
									} else {
										_circleMemberHTML.id = 'hidden';
										if(network.ie) {
											_membersHTML += _supplantTemplate(network.options.circleMemberTemplateIE, _circleMemberHTML);
										} else {
											_membersHTML += _supplantTemplate(network.options.circleMemberTemplate, _circleMemberHTML);
										}
									}
								}
							});
							var _circleHTML = {
								id: circleIndex,
								name: circleItem.name,
								members: circleItemMembers,
								fullClass: '',
								list_id: circleIndex.replace('group', ''),
								users: _membersHTML
							};
							// add 'full' class if circle has members
							if(circleItemMembers>0) {
								_circleHTML.fullClass = network.options.dropFullClass;
							}
							circlesHolder.innerHTML += _supplantTemplate(network.options.circleTemplate, _circleHTML);
						});
						network.circles = circlesHolder.getElements(network.options.circleElement);
						_usersDroppable();
						
						// circle hover
						network.circles.addEvents({
							'mouseover': function() {
								this.setStyles({
									width: network.options.dropMaxWidth,
									height: network.options.dropMaxHeight,
									margin: network.options.dropMouseoverOffset
								});
								this.addClass(network.options.circleHoverClass);
								this.getParent().addClass(network.options.hintShowClass);
							},
							'mouseout': function() {
								this.setStyles({
									width: network.options.dropMinWidth,
									height: network.options.dropMinHeight,
									margin: network.options.dropMouseoutOffset
								});
								this.removeClass(network.options.circleHoverClass);
								this.getParent().removeClass(network.options.hintShowClass);
							}
						});
						
						// drag/drop friends
						network.users.makeDraggable({
							
							droppables: network.circles,
							onDrag: function(draggable) {
								draggable.addClass(network.options.dragMoveClass);
								draggable.getParent().addClass(network.options.dragParentPriorityClass);
								$$('.' + network.options.dropHLClass).removeClass(network.options.dropHLClass);
							},
							
							onEnter: function(draggable, droppable){
								draggable.addClass(network.options.dragHoverClass);
								droppable.addClass(network.options.dropHoverClass);
								droppable.morph({
									width: network.options.dropMaxWidth,
									height: network.options.dropMaxHeight,
									margin: network.options.dropMouseoverOffset
								});
							},
							
							onLeave: function(draggable, droppable){
								draggable.removeClass(network.options.dragHoverClass);
								droppable.removeClass(network.options.dropHoverClass);
								droppable.morph({
									width: network.options.dropMinWidth,
									height: network.options.dropMinHeight,
									margin: network.options.dropMouseoutOffset
								});
								var circleLeaveFx = new Fx.Morph(droppable, {
									duration: network.options.circlesSlideDuration
								});
								circleLeaveFx.start({
									width: network.options.dropMinWidth,
									height: network.options.dropMinHeight,
									margin: network.options.dropMouseoutOffset
								});
							},
							
							onDrop: function(draggable, droppable) {
								draggable.removeClass(network.options.dragHoverClass);
								draggable.removeClass(network.options.dragMoveClass);
								draggable.getParent().removeClass(network.options.dragParentPriorityClass);
								
								if(droppable) {
									droppable.removeClass(network.options.dropHoverClass);
									var _droppableId = droppable.get('circleid');
									var _friendId = draggable.get('friendid');
									var _user_id = draggable.get('user_id');
									var activeCircle = false;
									$each(network.db.circles, function(circleItem, index) {
										if(index==_droppableId) {
											activeCircle = circleItem;
										}
									});
									
									// add friend to circle
									network.loading.setStyle('display', 'block');
                                    $('add_circles').setStyle('display', 'none');
									if(activeCircle.members[_friendId.replace('user', '')]==0) {
										var addFriendRequest = new Request.JSON({
											url: network.options.friendsUrl,
											noCache: true,
											onSuccess: function(responseJSON){
												network.loading.setStyle('display', 'none');
                                                $('add_circles').setStyle('display', 'block');
												if(responseJSON.error==1) {
													draggable.setStyles({
														left: 0,
														top: 0
													});
												} else {
													//network.db = responseJSON; // renew circles database from server
													
													droppable.addClass(network.options.dropFullClass);
													activeCircle.members[_friendId.replace('user', '')] = 1;
													draggable.setStyles({
														left: 0,
														top: 0
													});
													_resetCircleTitle(droppable, activeCircle.members);
													
													// add small user icon to circle
													var _circleMemberUserpic = false;
													var _circleMemberUrl = false;
													var _circleMemberTitle = false;
													$each(network.db.allfriends, function(circleItem, circleIndex) {
														if(circleIndex==_friendId) {
															_circleMemberUserpic = circleItem.userpic;
															_circleMemberUrl = circleItem.profile;
															_circleMemberTitle = circleItem.name;
                                                            _circleMemberId = circleItem.user_id;
                                                        }
                                                    });
													var _circleMemberHTML = {
														id: droppable.getElements('.circle-user').length,
														userid: _friendId,
														userpic: _circleMemberUserpic,
														profile: _circleMemberUrl,
														user_id: _circleMemberId,
														name: _circleMemberTitle
													};
													if(_circleMemberHTML.userpic && droppable.getElements('.circle-user').length<network.options.maxUsers) {
														if(network.ie) {
															droppable.innerHTML += _supplantTemplate(network.options.circleMemberTemplateIE, _circleMemberHTML);
														} else {
															droppable.innerHTML += _supplantTemplate(network.options.circleMemberTemplate, _circleMemberHTML);
														}
														_usersDroppable();
													} else {
														_circleMemberHTML.id = 'hidden';
														if(network.ie) {
															droppable.innerHTML += _supplantTemplate(network.options.circleMemberTemplateIE, _circleMemberHTML);
														} else {
															droppable.innerHTML += _supplantTemplate(network.options.circleMemberTemplate, _circleMemberHTML);
														}
														_usersDroppable();
													}
												}
											}
										}).send({
											method: 'post',
											data: 'action=add&user_id='+_user_id+'&friend=' + _friendId.replace('user', '') + '&circle=' + _droppableId.replace('group', '')
										});
									} else {
										network.loading.setStyle('display', 'none');
                                        $('add_circles').setStyle('display', 'block');
										draggable.setStyles({
											left: 0,
											top: 0
										});
									}
								} else {
									draggable.addClass(network.options.dragMoveClass);
									draggable.getParent().addClass(network.options.dragParentPriorityClass);
									var draggableMove = new Fx.Morph(draggable, {
										link: 'chain'
									});
									draggableMove
										.start({
											left: 0,
											top: 0
										})
										.chain(function() {
											draggable.removeClass(network.options.dragMoveClass);
											draggable.getParent().removeClass(network.options.dragParentPriorityClass);
										});
								}
							}
						});
						
						// friend hover
						network.users.addEvents({
							'mouseover': function() {
								var _hoverId = this.get('friendid').replace('user', '');
								$each(network.db.circles, function(circleItem, index) {
									if(circleItem.members[_hoverId]>0) {
										network.circles[circleItem.index].addClass(network.options.dropHLClass);
									}
								});
							},
							'mouseout': function() {
								var _hoverId = this.get('friendid').replace('user', '');
								$each(network.db.circles, function(circleItem, index) {
									if(circleItem.members[_hoverId]>0) {
										network.circles[circleItem.index].removeClass(network.options.dropHLClass);
									}
								});
							}
						});
					}
				}
			});
			getNetworkData.send();
		}
	},
    searchMembers: function(){
        var network = this;
        network.loading.setStyle('display', 'block');
        $('users').empty();
        $('circles').empty();
        $('circles_pagination').empty();
        this.initialize('users', 'circles')
        return true;
    }
});