<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		
		<title>Sleepy</title>
		
		<link href="css/core.css" rel="stylesheet" type="text/css" />
		<script src="js/jquery.js"></script>
	</head>
	<body>
		<div id="wrap">
			<div id="bubble">Sleepy needs javascript enabled to run. Enable javascript in your browser settings and try again</div>
		</div>
		
		<script type="text/javascript">
			jQuery(function($){
				// change no js message			
				$('#bubble').text('...');
				
				// init the game when jquery is ready
				game.init();
			});
			
			// the game, one big object!
			game = {
				
				// set some defaults
				window:			$(window),
				canvas:			$('body'),
				
				firstRun: 		true,
				running: 		false,
				msg:			$('#bubble'),
				
				time:			10000,
				timer:			$('<div id="timer" />'),				
				score:			0,
				scorer:			$('<div id="scorer" />'),
				
				pps:			200, // pixels per second
				
				fallspace:		null,
				
				// init!!
				init: function()
				{
					// calibrate
					game.calibrate();
									
					// change the game message
					game.msg.text('Press spacebar to start...');
					
					//set spacebar start
					game.window.on('keypress', function(e){
						if(game.running === false && e.keyCode === 32)
						{
							game.msg.fadeOut(100, function(){ $(this).text(''); });
							game.startGame();
						}
					});
					
					// reset values on resize
					game.window.on('resize', function(){
						// recalibrate
						game.calibrate();				
					});
					
					console.log(game);
				},
				
				calibrate: function()
				{
					// set calc vars
					game.wwidth = game.window.width();
					game.wheight = $(window).height();
					
					game.fallspace = [0,game.wwidth];
					
					game.falltime = parseInt((this.wheight / this.pps) * 1000); // the time it should take a stimlant to fall
					game.underground = parseInt(this.wheight + 75 + 10); // the top position to hide an element below the viewport
				},
				
				finish: function()
				{
					game.msg.text('You scored ' + game.score + ', press spacebar to restart...').fadeIn(100);
					game.score = 0;
					game.window.on('keypress', function(e){
						if(game.running === false && e.keyCode === 32)
						{
							game.msg.fadeOut(100, function(){ $(this).text(''); });
							game.startGame();
						}
					});
				},
				
				startGame: function()
				{
					game.running = true;
					game.attachTimer();
					game.attachScorer();
					
					game.timerInt = setInterval(game.updateTime, 1000); // this will adjust the game length, higer = longer
					game.objectInt = setInterval(game.attachObject, 700); // this will adjust the frequency of object, higher = less often
				},
				
				stopGame: function()
				{
					game.running = false;
					game.canvas.find('.object').remove();
					game.detachTimer();
					game.detachScorer();
					
					clearInterval(game.timerInt);
					clearInterval(game.objectInt);
					
					game.finish();
				},
				
				attachTimer: function(){
					game.timer.css({
						position: 'absolute',
						bottom: 10,
						right: 10,
						textAlign: 'right'
					}).text(Math.ceil(game.time/1000) + ' seconds left');	
					
					game.canvas.append(game.timer);
				},
				
				updateTime: function()
				{
					game.time = game.time - 1000;
					game.timer.text(Math.ceil(game.time/1000) + ' seconds left');
					if(game.time === 0)
					{
						game.time = 10000;
						game.stopGame();
					}	
					return true;
				},
				
				detachTimer: function()
				{
					game.timer.remove();
				},
				
				attachScorer: function()
				{
					game.scorer.css({
						position: 'absolute',
						bottom: 10,
						left: 10,
						textAlign: 'left'
					}).text(game.score);
					game.canvas.append(game.scorer);
				},
				
				updateScore: function(s)
				{
					game.score = game.score + s;
					game.score = game.score < 0 ? 0 : game.score;
					game.scorer.text(game.score);
				},
				
				detachScorer: function(){
					game.scorer.remove();
				},
				
				attachObject: function()
				{
					//build object
					var o = $('<div class="stimulant object" />');
					var v = objects[parseInt(Math.random() * objects.length)];
					var t = v.h * -1;
					var l = game.getLeftPos(v.w);
					
					//add to dom
					o
					.addClass(v.c)
					.css({
						top: t,
						left: l,
					})
					.bind('click', function(){
						$(this).remove();
						
						game.updateScore(v.p);
					})
					.appendTo(game.canvas)
					.animate({top: game.underground}, game.falltime, 'linear');	
					
					game.getNextFallSpace(v.w, l);
				},
				
				//mathmatical!
				
				getLeftPos: function(width)
				{
					// given an objects width, get a random left position within the fall space
					
					// default return	
					var out = 0;
					
					//set min and max
					var min = typeof(game.fallspace[0]) !== 'undefined' ? parseInt(game.fallspace[0]) : 0; // default to 0
					var max = typeof(game.fallspace[1]) !== 'undefined' ? parseInt(game.fallspace[1] - width) : (game.wwidth - width); // default to game window width - object width
					
					out = min + Math.ceil(Math.random() * (max - min));
					
					return out;
				},
				
				getNextFallSpace: function(width, left)
				{
					// callculate the next fall space given the previous object width and left pos
					
					// get right side
					var right = game.wwidth - (left + width);
					
					if(left > right)
					{
						game.fallspace = [0, left];
					}
					else if(left < right)
					{
						game.fallspace = [(left + width), game.wwidth];
					}
					else
					{
						// left or right
						if(Math.round(Math.random() * 1) === 1)
						{
							game.fallspace = [0, left];
						}
						else
						{
							game.fallspace = [(left + width), game.wwidth];	
						}
					}
					
					return;
				}
			}
			
			objects = [
				{c: 'redbull', w: 60, h: 148, p: 20},
				{c: 'coffee', w: 108, h: 128, p: 10},
				{c: 'coke', w: 76, h: 136, p: 5},
			];
		</script>
	</body>
</html>