/*
 Class: ProtoFlow
 
 License:
 Copyright (c) 2008 DeenSoft.com - You are free to use this file wherever you wish but please do let us know at blog.deensoft.com
 so that we can showcase it. You can even post your bugs on our blog and we will fix them asap.
 This code is being release under open-terms. Use at your own risk. Give us feedback. Help us fix bugs and implement new features. :)
 <contact@deensoft.com>
 
 You can follow up with more comments and suggestions on our blog <http://blog.deensoft.com>
  
 Description:
 	ProtoFlow v0.8 is a very early preview release that simulates Apples CoverFlow effect using Prototype/Scriptaculous lib.
 
 ChangeLog:
 	July 7, 2008:
 	* Fixed z-index issue - Thanks to Xavvier (http://www.euclide.org/) for the fix.
 	March 26, 2008:
 	* Fixed major issue with IE7 and z-index for scrollbar (Thanks to all those who contributed on the blog)
 	March 19, 2008
 	* Fixed issues with IE
 	* Fixed major bug with images and links...
 	
 	Initial:
 	* Added Reflection
 	* Fixed Captions
 	
 	How to use:
 	(start code)
 	var myFlow = new ProtoFlow(
 						$('myElem'),
 						{
 							captions: 'captionsList'
 						}
 	);
 	(end)
 */

