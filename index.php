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
				
				pps:			120, // pixels per second
				
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
					
					game.timerInt = setInterval(game.updateTime, 1000);
					game.objectInt = setInterval(game.attachObject, 400);
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
					//random between simulants and relaxants
					if(parseInt(Math.random() * 3) > 0)
					{
						game.attachStimulant();
					}
					else
					{
						game.attachRelaxant();
					}
				},
				
				attachStimulant: function()
				{
					var stimulant = $('<div class="stimulant object" />');
					
					stimulant
					.css({
						position: 'absolute',
						top: -75,
						left: parseInt(Math.random() * game.wwidth),
						background: '#0c0',
						width: 50,
						height: 75
					})
					.bind('click', function(){
						$(this).remove();
						
						game.updateScore(10);
					})
					.appendTo(game.canvas)
					.animate({top: game.underground}, game.falltime, 'linear');
				},
				
				attachRelaxant: function()
				{
					var stimulant = $('<div class="relaxant object" />');
					
					stimulant
					.css({
						position: 'absolute',
						top: -75,
						left: parseInt(Math.random() * game.wwidth),
						background: '#c00',
						width: 50,
						height: 75
					})
					.bind('click', function(){
						$(this).remove();
						
						game.updateScore(-10);
					})
					.appendTo(game.canvas)
					.animate({top: game.underground}, game.falltime, 'linear');
				},
			}
		</script>
	</body>
</html>