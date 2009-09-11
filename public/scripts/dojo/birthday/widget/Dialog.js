/**
 * Custom Dialog
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");
dojo.require('dijit.Dialog');
dojo.provide("birthday.widget.Dialog");


dojo.declare("birthday.widget.Dialog", [dijit.Dialog],
	{
	
    /**
     * the path to the template
     */
    templateString: '',
    templatePath: dojo.moduleUrl("birthday.widget","templates/Dialog.html"),  
    leftPos: true,
	WorkSpace: null,

	/**
	 * 
	 */
	postCreate: function() {
		
		this.inherited(arguments);
		
		dojo.connect(this.titleBar, "onmouseup", this, "savePosition");
		var self = this;
		
	
	},
	
	/**
	 * Position modal dialog in the viewport. If no relative offset
	 * in the viewport has been determined (by dragging, for instance),
	 * center the node. Otherwise, use the Dialog's stored relative offset,
	 * and position the node to top: left: values based on the viewport.
	 */
	_position: function(){
	
			if(!dojo.hasClass(dojo.body(),"dojoMove")){
				
				var node = this.domNode;
				var viewport = dijit.getViewport();
				var p = this._relativePosition;
				var mb = p ? null : dojo.marginBox(node);
				
				if (this.leftPos) {
				    dojo.style(node,{
				        left: Math.floor(viewport.l + (p ? p.l : (viewport.w - mb.w) / 2)) + "px",
					    top: Math.floor(viewport.t + (p ? p.t : (viewport.h - mb.h) / 2)) + "px"
				    });
				} else {
				    dojo.style(node,{
					    top: Math.floor(viewport.t + (p ? p.t : (viewport.h - mb.h) / 2)) + "px"
				    });
				}
			}

	},


    /**
     * stuff we need to do before showing the Dialog for the first
     * time (but we defer it until right beforehand, for
     * performance reasons)
     */	
	_setup: function() {

			var node = this.domNode;

			if(this.titleBar && this.draggable){
				this._moveable = (dojo.isIE == 6) ?
					new dojo.dnd.TimedMoveable(node, { handle: this.titleBar }) :	// prevent overload, see #5285
					new dojo.dnd.Moveable(node, { handle: this.titleBar, timeout: 0 });
				dojo.subscribe("/dnd/move/stop",this,"_endDrag");
			}else{
				dojo.addClass(node,"dijitDialogFixed"); 
			}

			this._underlay = new dijit.DialogUnderlay({
				id: this.id+"_underlay", // TODO: Dojo 1.3.0 incompatibility?
				"class": dojo.map(this["class"].split(/\s/), function(s){ return s+"_underlay"; }).join(" ")
			}); 
			
			this._underlay.style.display="none";

			var underlay = this._underlay;

			this._fadeIn = dojo.fadeIn({
				node: node,
				duration: this.duration
			 });

			this._fadeOut = dojo.fadeOut({
				node: node,
				duration: this.duration,
				onEnd: function(){
					node.style.visibility="hidden";
					node.style.top = "-9999px";
					underlay.hide();
				}
			 });
	},
	
	
	/**
	 * Show the dialog but without the underlay layer
	 */
	show: function(){
	    // summary: display the dialog

			if(this.open){ return; }
			
			// first time we show the dialog, there's some initialization stuff to do			
			if(!this._alreadyInitialized){
				this._setup();
				this._alreadyInitialized=true;
			}

			if(this._fadeOut.status() == "playing"){
				this._fadeOut.stop();
			}

			this._modalconnects.push(dojo.connect(window, "onscroll", this, "layout"));
			//this._modalconnects.push(dojo.connect(window, "onresize", this, "layout"));
			//this._modalconnects.push(dojo.connect(dojo.doc.documentElement, "onkeypress", this, "_onKey"));

			dojo.style(this.domNode, {
				opacity:0,
				visibility:""
			});
			
			this.open = true;
			this._loadCheck(); // lazy load trigger

			this._size();
			this._position();

			this._fadeIn.play();

			this._savedFocus = dijit.getFocus(this);

			if(this.autofocus){
				// find focusable Items each time dialog is shown since if dialog contains a widget the 
				// first focusable items can change
				this._getFocusItems(this.domNode);
	
				// set timeout to allow the browser to render dialog
				setTimeout(dojo.hitch(dijit,"focus",this._firstFocusItem), 50);
			}
	},
	
	savePosition: function() {
		data = new Object();
		data.position = dojo.coords(this.domNode); 
	}
	

});
	