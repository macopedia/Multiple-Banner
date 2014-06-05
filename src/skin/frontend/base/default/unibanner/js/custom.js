function slideshow(start,last,interval,eff1) {
	var frame = start;
	var nextframe = start+1;
	Effect.Appear('img1',{duration:.1,from:0.0,to:1.0});
	eff1('img1');
	setInterval(function() {
		Effect.Fade('img'+frame,{duration:.1,from:1.0,to:0.0,afterFinish:function(){
				$('img'+frame).hide();
				Effect.Appear('img'+nextframe,{duration:.1,from:0.0,to:1.0});
				eff1('img'+nextframe);
				frame = nextframe;
				nextframe = (frame == last) ? start : nextframe+1;
			}});
	},interval);
	return;
};

function slideshowdefault(start,last,interval) {
	var frame = start;
	var nextframe = start+1;
	Effect.Appear('img1',{duration:.5,from:0.0,to:1.0});
	setInterval(function() {
		Effect.Fade('img'+frame,{duration:.5,from:1.0,to:0.0,afterFinish:function(){
				$('img'+frame).hide();
				Effect.Appear('img'+nextframe,{duration:.5,from:0.0,to:1.0});
				frame = nextframe;
				nextframe = (frame == last) ? start : nextframe+1;
			}});
	},interval);
	return;
};