var ProtoFlow = Class.create({
    /*
     Function: initialize
     Constructor for ProtoFlow Class.
     
     Parameters:
     elem - {Mixed} the HTML object that ProtoFlow is initialize from. This can be either an HTML Object or ID to an HTML object.
     opt  - {Object} config Object
     */
    initialize: function(elem, opt){
        opt = opt || {};
        this.options = {
            startIndex: 2,
            interval: 60,
            slider: true,
            flex: 110,
            captions: false,
            autoplay: false,
            autoplayInterval: 5,
            useReflection: false,
            enableOnClickScroll: false,
			enableKeyboard: true,
			enableMouse: true
        };
		Object.extend(this.options, opt);
		
        this.useCaptions = this.options.captions; //initially we don't wanna use captions unless turned on?
        this.elem = $(elem);
        if (!this.elem) 
            return;
        
        //console.log(this.elem.select('img'));
        this.elem.setStyle({
            overflow: "hidden",
            position: "relative"
        });
        this.imageStack = this.elem.select('img'); //this.elem.childElements();
        if (this.options.captions != false) {
            this.captions = this.imageStack.pluck("alt");
            //this.captions = this.captionsHolder.childElements();		
            //this.captions.each( ( function(elem, i){this.captions[i] = elem.innerHTML;} ).bind(this));
            this.captionsCount = this.captions.size();
        }
        
        if (this.options.useReflection) {
            this.imageStack.each(function(elem){
                Reflection.add(elem, {
                    opacity: 2 / 3
                });
            }.bind(this));
            this.stack = this.elem.childElements();
        }
        else {
            this.stack = this.imageStack;
        }
        this.stackCount = (this.stack).size();
        
        
        
        if (this.useCaptions) {
            this.captionHolder = new Element('div');
            this.captionHolder.className = "captionHolder";
            this.captionHolder.setStyle({
                width: "100%",
                textAlign: "center",
                position: 'absolute',
                left: "0px",
                top: (Element.getHeight(this.elem) - 80) + "px"
            });
            this.elem.appendChild(this.captionHolder);
        }
        
        
        
        this.currPos = this.options.startIndex - 1;
        this.currIndex = this.currPos;
        /* slider */
        if (this.options.slider) {
            this.sliderContainer = new Element('div');
            this.sliderContainer.setStyle({
                width: '200px',
                height: '10px',
                position: 'absolute',
                top: (Element.getHeight(this.elem) - 30) + "px",
                left: (Element.getWidth(this.elem) / 2 - (137 / 2)) + "px",
                zIndex: 99999999
            });
            
            this.sliderTrack = new Element('div');
            this.sliderTrack.className = "sliderTrack";
            
            this.sliderHandle = new Element('div');
            this.sliderHandle.className = "sliderHandle";
            
            this.sliderTrack.appendChild(this.sliderHandle);
            this.sliderContainer.appendChild(this.sliderTrack);
            
            this.elem.appendChild(this.sliderContainer);
            
            this.slider = new Control.Slider(this.sliderHandle, this.sliderTrack, {
                range: $R(0, this.getStackCount() - 1),
                sliderValue: this.getCurrentPos(), // won't work if set to 0 due to a bug(?) in script.aculo.us
                onSlide: this.handleSlider.bind(this),
                onChange: this.handleSlider.bind(this)
            });
        }
        
        
        
        this.timer = 0;
        
        
        /* sets up click listener on all the elements in the stack */
        this.stack.each(function(elem){
            elem.identify();
            //console.log(elem);
            //elem.observe("mouseover", function(){this.setStyle({"border": "2px solid yellow;"});});
            if (this.options.enableOnClickScroll) 
                elem.observe('click', this.handleClick.bind(this));
        }.bind(this));
        
        if (this.options.enableOnClickScroll) //lets go through all the <a> tags and disable them..
        {
            this.disableLinks();
        }
        
        this.goTo(this.currPos);
        
        this.autoplayer = null;
        if (this.options.autoplay) {
            this.autoplayer = new PeriodicalExecuter(this.autoPlay.bind(this), this.options.autoplayInterval);
        }

		
		/**
		* Added Keyboard support thanks to Martin (martin[at]noremember.org)
		*/
		if(this.options.enableKeyboard) {
		 document.observe('keyup', (function(e) {
		   var code = e.keyCode;
		   if(37 == code) this.previous();
		   if(39 == code) this.next();
		 }).bind(this));
		}

		if(this.options.enableMouse) {
		 var eventType = Prototype.Browser.Gecko ? "DOMMouseScroll" : "mousewheel";

		 Event.observe(this.elem, eventType, (function(e) {
		   this.enableMouse(e);
		 }).bind(this), false);

		 if (this.useCaptions) {
		   Event.observe(this.captionHolder, eventType, (function(e) {
			 this.enableMouse(e);
		   }).bind(this), false);
		 }
		}
		/***/
        Event.observe(window, 'resize', this.handleWindowResize.bind(this));
    },
	/**
	 * Function: diableLinks
	 * 
	 * This function goes through all the anchor tags in the stack and simply disables the href attribute.
	 * This is required if the user has set 'enableOnClickScroll' to be true
	 * 
	 * A fix was contributed by Martin. Thank you!
	 */
	 disableLinks: function(){
		this.elem.select("a").each(function(a){
			a.observe('click', function(e) {
				e.preventDefault();
			});
		});
	},
	/**
	 * Function: autoPlay
	 * 
	 * Internal function that is used to scroll through the list automatically
	 */
    autoPlay: function(){
        if ((this.currIndex + 2) > this.stackCount) {
            this.currIndex = 0;
        }
        this.currIndex = this.currIndex + 1
        this.goTo(this.currIndex);
    },
	/**
	 * Function: handleWindowResize
	 * 
	 * Currently does nothing. This will be implemented later on. Internal Function
	 * Parameters:
	 * event - {Object} Browser generated event
	 */
    handleWindowResize: function(event){
    },
	/**
	 * Function: hanldeWheel
	 * This function should handle onwheel scroll events. This is quite buggy at the moment so disabled 
	 * by default.
	 * 
	 * Parameters:
	 * 
	 * event - {Object} Browser Event
	 */
    handleWheel: function(event){
        v = Event.wheel(event);
        this.goTo(this.currIndex + v);
        this.slider.setValue(this.currIndex + v);
    },
	/**
	 * Function: handleSliderChange
	 * wrapper function called after slider has been moved. This in turn calls <goTo> function which 
	 * moves the images around.
	 * 
	 * Parameters:
	 * index - {int} index to move to
	 */
    handleSliderChange: function(index){
        this.goTo(index);
    },
	/**
	 * Function: handleSlider
	 * wrapper function called after slider has been moved. This in turn calls <goTo> function which 
	 * moves the images around.
	 * 
	 * Parameters:
	 * index - {int} index to move to.
	 */	
    handleSlider: function(index){
        if (index) 
            this.goTo(index);
    },
	/**
	 * function: handleClick
	 * When an image is clicked we handle it by finding the appropiate index and then moving the images
	 * and updating the scrollbar as well.
	 * 
	 * Parameters:
	 * e - {Object} Browser Event
	 */
    handleClick: function(e){
        var elem = Event.element(e);
        
        var v = elem.getAttribute("index");
        
        if (!v && this.options.useReflection) {
            elem = elem.up('a');
            v = elem.getAttribute("index");
        }
        
        this.currIndex = v;
        this.goTo(v);
        this.updateSlider(v);
    },
	/**
	 * Function: getCurrentPos
	 * Returns the current position of the image that is being focused on.
	 * 
	 * Returns: 
	 * current position - {int} index
	 */
    getCurrentPos: function(){
        return this.currPos;
    },
	/**
	 * function: goTo
	 * takes an index and moves the images to that index based on the flex and index provided. This also
	 * updates the captions to show the correct caption if enabled.
	 * 
	 * Parameters:
	 * index - {int} index in the stack to move to
	 */
    goTo: function(index){
        this.slideTo(index * this.options.flex * -1);
        //this.currPos = Math.round(index);
        if (this.useCaptions) {
            this.captionHolder.innerHTML = this.captions[Math.round(index)];
        }
    },
	/**
	 * function: updateSlider
	 * updates the slider to currect position. 
	 * 
	 * parameters:
	 * index - {int} Index for the stack
	 */
    updateSlider: function(index){
        if (this.options.slider) 
            this.slider.setValue(index);
    },
	/**
	 * function: step
	 * Steps through the animation process. This is what makes it look so smooth ;)
	 */
    step: function(){
        if (this.target < this.currPos - 1 || this.target > this.currPos + 1) {
            this.moveTo(this.currPos + (this.target - this.currPos) / 5);
            window.setTimeout(this.step.bind(this), this.options.interval);
            this.timer = 1;
        }
        else {
            this.timer = 0;
            
        }
    },
	/**
	 * function: slideTo
	 * This function sets up a timer and calls step on the interval specified via options.
	 * parameters:
	 * x - {int} index to move to
	 */
    slideTo: function(x){
        this.target = x;
        
        if (this.timer == 0) {
            window.setTimeout(this.step.bind(this), this.options.interval);
            this.timer = 1;
        }
        
        
    },
    /**
     * function: moveTo
     * Actually moves the images based on the position provided by <step> function
     * 
     * parameters:
     * currentPos - {int} Position to move to
     */
    moveTo: function(currentPos){
        var x = currentPos;
        this.currPos = currentPos;
        var width = Element.getWidth(this.elem);
        var height = Element.getHeight(this.elem);
        
        var top = this.elem.offsetTop;
        var zIndex = this.stackCount;
		var flex = this.options.flex;
        this.stack.each(function(elem, index){

            Element.absolutize(elem);
            elem.setAttribute("index", index);

			var wsize=300;
        	var hsize=300;

            if (this.options.useReflection) {
				wsize=elem.select('img')[0].width;
				hsize=elem.select('img')[0].height;
			} else {
				wsize=elem.width;
				hsize=elem.height;
			}
            
            if (this.options.useReflection) {
                elem.down(1).setAttribute('index', index);
            }

			var sign=1;
			if(x < 0)
				sign=-1;

			var mvt=Math.abs(x/flex/this.stackCount);

			if(mvt > 0.2) {
				mvt=Math.min(1, Math.pow(mvt, 0.1));
			} else if(mvt > 0.01) {
				mvt=mvt*4;
			}

			var nx=(width  - wsize)/2 + sign * (width - wsize - 20) * mvt / 2;
			var ny=(20 + height - hsize)/2 - (height - hsize - 10) * mvt / 2;

            elem.setStyle({
                left: nx+'px',
                top: ny+'px',
                textAlign: "center"
            });
            //console.log(elem);
            elem.style.zIndex = zIndex;
            
            if (x < - (flex/2)) 
                zIndex++;
            else 
                zIndex--;

            x += this.options.flex;
            
        }.bind(this));
    },
    /**
     Function: getStackCount
     Returns the count of elements in the stack
     
     Returns:
     stackCount - {int} An integer value indicating how many elements are there in the stack.
     */
    getStackCount: function(){
        return this.stackCount;
    },
	decreaseIndex: function(e) {
		if(this.currIndex > 0)
			this.currIndex--;
	},

	increaseIndex: function(e) {
		if (this.currIndex < this.getStackCount() - 1)
			this.currIndex++;
	},

	previous: function(e) {
		this.decreaseIndex();
		this.toCurrentIndex();
	},

	next: function(e) {
		this.increaseIndex();
		this.toCurrentIndex();
	},

	enableMouse: function(e) {
		Event.wheel(e)< 0 ? this.next() : this.previous();
		Event.stop(e);
	},

	toCurrentIndex: function(e) {
		this.goTo(this.currIndex);
		this.updateSlider(this.currIndex);
	}	
});

