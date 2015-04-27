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
    <h1 align="center">&nbsp;</h1>
    <div class="container-fluid clearfix" ng-app="myApp" ng-controller="myCtrl">
    
	    <div class="board pull-left">
	        <table>
	          <tr ng-repeat="(y, letter) in board">
	            <td ng-repeat="(x,num) in letter" data-col="{{x}}" data-row="{{y}}" ng-click="pieceSelect(x,y)" class="block-{{x}}-{{y}} piece-{{num.color}} piece">
	
	              <div align="center">
	              
		          		<div ng-if="num.color == myColor" class="myPiece">
			                <img class="" src="gfx/pieces/{{num.piece}}.png">
			                <div class="pieceRank">{{num.piece}}</div>
		                </div>
		                
		                <div ng-if="num.color != myColor && num.empty != 'true' && num.empty != 'water'" class="opponentPiece">
		                </div>
		                
		                <!-- WATER -->
		                <div ng-if='num.empty == "water"' class="water">
		                	~~
		                </div>
	              </div>
	
	            </td>
	          </tr>
	        </table>
	    </div>
	    
	    <div class="log pull-left">
	    
	    	<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title" align="center">Alerts</h3>
				</div>
				<div class="panel-body ">
					<div class="game-alert" ng-repeat="alert in alerts track by $index">{{alert}}</div>
				</div>
			</div>
			
			<div class="panel panel-success" ng-hide="capturedPieces.length == 0">
				<div class="panel-heading">
					<h3 class="panel-title" align="center">Captured</h3>
				</div>
				<div class="panel-body taken-panel">
					<div class="game-alert taken-piece taken-{{piece.color}}" ng-repeat="piece in capturedPieces | orderBy:'-piece'">
						<img src="gfx/pieces/{{piece.piece}}.png">
						<div class="pieceRank" align="center">{{piece.piece}}</div>
						<h2 class="quantity">x{{piece.quantity}}</h2>
					</div>
				</div>
			</div>
			
			<div class="panel panel-danger" ng-hide="stolenPieces.length == 0">
				<div class="panel-heading">
					<h3 class="panel-title" align="center">Lost</h3>
				</div>
				<div class="panel-body taken-panel">
					<div class="game-alert taken-piece taken-{{piece.color}}" ng-repeat="piece in stolenPieces | orderBy:'-piece'">
						<img src="gfx/pieces/{{piece.piece}}.png">
						<div class="pieceRank" align="center">{{piece.piece}}</div>
						<h2 class="quantity">x{{piece.quantity}}</h2>
					</div>
				</div>
			</div>
			
	    </div>

    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
  </body>

