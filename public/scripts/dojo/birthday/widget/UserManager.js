/**
 * Custom widget for handling user data
 */
dojo.require("dijit._Widget");
dojo.require("dijit._Templated");

dojo.provide("birthday.widget.UserManager");

dojo.declare("birthday.widget.UserManager",  [dijit._Widget, dijit._Templated],
	{
	
    /**
     * the path to the template
     */
    templateString: '',
    templatePath: dojo.moduleUrl("birthday.widget","templates/UserManager.html"), 
    
    contentNode: null,
    saveButtonNode: null,
    deleteButtonNode: null,
    additionalDataNode: null,
    
    emptyData: {
        id: "",
        firstname: "",
        secondname: "",
        birthdate: new Date()
    }, 
    
    userData: {},
    
    additionalData: null,
        
	postCreate: function() {
		
        this.inherited(arguments);
        
        //copy content from original node
        //var domContent = this.srcNodeRef.innerHTML;
        //this.contentNode.innerHTML = domContent;
       
        dojo.parser.parse(this.contentNode);
        dojo.parser.parse(this.deleteButtonNode);
        dojo.parser.parse(this.saveButtonNode);
        return;
        /*
        var additionalDataString = '{a: "a"}';
        for(var i = 0; i < this.contentNode.childNodes.length; ++i)
        {
            if(this.contentNode.childNodes[i].id == "additionalData") {
                additionalDataString = this.contentNode.childNodes[i].innerHTML;
                this.contentNode.removeChild(this.contentNode.childNodes[i]);
                break;
            }
        }
        
        alert(additionalDataString);
        
        this.additionalData = eval(additionalDataString);
		//dojo.connect(this.titleBar, "onmouseup", this, "savePosition");
		//var self = this;
		console.debug(this.additionalData);
	    */
	},
	
	addUser: function(data) {
	    this.clearData();
	    
	    var birthdate = new Date();
	    
	    birthdate.setDate(data.day);
	    birthdate.setFullYear(data.year);
	    birthdate.setMonth(data.month - 1);
	    
	    var userData = this.emptyData;
	    userData.birthdate = birthdate;
	    this.setData(userData);
	    
	    this.deleteButtonNode.style.display = "none";
	    
	},
	
    editUser: function(id) {
        //this.clearData();
                
        this.deleteButtonNode.style.display = "block";
        
        if(id == null) {
            alert('No id passed!');
            return;
        }
        
        var self = this;
        
        dojo.xhrGet({
            url: '/user/edit/id/' + id,
            handleAs: 'json',

            error: function(responseObject, ioArgs){
                //alert('Communication failed');
            },
            
            load: function(responseObject, ioArgs){
                if (responseObject.toString() == 'false'){
                    alert('Could not delete');
                } else {

                    var _data = responseObject;
                    
                    var birthdate = new Date();

                    birthdate.setDate(responseObject.birthday);
                    birthdate.setMonth(responseObject.birthmonth - 1);
                    birthdate.setFullYear(responseObject.birthyear);

                    _data.birthdate = birthdate;

                    self.setData(_data);

                }
            }
        });
        
    },	
	
	setData: function(_data) {

	    this.userData = _data;
        for(var iKey in this.userData) {
            if(dijit.byId(iKey)) {
                dijit.byId(iKey).attr('value', this.userData[iKey]);
            } else {
                console.debug("setData: key " + iKey + "doesn't exist");
            }            
        }	    
	},
	
	clearData: function() {
	    for(var iKey in this.emptyData) {
	        if(dijit.byId(iKey)) {
	            dijit.byId(iKey).attr('value', this.emptyData[iKey]);
	        } else {
	            console.debug("clearData: key " + iKey + "doesn't exist");
	        }
	    }
	},
	
	
	getUserDataFromForm: function() {
	    var newUserData = {};
	    for(var _iKey in this.emptyData) {
            if(dijit.byId(_iKey)) {
                console.debug(_iKey + " " + dijit.byId(_iKey).attr('value'));
                if(_iKey == "birthdate") {
                    newUserData[_iKey] = dijit.byId(_iKey).valueNode.value;
                } else {
                    newUserData[_iKey] = dijit.byId(_iKey).attr('value');
                }
            } else {
                console.debug("fillUserDataFromForm: key " + _iKey + "doesn't exist");
            }
	    }
	    
	    console.debug(newUserData);
	    return newUserData;
	    
	},
	
	saveUser: function() {
        
	    
	    var content = this.getUserDataFromForm();
	    console.debug(content);

	    
        var self = this;
        
        
        dojo.xhrPost({
            url: '/user/save',
            handleAs: 'text',

            content: content,
            error: function(responseObject, ioArgs){
                alert('Communication failed');
            },
            
            load: function(responseObject, ioArgs){
                if (responseObject.toString() == 'false'){
                    alert('Could not save');
                } else {

                    location.href="/";

                }
            }
        });
	},
	
	deleteUser: function() {
	    this.deleteUserById(null);
	},
	
	deleteUserById: function(id) {
	    
	    if(id === null) {
	        if(this.userData.id === null) {
    	        alert('No id passed!');
    	        return;
	        } else {
	            id = this.userData.id;
	        }
	    }
	    console.debug(id);
	    var self = this;
	    
	    var content = {};
	    content.id = id;
	    
        dojo.xhrPost({
            url: '/user/delete',
            content: content,
            handleAs: 'text',

            error: function(responseObject, ioArgs){
                alert('Communication failed');
            },
            
            load: function(responseObject, ioArgs){
                if (responseObject.toString() == 'false'){
                    alert('Could not delete');
                } else {
                    self.clearData();
                    
                    location.href = "/";
                }
            }
        });
	}

});