/**
* Code contributed by Martin
*/
Object.extend(Event, {
	wheel: function(event){
		var delta = 0;
		if (!event) event = window.event;

		if (event.wheelDelta) {
			delta = event.wheelDelta/120;
			if (window.opera) delta = -delta;
		} 
		else if (event.detail) {
			delta = -event.detail/3;
		}
		return Math.round(delta); //Safari Round
	}
});

/**
 * reflection.js v1.8
 * http://cow.neondragon.net/stuff/reflection/
 * Freely distributable under MIT-style license.
 */
 
/* From prototype.js */
if (!document.getElementsByClassName) {
	document.getElementsByClassName = function(className) {
		var children = document.getElementsByTagName('*') || document.all;
		var elements = new Array();
	  
		for (var i = 0; i < children.length; i++) {
			var child = children[i];
			var classNames = child.className.split(' ');
			for (var j = 0; j < classNames.length; j++) {
				if (classNames[j] == className) {
					elements.push(child);
					break;
				}
			}
		}
		return elements;
	}
}

var Reflection = {
	defaultHeight : 0.5,
	defaultOpacity: 0.5,
	
	add: function(image, options) {
		Reflection.remove(image);
		
		doptions = { "height" : Reflection.defaultHeight, "opacity" : Reflection.defaultOpacity }
		if (options) {
			for (var i in doptions) {
				if (!options[i]) {
					options[i] = doptions[i];
				}
			}
		} else {
			options = doptions;
		}
	
		try {
			var d = document.createElement('div');
			var p = image;
			
			var classes = p.className.split(' ');
			var newClasses = '';
			for (j=0;j<classes.length;j++) {
				if (classes[j] != "reflect") {
					if (newClasses) {
						newClasses += ' '
					}
					
					newClasses += classes[j];
				}
			}

			var reflectionHeight = Math.floor(p.height*options['height']);
			var divHeight = Math.floor(p.height*(1+options['height']));
			
			var reflectionWidth = p.width;
			
			if (document.all && !window.opera) {
				/* Fix hyperlinks */
                if(p.parentElement.tagName == 'A') {
	                var d = document.createElement('a');
	                d.href = p.parentElement.href;
                }  
                    
				/* Copy original image's classes & styles to div */
				d.className = newClasses;
				p.className = 'reflected';
				
				d.style.cssText = p.style.cssText;
				p.style.cssText = 'vertical-align: bottom';
			
				var reflection = document.createElement('img');
				reflection.src = p.src;
				reflection.style.width = reflectionWidth+'px';
				reflection.style.display = 'block';
				reflection.style.height = p.height+"px";
				
				reflection.style.marginBottom = "-"+(p.height-reflectionHeight)+'px';
				reflection.style.filter = 'flipv progid:DXImageTransform.Microsoft.Alpha(opacity='+(options['opacity']*100)+', style=1, finishOpacity=0, startx=0, starty=0, finishx=0, finishy='+(options['height']*100)+')';
				
				d.style.width = reflectionWidth+'px';
				d.style.height = divHeight+'px';
				p.parentNode.replaceChild(d, p);
				
				d.appendChild(p);
				d.appendChild(reflection);
			} else {
				var canvas = document.createElement('canvas');
				if (canvas.getContext) {
					/* Copy original image's classes & styles to div */
					d.className = newClasses;
					p.className = 'reflected';
					
					d.style.cssText = p.style.cssText;
					p.style.cssText = 'vertical-align: bottom';
			
					var context = canvas.getContext("2d");
				
					canvas.style.height = reflectionHeight+'px';
					canvas.style.width = reflectionWidth+'px';
					canvas.height = reflectionHeight;
					canvas.width = reflectionWidth;
					
					d.style.width = reflectionWidth+'px';
					d.style.height = divHeight+'px';
					p.parentNode.replaceChild(d, p);
					
					d.appendChild(p);
					d.appendChild(canvas);
					
					context.save();
					
					context.translate(0,image.height-1);
					context.scale(1,-1);
					
					context.drawImage(image, 0, 0, reflectionWidth, image.height);
	
					context.restore();
					
					context.globalCompositeOperation = "destination-out";
					var gradient = context.createLinearGradient(0, 0, 0, reflectionHeight);
					
					gradient.addColorStop(1, "rgba(255, 255, 255, 1.0)");
					gradient.addColorStop(0, "rgba(255, 255, 255, "+(1-options['opacity'])+")");
		
					context.fillStyle = gradient;
					if (navigator.appVersion.indexOf('WebKit') != -1) {
						context.fill();
					} else {
						context.fillRect(0, 0, reflectionWidth, reflectionHeight*2);
					}
				}
			}
		} catch (e) {
	    }
	},
	
	remove : function(image) {
		if (image.className == "reflected") {
			image.className = image.parentNode.className;
			image.parentNode.parentNode.replaceChild(image, image.parentNode);
		}
	}
}

function addReflections() {
	var rimages = document.getElementsByClassName('reflect');
	for (i=0;i<rimages.length;i++) {
		var rheight = null;
		var ropacity = null;
		
		var classes = rimages[i].className.split(' ');
		for (j=0;j<classes.length;j++) {
			if (classes[j].indexOf("rheight") == 0) {
				var rheight = classes[j].substring(7)/100;
			} else if (classes[j].indexOf("ropacity") == 0) {
				var ropacity = classes[j].substring(8)/100;
			}
		}
		
		Reflection.add(rimages[i], { height: rheight, opacity : ropacity});
	}
}

var previousOnload = window.onload;
window.onload = function () { if(previousOnload) previousOnload(); addReflections(); }