<script type="text/javascript">
  var app = angular.module('myApp', []);
  app.controller('myCtrl', function($scope) {
  		
  		$scope.initialising = true;
  		$scope.isExpectingMove;
  		$scope.myColor = window.location.hash.substr(1).split('&')[0];
  		$scope.opponentColor = ($scope.myColor == "B") ? "R" : "B";
  		$scope.options = [];
  		$scope.capturedPieces = [];
        $scope.stolenPieces = [];
        $scope.selectedPiece = "";
        $scope.gameSession = window.location.hash.substr(1).split('&')[1];
        $scope.board;
        $scope.alerts = ["initialising game..."];
  		
  		$scope.gameInit = function() {
  		
	  		$.post("services/getGameInit.php", {gameSession: $scope.gameSession}, function(data) {
		  		data = eval("["+data+"]");
		  		data = data[0];
		  		
		  		var mySide = ($scope.myColor == "B") ? data.B_Start : data.R_Start ;
		  		var opponentSide = ($scope.myColor == "B") ? data.R_Start : data.B_Start ;
		  		var neutralGround = [[{"empty":"true"},{"empty":"true"},{"empty":"water"},{"empty":"water"},{"empty":"true"},{"empty":"true"},{"empty":"water"},{"empty":"water"},{"empty":"true"},{"empty":"true"}],[{"empty":"true"},{"empty":"true"},{"empty":"water"},{"empty":"water"},{"empty":"true"},{"empty":"true"},{"empty":"water"},{"empty":"water"},{"empty":"true"},{"empty":"true"}]];
		  		var reverseOpponentArray = [];
		  		
		  		for (var i = opponentSide.length-1; i >= 0; i--) {
			  		reverseOpponentArray[(opponentSide.length-1)-i] = opponentSide[i].reverse(); 
		  		}
		  		
		  		$scope.$apply(function() {
			  		$scope.board = reverseOpponentArray.concat(neutralGround, mySide);
			  		if (data.StartingTeam == $scope.myColor) {
				  		$scope.isExpectingMove = false;
				  		$scope.addAlert("Your move");
			  		} else {
				  		$scope.isExpectingMove = true;
				  		$scope.addAlert("Waiting for opponents move");
			  		}
			  		$scope.initialising = false;
		  		});
		  		
		  	})
  		}
  		
  		
  		$scope.gameInit();
  		
  		
  		//Check for moves
  		setInterval(function() {
	  		if ($scope.isExpectingMove === true) {
		  		$.post("services/getMove.php", {'gameSession': $scope.gameSession, 'myColor': $scope.myColor}, function(data) {
			  		if (data != "0") {
				  		data = eval("["+data+"]");
				  		data = data[0];
				  		data.oldpos = eval(data.oldpos);
				  		data.newpos = eval(data.newpos);  
				  		
				  		$scope.$apply(function() {
				  			var oldpos = [parseInt( data.oldpos[0] ),parseInt(data.oldpos[1])];
				  			var newpos = [parseInt(data.newpos[0]),parseInt(data.newpos[1])];
				  			
				  			if (data.color == $scope.myColor) {
					  			$scope.requestMove(oldpos, newpos);
				  			} else {
					  			$scope.requestMove( [(9-oldpos[0]) , (9-oldpos[1])] , [(9-newpos[0]) , (9-newpos[1])]);
				  			}
				  			
				  			
				  			$scope.isExpectingMove = false;
				  			$scope.addAlert("Your move");
				  		});
			  		}
		  		})
	  		}
  		}, 1500);
  		
        $scope.move = function(oldpos, newpos) {

            var oldPiece = $scope.board[oldpos[1]][oldpos[0]];
            var newPiece = $scope.board[newpos[1]][newpos[0]];

            $scope.board[newpos[1]][newpos[0]] = {"color": oldPiece.color, "piece": oldPiece.piece};
            $scope.board[oldpos[1]][oldpos[0]] = {"empty": "true"};

            $scope.options = [];

        }

        $scope.requestMove = function(oldpos,newpos) {

			var isMyPiece = $scope.board[oldpos[1]][oldpos[0]].color == $scope.myColor;

            if ($scope.board[newpos[1]][newpos[0]].empty != "true") {
				
                var attacker = $scope.board[oldpos[1]][oldpos[0]];
                var attacked = $scope.board[newpos[1]][newpos[0]];

                $scope.addAlert(toColor(attacker.color) + attacker.piece + " attacks " + toColor(attacked.color) + attacked.piece);

                // Attack a bomb
                if (attacked.piece == "B") {

                    // Attack with 3
                    if (attacker.piece == "3") {
                    	
                    	$scope.addAlert( toColor(attacker.color) + " 3 detonates " + toColor(attacked.color) + " bomb!" );
                    	$scope.attack(attacker,attacked);
                        $scope.move(oldpos,newpos);
                        $scope.revealPieces(newpos, "stolen");
                        
                    } else {
                    
                    	$scope.addAlert( toColor(attacked.color) + " bomb, explodes " + toColor(attacker.color) + " " + attacked.piece );
                    	$scope.attack(attacked,attacker);
                        $scope.board[oldpos[1]][oldpos[0]] = {"empty": "true"};
                        $scope.revealPieces("stolen", newpos);
                        
                    }

                    // Find flag
                } else if (attacked.piece == "F") {
                
                    $scope.addAlert( toColor(attacker.color) + " team won the game!" );
                    $scope.attack(attacker,attacked);
                    $scope.move(oldpos,newpos);
                    $scope.revealPieces(newpos, "stolen");
                    
                } else {

                    // Attack 10 with a 1
                    if (parseInt(attacker.piece) == 1 && parseInt(attacked.piece) == 10) {
                    	
                    	$scope.addAlert( toColor(attacker.color) + " spy, kidnapped " + toColor(attacked.color) + " 10");
                    	$scope.attack(attacker,attacked);
                        $scope.move(oldpos,newpos);
                        $scope.revealPieces(newpos, "stolen");
                        
                    } else if (parseInt(attacker.piece) > parseInt(attacked.piece)) {
                    	
                    	$scope.addAlert( toColor(attacker.color) + " " + attacker.piece +", defeated " + toColor(attacked.color) + " " + attacked.piece);
                    	$scope.attack(attacker,attacked);
                        $scope.move(oldpos,newpos);
                        $scope.revealPieces(newpos, "stolen");
                        
                    } else if (parseInt(attacker.piece) == parseInt(attacked.piece)) {
                    	
                    	$scope.addAlert( toColor(attacker.color) + " " + attacker.piece +", attacked " + toColor(attacked.color) + " " + attacked.piece + ". Indecisive result");
                    	$scope.attack(attacker,attacked);
                    	$scope.attack(attacked,attacker);
                        $scope.board[newpos[1]][newpos[0]] = {"empty": "true"};
                        $scope.board[oldpos[1]][oldpos[0]] = {"empty": "true"};
                        $scope.revealPieces("stolen", "stolen");
                        
                    } else {

                    	$scope.addAlert( toColor(attacker.color) + " " + attacker.piece +", battled and lost " + toColor(attacked.color) + " " + attacked.piece); 
                    	$scope.attack(attacked,attacker);                   
                        $scope.board[oldpos[1]][oldpos[0]] = {"empty": "true"};
                        $scope.revealPieces("stolen", newpos);
                        
                    }
                }
            } else {
            	var oldPiece = $scope.board[oldpos[1]][oldpos[0]];
            	if (isMyPiece) {
	            	$scope.addAlert("You moved " + oldPiece.piece + " to " + (newpos[0]+1) + ":" + (newpos[1]+1));
            	} else {
	            	$scope.addAlert("Opponent moved from " + (oldpos[0]+1) + ":" + (oldpos[1]+1) + " to " + (newpos[0]+1) + ":" + (newpos[1]+1));
            	}
                
                $scope.move(oldpos,newpos);
            }

            $scope.options = [];
            $scope.selectedPiece = "";
        }

        $scope.getOptions = function(x,y) {
            $scope.options = [];
            x = parseInt(x);
            y = parseInt(y);

            if ($scope.board[y][x].piece == "2") {


                // move forward
                var i = 1;
                var unblocked = true;

                while (unblocked) {
                    try {
                    var movingTo = $scope.board[y-i][x];
                    if (y >= i) {
                        if (movingTo.empty == "true") {
                            $scope.options.push({"text": "Move forward x" + i, "command": "["+x+","+y+"],["+x+","+(y-i)+"]"});
                        } else if (movingTo.color != $scope.myColor && movingTo.empty != "true" && movingTo.empty != "water") {
                            $scope.options.push({"text": "Attack forward x" + i, "command": "["+x+","+y+"],["+x+","+(y-i)+"]"});
                            unblocked = false;
                         } else {
                             unblocked = false;
                          }
                        } else {
                          unblocked = false;
                        }
                    } catch (err) {}

                        if (i < 8) {
                            i++;
                        } else {
                            unblocked = false;
                        }
                }

                // move backwards
                var i = 1;
                var unblocked = true;

                while (unblocked) {
                    try {
                        var movingTo = $scope.board[y+i][x];

                        if (y <= (9-i)) {
                            if (movingTo.empty == "true") {
                                $scope.options.push({"text": "Move backwards x" + i, "command": "["+x+","+y+"],["+x+","+(y+i)+"]"});
                            } else if (movingTo.color != $scope.myColor && movingTo.empty != "true" && movingTo.empty != "water") {
                                $scope.options.push({"text": "Attack Backwards x" + i, "command": "["+x+","+y+"],["+x+","+(y+i)+"]"});
                                unblocked = false;
                            } else {
                                unblocked = false;
                            }
                        } else {
                            unblocked = false;
                        }
                    } catch(err){}

                        if (i < 8) {
                            i++;
                        } else {
                            unblocked = false;
                        }

                    
                }

                // move right
                var i = 1;
                var unblocked = true;

                while (unblocked) {
                    try {
                        var movingTo = $scope.board[y][x+i];

                        if (x <= (9-i)) {
                            if (movingTo.empty == "true") {
                                $scope.options.push({"text": "Move right x" + i, "command": "["+x+","+y+"],["+(x+i)+","+(y)+"]"});
                            } else if (movingTo.color != $scope.myColor && movingTo.empty != "true" && movingTo.empty != "water") {
                                $scope.options.push({"text": "Attack right x" + i, "command": "["+x+","+y+"],["+(x+i)+","+(y)+"]"});
                                unblocked = false;
                            } else {
                                unblocked = false;
                            }
                        } else {
                            unblocked = false;
                        }

                    } catch (err){}

                        if (i < 8) {
                            i++;
                        } else {
                            unblocked = false;
                        }
                }

                // move left
                var i = 1;
                var unblocked = true;

                while (unblocked) {
                    try {
                        var movingTo = $scope.board[y][x-i];

                        if (x >= i) {
                            if (movingTo.empty == "true") {
                                $scope.options.push({"text": "Move left x" + i, "command": "["+x+","+y+"],["+(x-i)+","+(y)+"]"});
                            } else if (movingTo.color != $scope.myColor && movingTo.empty != "true" && movingTo.empty != "water") {
                                $scope.options.push({"text": "Attack left x" + i, "command": "["+x+","+y+"],["+(x-i)+","+(y)+"]"});
                                unblocked = false;
                            } else {
                                unblocked = false;
                            }
                        } else {
                            unblocked = false;
                        }
                    } catch(err){}

                        if (i < 8) {
                            i++;
                        } else {
                            unblocked = false;
                        }
                }

            } else if ($scope.board[y][x].piece != "B" && $scope.board[y][x].piece != "F") {

               try {
                    if (y >= 1 && $scope.board[y-1][x].empty == "true") {
                        $scope.options.push({"text": "Move forward", "command": "["+x+","+y+"],["+x+","+(y-1)+"]"});
                    }
                    if (y<=8 && $scope.board[y+1][x].empty == "true") {
                        $scope.options.push({"text": "Move backwards", "command": "["+x+","+y+"],["+x+","+(y+1)+"]"});
                    }
                    if (x<=8 && $scope.board[y][x+1].empty == "true") {
                        $scope.options.push({"text": "Move right", "command": "["+x+","+y+"],["+(x+1)+","+y+"]"});
                    }
                    if (x>=1 && $scope.board[y][x-1].empty == "true") {
                       $scope.options.push({"text": "Move left", "command": "["+x+","+y+"],["+(x-1)+","+y+"]"});
                    }
                } catch (err) {}

                try {
                    if (y >= 1  && $scope.board[y-1][x].color != $scope.myColor && $scope.board[y-1][x].empty != "true" && $scope.board[y-1][x].empty != "water") {
                        $scope.options.push({"text": "Attack forward", "command": "["+x+","+y+"],["+x+","+(y-1)+"]"});
                    }
                    if (y<=8 && $scope.board[y+1][x].color != $scope.myColor && $scope.board[y+1][x].empty != "true" && $scope.board[y+1][x].empty != "water") {
                        $scope.options.push({"text": "Attack backwards", "command": "["+x+","+y+"],["+x+","+(y+1)+"]"});
                    }
                    if (x<=8 && $scope.board[y][x+1].color != $scope.myColor && $scope.board[y][x+1].empty != "true" && $scope.board[y][x+1].empty != "water") {
                        $scope.options.push({"text": "Attack right", "command": "["+x+","+y+"],["+(x+1)+","+y+"]"});
                    }
                    if (x>=1 && $scope.board[y][x-1].color != $scope.myColor && $scope.board[y][x-1].empty != "true" && $scope.board[y][x-1].empty != "water") {
                        $scope.options.push({"text": "Attack left", "command": "["+x+","+y+"],["+(x-1)+","+y+"]"});
                    }
                } catch (err){} 

            }
            
            
        }

        $scope.pieceSelect = function (x,y) {

            var block = ".block-" +x+ "-" +y;
            var isMyPiece = $scope.board[y][x].color == $scope.myColor;

            if (isMyPiece && $scope.board[y][x].piece != "B" && $scope.board[y][x].piece != "F" && $scope.isExpectingMove == false) {
                $("td").removeClass("select");
                $(block).addClass("select");
                $scope.selectedPiece = [x,y]; 
            }

            // VISUAL SELECTION
            if (typeof($scope.selectedPiece) == "object") {
                $scope.getOptions($scope.selectedPiece[0],$scope.selectedPiece[1]);
                
                if ($scope.isPossibleMove($scope.selectedPiece, [x,y]) == true) {
                    //$(block).css("background-color", "lime");
                    if ($scope.isExpectingMove == false) {
                    
	                    var oldpos = $scope.selectedPiece.slice(0);
	                    
	                    // Request move
	                    //$scope.requestMove($scope.selectedPiece, [x,y]);
	                    
	                    // ADD MOVE TO SERVER
	                    $.post("services/setMove.php", {"oldpos": "[" + oldpos[0] + "," + oldpos[1] + "]", "newpos": "[" + x + "," + y + "]", "myColor": $scope.myColor, 'gameSession': $scope.gameSession},function(data){
		                    
						  		if (data != "0") {
							  		data = eval("["+data+"]");
							  		data = data[0];
							  		
							  		data.oldpos = eval(data.oldpos);
							  		data.newpos = eval(data.newpos);  
							  		
							  		$scope.$apply(function() {
							  			var oldpos = [parseInt( data.oldpos[0] ),parseInt(data.oldpos[1])];
							  			var newpos = [parseInt(data.newpos[0]),parseInt(data.newpos[1])];
							  			
							  			if (data.color == $scope.myColor) {
								  			$scope.requestMove(oldpos, newpos);
								  			$scope.isExpectingMove = true;
								  			$scope.addAlert("Waiting for opponents move");
							  			}
							  			
							  		});
							  		
						  		}
		                    
	                    });
	                    
	                    // Check whether or not the move went through
	                    $scope.$apply(function() {
	                    	if ($scope.isExpectingMove == false) {
		                    	$scope.addAlert("Server error. Please try again");
							}
	                    });
	                    
		                   
                    }
                        
                }
            }

        }

        $scope.deselect = function() {
            $scope.options = [];
        }

        $scope.isPossibleMove = function(oldpos, newpos) {
            var commandMatch = "[" + oldpos[0] + "," + oldpos[1] + "],[" + newpos[0] + "," + newpos[1] + "]";
            for (var i = 0; i < $scope.options.length; i++) {
                if ($scope.options[i].command == commandMatch) {
                    return true;
                }
            }
        }
        
        $scope.addAlert = function(alert) {
	        $scope.alerts.unshift(alert);
        }
        
        $scope.attack = function(winner,loser) {
        	var isMyPiece = winner.color == $scope.myColor;
	        if (isMyPiece) {
		        $scope.addCapturedPiece(loser.piece);
		        console.log("captured: " + $scope.capturedPieces);
	        } else {
		        $scope.addStolenPiece(loser.piece);
		        console.log("stolen: " + $scope.stolenPieces);
	        }
        }
        
        $scope.revealPieces = function(attackerPos, attackedPos) {
        	setTimeout(function(){
        	
	        	if (attackerPos != "stolen") {
        	
	        		var x = attackerPos[0];
	        		var y = attackerPos[1];
		        	var block = ".block-" +x+ "-" +y;
		        	
		        	var piece = $scope.board[attackerPos[1]][attackerPos[0]];
		        	var isMyPiece = piece.color == $scope.myColor;
		        	if (isMyPiece == false) {
						
							$(block).find(".opponentPiece").css("display","none");
							
							$(block).find("div").append("\
				        		<div class='viewOpponentPiece'>\
				        			<img src='gfx/pieces/"+piece.piece+".png'>\
				        			<div class='pieceRank'>"+piece.piece+"</div>\
				        		</div>\
							");
							
							setTimeout(function(){
								$(block).find(".opponentPiece").css("display","block");
								$(block).find("div").remove(".viewOpponentPiece");
							}, 3000);
						 
		        	}
		        	
	        	}
	        	
	        	if (attackedPos != "stolen") {
        	
	        		var x = attackedPos[0];
	        		var y = attackedPos[1];
		        	var block = ".block-" +x+ "-" +y;
		        	
		        	var piece = $scope.board[attackedPos[1]][attackedPos[0]];
		        	var isMyPiece = piece.color == $scope.myColor;
		        	if (isMyPiece == false) {
						
							$(block).find(".opponentPiece").css("display","none");
							
							$(block).find("div").append("\
				        		<div class='viewOpponentPiece'>\
				        			<img src='gfx/pieces/"+piece.piece+".png'>\
				        			<div class='pieceRank'>"+piece.piece+"</div>\
				        		</div>\
							");
							
							setTimeout(function(){
								$(block).find(".opponentPiece").css("display","block");
								$(block).find("div").remove(".viewOpponentPiece");
							}, 3000);
						 
		        	}
		        	
	        	}
	        	
	        	
	        	
        	}, 100);
        	
	       
        }
        
        $scope.addStolenPiece = function(piece) {
        
        	for (var i = 0; i < $scope.stolenPieces.length; i++) {
        	
	        	if ($scope.stolenPieces[i].piece == piece) {
	        		var quantity = parseInt($scope.stolenPieces[i].quantity) + 1; 
		        	$scope.stolenPieces[i] = {"piece": piece, "quantity": quantity, "color": $scope.myColor}
		        	return;
		        }
			}
		        
        	$scope.stolenPieces.unshift({"piece": piece, "quantity": 1, "color": $scope.myColor});
	        
        }
        
        $scope.addCapturedPiece = function(piece) {
        
        	for (var i = 0; i < $scope.capturedPieces.length; i++) {
        	
	        	if ($scope.capturedPieces[i].piece == piece) {
	        		var quantity = parseInt($scope.capturedPieces[i].quantity) + 1; 
		        	$scope.capturedPieces[i] = {"piece": piece, "quantity": quantity, "color": $scope.opponentColor}
		        	return;
		        }
			}
		        
        	$scope.capturedPieces.unshift({"piece": piece, "quantity": 1, "color": $scope.opponentColor});
	        
        }


  });
  app.filter('reverse', function() {
	  return function(items) {
	   return items.slice().reverse();
	  };
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