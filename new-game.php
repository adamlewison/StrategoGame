<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>
    <title>Stratego: Strategy board game</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
    <link rel="stylesheet" href="stylesheets/style.css?v=<?= time(); ?>">
  </head>
  <body>
    
    <div class="container-fluid clearfix" ng-app="myApp" ng-controller="myCtrl">
    	<h1>Start new game</h1>
    	
    	<div class="newSession" ng-hide="isSessionSet">
    		<p>Create / join session</p>
    		<input ng-model="session" class="form-control"><br/>
    		<button ng-click="checkSession()" class="btn btn-default">Join / Create</button>
    	</div>
    	<div ng-show="isSessionSet">
		    <div class="board">
		        <table>
		          <tr ng-repeat="(y, rows) in availableSpaces">
		            <td ng-repeat="(x,space) in rows track by $index" class="block-{{x}}-{{y}} piece-{{myColor}} piece ">
		
		              <div align="center" ng-if="space.vacant != 'true'" ng-click="inUsePieceSelect(x,y)" class="myPiece myPieceSelection">
			          		<div class="">
				                <img src="gfx/pieces/{{space.piece}}.png" draggable="false">
				                <div class="pieceRank">{{space.piece}}</div>
			                </div>
		              </div>
		              
		               <div align="center" ng-if="space.vacant == 'true'" class="availableSpace" ng-click="selectAvailableSpace(x,y)">
			          		{{x+1}} : {{y+1}}
		              </div>
		
		            </td>
		          </tr>
		        </table>
		        
		    </div>
		    <div>&nbsp;</div>
		    <div class="availablePieces clearfix">
		    	<div align="center" ng-repeat="(x,piece) in availablePieces" ng-click="selectAvailablePiece(x)" class="availablePiece {{myColor}} pull-left availablePiece-{{x}}">
		    		<img src="gfx/pieces/{{piece.piece}}.png" draggable="false">
		    		<div class="pieceRank">{{piece.piece}}</div>
		    		<h3 class="quantity">(x{{piece.quantity}})</h3>
		    	</div>
		    
		    </div>
		    <div>&nbsp;</div>
		    <div>&nbsp;</div>
		    <div class="done"><button class="btn btn-success" ng-click="teamSubmit()">complete</button></div>
    	</div>


    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  </body>

<script type="text/javascript">
  var app = angular.module('myApp', []);
  app.controller('myCtrl', function($scope) {
  
	  $scope.availablePieces = [{piece: "F", quantity:1},{piece: "B", quantity:6},{piece: 10, quantity:1},{piece: 9, quantity:1},{piece: 8, quantity:2},{piece: 7, quantity:3},{piece: 6, quantity:4},{piece: 5, quantity:4},{piece: 4, quantity:4},{piece: 3, quantity:5},{piece: 2, quantity:8},{piece: 1, quantity:1}];
	  
	  $scope.availableSpaces = [[{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"}],
	  [{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"}],
	  [{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"}],
	  [{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"},{"vacant": "true"}]];
	  $scope.myColor;
	  $scope.selectedPiece;
	  $scope.selectedPieceIndex;
	  $scope.session;
	  $scope.isSessionSet = false;
	  
	  $scope.checkSession = function() {
		  $.post("services/checkSession.php", {session: $scope.session}, function(data) {
			  $scope.$apply(function(){
				  if (data == "1") {
					  $scope.isSessionSet = true;
					  $scope.myColor = "B";
				  } else {
					  $scope.isSessionSet = true;
					  $scope.myColor = "R";
				  }
			  })
			  
		  })
		  
	  }
	  
	  $scope.selectAvailablePiece = function(i) {
		  var block = ".availablePiece-" + i;
		  $(".availablePiece").removeClass("selectAvailablePiece");
		  $(".piece").find('.myPiece').removeClass("selectAvailablePiece");
		  $(block).addClass("selectAvailablePiece");
		  
		  $scope.selectedPiece = $scope.availablePieces[i];
		  $scope.selectedPieceIndex = i;
		  
	  }
	  
	  $scope.inUsePieceSelect = function(x,y) {
		  var block = ".block-" + x + "-" + y;
		  $(".piece").find('.myPiece').removeClass("selectAvailablePiece");
		  $(".availablePiece").removeClass("selectAvailablePiece");
		  $(block).find('.myPiece').addClass("selectAvailablePiece");
		  
		  $scope.selectedPiece = $scope.availableSpaces[y][x];
		  $scope.selectedPieceIndex = [x,y];
	  }
	  
	  $scope.selectAvailableSpace = function(x,y) {
		  if (typeof($scope.selectedPiece) == "object") {
		  
			  var k = $scope.selectedPieceIndex;
			  
			  if (typeof($scope.selectedPieceIndex) == "number") {
			  
				  $scope.availableSpaces[y][x] = {piece: $scope.selectedPiece.piece, color: $scope.myColor};
				  
				  //update quantities
				  var quantity = $scope.availablePieces[k].quantity;
				  if (quantity == 1) {
					  $scope.availablePieces.splice(k, 1);
				  } else {
					  $scope.availablePieces[k].quantity = parseInt(quantity) - 1;
				  }
				  
			  } else if (typeof($scope.selectedPieceIndex) == "object") {
				  $scope.availableSpaces[y][x] = {piece: $scope.selectedPiece.piece, color: $scope.myColor};
				  $scope.availableSpaces[k[1]][k[0]] = {"vacant":"true"};
			  }
			  
			  
			  $scope.selectedPiece = "";
			  $(".availablePiece").removeClass("selectAvailablePiece");
			  $(".piece").find('.myPiece').removeClass("selectAvailablePiece");
		  }
	  }
	  
	  $scope.teamSubmit = function() {
		  var team = JSON.stringify($scope.availableSpaces);
		  $.post("services/setTeam.php",{color: $scope.myColor, team: team, session: $scope.session}, function(data) {
			  setInterval(function(){
			  	$.post("services/getOpponentReadyStatus.php", {color: $scope.myColor, session: $scope.session}, function(a) {
				  	if (a == "1") {
					  	window.location = "http://ysk.co.za/stratego/play-game.php#" + $scope.myColor + "&" + $scope.session;
				  	} else {
					  	$(".done").html("waiting for opponents team"); 
				  	}
			  	}) 
			  }, 3000)
		  })
	  }
  });
 
  function toColor(a) {
	  switch (a) {
		  case "R":
		  	return "Red";
		  	break;
		  case "B":
		  	return "Blue";
		  	break;
	  }
  }
</script>
</html>