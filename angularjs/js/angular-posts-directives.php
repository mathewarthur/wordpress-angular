<?php
header("Content-type: text/javascript");
require_once('../../../../wp-config.php');
$howmany = get_option('posts_per_page');
foreach (new DirectoryIterator("../theme/partials") as $fileInfo) {
    if($fileInfo->isDot()) continue;
    $template_type = explode('-', $fileInfo->getFilename());
    $template_name = str_replace(".html","",ucfirst($template_type[0]).ucfirst($template_type[1]));
    $template_var = str_replace(".html","",$template_type[0]."_".$template_type[1]);
    switch ($template_type[0]) {
	    case "archive": ?>
	        angular_app.directive('ng<?=$template_name;?>', ['$http', '$rootScope', function($http, $rootScope){
				return {
					transclude: true,
					restrict: 'E',
					scope: {
						author: '=authorId',
						authorName: '@authorName',
						cat: '=catId',
						catName: '@catSlug',
						order: '@postOrder',
						orderBy: '@postOrderby',
						search: '@search',
						postType: '@postType',
						perPage: '@perPage',
						page: '@page',
						offset: '@offset',
						pageParent: '@pageParent'
					},
					controller: ['$scope', '$http', '$location', function($scope, $http, $location) {
						$scope.currentUrl = $location.absUrl();
						$scope.getPosts = function(filters, postType, page){
							$scope.baseURL = wpAngularVars.base + '/posts?';
							$scope.baseURLPages = wpAngularVars.base + '/pages?';
							if(postType){
								$scope.baseURL = wpAngularVars.base + '/' + postType + '?';
							}
							if(filters.length > 0){
								angular.forEach(filters, function(value, key){
									if( value.filter === 'posts_per_page' ){
										$scope.baseURL = $scope.baseURL + 'per_page=' + value.value + '&';	
									} else if( value.filter == 'page' ) {
										$scope.baseURL = $scope.baseURL + 'page=' + value.value + '&';	
									} else {
										$scope.baseURL = $scope.baseURL + 'filter['+ value.filter + ']=' + value.value + '&';	
									}
									if( value.filter === 'page_parent' ){
										$scope.baseURL = $scope.baseURL + 'filter[post_parent]=' + value.value + '&perpage=-1';	
									}
								})
							}
			                if(page){
			                    $scope.baseURL = $scope.baseURL + '&page=' + page;
			                }
							$http.get($scope.baseURL).then(function(res){
							 	$scope.postsD = res.data;
							});
						}
					}],
					link: function($scope, $elm, attrs, ctrl){
						// Filters Array
						$scope.filters = [];
						if($scope.author){ $scope.filters.push({'filter': 'author', 'value': $scope.author}); }
						if($scope.authorName){ $scope.filters.push({'filter': 'author_name', 'value': $scope.authorName}); }
						if($scope.cat){ $scope.filters.push({'filter': 'cat', 'value': $scope.cat}); }
						if($scope.catName){ $scope.filters.push({'filter': 'category_name', 'value': $scope.catName}); }
						if($scope.order){ $scope.filters.push({'filter': 'order', 'value': $scope.order}); }
						if($scope.orderBy) { $scope.filters.push({'filter': 'orderby', 'value': $scope.orderBy }); }
						if($scope.search) { $scope.filters.push({'filter': 's', 'value': $scope.search}); }
						if($scope.perPage) { $scope.filters.push({'filter': 'posts_per_page', 'value': $scope.perPage}); }
						if(!$scope.perPage) { $scope.filters.push({'filter': 'posts_per_page', 'value': <?=$howmany;?>}); }
						if($scope.page) { $scope.filters.push({'filter': 'page', 'value': $scope.page}); }	
						if($scope.offset) { $scope.filters.push({'filter': 'offset', 'value': $scope.offset}); }	
						if($scope.pageParent) { $scope.filters.push({'filter': 'page_parent', 'value': $scope.pageParent}); }	
						$scope.getPosts($scope.filters, $scope.postType);
					},
					template: '<div ng-if="post.name != \'Uncategorized\'" class="ngListWrapper" ng-repeat="post in postsD"><ng-include src="\''+wpAngularVars.template_directory.<?=$template_var;?>+'\'"></ng-include></div>'
				}
			}]);
	        <? break;
	    case "single": ?>
	        angular_app.directive('ng<?=$template_name;?>', ['$http', '$rootScope', function($http, $rootScope){
				return {
					transclude: true,
					restrict: 'E',
					scope: {
						id: '=id',
						postType: '@postType'
					},
					controller: ['$scope', '$http', function($scope, $http) {
			      		$scope.getPost = function(id, postType) {
			      		if(postType){
							$scope.baseURL = wpAngularVars.base + '/' + postType + '/';
						} else {
							$scope.baseURL = wpAngularVars.base + '/posts/';
						}
				    	$http.get($scope.baseURL + id).then(function(res){
							$scope.post = res.data;
						});
			      	}
			    }],
			    link: function($scope, $elm, attrs, ctrl){
			    	$scope.getPost($scope.id,$scope.postType);
			    },
					template: '<div class="ngSingleWrapper"><ng-include src="\''+wpAngularVars.template_directory.<?=$template_var;?>+'\'"></ng-include></div>'
				}
			}]);
	        <? break;
	}
}
?>

