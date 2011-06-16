// user javascript

var user = {};

user.adminLevels = new Array();
user.users;

user.showNewUser = function(){
    var newUserForm = new Ext.FormPanel({
        labelWidth:100,
        url:'./user/createuser',
        frame:true,
        width:275,
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.ComboBox({
            fieldLabel:'Admin Level',
            width: 120,
            store: user.adminLevelDataStore(),
            typeAhead: false,
            forceSelection:true,
            editable:false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            selectOnFocus:true,
            emptyText: 'Admin Level..',
            hiddenName: 'ADMIN_LEVEL',
            allowBlank:false,
            triggerAction: 'all'
        }),{
            fieldLabel:'Username',
            name:'USERNAME',
            allowBlank:false
        },{
            fieldLabel:'Password',
            name:'PASSWORD',
            inputType:'password',
            allowBlank:false
        },{
            fieldLabel:'First Name',
            name:'FNAME',
            allowBlank:true
        },{
            fieldLabel:'Last Name',
            name:'LNAME',
            allowBlank:true
        },{
	        xtype:'hidden',
			name:'lic'
	    },{
            fieldLabel:'Email',
            name:'EMAIL',
            allowBlank:true
        }],
        buttons:[{
            text:'Save',
            formBind: true,
            handler:function(){
				newUserForm.getForm().findField('lic').setValue(main.LIC);
                newUserForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        user.getAllusers();
                        Ext.MessageBox.alert('Success', "User Created");
                        win.destroy();
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Error!', obj.errors.reason);
                        }
                        else
                        {
                            Ext.Msg.alert('Warning!', 'Server is unreachable');
                        }
                    }
                });
            }
        },
        {
            text: 'Cancel',
            formBind: false,
            handler:function(){
                win.destroy();
            }
        }]
    });
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:300,
        height:250,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Create User',
        items: [newUserForm]
    });
    win.show();
};

user.showUserInformation = function(index){
    var userObj   = user.users[index];
    var username  = userObj.username.replace(/'/gi, "\\'");
	
    var editUserForm = new Ext.FormPanel({
        labelWidth:100,
        url:'./user/updateuser',
        frame:true,
        width:275,
        defaultType:'textfield',
        monitorValid:true,
        items:[new Ext.form.ComboBox({
            fieldLabel:'Status',
            width: 120,
            store: new Ext.data.SimpleStore({
                fields: ['statusValue', 'statusDescription'],
                data : [['1','Active'],['0','Inactive']]
            }),
            typeAhead: false,
            displayField: 'statusDescription',
            valueField: 'statusValue',
            mode: 'local',
            forceSelection:true,
            editable:false,
            selectOnFocus:true,
            emptyText: 'Status',
            hiddenName: 'ACTIVE',
            allowBlank:false,
            triggerAction: 'all',
            value:userObj.active
        }),new Ext.form.ComboBox({
            fieldLabel:'Admin Level',
            width: 120,
            store: user.adminLevelDataStore(),
            typeAhead: false,
            displayField: 'desc',
            valueField: 'id',
            mode: 'local',
            forceSelection:true,
            editable:false,
            selectOnFocus:true,
            emptyText: 'Admin Level..',
            hiddenName: 'ADMIN_LEVEL',
            allowBlank:false,
            triggerAction: 'all',
            value:userObj.adminLevelId
        }),{
            fieldLabel:'Username',
            name:'USERNAME',
            value:username,
            allowBlank:false
        },{
            fieldLabel:'Password',
            name:'PASSWORD',
            inputType:'password',
            value:userObj.password,
            allowBlank:false
        },{
            fieldLabel:'First Name',
            name:'FNAME',
            value:userObj.fName,
            allowBlank:true
        },{
            fieldLabel:'Last Name',
            name:'LNAME',
            value:userObj.lName,
            allowBlank:true
        },{
	        xtype:'hidden',
			name:'lic'
	    },{
            fieldLabel:'Email',
            name:'EMAIL',
            value:userObj.email,
            allowBlank:true
        },new Ext.form.Hidden({
            name:'USER_ID',
            value:userObj.id
        })],
        buttons:[{
            text:'Save',
            formBind: true,
            handler:function(){
                editUserForm.getForm().findField('lic').setValue(main.LIC);
                editUserForm.getForm().submit({
                    method:'POST',
                    waitTitle:'Connecting',
                    waitMsg:'Sending data...',
                    success:function(form, action)
                    {
                        user.getAllusers();
                        Ext.MessageBox.alert('Success', "User Updated");
                        win.destroy();
                    },
                    failure:function(form, action)
                    {
                        if(action.failureType == 'server')
                        {
                            obj = Ext.util.JSON.decode(action.response.responseText);
                            Ext.Msg.alert('Error!', obj.errors.reason);
                        }
                        else
                        {
                            Ext.Msg.alert('Warning!', 'Server is unreachable');
                        }
                    }
                });
            }
        },
        {
            text: 'Cancel',
            formBind: false,
            handler:function(){
                win.destroy();
            }
        }]
    });
	
    if(win){
        win.destroy();
    }
    win = new Ext.Window({
        layout:'fit',
        width:300,
        height:280,
        closable: false,
        resizable: false,
        plain: true,
        modal: true,
        title: 'Edit User',
        items: [editUserForm]
    });
    win.show();
};

user.activeConvertor = function(val){
    if(val == 1){
        return 'Yes';
    }
    else{
        return 'No';
    }
};

user.buildUserList = function(admins){
    user.users = admins;
    var length = admins.length;
    var data = '[';
    for(var i = 0; i < length; i++){
		
        var username  = admins[i].username.replace(/'/gi, "\\'");
        var lastLogin = admins[i].lastLogin;
        if(lastLogin == null){
            lastLogin = 'N/A';
        }
		
        data += "['<a href=\"javascript:user.showUserInformation("+i+");\">"+username+"</a>',";
        data += "'"+lastLogin+"',";
        data += "'"+user.activeConvertor(admins[i].active)+"',";
        if(i == (length - 1)){
            data += "]";
        }
        else{
            data += "],";
        }
    }
    data += ']';
	
    var dataObj = eval(data);
	
    var grid = grids.createAdminListGrid(dataObj);
    try{
        userPanel.remove(userPanel.getComponent(0))
        }catch(ex){/*do nothing*/};
    userPanel.add(grid);
    viewport.doLayout();
};

user.getAllusers = function(){
    try{
        userPanel.remove(userPanel.getComponent(0))
        }catch(ex){/*do nothing*/};
    userPanel.add(main.insertLoadingPanel());
    viewport.doLayout();
	
    Ext.Ajax.request({
        url: './user/userlist',
        method:'post',
        params: {
            AGENT_ID:agent.AGENT_ID,
            ZONE_ID:zones.ZONE_ID,
			lic:main.LIC
            },
        success:function(response){
            obj = Ext.util.JSON.decode(response.responseText);
            user.buildUserList(obj.admins);
        },
        failure:function(response){
            Ext.Msg.alert('Error!','Error Getting Users');
        }
    });
};

user.adminLevelDataStore = function(){
    var simpleDataStore = new Ext.data.SimpleStore({
        fields: ['id', 'desc']
        });
    adminLevels = user.adminLevels;
    var length = adminLevels.length;
    for(var i = 0; i < length; i++){
        simpleDataStore.add(new Ext.data.Record(adminLevels[i]));
    }
    return simpleDataStore;
};