angular_app.directive('ngLoadMore', ['$http', '$rootScope', function($http, $rootScope){
	return {
		template: '<button id="infinite-scroll" type="button">Load More</button><script>jQuery("#infinite-scroll").click(function(){if (jQuery(this).hasClass("last-one")) {jQuery(this).remove();}var postcount = jQuery("article").length;var theloop = jQuery("#main").find(".loop").first().clone();theloop.attr("offset",postcount).addClass("ng-posts-added");jQuery("#main").append(theloop);var content = jQuery(".ng-posts-added");angular.element(document).injector().invoke(function($compile) {var scope = angular.element(content).scope();$compile(content)(scope);});jQuery(".ng-posts-added").removeClass("ng-posts-added");if (jQuery(".loop[offset=\'"+(postcount-<?=$howmany;?>)+"\']").length) {jQuery("#infinite-scroll").addClass("last-one");}});</script>'
	}
}]);

angular_app.directive('ngTemplateReady', ['$http', '$rootScope', '$timeout', function($http, $rootScope, $timeout){
    return {
        restrict:'A',
        link: function ($scope) {
            var waitForRenderAndDoSomething = function() {
			if($http.pendingRequests.length > 0) {
				$timeout(waitForRenderAndDoSomething);
			} else {
			  	$scope.templateReady = true;
			  	jQuery(window).trigger("resize");
			}
			}
			$timeout(waitForRenderAndDoSomething);
        }
    } 
}]);

angular_app.directive("ngStickyNav", function stickyNav($window){  
  function stickyNavLink(scope, element){
    var w = angular.element($window),
        size = element[0].clientHeight,
        top = 0;
    function toggleStickyNav(){
      if(!element.hasClass('controls-fixed') && $window.pageYOffset > top + size){
        var elheight = element.outerHeight();
        element.addClass('controls-fixed');
        jQuery('#header').css("margin-top", elheight+"px");
      } else if(element.hasClass('controls-fixed') && $window.pageYOffset <= top + size){
        element.removeClass('controls-fixed');
        jQuery('#header').css("margin-top", "0px");
      }
    }
    scope.$watch(function(){
      return element[0].getBoundingClientRect().top + $window.pageYOffset;
    }, function(newValue, oldValue){
      if(newValue !== oldValue && !element.hasClass('controls-fixed')){
        top = newValue;
      }
    });
    w.bind('resize', function stickyNavResize(){
      element.removeClass('controls-fixed');
      top = element[0].getBoundingClientRect().top + $window.pageYOffset;
      toggleStickyNav();
    });
    w.bind('scroll', toggleStickyNav);
  }
  return {
    scope: {},
    restrict: 'A',
    link: stickyNavLink
  };
});